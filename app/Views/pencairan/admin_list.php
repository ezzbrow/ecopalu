<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

    <h3 class="mb-4">Pencairan Reward — Verifikasi Admin</h3>

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

    <!-- ============== 4 CARD COUNT STATUS ============== -->
    <div class="row g-3 mb-4">
        <?php
            $statusCard = [
                ['menunggu', 'bg-secondary', 'bi-hourglass-split', 'Menunggu'],
                ['diproses', 'bg-info',       'bi-arrow-repeat',   'Diproses'],
                ['berhasil', 'bg-success',    'bi-check2-circle',  'Berhasil'],
                ['ditolak',  'bg-danger',     'bi-x-circle',       'Ditolak'],
            ];
            foreach ($statusCard as [$key, $badge, $icon, $label]):
                $count = $counts[$key] ?? 0;
                $isActive = ($status_filter === $key);
        ?>
            <div class="col-md-3">
                <a href="?status=<?= $key ?>" class="text-decoration-none">
                    <div class="card stat-card h-100" style="border-top: 4px solid <?= $isActive ? '#22c55e' : '#cbd5e1' ?>;">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi <?= $icon ?> stat-icon me-2"></i>
                                <small><?= $label ?></small>
                            </div>
                            <h3 class="mb-0"><?= number_format((int) $count, 0, ',', '.') ?></h3>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="d-flex gap-2 mb-3">
        <a href="?status=semua" class="btn btn-sm <?= $status_filter === 'semua' ? 'btn-success' : 'btn-outline-secondary' ?>">Semua</a>
    </div>

    <!-- ============== TABEL PENCAIRAN ============== -->
    <div class="card section-card">
        <div class="card-body">
            <?php if (empty($rows)): ?>
                <div class="text-muted small py-4 text-center border rounded bg-light">
                    Belum ada pengajuan<?= $status_filter !== 'semua' ? ' dengan status ' . $status_filter : '' ?>.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Nominal</th>
                                <th>E-Wallet</th>
                                <th>Status</th>
                                <th>Tanggal Ajukan</th>
                                <th width="320">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $r): ?>
                                <?php
                                    $statusBadge = [
                                        'menunggu' => 'bg-secondary',
                                        'diproses' => 'bg-info text-dark',
                                        'berhasil' => 'bg-success',
                                        'ditolak'  => 'bg-danger',
                                    ];
                                    $badge = $statusBadge[$r['status']] ?? 'bg-secondary';
                                ?>
                                <tr>
                                    <td><?= (int) $r['id'] ?></td>
                                    <td>
                                        <strong><?= esc($r['user_name'] ?? 'User #' . $r['user_id']) ?></strong><br>
                                        <small class="text-muted"><?= esc($r['user_email'] ?? '') ?></small>
                                    </td>
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
                                    <td><span class="badge <?= esc($badge) ?>"><?= esc(ucfirst($r['status'])) ?></span></td>
                                    <td>
                                        <small><?= esc(formatTanggalIndonesia(substr($r['created_at'], 0, 10))) ?></small><br>
                                        <small class="text-muted"><?= esc(substr($r['created_at'], 11, 5)) ?> WITA</small>
                                    </td>
                                    <td>
                                        <?php if ($r['status'] === 'menunggu'): ?>
                                            <form method="post" action="<?= base_url('pencairan/'.$r['id'].'/approve') ?>" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button class="btn btn-success btn-sm" type="submit">
                                                    <i class="bi bi-check2"></i> Approve
                                                </button>
                                            </form>
                                            <button class="btn btn-danger btn-sm" type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#rejectForm<?= $r['id'] ?>">
                                                <i class="bi bi-x"></i> Tolak
                                            </button>
                                            <div class="collapse mt-2" id="rejectForm<?= $r['id'] ?>">
                                                <form method="post" action="<?= base_url('pencairan/'.$r['id'].'/reject') ?>">
                                                    <?= csrf_field() ?>
                                                    <textarea name="alasan_penolakan" class="form-control form-control-sm mb-1" rows="2"
                                                              placeholder="Alasan penolakan (wajib)" required></textarea>
                                                    <button class="btn btn-danger btn-sm" type="submit">Konfirmasi Tolak</button>
                                                </form>
                                            </div>
                                        <?php elseif ($r['status'] === 'diproses'): ?>
                                            <form method="post" action="<?= base_url('pencairan/'.$r['id'].'/mark-transferred') ?>" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button class="btn btn-primary btn-sm" type="submit"
                                                        title="Normalnya auto-settle via simulasi Midtrans (3 detik). Tombol ini = fallback manual.">
                                                    <i class="bi bi-check2-all"></i> Tandai Selesai
                                                </button>
                                            </form>
                                        <?php elseif ($r['status'] === 'ditolak' && ! empty($r['alasan_penolakan'])): ?>
                                            <small class="text-muted">Alasan: <?= esc($r['alasan_penolakan']) ?></small>
                                        <?php elseif ($r['status'] === 'berhasil' && ! empty($r['tanggal_transfer'])): ?>
                                            <small class="text-muted">Transfer: <?= esc(formatTanggalIndonesia(substr($r['tanggal_transfer'], 0, 10))) ?></small>
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
    .stat-card, .section-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
    }
    .stat-card .stat-icon { font-size: 1.4rem; color: #22c55e; }
    .stat-card small { color: #64748b; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }
    .stat-card h3 { font-size: 1.6rem; font-weight: 700; color: #0f172a; margin: 4px 0 0; }
    .section-card h5 { color: #0f172a; font-weight: 700; }
</style>