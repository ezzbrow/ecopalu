<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

    <h3 class="mb-4">Pencairan Reward</h3>

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

    <!-- ============== SALDO SECTION ============== -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card stat-card stat-poin h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-coin stat-icon me-2"></i>
                        <small>Saldo Tersedia</small>
                    </div>
                    <h3 class="mb-1"><?= number_format((int) ($saldo_tersedia ?? 0), 0, ',', '.') ?> <small class="text-muted fs-6">coin</small></h3>
                    <span class="text-muted small">
                        ≈ Rp <?= number_format((int) ($saldo_rupiah ?? 0), 0, ',', '.') ?>
                        (@ Rp <?= number_format((int) $rate, 0, ',', '.') ?>/coin)
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card stat-riwayat h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-clock-history stat-icon me-2"></i>
                        <small>Sedang Diproses</small>
                    </div>
                    <h3 class="mb-1"><?= number_format((int) ($saldo_hold ?? 0), 0, ',', '.') ?> <small class="text-muted fs-6">coin</small></h3>
                    <span class="text-muted small">Menunggu verifikasi Admin</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card stat-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-info-circle stat-icon me-2"></i>
                        <small>Minimal Pencairan</small>
                    </div>
                    <h3 class="mb-1">Rp <?= number_format((int) $min_rupiah, 0, ',', '.') ?></h3>
                    <span class="text-muted small"><?= (int) $min_coin ?> coin (kelipatan Rp <?= number_format(10000, 0, ',', '.') ?>)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ============== CTA AJUKAN ============== -->
    <div class="card cta-card mb-4">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h5 class="mb-1"><i class="bi bi-cash-coin"></i> Ajukan Pencairan</h5>
                <p class="text-muted small mb-0">
                    Saldo Anda akan dikirim ke e-wallet pilihan. Verifikasi &amp; transfer
                    dilakukan manual oleh Admin EcoPalu.
                </p>
            </div>
            <a href="<?= base_url('pencairan/create') ?>" class="btn btn-eco btn-lg">
                <i class="bi bi-plus-circle"></i> Ajukan Sekarang
            </a>
        </div>
    </div>

    <!-- ============== RIWAYAT ============== -->
    <div class="card section-card">
        <div class="card-body">
            <h5 class="mb-3"><i class="bi bi-clock-history text-success me-2"></i> Riwayat Pencairan</h5>

            <?php if (empty($riwayat)): ?>
                <div class="text-muted small py-4 text-center border rounded bg-light">
                    Belum ada pengajuan pencairan.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Tanggal</th>
                                <th>Nominal</th>
                                <th>E-wallet</th>
                                <th>Status</th>
                                <th width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($riwayat as $r): ?>
                                <?php
                                    $statusBadge = [
                                        'menunggu'  => 'bg-secondary',
                                        'diproses'  => 'bg-info text-dark',
                                        'berhasil'  => 'bg-success',
                                        'ditolak'   => 'bg-danger',
                                    ];
                                    $badge = $statusBadge[$r['status']] ?? 'bg-secondary';
                                ?>
                                <tr>
                                    <td><?= (int) $r['id'] ?></td>
                                    <td><?= esc(formatTanggalIndonesia(substr($r['created_at'], 0, 10))) ?></td>
                                    <td>
                                        <strong><?= number_format((int) $r['nominal_coin'], 0, ',', '.') ?> coin</strong>
                                        <?php if ((int) ($r['nominal_rupiah'] ?? 0) > 0): ?>
                                            <br><small class="text-muted">Rp <?= number_format((int) $r['nominal_rupiah'], 0, ',', '.') ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark"><?= esc(ucfirst($r['jenis_ewallet'] ?? '-')) ?></span><br>
                                        <small class="text-muted"><?= esc($r['nomor_ewallet'] ?? '-') ?></small>
                                    </td>
                                    <td>
                                        <span class="badge <?= esc($badge) ?>"><?= esc(ucfirst($r['status'])) ?></span>
                                        <?php if ($r['status'] === 'ditolak' && ! empty($r['alasan_penolakan'])): ?>
                                            <br><small class="text-danger"><?= esc($r['alasan_penolakan']) ?></small>
                                        <?php elseif ($r['status'] === 'berhasil' && ! empty($r['tanggal_transfer'])): ?>
                                            <br><small class="text-muted"><?= esc(formatTanggalIndonesia(substr($r['tanggal_transfer'], 0, 10))) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($r['status'] === 'diproses'): ?>
                                            <a href="<?= base_url('pencairan/processing/' . (int) $r['id']) ?>"
                                               class="btn btn-sm btn-info text-white">
                                                <i class="bi bi-eye"></i> Lihat
                                            </a>
                                        <?php elseif ($r['status'] === 'berhasil'): ?>
                                            <a href="<?= base_url('pencairan/success/' . (int) $r['id']) ?>"
                                               class="btn btn-sm btn-success text-white">
                                                <i class="bi bi-receipt"></i> Detail
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?= $this->endSection() ?>

<style>
    .stat-card, .cta-card, .section-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
    }
    .stat-card .stat-icon { font-size: 1.4rem; color: #22c55e; }
    .stat-card small { color: #64748b; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }
    .stat-card h3 { font-size: 1.6rem; font-weight: 700; color: #0f172a; margin: 4px 0 0; }
    .stat-poin    { border-top: 4px solid #22c55e; }
    .stat-riwayat { border-top: 4px solid #3b82f6; }
    .stat-info    { border-top: 4px solid #f59e0b; }
    .cta-card { background: linear-gradient(135deg, #DCFCE7 0%, #f0fdf4 100%); border-left: 4px solid #22c55e; }
    .cta-card h5 { color: #0f172a; font-weight: 700; }
    .section-card h5 { color: #0f172a; font-weight: 700; }
</style>