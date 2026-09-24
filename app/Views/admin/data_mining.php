<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<style>
    .mining-hero {
        background: linear-gradient(135deg, #059669 0%, #10B981 50%, #34D399 100%);
        border-radius: 18px;
        color: white;
        padding: 30px;
        margin-bottom: 28px;
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.2);
    }
    .metric-card {
        background: white;
        border-radius: 16px;
        padding: 22px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
        border: 1px solid #f1f5f9;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .metric-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }
    .metric-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        margin-bottom: 12px;
    }
    .flow-badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        letter-spacing: 0.3px;
    }
    .card-cluster {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        padding: 18px;
        background: #ffffff;
        position: relative;
        overflow: hidden;
    }
    .card-cluster::before {
        content: "";
        position: absolute;
        top: 0; left: 0;
        width: 5px; height: 100%;
    }
    .cluster-0::before { background: #10B981; }
    .cluster-1::before { background: #3B82F6; }
    .cluster-2::before { background: #F59E0B; }
    
    .nav-pills .nav-link {
        color: #64748b;
        font-weight: 500;
        border-radius: 10px;
        padding: 10px 20px;
    }
    .nav-pills .nav-link.active {
        background-color: #10B981;
        color: white;
    }
    .table-modern {
        vertical-align: middle;
    }
    .table-modern thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0;
        padding: 12px 16px;
    }
    .table-modern tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
    }
</style>

<!-- ================= HERO HEADER ================= -->
<div class="mining-hero">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <span class="badge bg-white text-success px-3 py-1 mb-2 fw-semibold" style="font-size: 0.8rem;">
                <i class="bi bi-cpu-fill me-1"></i> Data Mining Center EcoPalu
            </span>
            <h2 class="fw-bold mb-2">Segmentasi Nasabah & Pola Kombinasi Sampah</h2>
            <p class="mb-0 opacity-90" style="font-size: 0.95rem;">
                Modul analitik cerdas berbasis <strong>K-Means Clustering</strong> untuk profil nasabah dan <strong>Apriori Algorithm</strong> untuk aturan asosiasi jenis sampah.
            </p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <form action="<?= base_url('admin/data-mining/run') ?>" method="post" onsubmit="document.getElementById('btnRunMining').disabled=true; document.getElementById('btnRunMining').innerHTML='<i class=\'bi bi-arrow-repeat spin\'></i> Memproses Python...';">
                <?= csrf_field() ?>
                <button type="submit" id="btnRunMining" class="btn btn-light text-success fw-bold px-4 py-2 shadow-sm rounded-pill">
                    <i class="bi bi-play-circle-fill me-1"></i> Jalankan Mining Sekarang
                </button>
            </form>
            <small class="d-block mt-2 opacity-75">
                <i class="bi bi-clock-history me-1"></i> Terakhir diolah: <?= $lastRunTime ? date('d M Y H:i', strtotime($lastRunTime)) : 'Belum pernah' ?>
            </small>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- ================= SUMMARY METRICS ================= -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="metric-card">
            <div class="metric-icon" style="background: #ECFDF5; color: #10B981;">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="text-muted small">Nasabah Teranalisis</div>
            <div class="h3 fw-bold mb-1 text-dark"><?= count($klaster) ?></div>
            <span class="text-success small fw-semibold">
                <i class="bi bi-check2"></i> K-Means Segmentation
            </span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="metric-card">
            <div class="metric-icon" style="background: #EFF6FF; color: #3B82F6;">
                <i class="bi bi-diagram-3-fill"></i>
            </div>
            <div class="text-muted small">Aturan Asosiasi (Rules)</div>
            <div class="h3 fw-bold mb-1 text-dark"><?= count($asosiasi) ?></div>
            <span class="text-primary small fw-semibold">
                <i class="bi bi-arrow-left-right"></i> Apriori Market Basket
            </span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="metric-card">
            <div class="metric-icon" style="background: #FEF3C7; color: #F59E0B;">
                <i class="bi bi-database-check"></i>
            </div>
            <div class="text-muted small">Total Setoran Sampah Selesai</div>
            <div class="h3 fw-bold mb-1 text-dark"><?= $rawStats['txCount'] ?> Transaksi</div>
            <span class="text-secondary small">
                Total Berat: <strong><?= number_format($rawStats['weight'], 1) ?> kg</strong>
            </span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="metric-card">
            <div class="metric-icon" style="background: #F3E8FF; color: #9333EA;">
                <i class="bi bi-coin"></i>
            </div>
            <div class="text-muted small">Total Poin Disirkulasikan</div>
            <div class="h3 fw-bold mb-1 text-dark"><?= number_format($rawStats['points']) ?></div>
            <span class="text-muted small">
                Data sumber: MySQL `transaksi_coin`
            </span>
        </div>
    </div>
</div>

<!-- ================= ALUR DATA MINING EXPLANATION ACCORDION ================= -->
<div class="card border-0 rounded-4 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h5 class="fw-bold mb-1 text-dark">
                    <i class="bi bi-info-circle-fill text-success me-2"></i>
                    Arsitektur & Alur Dua Tahap (Two-Stage Architecture)
                </h5>
                <p class="text-muted small mb-0">
                    Penjelasan integrasi antara sistem operasional PHP (Transaksi) dan modul analitik Python (Data Mining).
                </p>
            </div>
            <button class="btn btn-sm btn-outline-success rounded-pill px-3" type="button" data-bs-toggle="collapse" data-bs-target="#alurCollapse">
                <i class="bi bi-eye"></i> Tampilkan / Sembunyikan Alur
            </button>
        </div>

        <div class="collapse show" id="alurCollapse">
            <div class="p-3 rounded-3 bg-light border">
                <div class="row g-3">
                    <div class="col-md-6 border-end">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-secondary">Tahap 1</span>
                            <strong class="text-dark">Sistem PHP (Transaksi Harian)</strong>
                        </div>
                        <ol class="small text-muted mb-0 ps-3">
                            <li>Warga mengajukan penjemputan sampah melalui aplikasi.</li>
                            <li>Admin memverifikasi dan mitra bank sampah menjemput fisik sampah.</li>
                            <li>Berat aktual dicatat & sistem PHP menghitung poin koin otomatis.</li>
                            <li>Data tersimpan di tabel MySQL: <code>penjemputan</code> & <code>transaksi_coin</code>.</li>
                        </ol>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-success">Tahap 2</span>
                            <strong class="text-dark">Modul Python (Data Mining Terpisah)</strong>
                        </div>
                        <ol class="small text-muted mb-0 ps-3" start="5">
                            <li>Skrip Python di folder <code>/data-mining/</code> dijalankan terjadwal/manual.</li>
                            <li><strong>K-Means (<code>kmeans.py</code>)</strong> membaca data agregasi & mengelompokkan nasabah ke tabel <code>hasil_klaster</code>.</li>
                            <li><strong>Apriori (<code>apriori.py</code>)</strong> menganalisis keranjang transaksi & menyimpan pola ke <code>hasil_asosiasi</code>.</li>
                            <li>Halaman Admin ini tinggal melakukan <code>SELECT</code> biasa untuk menampilkan hasil tanpa beban komputasi berat.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= TAB NAVIGATION ================= -->
<ul class="nav nav-pills mb-4" id="miningTabs" role="tablist">
    <li class="nav-item me-2" role="presentation">
        <button class="nav-link active" id="kmeans-tab" data-bs-toggle="pill" data-bs-target="#tab-kmeans" type="button" role="tab">
            <i class="bi bi-pie-chart-fill me-1"></i> Segmentasi Nasabah (K-Means)
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="apriori-tab" data-bs-toggle="pill" data-bs-target="#tab-apriori" type="button" role="tab">
            <i class="bi bi-diagram-3-fill me-1"></i> Pola Kombinasi Sampah (Apriori)
        </button>
    </li>
</ul>

<div class="tab-content" id="miningTabsContent">

    <!-- ================= TAB 1: K-MEANS CLUSTERING ================= -->
    <div class="tab-pane fade show active" id="tab-kmeans" role="tabpanel">

        <!-- Cluster Cards Summary -->
        <div class="row g-3 mb-4">
            <?php 
            $cColors = [
                'Nasabah Sangat Aktif (Prioritas)' => ['color' => '#10B981', 'bg' => '#ECFDF5', 'icon' => 'bi-star-fill'],
                'Nasabah Potensial (Sedang)'       => ['color' => '#3B82F6', 'bg' => '#EFF6FF', 'icon' => 'bi-graph-up-arrow'],
                'Nasabah Pasif (Perlu Reaktivasi)'  => ['color' => '#F59E0B', 'bg' => '#FEF3C7', 'icon' => 'bi-bell-fill'],
            ];
            foreach ($clusterSummary as $label => $cs): 
                $style = $cColors[$label] ?? ['color' => '#64748b', 'bg' => '#f1f5f9', 'icon' => 'bi-person'];
            ?>
            <div class="col-md-4">
                <div class="card-cluster shadow-sm">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold" style="color: <?= $style['color'] ?>;">
                            <i class="bi <?= $style['icon'] ?> me-1"></i> <?= esc($label) ?>
                        </span>
                        <span class="badge rounded-pill" style="background: <?= $style['bg'] ?>; color: <?= $style['color'] ?>; font-size: 0.85rem;">
                            <?= $cs['count'] ?> Nasabah
                        </span>
                    </div>
                    <div class="d-flex justify-content-between small text-muted my-2">
                        <span>Total Berat: <strong><?= number_format($cs['total_berat'], 1) ?> kg</strong></span>
                        <span>Total Poin: <strong><?= number_format($cs['total_poin']) ?></strong></span>
                    </div>
                    <div class="p-2 rounded bg-light small mt-2">
                        <strong>Strategi:</strong> <?= esc($cs['rekomendasi']) ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Tabel Detail Hasil K-Means -->
        <div class="card border-0 rounded-4 shadow-sm">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-table text-success me-2"></i> Tabel Data Klaster Nasabah (Tabel: <code>hasil_klaster</code>)
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-modern table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Nasabah</th>
                            <th>Frekuensi Setor</th>
                            <th>Total Berat</th>
                            <th>Total Poin</th>
                            <th>Recency (Terakhir Setor)</th>
                            <th>Klaster Terbentuk</th>
                            <th>Rekomendasi Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($klaster)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <em>Belum ada hasil klaster. Silakan klik tombol "Jalankan Mining Sekarang".</em>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($klaster as $row): 
                                $badgeClass = 'bg-secondary';
                                if (str_contains($row['cluster_label'], 'Prioritas') || str_contains($row['cluster_label'], 'Sangat Aktif')) {
                                    $badgeClass = 'bg-success';
                                } elseif (str_contains($row['cluster_label'], 'Potensial')) {
                                    $badgeClass = 'bg-primary';
                                } elseif (str_contains($row['cluster_label'], 'Pasif')) {
                                    $badgeClass = 'bg-warning text-dark';
                                }
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= esc($row['nama_nasabah']) ?></div>
                                    <div class="text-muted small"><?= esc($row['email']) ?></div>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= $row['frekuensi'] ?>x</span></td>
                                <td><strong><?= number_format($row['total_berat'], 1) ?></strong> kg</td>
                                <td><span class="text-success fw-bold">+<?= number_format($row['total_poin']) ?></span></td>
                                <td>
                                    <?php if ($row['recency_hari'] <= 7): ?>
                                        <span class="text-success small fw-semibold"><i class="bi bi-check-circle"></i> <?= $row['recency_hari'] ?> hari lalu</span>
                                    <?php elseif ($row['recency_hari'] <= 21): ?>
                                        <span class="text-muted small"><?= $row['recency_hari'] ?> hari lalu</span>
                                    <?php else: ?>
                                        <span class="text-danger small fw-semibold"><i class="bi bi-exclamation-circle"></i> <?= $row['recency_hari'] ?> hari lalu</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= $badgeClass ?> px-2 py-1">
                                        <?= esc($row['cluster_label']) ?>
                                    </span>
                                </td>
                                <td class="small text-muted" style="max-width: 280px;">
                                    <?= esc($row['rekomendasi']) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ================= TAB 2: APRIORI RULES ================= -->
    <div class="tab-pane fade" id="tab-apriori" role="tabpanel">

        <div class="card border-0 rounded-4 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="metric-icon mb-0" style="background: #EFF6FF; color: #3B82F6;">
                        <i class="bi bi-lightbulb-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">Informasi Metrik Algoritma Apriori</h6>
                        <p class="text-muted small mb-0">
                            <strong>Support:</strong> Seberapa sering kombinasi sampah tersebut muncul bersamaan. | 
                            <strong>Confidence:</strong> Kepastian bahwa jika sampah A disetor, maka nasabah juga menyetor sampah B. | 
                            <strong>Lift Ratio:</strong> Kekuatan korelasi (Lift &gt; 1.0 membuktikan hubungan positif kuat).
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 rounded-4 shadow-sm">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-arrow-left-right text-primary me-2"></i> Hasil Aturan Asosiasi (Tabel: <code>hasil_asosiasi</code>)
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-modern table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jika Menyetor (Antecedent)</th>
                            <th class="text-center">Arah</th>
                            <th>Maka Menyetor (Consequent)</th>
                            <th>Support</th>
                            <th>Confidence</th>
                            <th>Lift Ratio</th>
                            <th>Interpretasi & Rekomendasi Operasional</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($asosiasi)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <em>Belum ada aturan asosiasi. Silakan klik tombol "Jalankan Mining Sekarang".</em>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($asosiasi as $rule): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <span class="badge bg-light text-primary border px-3 py-2 fw-semibold fs-6">
                                        <?= esc($rule['antecedents']) ?>
                                    </span>
                                </td>
                                <td class="text-center text-muted">
                                    <i class="bi bi-arrow-right fs-5 text-success"></i>
                                </td>
                                <td>
                                    <span class="badge bg-light text-success border px-3 py-2 fw-semibold fs-6">
                                        <?= esc($rule['consequents']) ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?= number_format($rule['support'] * 100, 1) ?>%</strong>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 6px; width: 60px;">
                                            <div class="progress-bar bg-success" style="width: <?= $rule['confidence'] * 100 ?>%"></div>
                                        </div>
                                        <span class="fw-bold text-success"><?= number_format($rule['confidence'] * 100, 1) ?>%</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success border border-success px-2 py-1">
                                        <?= number_format($rule['lift'], 2) ?>x
                                    </span>
                                </td>
                                <td class="small" style="max-width: 320px;">
                                    <?= esc($rule['keterangan']) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
