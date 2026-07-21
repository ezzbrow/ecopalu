<?php

namespace App\Controllers;

use App\Models\PencairanModel;
use App\Models\TransaksiCoinModel;

/**
 * PencairanController — handle alur pencairan reward (Step 8-10 spec).
 *
 * Alur MANUAL (tanpa integrasi Midtrans/QRIS):
 *   1. User ajukan pencairan via /pencairan/create
 *   2. Admin EcoPalu verifikasi via /pencairan/admin
 *   3. Admin transfer manual ke e-wallet user, tandai "selesai" via markTransferred
 *
 * Rate: 1 coin = Rp 400 (lihat notifikasi_helper::rateCoinToRupiah).
 * Min: 25 coin = Rp 10.000.
 * Kelipatan: Rp 10.000 (preset: 10k, 20k, 50k, 100k).
 *
 * Status enum (sudah ada di tabel): menunggu | diproses | berhasil | ditolak
 *
 * Routes (di app/Config/Routes.php):
 *   GET  /pencairan         : index() — user lihat saldo + riwayat
 *   GET  /pencairan/create  : create() — form
 *   POST /pencairan/store   : store() — simpan
 *   GET  /pencairan/admin   : adminList() — admin lihat semua
 *   POST /pencairan/(:num)/approve          : approve()
 *   POST /pencairan/(:num)/mark-transferred : markTransferred()
 *   POST /pencairan/(:num)/reject          : reject() — butuh POST alasan_penolakan
 */
class PencairanController extends BaseController
{
    protected $pencairanModel;
    protected $coinModel;

    // Konstanta bisnis (idealnya di config atau DB, tapi hardcode OK untuk iterasi ini)
    private const MIN_RUPIAH          = 10000;   // 25 coin @ Rp 400
    private const KELIPATAN_RUPIAH    = 10000;
    private const PRESET_RUPIAH       = [10000, 20000, 50000, 100000];

    public function __construct()
    {
        $this->pencairanModel = new PencairanModel();
        $this->coinModel      = new TransaksiCoinModel();
    }

    /**
     * GET /pencairan
     * Tampilkan saldo coin user + form pencairan + riwayat.
     */
    public function index()
    {
        $userId = (int) (session('user_id') ?? 0);
        if ($userId <= 0) {
            return redirect()->to('/login');
        }

        $saldoCoin     = $this->coinModel->saldoCoin($userId);
        $coinHold      = $this->pencairanModel->totalCoinHold($userId);
        $coinBerhasil  = $this->pencairanModel->totalCoinDicairkanBerhasil($userId);
        $saldoTersedia = $saldoCoin - $coinHold - $coinBerhasil;
        $riwayat       = $this->pencairanModel->getRiwayatByUser($userId, 20);
        $rate          = rateCoinToRupiah();

        $data = [
            'title'             => 'Pencairan Reward — EcoPalu',
            'unread_count'      => (new DashboardController())->getUnreadCountForCurrentUser(),
            'notifList'        => (new DashboardController())->getRecentNotifForCurrentUser(10),
            'saldo_coin'        => $saldoCoin,
            'saldo_hold'        => $coinHold,
            'saldo_berhasil'    => $coinBerhasil,
            'saldo_tersedia'    => max(0, $saldoTersedia),
            'saldo_rupiah'      => max(0, $saldoTersedia) * $rate,
            'rate'              => $rate,
            'min_rupiah'        => self::MIN_RUPIAH,
            'min_coin'          => self::MIN_RUPIAH / $rate,
            'preset'            => self::PRESET_RUPIAH,
            'riwayat'           => $riwayat,
        ];
        return view('pencairan/index', $data);
    }

    /**
     * GET /pencairan/create
     * Form preset nominal.
     */
    public function create()
    {
        $userId = (int) (session('user_id') ?? 0);
        if ($userId <= 0) {
            return redirect()->to('/login');
        }
        $data = $this->buildFormData($userId);
        return view('pencairan/create', $data);
    }

    /**
     * POST /pencairan/store
     * Simpan pengajuan. Validasi: nominal di preset, kelipatan, saldo cukup.
     */
    public function store()
    {
        $userId = (int) (session('user_id') ?? 0);
        if ($userId <= 0) {
            return redirect()->to('/login');
        }

        $rules = [
            'nominal_rupiah'  => 'required|integer|greater_than_equal_to[' . self::MIN_RUPIAH . ']',
            'jenis_ewallet'   => 'required|in_list[dana,ovo,gopay,shopeepay]',
            'nomor_ewallet'   => 'required|min_length[8]|max_length[20]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', 'Validasi gagal: ' . implode('; ', array_values($this->validator->getErrors())));
        }

        $nominalRupiah = (int) $this->request->getPost('nominal_rupiah');
        $rate          = rateCoinToRupiah();
        $nominalCoin   = intdiv($nominalRupiah, $rate);

        // Validasi kelipatan (10k)
        if ($nominalRupiah % self::KELIPATAN_RUPIAH !== 0) {
            return redirect()->back()->withInput()
                ->with('error', 'Nominal harus kelipatan Rp ' . number_format(self::KELIPATAN_RUPIAH, 0, ',', '.'));
        }

        // Validasi saldo cukup
        $saldoTersedia = $this->saldoTersedia($userId);
        if ($nominalCoin > $saldoTersedia) {
            return redirect()->back()->withInput()
                ->with('error', 'Saldo tidak cukup. Saldo tersedia: ' . $saldoTersedia . ' coin.');
        }

        $newId = $this->pencairanModel->insert([
            'user_id'         => $userId,
            'nominal_coin'    => $nominalCoin,
            'nominal_rupiah'  => $nominalRupiah,
            'jenis_ewallet'   => $this->request->getPost('jenis_ewallet'),
            'nomor_ewallet'   => $this->request->getPost('nomor_ewallet'),
            'status'          => 'menunggu',
        ]);

        insertNotifikasi(
            $userId,
            'user',
            'Pengajuan pencairan diterima',
            'Pengajuan pencairan Rp ' . number_format($nominalRupiah, 0, ',', '.') . ' sedang menunggu verifikasi Admin.',
            'pencairan',
            $newId
        );

        return redirect()->to('/pencairan')
            ->with('success', 'Pengajuan pencairan berhasil dikirim. Menunggu verifikasi Admin.');
    }

    /**
     * GET /pencairan/admin
     * List semua pencairan, filter by status (default semua).
     */
    public function adminList()
    {
        $role = session('role') ?? '';
        if ($role !== 'admin') {
            return redirect()->to('/dashboard/user')->with('error', 'Hanya admin yang dapat mengakses.');
        }

        $statusFilter = $this->request->getGet('status') ?? 'semua';
        $builder = $this->pencairanModel
            ->select('pencairan_reward.*, users.name AS user_name, users.email AS user_email')
            ->join('users', 'users.id = pencairan_reward.user_id', 'left')
            ->where('pencairan_reward.deleted_at', null)
            ->orderBy('pencairan_reward.created_at', 'DESC');
        if ($statusFilter !== 'semua') {
            $builder->where('pencairan_reward.status', $statusFilter);
        }
        $rows = $builder->findAll();

        $data = [
            'title'         => 'Pencairan Reward — Admin',
            'unread_count'  => (new DashboardController())->getUnreadCountForCurrentUser(),
            'notifList'    => (new DashboardController())->getRecentNotifForCurrentUser(10),
            'rows'          => $rows,
            'status_filter' => $statusFilter,
            'counts'        => [
                'menunggu' => $this->pencairanModel->where('status', 'menunggu')->where('deleted_at', null)->countAllResults(),
                'diproses' => $this->pencairanModel->where('status', 'diproses')->where('deleted_at', null)->countAllResults(),
                'berhasil' => $this->pencairanModel->where('status', 'berhasil')->where('deleted_at', null)->countAllResults(),
                'ditolak'  => $this->pencairanModel->where('status', 'ditolak')->where('deleted_at', null)->countAllResults(),
            ],
        ];
        return view('pencairan/admin_list', $data);
    }

    /**
     * POST /pencairan/(:num)/approve
     * Admin approve: status menunggu → diproses.
     */
    public function approve($id)
    {
        if (session('role') !== 'admin') {
            return redirect()->to('/dashboard/user')->with('error', 'Hanya admin.');
        }
        $row = $this->pencairanModel->find($id);
        if (! $row || $row['status'] !== 'menunggu') {
            return redirect()->to('/pencairan/admin')
                ->with('error', 'Pengajuan tidak ditemukan atau status bukan menunggu.');
        }
        $this->pencairanModel->update($id, ['status' => 'diproses']);
        insertNotifikasi(
            $row['user_id'],
            'user',
            'Pencairan diproses',
            'Pengajuan pencairan Anda sedang diproses oleh Admin.',
            'pencairan',
            $id
        );
        return redirect()->to('/pencairan/admin?status=diproses')
            ->with('success', 'Pengajuan disetujui untuk diproses.');
    }

    /**
     * POST /pencairan/(:num)/mark-transferred
     * Admin: status diproses → berhasil + catat tanggal_transfer.
     */
    public function markTransferred($id)
    {
        if (session('role') !== 'admin') {
            return redirect()->to('/dashboard/user')->with('error', 'Hanya admin.');
        }
        $row = $this->pencairanModel->find($id);
        if (! $row || $row['status'] !== 'diproses') {
            return redirect()->to('/pencairan/admin')
                ->with('error', 'Pengajuan tidak ditemukan atau status bukan diproses.');
        }
        $this->pencairanModel->update($id, [
            'status'          => 'berhasil',
            'tanggal_transfer' => date('Y-m-d H:i:s'),
        ]);
        insertNotifikasi(
            $row['user_id'],
            'user',
            'Pencairan berhasil',
            'Pencairan Rp ' . number_format((int) ($row['nominal_rupiah'] ?? 0), 0, ',', '.') . ' telah ditransfer ke e-wallet Anda.',
            'pencairan',
            $id
        );
        return redirect()->to('/pencairan/admin?status=berhasil')
            ->with('success', 'Pencairan ditandai selesai. Saldo user telah dihitung otomatis.');
    }

    /**
     * POST /pencairan/(:num)/reject
     * Admin reject: status → ditolak + alasan_penolakan. Saldo coin user TIDAK
     * dikurangi karena belum pernah dipotong (desain hold) — jadi tidak perlu
     * di-restore.
     */
    public function reject($id)
    {
        if (session('role') !== 'admin') {
            return redirect()->to('/dashboard/user')->with('error', 'Hanya admin.');
        }
        $alasan = trim((string) $this->request->getPost('alasan_penolakan'));
        if ($alasan === '') {
            return redirect()->back()
                ->with('error', 'Alasan penolakan wajib diisi.');
        }
        $row = $this->pencairanModel->find($id);
        if (! $row || ! in_array($row['status'], ['menunggu', 'diproses'], true)) {
            return redirect()->to('/pencairan/admin')
                ->with('error', 'Pengajuan tidak ditemukan atau status tidak bisa ditolak.');
        }
        $this->pencairanModel->update($id, [
            'status'            => 'ditolak',
            'alasan_penolakan'  => $alasan,
        ]);
        insertNotifikasi(
            $row['user_id'],
            'user',
            'Pencairan ditolak',
            'Pengajuan pencairan Anda ditolak. Alasan: ' . $alasan,
            'pencairan',
            $id
        );
        return redirect()->to('/pencairan/admin?status=ditolak')
            ->with('success', 'Pengajuan ditolak.');
    }

    /**
     * Helper: build data umum untuk halaman user (saldo, preset, dll).
     */
    private function buildFormData(int $userId): array
    {
        $saldoCoin    = $this->coinModel->saldoCoin($userId);
        $saldoTersedia = $this->saldoTersedia($userId);
        $rate         = rateCoinToRupiah();
        return [
            'title'         => 'Ajukan Pencairan — EcoPalu',
            'unread_count'  => (new DashboardController())->getUnreadCountForCurrentUser(),
            'notifList'    => (new DashboardController())->getRecentNotifForCurrentUser(10),
            'saldo_coin'    => $saldoCoin,
            'saldo_tersedia'=> $saldoTersedia,
            'saldo_rupiah'  => $saldoTersedia * $rate,
            'rate'          => $rate,
            'min_rupiah'    => self::MIN_RUPIAH,
            'min_coin'      => self::MIN_RUPIAH / $rate,
            'preset'        => self::PRESET_RUPIAH,
        ];
    }

    /**
     * Helper: saldo coin yang bisa dicairkan (= total - hold - berhasil).
     */
    private function saldoTersedia(int $userId): int
    {
        $total    = $this->coinModel->saldoCoin($userId);
        $hold     = $this->pencairanModel->totalCoinHold($userId);
        $berhasil = $this->pencairanModel->totalCoinDicairkanBerhasil($userId);
        return max(0, $total - $hold - $berhasil);
    }
}