<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

    <!-- ============== DASHBOARD USER (EcoFriend) ============== -->
    <h3 class="mb-4">Beranda</h3>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <!-- ============== A. TOTAL POIN + RIWAYAT PENUKARAN + INFO SYARAT 50KG ============== -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card stat-poin">
                <small>Total Poin Anda</small>
                <h3><?= number_format((int) ($total_poin ?? 0), 0, ',', '.') ?></h3>
                <span class="text-muted small">Dari <?= (int) ($total_transaksi ?? 0) ?> transaksi</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stat-riwayat">
                <small>Riwayat Penukaran</small>
                <h3><?= number_format((int) ($riwayat_coin_dicairkan ?? 0), 0, ',', '.') ?></h3>
                <span class="text-muted small">
                    <?= (int) ($jumlah_pencairan_berhasil ?? 0) ?> pencairan berhasil
                    <?php if ((int) ($riwayat_rupiah_dicairkan ?? 0) > 0): ?>
                        · Rp <?= number_format((int) ($riwayat_rupiah_dicairkan ?? 0), 0, ',', '.') ?>
                    <?php endif; ?>
                </span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stat-info">
                <small>Syarat Pencairan</small>
                <p class="mb-0 small text-muted">
                    Minimal pencairan <strong>50kg</strong>, setara ± 1.600–1.700 botol plastik 1,5L.
                    Poin dicairkan manual oleh Admin EcoPalu ke e-wallet Anda.
                </p>
            </div>
        </div>
    </div>

    <!-- ============== B. AJUKAN PENJEMPUTAN (CTA) ============== -->
    <div class="action-cta mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="mb-1">Ajukan Penjemputan Sampah</h5>
                <p class="text-muted small mb-0">
                    Pengajuan hanya bisa di hari selain <strong>Rabu &amp; Sabtu</strong>
                    (hari operasional penjemputan Bank Sampah).
                </p>
            </div>
            <a href="<?= base_url('penjemputan/create') ?>" class="btn btn-eco btn-lg">
                <i class="bi bi-truck"></i> Ajukan Sekarang
            </a>
        </div>
    </div>

    <!-- ============== C. STATUS PENGAJUAN (5 row terakhir) ============== -->
    <div class="status-section mb-4">
        <div class="status-header">
            <h5 class="mb-0">Status Pengajuan</h5>
            <a href="<?= base_url('penjemputan?role=user') ?>" class="small">Lihat semua →</a>
        </div>

        <?php if (empty($user_penjemputan)): ?>
            <div class="text-muted small py-3 text-center">
                Belum ada pengajuan. <a href="<?= base_url('penjemputan/create') ?>">Ajukan sekarang</a>.
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

    <!-- ============== E. REWARD & POIN (placeholder) ============== -->
    <div class="reward-section mb-4">
        <h5 class="mb-2">Reward & Poin</h5>
        <p class="text-muted small mb-3">
            Fitur pencairan poin sedang dalam pengembangan. Nantinya, Anda bisa mengajukan pencairan manual ke Admin EcoPalu yang akan mentransfer ke e-wallet Anda.
        </p>
        <div class="alert alert-light border">
            <i class="bi bi-clock-history"></i>
            <strong>Coming Soon:</strong> Form pencairan + Riwayat Penukaran lengkap.
        </div>
    </div>

    <!-- ============== QUICK LINKS ============== -->
    <div class="quick-links mb-4">
        <h6 class="mb-3 text-muted">Akses Cepat</h6>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?= base_url('penjemputan/create') ?>" class="btn btn-outline-eco">
                <i class="bi bi-truck"></i> Ajukan Penjemputan
            </a>
            <a href="<?= base_url('password/change') ?>" class="btn btn-outline-eco">
                <i class="bi bi-key"></i> Ubah Password
            </a>
            <a href="<?= base_url('edukasi') ?>" class="btn btn-outline-eco">
                <i class="bi bi-book"></i> Edukasi
            </a>
        </div>
    </div>

    <!-- ============== H. KALENDER (highlight hari operasional) ============== -->
    <div class="mt-4" id="kalender">
        <?= $this->include('dashboard/_partial_kalender') ?>
    </div>

<?= $this->endSection() ?>

<style>
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 18px 20px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
        border-left: 5px solid #cbd5e1;
    }
    .stat-card small {
        color: #64748b;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        display: block;
    }
    .stat-card h3 {
        font-size: 1.8rem;
        font-weight: 700;
        color: #0f172a;
        margin: 4px 0 0;
    }
    .stat-poin    { border-left-color: #22c55e; }
    .stat-riwayat { border-left-color: #3b82f6; }
    .stat-info    { border-left-color: #f59e0b; }

    .action-cta {
        background: linear-gradient(135deg, #DCFCE7 0%, #f0fdf4 100%);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
    }

    .status-section,
    .reward-section,
    .quick-links {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
    }
    .status-header {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        margin-bottom: 12px;
    }
    .status-header h5 {
        color: #0f172a;
        font-weight: 700;
    }
    .btn-outline-eco {
        border: 2px solid #22c55e;
        color: #22c55e;
        background: white;
    }
    .btn-outline-eco:hover {
        background: #22c55e;
        color: white;
    }
</style>