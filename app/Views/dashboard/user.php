<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

    <!-- ============== DASHBOARD USER (EcoFriend) ============== -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="mb-1">Beranda</h3>
            <p class="text-muted small mb-0">Pantau saldo, ajukan penjemputan, dan kelola akun Anda di sini.</p>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- ============== A. 3 CARD STATISTIK ATAS (grid) ============== -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card stat-card stat-poin h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-coin stat-icon me-2"></i>
                        <small>Total Poin Anda</small>
                    </div>
                    <h3 class="mb-1"><?= number_format((int) ($total_poin ?? 0), 0, ',', '.') ?></h3>
                    <span class="text-muted small">Dari <?= (int) ($total_transaksi ?? 0) ?> transaksi</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card stat-riwayat h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-clock-history stat-icon me-2"></i>
                        <small>Riwayat Penukaran</small>
                    </div>
                    <h3 class="mb-1"><?= number_format((int) ($riwayat_coin_dicairkan ?? 0), 0, ',', '.') ?></h3>
                    <span class="text-muted small">
                        <?= (int) ($jumlah_pencairan_berhasil ?? 0) ?> pencairan berhasil
                        <?php if ((int) ($riwayat_rupiah_dicairkan ?? 0) > 0): ?>
                            · Rp <?= number_format((int) ($riwayat_rupiah_dicairkan ?? 0), 0, ',', '.') ?>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card stat-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-info-circle stat-icon me-2"></i>
                        <small>Syarat Pencairan</small>
                    </div>
                    <p class="mb-0 small text-muted">
                        Minimal pencairan <strong>50kg</strong>, setara ± 1.600–1.700 botol plastik 1,5L.
                        Poin dicairkan manual oleh Admin EcoPalu ke e-wallet Anda.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- ============== B. AJUKAN PENJEMPUTAN (CTA card) ============== -->
    <div class="card cta-card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h5 class="mb-1"><i class="bi bi-truck"></i> Ajukan Penjemputan Sampah</h5>
                    <p class="text-muted small mb-0">
                        Pengajuan hanya bisa di hari <strong>Rabu &amp; Sabtu</strong>
                        (hari operasional penjemputan Bank Sampah Kabelotapura).
                    </p>
                </div>
                <a href="<?= base_url('penjemputan/create') ?>" class="btn btn-eco btn-lg">
                    <i class="bi bi-plus-circle"></i> Ajukan Sekarang
                </a>
            </div>
        </div>
    </div>

    <!-- ============== C. STATUS PENGAJUAN (5 row terakhir) ============== -->
    <div class="card section-card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="mb-0">
                    <i class="bi bi-list-check text-success me-2"></i>
                    Status Pengajuan
                </h5>
                <a href="<?= base_url('penjemputan?role=user') ?>" class="small">Lihat semua →</a>
            </div>

            <?php if (empty($user_penjemputan)): ?>
                <div class="text-muted small py-4 text-center border rounded bg-light">
                    Belum ada pengajuan. <a href="<?= base_url('penjemputan/create') ?>" class="fw-semibold">Ajukan sekarang</a>.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Kategori</th>
                                <th>Berat (Kg)</th>
                                <th>Tanggal Jemput</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($user_penjemputan as $r): ?>
                                <?php
                                    $statusBadge = [
                                        'menunggu'                => 'bg-secondary',
                                        'disetujui'               => 'bg-info text-dark',
                                        'menunggu_pemberian_poin' => 'bg-warning text-dark',
                                        'selesai'                 => 'bg-success',
                                        'ditolak'                 => 'bg-danger',
                                    ];
                                    $badge = $statusBadge[$r['status']] ?? 'bg-secondary';
                                ?>
                                <tr>
                                    <td><?= (int) ($r['id'] ?? 0) ?></td>
                                    <td><?= (int) ($r['kategori_sampah_id'] ?? 0) ?></td>
                                    <td><?= esc((string) ($r['berat'] ?? '-')) ?></td>
                                    <td><?= esc((string) ($r['tanggal_jemput'] ?? '-')) ?></td>
                                    <td><span class="badge <?= esc($badge) ?>"><?= esc(ucwords(str_replace('_', ' ', $r['status'] ?? ''))) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ============== E. REWARD & POIN (placeholder alert-info) ============== -->
    <div class="card section-card mb-4">
        <div class="card-body">
            <h5 class="mb-3">
                <i class="bi bi-cash-coin text-success me-2"></i>
                Reward & Poin
            </h5>
            <div class="alert alert-info mb-0" role="alert">
                <i class="bi bi-clock-history me-1"></i>
                <strong>Coming Soon:</strong>
                Fitur pencairan poin sedang dalam pengembangan. Nantinya, Anda bisa mengajukan
                pencairan manual ke Admin EcoPalu yang akan mentransfer ke e-wallet Anda
                (DANA / OVO / GoPay / ShopeePay). Riwayat penukaran lengkap juga akan tersedia di sini.
            </div>
        </div>
    </div>

    <!-- ============== QUICK LINKS (Akses Cepat) ============== -->
    <div class="card section-card mb-4">
        <div class="card-body">
            <h5 class="mb-3">
                <i class="bi bi-lightning-charge text-success me-2"></i>
                Akses Cepat
            </h5>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= base_url('penjemputan/create') ?>" class="btn btn-outline-eco">
                    <i class="bi bi-truck"></i> Ajukan Penjemputan
                </a>
                <a href="<?= base_url('penjemputan?role=user') ?>" class="btn btn-outline-eco">
                    <i class="bi bi-list-check"></i> Status Pengajuan
                </a>
                <a href="<?= base_url('password/change') ?>" class="btn btn-outline-eco">
                    <i class="bi bi-key"></i> Ubah Password
                </a>
                <a href="<?= base_url('edukasi') ?>" class="btn btn-outline-eco">
                    <i class="bi bi-book"></i> Edukasi
                </a>
            </div>
        </div>
    </div>

    <!-- ============== H. KALENDER (highlight hari operasional) ============== -->
    <div class="mt-4" id="kalender">
        <?= $this->include('dashboard/_partial_kalender') ?>
    </div>

<?= $this->endSection() ?>

<style>
    /* Card styling konsisten dengan branding hijau EcoPalu */
    .stat-card,
    .cta-card,
    .section-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover,
    .cta-card:hover,
    .section-card:hover {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    }
    .stat-card .stat-icon {
        font-size: 1.4rem;
        color: #22c55e;
    }
    .stat-card small {
        color: #64748b;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }
    .stat-card h3 {
        font-size: 1.75rem;
        font-weight: 700;
        color: #0f172a;
        margin: 4px 0 0;
    }
    .stat-poin    { border-top: 4px solid #22c55e; }
    .stat-riwayat { border-top: 4px solid #3b82f6; }
    .stat-info    { border-top: 4px solid #f59e0b; }

    /* CTA Ajukan Penjemputan */
    .cta-card {
        background: linear-gradient(135deg, #DCFCE7 0%, #f0fdf4 100%);
        border-left: 4px solid #22c55e;
    }
    .cta-card h5 {
        color: #0f172a;
        font-weight: 700;
    }

    /* Section card headings */
    .section-card h5 {
        color: #0f172a;
        font-weight: 700;
    }

    /* Outline button konsisten */
    .btn-outline-eco {
        border: 2px solid #22c55e;
        color: #22c55e;
        background: white;
        font-weight: 500;
    }
    .btn-outline-eco:hover {
        background: #22c55e;
        color: white;
    }
</style>