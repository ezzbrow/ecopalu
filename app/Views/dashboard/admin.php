<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

    <!-- ============== DASHBOARD ADMIN ECOPALU ============== -->
    <h3 class="mb-4">Beranda Admin EcoPalu</h3>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <!-- ============== 6 CARD STATISTIK ============== -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card stat-disetujui">
                <small>Disetujui</small>
                <h3><?= number_format((int) ($card_disetujui ?? 0), 0, ',', '.') ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stat-menunggu">
                <small>Menunggu Verifikasi</small>
                <h3><?= number_format((int) ($card_menunggu ?? 0), 0, ',', '.') ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stat-menunggu-poin">
                <small>Menunggu Pemberian Poin</small>
                <h3><?= number_format((int) ($card_menunggu_poin ?? 0), 0, ',', '.') ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stat-selesai">
                <small>Selesai (Riwayat)</small>
                <h3><?= number_format((int) ($card_selesai ?? 0), 0, ',', '.') ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stat-user-aktif">
                <small>User Aktif</small>
                <h3><?= number_format((int) ($card_user_aktif ?? 0), 0, ',', '.') ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stat-total-coin">
                <small>Total Coin Diberikan</small>
                <h3><?= number_format((int) ($card_total_coin ?? 0), 0, ',', '.') ?></h3>
            </div>
        </div>
    </div>

    <!-- ============== GRAFIK REWARD DICAIRKAN ============== -->
    <?= $this->include('dashboard/_partial_chart_pencairan', [
        'pencairan_7hari'        => $pencairan_7hari ?? [],
        'pencairan_total_record' => $pencairan_total_record ?? 0,
    ]) ?>

    <!-- ============== DAFTAR PENJEMPUTAN (6-TAB) ============== -->
    <div class="penjemputan-section">
        <div class="penjemputan-header">
            <h5 class="mb-0">Daftar Penjemputan</h5>
            <a href="<?= base_url('penjemputan?role=admin') ?>" class="small">Lihat semua di halaman penuh →</a>
        </div>

        <?php
            $tabs = [
                'semua'                    => 'Semua',
                'menunggu'                 => 'Menunggu Verifikasi',
                'disetujui'                => 'Disetujui',
                'menunggu_pemberian_poin'   => 'Menunggu Pemberian Poin',
                'ditolak'                  => 'Ditolak',
                'selesai'                  => 'Selesai',
            ];
            $adminTab = $_GET['tab'] ?? 'semua';
        ?>
        <ul class="nav nav-tabs mb-3">
            <?php foreach ($tabs as $key => $label): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $adminTab === $key ? 'active' : '' ?>"
                       href="?tab=<?= esc($key) ?>">
                        <?= esc($label) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php
            // Filter rows sesuai tab (reuse data dari controller)
            $rows = $admin_penjemputan ?? [];
            if ($adminTab !== 'semua') {
                $rows = array_values(array_filter($rows, static fn($r) => ($r['status'] ?? null) === $adminTab));
            }
            // Sort by created_at DESC
            usort($rows, static fn($a, $b) => strcmp((string)($b['created_at'] ?? ''), (string)($a['created_at'] ?? '')));
        ?>

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
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">Belum ada data penjemputan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $r): ?>
                            <?php
                                $statusBadge = [
                                    'menunggu'                => 'bg-secondary',
                                    'disetujui'               => 'bg-info text-dark',
                                    'menunggu_pemberian_poin' => 'bg-warning text-dark',
                                    'selesai'                 => 'bg-success',
                                    'ditolak'                 => 'bg-danger',
                                ];
                                $statusLabel = [
                                    'menunggu'                => 'Menunggu',
                                    'disetujui'               => 'Disetujui',
                                    'menunggu_pemberian_poin' => 'Menunggu Poin',
                                    'selesai'                 => 'Selesai',
                                    'ditolak'                 => 'Ditolak',
                                ];
                                $badge = $statusBadge[$r['status']] ?? 'bg-secondary';
                                $label = $statusLabel[$r['status']] ?? $r['status'];
                            ?>
                            <tr>
                                <td><?= (int) ($r['id'] ?? 0) ?></td>
                                <td><?= (int) ($r['user_id'] ?? 0) ?></td>
                                <td><?= (int) ($r['kategori_sampah_id'] ?? 0) ?></td>
                                <td><?= esc((string) ($r['berat'] ?? '-')) ?></td>
                                <td><?= esc((string) ($r['tanggal_jemput'] ?? '-')) ?></td>
                                <td><?= esc((string) ($r['alamat'] ?? '-')) ?></td>
                                <td><span class="badge <?= esc($badge) ?>"><?= esc($label) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ============== AKSI CEPAT ============== -->
    <div class="quick-actions">
        <h6 class="mb-3 text-muted">Aksi Cepat</h6>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?= base_url('users') ?>" class="btn btn-eco">
                <i class="bi bi-people"></i> Kelola User
            </a>
            <a href="<?= base_url('kategori-sampah') ?>" class="btn btn-eco">
                <i class="bi bi-recycle"></i> Kategori Sampah
            </a>
            <a href="<?= base_url('transaksi-coin') ?>" class="btn btn-eco">
                <i class="bi bi-coin"></i> Transaksi Coin
            </a>
            <a href="<?= base_url('pencairan') ?>" class="btn btn-outline-eco" title="Coming soon">
                <i class="bi bi-cash-stack"></i> Pencairan Reward
            </a>
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
    }
    .stat-card h3 {
        font-size: 1.8rem;
        font-weight: 700;
        color: #0f172a;
        margin: 4px 0 0;
    }
    .stat-disetujui    { border-left-color: #3b82f6; }
    .stat-menunggu     { border-left-color: #94a3b8; }
    .stat-menunggu-poin{ border-left-color: #f59e0b; }
    .stat-selesai      { border-left-color: #22c55e; }
    .stat-user-aktif   { border-left-color: #8b5cf6; }
    .stat-total-coin   { border-left-color: #15803d; }

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

    .quick-actions {
        background: white;
        border-radius: 16px;
        padding: 20px 24px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
        margin-bottom: 24px;
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