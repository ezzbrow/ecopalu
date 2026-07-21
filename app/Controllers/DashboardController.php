<?php

namespace App\Controllers;

use App\Models\NotificationModel;
use App\Models\PencairanModel;
use App\Models\PenjemputanModel;
use App\Models\TransaksiCoinModel;
use App\Models\UserModel;

/**
 * DashboardController — 3 method (user, admin, banksampah).
 * Pakai layout 'layouts/dashboard' (role-aware sidebar + topbar notifikasi).
 */
class DashboardController extends BaseController
{
    protected $notifModel;

    public function __construct()
    {
        $this->notifModel = new NotificationModel();
    }

    /**
     * Hitung jumlah notifikasi belum dibaca untuk user session saat ini.
     */
    private function getUnreadCount(): int
    {
        $userId = (int) (session('user_id') ?? 0);
        if ($userId <= 0) {
            return 0;
        }
        return $this->notifModel
            ->where('recipient_user_id', $userId)
            ->where('is_read', 0)
            ->countAllResults();
    }

    /**
     * Ambil 10 notifikasi terbaru untuk topbar dropdown.
     */
    private function getRecentNotif(int $limit = 10): array
    {
        $userId = (int) (session('user_id') ?? 0);
        if ($userId <= 0) {
            return [];
        }
        return $this->notifModel->getForUser($userId, $limit);
    }

    public function user()
    {
        $userId      = (int) (session('user_id') ?? 0);
        $coinModel   = new TransaksiCoinModel();
        $penjemputanModel = new PenjemputanModel();
        $pencairanModel   = new PencairanModel();

        // Total poin dari transaksi_coin (hanya yang status=selesai, supaya tidak double-count).
        $totalPoin = 0;
        $totalTransaksi = 0;
        if ($userId > 0) {
            $row = $coinModel
                ->selectSum('total_coin')
                ->select('COUNT(*) AS total_transaksi', false)
                ->where('user_id', $userId)
                ->where('deleted_at', null)
                ->get()
                ->getRow();
            $totalPoin       = (int) ($row->total_coin ?? 0);
            $totalTransaksi  = (int) ($row->total_transaksi ?? 0);
        }

        // Riwayat Penukaran: agregasi pencairan_reward per user.
        $riwayatCoinDicairkan = 0;
        $riwayatRupiahDicairkan = 0;
        $jumlahPencairanBerhasil = 0;
        if ($userId > 0) {
            $rRow = $pencairanModel
                ->selectSum('nominal_coin')
                ->selectSum('nominal_rupiah')
                ->select('COUNT(*) AS jumlah', false)
                ->where('user_id', $userId)
                ->where('status', 'berhasil')
                ->where('deleted_at', null)
                ->get()
                ->getRow();
            $riwayatCoinDicairkan   = (int) ($rRow->nominal_coin ?? 0);
            $riwayatRupiahDicairkan = (int) ($rRow->nominal_rupiah ?? 0);
            $jumlahPencairanBerhasil = (int) ($rRow->jumlah ?? 0);
        }

        // Status pengajuan user (5 status spec, TIDAK termasuk menunggu_pemberian_poin)
        $userPenjemputan = [];
        if ($userId > 0) {
            $userPenjemputan = $penjemputanModel
                ->where('user_id', $userId)
                ->where('deleted_at', null)
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->findAll();
        }

        $data = [
            'title'             => 'Dashboard User — EcoPalu',
            'unread_count'      => $this->getUnreadCount(),
            'notifList'         => $this->getRecentNotif(),

            // Poin & riwayat
            'total_poin'               => $totalPoin,
            'total_transaksi'          => $totalTransaksi,
            'riwayat_coin_dicairkan'   => $riwayatCoinDicairkan,
            'riwayat_rupiah_dicairkan' => $riwayatRupiahDicairkan,
            'jumlah_pencairan_berhasil' => $jumlahPencairanBerhasil,

            // Status pengajuan (5 row terakhir)
            'user_penjemputan'         => $userPenjemputan,
        ];
        return view('dashboard/user', $data);
    }

    /**
     * Dashboard Admin EcoPalu — 6 card + grafik reward dicairkan + tabel 6-tab.
     * TODO: nominal_rupiah masih NULL sampai rate coin→rupiah final.
     *       Sementara grafik pakai SUM(nominal_coin).
     */
    public function admin()
    {
        $penjemputanModel = new PenjemputanModel();
        $userModel       = new UserModel();
        $coinModel       = new TransaksiCoinModel();
        $pencairanModel  = new PencairanModel();

        // Fetch semua row penjemputan (admin lihat semua, Q1 spec).
        // View yang filter per tab dilakukan di sisi view.
        $adminPenjemputan = $penjemputanModel
            ->where('deleted_at', null)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $data = [
            'title'        => 'Dashboard Admin — EcoPalu',
            'unread_count' => $this->getUnreadCount(),
            'notifList'    => $this->getRecentNotif(),

            // 6 card statistik (semua null-coalesce ke 0 supaya tidak error saat tabel kosong)
            'card_disetujui'      => $this->countStatus($penjemputanModel, 'disetujui'),
            'card_menunggu'       => $this->countStatus($penjemputanModel, 'menunggu'),
            'card_menunggu_poin'  => $this->countStatus($penjemputanModel, 'menunggu_pemberian_poin'),
            'card_selesai'        => $this->countStatus($penjemputanModel, 'selesai'),
            'card_user_aktif'     => $this->countUsersActive($userModel),
            'card_total_coin'     => $this->sumTotalCoin($coinModel),

            // Grafik reward dicairkan 7 hari + flag empty state
            'pencairan_7hari'         => $pencairanModel->getLast7Days(),
            'pencairan_total_record'  => $pencairanModel->totalRecord(),

            // Tabel 6-tab (view filter per tab via ?tab=)
            'admin_penjemputan'       => $adminPenjemputan,
        ];

        return view('dashboard/admin', $data);
    }

    /**
     * Helper: count row dengan status tertentu di tabel penjemputan.
     */
    private function countStatus(PenjemputanModel $m, string $status): int
    {
        return $m->where('status', $status)
                 ->where('deleted_at', null)
                 ->countAllResults();
    }

    /**
     * Helper: count user dengan role='user' (exclude soft-deleted).
     */
    private function countUsersActive(UserModel $m): int
    {
        return $m->where('role', 'user')
                 ->where('deleted_at', null)
                 ->countAllResults();
    }

    /**
     * Helper: SUM total_coin dari transaksi_coin. Null-coalesce ke 0.
     */
    private function sumTotalCoin(TransaksiCoinModel $m): int
    {
        $row = $m->selectSum('total_coin')
                  ->where('deleted_at', null)
                  ->get()
                  ->getRow();
        return (int) ($row->total_coin ?? 0);
    }

    public function banksampah()
    {
        $penjemputanModel = new PenjemputanModel();

        // Bank Sampah hanya perlu lihat 2 status: 'disetujui' (siap dijemput) & 'selesai'.
        $penjemputan = $penjemputanModel
            ->where('deleted_at', null)
            ->whereIn('status', ['disetujui', 'selesai'])
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $countMenunggu  = $penjemputanModel->where('status', 'disetujui')->where('deleted_at', null)->countAllResults();
        $countSelesai   = $penjemputanModel->where('status', 'selesai')->where('deleted_at', null)->countAllResults();

        $data = [
            'title'          => 'Dashboard Bank Sampah — EcoPalu',
            'unread_count'   => $this->getUnreadCount(),
            'notifList'      => $this->getRecentNotif(),
            'penjemputan'    => $penjemputan,
            'count_menunggu' => $countMenunggu,
            'count_selesai'  => $countSelesai,
        ];
        return view('dashboard/banksampah', $data);
    }
}