<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

    <!-- ============== DASHBOARD BANK SAMPAH ============== -->
    <h3 class="mb-4">Beranda Bank Sampah</h3>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <!-- ============== 2 CARD STATISTIK ============== -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="stat-card stat-menunggu">
                <small>Menunggu (perlu dijemput)</small>
                <h3><?= number_format((int) ($count_menunggu ?? 0), 0, ',', '.') ?></h3>
                <span class="text-muted small">Status: disetujui</span>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card stat-selesai">
                <small>Selesai (riwayat)</small>
                <h3><?= number_format((int) ($count_selesai ?? 0), 0, ',', '.') ?></h3>
                <span class="text-muted small">Sudah dikonfirmasi</span>
            </div>
        </div>
    </div>

    <!-- ============== DAFTAR PENJEMPUTAN (2 status: disetujui & selesai) ============== -->
    <div class="penjemputan-section">
        <div class="penjemputan-header">
            <h5 class="mb-0">Daftar Penjemputan</h5>
            <a href="<?= base_url('penjemputan?role=banksampah') ?>" class="small">Lihat semua di halaman penuh →</a>
        </div>

        <?php
            $rows = $penjemputan ?? [];
            $tab = $_GET['tab'] ?? 'menunggu';
            if ($tab !== 'semua') {
                $rows = array_values(array_filter($rows, static fn($r) => ($r['status'] ?? null) === $tab));
            }
            usort($rows, static fn($a, $b) => strcmp((string)($b['created_at'] ?? ''), (string)($a['created_at'] ?? '')));
        ?>

        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link <?= $tab === 'menunggu' ? 'active' : '' ?>" href="?tab=menunggu">Menunggu</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $tab === 'selesai' ? 'active' : '' ?>" href="?tab=selesai">Selesai</a>
            </li>
        </ul>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Kategori</th>
                        <th>Berat (Kg)</th>
                        <th>Tanggal Jemput</th>
                        <th>Alamat</th>
                        <th>Status</th>
                        <th width="220">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">Belum ada data penjemputan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $r): ?>
                            <?php
                                $statusBadge = [
                                    'disetujui' => 'bg-info text-dark',
                                    'selesai'   => 'bg-success',
                                ];
                                $badge = $statusBadge[$r['status']] ?? 'bg-secondary';
                            ?>
                            <tr>
                                <td><?= (int) ($r['id'] ?? 0) ?></td>
                                <td><?= (int) ($r['user_id'] ?? 0) ?></td>
                                <td><?= (int) ($r['kategori_sampah_id'] ?? 0) ?></td>
                                <td><?= esc((string) ($r['berat'] ?? '-')) ?></td>
                                <td><?= esc((string) ($r['tanggal_jemput'] ?? '-')) ?></td>
                                <td><?= esc((string) ($r['alamat'] ?? '-')) ?></td>
                                <td><span class="badge <?= esc($badge) ?>"><?= esc(ucfirst($r['status'] ?? '')) ?></span></td>
                                <td>
                                    <?php if (($r['status'] ?? '') === 'disetujui'): ?>
                                        <form method="post"
                                              action="<?= base_url('penjemputan/konfirmasi-selesai/'.$r['id']) ?>"
                                              class="d-inline"
                                              onsubmit="return confirm('Konfirmasi sampah sudah dijemput?')">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-success btn-sm" type="submit">
                                                <i class="bi bi-check2-circle"></i> Konfirmasi Selesai
                                            </button>
                                        </form>
                                        <a href="https://www.google.com/maps?q=<?= $r['latitude'] ?? 0 ?>,<?= $r['longitude'] ?? 0 ?>"
                                           target="_blank" class="btn btn-info btn-sm">
                                            <i class="bi bi-geo-alt"></i> Peta
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">Sudah dikonfirmasi</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ============== KALENDER (highlight hari operasional) ============== -->
    <div class="mt-4" id="kalender">
        <?= $this->include('dashboard/_partial_kalender') ?>
    </div>

    <div class="mt-3">
        <small class="text-muted">
            Session aktif — user_id: <?= esc(session()->get('user_id')) ?> &middot; role: <?= esc(session()->get('role')) ?>
        </small>
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
    .stat-menunggu { border-left-color: #3b82f6; }
    .stat-selesai  { border-left-color: #22c55e; }

    .penjemputan-section {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
        margin-bottom: 24px;
    }
    .penjemputan-header {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        margin-bottom: 16px;
    }
    .penjemputan-header h5 {
        color: #0f172a;
        font-weight: 700;
    }
</style>