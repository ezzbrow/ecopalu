<?php

namespace App\Controllers;

use App\Models\KategoriSampahModel;
use App\Models\PenjemputanModel;
use App\Models\TransaksiCoinModel;
use App\Models\UserModel;

class PenjemputanController extends BaseController
{
    protected $penjemputanModel;
    protected $kategoriModel;
    protected $userModel;
    protected $coinModel;

    public function __construct()
    {
        $this->penjemputanModel = new PenjemputanModel();
        $this->kategoriModel    = new KategoriSampahModel();
        $this->userModel        = new UserModel();
        $this->coinModel        = new TransaksiCoinModel();
    }

    /**
     * Daftar penjemputan — tab difilter via ?status=...
     * Status yang dipakai di UI: semua, menunggu, disetujui, ditolak, selesai,
     * menunggu_pemberian_poin (khusus Admin EcoPalu).
     *
     * Q1 (Langkah C): role & ownership filter berdasarkan session, BUKAN query string.
     *   - admin: lihat semua data
     *   - banksampah: hanya status='disetujui' (siap dijemput)
     *   - user: hanya row miliknya sendiri (user_id = session)
     */
    public function index()
    {
        $role     = session()->get('role');
        $userId   = (int) session()->get('user_id');
        $tab      = $this->request->getGet('status') ?? 'semua';

        $builder = $this->penjemputanModel->orderBy('created_at', 'DESC');

        // Q1: Filter data per role
        switch ($role) {
            case 'banksampah':
                // Hanya yang sudah disetujui admin (= "Menunggu" Bank Sampah)
                $builder->where('status', 'disetujui');
                break;
            case 'user':
                // Hanya row milik sendiri
                $builder->where('user_id', $userId);
                break;
            case 'admin':
            default:
                // Admin: tidak ada filter tambahan, lihat semua
                break;
        }

        // Tab filter (sesuai role, untuk UI)
        switch ($tab) {
            case 'menunggu':
                $builder->where('status', 'menunggu');
                break;
            case 'disetujui':
                $builder->where('status', 'disetujui');
                break;
            case 'ditolak':
                $builder->where('status', 'ditolak');
                break;
            case 'selesai':
                $builder->where('status', 'selesai');
                break;
            case 'menunggu_pemberian_poin':
                $builder->where('status', 'menunggu_pemberian_poin');
                break;
            default:
                // semua / unknown: no filter
                break;
        }

        $data = [
            'penjemputan' => $builder->findAll(),
            'tab'         => $tab,
            'role'        => $role,
        ];

        return view('penjemputan/index', $data);
    }

    /**
     * Form pengajuan oleh User.
     * Validasi "tanggal_jemput wajib jatuh di hari penjemputan (Rabu/Sabtu)"
     * dilakukan di store() — di sini form selalu ditampilkan, JS onchange
     * memberi feedback UX, dan backend store() jadi otoritas.
     */
    public function create()
    {
        $data['kategori'] = $this->kategoriModel->findAll();
        return view('penjemputan/create', $data);
    }

    /**
     * Simpan pengajuan dari User.
     * Default status = 'menunggu' (menunggu verifikasi Admin EcoPalu).
     */
    public function store()
    {
        $userId = (int) session()->get('user_id');
        $tanggalJemput = (string) $this->request->getPost('tanggal_jemput');

        // Validasi: tanggal_jemput wajib jatuh di hari penjemputan (Rabu/Sabtu)
        if (! isHariPenjemputan($tanggalJemput)) {
            return redirect()->back()->withInput()->with('error',
                'Tanggal penjemputan hanya bisa dipilih pada hari Rabu atau Sabtu.');
        }

        // Validasi ringan foreign key existence
        $kategoriId = $this->request->getPost('kategori_sampah_id');
        if (! $this->kategoriModel->find($kategoriId)) {
            return redirect()->back()->withInput()->with('error', 'Kategori sampah tidak valid.');
        }
        if ($userId && ! $this->userModel->find($userId)) {
            return redirect()->back()->withInput()->with('error', 'Session user tidak valid. Silakan login ulang.');
        }

        $id = $this->penjemputanModel->insert([
            'user_id'            => $userId,
            'kategori_sampah_id' => $kategoriId,
            'berat'              => $this->request->getPost('berat'),
            'tanggal_jemput'     => $tanggalJemput,
            'alamat'             => $this->request->getPost('alamat'),
            'latitude'           => $this->request->getPost('latitude'),
            'longitude'          => $this->request->getPost('longitude'),
            'status'             => 'menunggu',
        ]);

        // Notifikasi ke semua Admin EcoPalu (role=admin)
        $admins = $this->userModel->where('role', 'admin')->findAll();
        foreach ($admins as $admin) {
            insertNotifikasi(
                $admin['id'],
                'admin',
                'Pengajuan penjemputan baru',
                'Ada pengajuan penjemputan sampah baru yang menunggu verifikasi.',
                'penjemputan',
                $id
            );
        }

        return redirect()->to('/penjemputan?status=menunggu&role=user')
            ->with('success', 'Pengajuan berhasil dikirim. Menunggu verifikasi Admin EcoPalu.');
    }

    public function edit($id)
    {
        $row = $this->penjemputanModel->find($id);
        if (! $row) {
            return redirect()->to('/penjemputan')
                ->with('error', 'Data tidak ditemukan.');
        }

        // Q3: Validasi granular — user hanya boleh edit row miliknya sendiri
        // DAN status masih 'menunggu'. Admin bebas.
        $sessionRole = session()->get('role');
        $sessionUserId = (int) session()->get('user_id');
        if ($sessionRole === 'user') {
            if ((int) $row['user_id'] !== $sessionUserId) {
                return redirect()->to('/penjemputan')
                    ->with('error', 'Anda tidak boleh mengedit pengajuan milik user lain.');
            }
            if ($row['status'] !== 'menunggu') {
                return redirect()->to('/penjemputan')
                    ->with('error', 'Pengajuan yang sudah diverifikasi tidak dapat diedit. Hubungi admin untuk perubahan.');
            }
        }

        $data['penjemputan'] = $row;
        $data['kategori']    = $this->kategoriModel->findAll();
        return view('penjemputan/edit', $data);
    }

    public function update($id)
    {
        $row = $this->penjemputanModel->find($id);
        if (! $row) {
            return redirect()->to('/penjemputan')
                ->with('error', 'Data tidak ditemukan.');
        }

        // Q3: Validasi granular sama dengan edit()
        $sessionRole = session()->get('role');
        $sessionUserId = (int) session()->get('user_id');
        if ($sessionRole === 'user') {
            if ((int) $row['user_id'] !== $sessionUserId) {
                return redirect()->to('/penjemputan')
                    ->with('error', 'Anda tidak boleh mengedit pengajuan milik user lain.');
            }
            if ($row['status'] !== 'menunggu') {
                return redirect()->to('/penjemputan')
                    ->with('error', 'Pengajuan yang sudah diverifikasi tidak dapat diedit. Hubungi admin untuk perubahan.');
            }
        }

        $this->penjemputanModel->update($id, [
            'kategori_sampah_id' => $this->request->getPost('kategori_sampah_id'),
            'berat'             => $this->request->getPost('berat'),
            'tanggal_jemput'    => $this->request->getPost('tanggal_jemput'),
            'alamat'            => $this->request->getPost('alamat'),
            'latitude'          => $this->request->getPost('latitude'),
            'longitude'         => $this->request->getPost('longitude'),
            'status'            => $this->request->getPost('status'),
        ]);

        return redirect()->to('/penjemputan?role=' . $sessionRole);
    }

    public function delete($id)
    {
        $this->penjemputanModel->delete($id);
        return redirect()->to('/penjemputan?role=admin');
    }

    // ---------- ACTIONS KHUSUS ROLE ----------

    /**
     * Admin EcoPalu: verifikasi TERIMA → status jadi 'disetujui'
     * lalu otomatis kirim notif ke Bank Sampah.
     */
    public function setujui($id)
    {
        $adminId = (int) ($this->request->getPost('admin_id') ?? 1); // sementara hardcode, nanti dari session

        $row = $this->penjemputanModel->find($id);
        if (! $row) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $this->penjemputanModel->update($id, [
            'status'       => 'disetujui',
            'verified_by'  => $adminId,
            'verified_at'  => date('Y-m-d H:i:s'),
        ]);

        // Notif ke User: pengajuan disetujui
        insertNotifikasi(
            $row['user_id'],
            'user',
            'Pengajuan disetujui',
            'Pengajuan penjemputan sampah Anda telah diverifikasi dan diteruskan ke Bank Sampah.',
            'penjemputan',
            $id
        );

        // Notif ke semua Bank Sampah: ada permintaan penjemputan
        $banks = $this->userModel->where('role', 'banksampah')->findAll();
        foreach ($banks as $bs) {
            insertNotifikasi(
                $bs['id'],
                'banksampah',
                'Permintaan penjemputan baru',
                'Ada permintaan penjemputan sampah yang perlu dijemput. Cek lokasi & alamat.',
                'penjemputan',
                $id
            );
        }

        return redirect()->to('/penjemputan?status=disetujui&role=admin')
            ->with('success', 'Pengajuan disetujui dan diteruskan ke Bank Sampah.')
            ->with('popup', 'Pengajuan berhasil disetujui. Penjemputan telah diteruskan ke Bank Sampah Kabelotapura.');
    }

    /**
     * Admin EcoPalu: verifikasi TOLAK dengan alasan.
     */
    public function tolak($id)
    {
        $adminId  = (int) ($this->request->getPost('admin_id') ?? 1);
        $alasan   = trim((string) $this->request->getPost('alasan_penolakan'));

        if ($alasan === '') {
            return redirect()->back()->with('error', 'Alasan penolakan wajib diisi.');
        }

        $row = $this->penjemputanModel->find($id);
        if (! $row) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $this->penjemputanModel->update($id, [
            'status'           => 'ditolak',
            'verified_by'      => $adminId,
            'verified_at'      => date('Y-m-d H:i:s'),
            'alasan_penolakan' => $alasan,
        ]);

        insertNotifikasi(
            $row['user_id'],
            'user',
            'Pengajuan ditolak',
            'Pengajuan penjemputan Anda ditolak. Alasan: ' . $alasan,
            'penjemputan',
            $id
        );

        return redirect()->to('/penjemputan?status=ditolak&role=admin')
            ->with('success', 'Pengajuan ditolak.');
    }

    /**
     * Bank Sampah: konfirmasi SELESAI setelah sampah fisik diterima.
     * Status sementara 'menunggu_pemberian_poin' sampai Admin EcoPalu finalisasi poin.
     */
    public function konfirmasiSelesai($id)
    {
        $banksampahId = (int) ($this->request->getPost('banksampah_id') ?? 1);

        $row = $this->penjemputanModel->find($id);
        if (! $row || $row['status'] !== 'disetujui') {
            return redirect()->back()->with('error',
                'Hanya pengajuan berstatus disetujui yang dapat dikonfirmasi selesai.');
        }

        $this->penjemputanModel->update($id, [
            'status'       => 'menunggu_pemberian_poin',
            'confirmed_by' => $banksampahId,
            'confirmed_at' => date('Y-m-d H:i:s'),
        ]);

        // Notif ke semua Admin EcoPalu
        $admins = $this->userModel->where('role', 'admin')->findAll();
        foreach ($admins as $admin) {
            insertNotifikasi(
                $admin['id'],
                'admin',
                'Sampah sudah dijemput',
                'Bank Sampah telah mengkonfirmasi penjemputan. Silakan finalisasi pemberian poin ke user.',
                'penjemputan',
                $id
            );
        }

        // Tidak ada notif "poin berhasil dikirim" ke Bank Sampah — sesuai spec

        return redirect()->to('/penjemputan?status=selesai&role=banksampah')
            ->with('success', 'Konfirmasi selesai terkirim. Menunggu Admin EcoPalu memfinalisasi poin.');
    }

    /**
     * Admin EcoPalu: finalisasi — hitung & insert koin ke transaksi_coin, status jadi 'selesai'.
     * koin = berat × coin_value kategori.
     */
    public function finalisasiPoin($id)
    {
        $adminId = (int) ($this->request->getPost('admin_id') ?? 1);

        $row = $this->penjemputanModel->find($id);
        if (! $row || $row['status'] !== 'menunggu_pemberian_poin') {
            return redirect()->back()->with('error',
                'Hanya pengajuan berstatus menunggu pemberian poin yang dapat difinalisasi.');
        }

        $kategori = $this->kategoriModel->find($row['kategori_sampah_id']);
        $coinPerKg = $kategori ? (int) $kategori['coin_value'] : 0;
        $totalCoin = (int) round(((float) $row['berat']) * $coinPerKg);

        // Insert ke transaksi_coin (satu baris per penjemputan selesai)
        $this->coinModel->insert([
            'user_id'           => $row['user_id'],
            'kategori_sampah_id' => $row['kategori_sampah_id'],
            'berat'             => $row['berat'],
            'total_coin'        => $totalCoin,
        ]);

        $this->penjemputanModel->update($id, [
            'status'          => 'selesai',
            'coin_award'      => $totalCoin,
            'coin_awarded_at' => date('Y-m-d H:i:s'),
            'finalized_by'    => $adminId,
        ]);

        insertNotifikasi(
            $row['user_id'],
            'user',
            'Poin berhasil ditambahkan',
            'Selamat! Anda mendapat ' . $totalCoin . ' coin dari penjemputan sampah.',
            'poin',
            $id
        );

        return redirect()->to('/penjemputan?status=menunggu_pemberian_poin&role=admin')
            ->with('success', 'Poin berhasil diberikan: ' . $totalCoin . ' coin.');
    }

    /**
     * Admin EcoPalu: tolak pemberian poin (sampah gagal di proses),
     * kembali ke status 'ditolak' dengan alasan.
     */
    public function tolakPoin($id)
    {
        $adminId = (int) ($this->request->getPost('admin_id') ?? 1);
        $alasan  = trim((string) $this->request->getPost('alasan_penolakan'));

        if ($alasan === '') {
            return redirect()->back()->with('error', 'Alasan pembatalan poin wajib diisi.');
        }

        $row = $this->penjemputanModel->find($id);
        if (! $row) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $this->penjemputanModel->update($id, [
            'status'           => 'ditolak',
            'alasan_penolakan' => $alasan,
            'coin_award'       => 0,
            'finalized_by'     => $adminId,
        ]);

        insertNotifikasi(
            $row['user_id'],
            'user',
            'Poin tidak diberikan',
            'Poin tidak dapat diberikan. Alasan: ' . $alasan,
            'poin',
            $id
        );

        return redirect()->to('/penjemputan?status=menunggu_pemberian_poin&role=admin')
            ->with('success', 'Poin dibatalkan.');
    }
}