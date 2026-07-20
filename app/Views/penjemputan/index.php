<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$role = $role ?? 'admin';
$tab  = $tab  ?? 'semua';

// Definisi tab sesuai role
if ($role === 'banksampah') {
    // Bank Sampah: hanya 2 status — Menunggu (= disetujui, artinya masih harus jemput) & Selesai
    $tabs = [
        'menunggu' => 'Menunggu',
        'selesai'  => 'Selesai',
    ];
} elseif ($role === 'user') {
    $tabs = [
        'semua'    => 'Semua',
        'menunggu' => 'Menunggu',
        'disetujui' => 'Disetujui',
        'ditolak'  => 'Ditolak',
        'selesai'  => 'Selesai',
    ];
} else {
    // Admin EcoPalu: tambah tab menunggu_pemberian_poin
    $tabs = [
        'semua'    => 'Semua',
        'menunggu' => 'Menunggu Verifikasi',
        'disetujui' => 'Disetujui',
        'menunggu_pemberian_poin' => 'Menunggu Pemberian Poin',
        'ditolak'  => 'Ditolak',
        'selesai'  => 'Selesai',
    ];
}

// Status → badge class (warna berbeda)
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
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Data Penjemputan</h2>

    <?php if ($role === 'user'): ?>
        <a href="<?= base_url('penjemputan/create') ?>" class="btn btn-eco">
            Ajukan Penjemputan
        </a>
    <?php endif; ?>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<!-- Tabs -->
<ul class="nav nav-tabs mb-3">
    <?php foreach ($tabs as $key => $label): ?>
        <li class="nav-item">
            <a class="nav-link <?= $tab === $key ? 'active' : '' ?>"
               href="<?= base_url('penjemputan?status=' . $key . '&role=' . $role) ?>">
                <?= $label ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>

<!-- Popup konfirmasi sukses (Bootstrap toast/alert, bukan confirm() HTML) -->
<?php if (session()->getFlashdata('popup')): ?>
    <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Berhasil</h5>
                </div>
                <div class="modal-body">
                    <?= session()->getFlashdata('popup') ?>
                </div>
                <div class="modal-footer">
                    <a href="<?= base_url('penjemputan?status=' . $tab . '&role=' . $role) ?>"
                       class="btn btn-success">OK</a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">

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
                    <th width="240">Aksi</th>
                </tr>
            </thead>

            <tbody>

            <?php if (! empty($penjemputan)): ?>

                <?php foreach ($penjemputan as $row): ?>

                    <?php
                    $badge = $statusBadge[$row['status']] ?? 'bg-secondary';
                    $label = $statusLabel[$row['status']] ?? $row['status'];
                    ?>

                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['user_id'] ?></td>
                        <td><?= $row['kategori_sampah_id'] ?></td>
                        <td><?= $row['berat'] ?></td>
                        <td><?= formatTanggalIndonesia($row['tanggal_jemput']) ?></td>
                        <td><?= esc($row['alamat']) ?></td>
                        <td><span class="badge <?= $badge ?>"><?= $label ?></span></td>

                        <td>
                            <?php if ($role === 'admin'): ?>
                                <?php if ($row['status'] === 'menunggu'): ?>
                                    <form method="post"
                                          action="<?= base_url('penjemputan/setujui/'.$row['id']) ?>"
                                          class="d-inline">
                                        <button class="btn btn-success btn-sm">Setujui</button>
                                    </form>
                                    <button class="btn btn-danger btn-sm"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#tolakForm<?= $row['id'] ?>">
                                        Tolak
                                    </button>
                                    <div class="collapse mt-2" id="tolakForm<?= $row['id'] ?>">
                                        <form method="post"
                                              action="<?= base_url('penjemputan/tolak/'.$row['id']) ?>">
                                            <textarea name="alasan_penolakan"
                                                      class="form-control form-control-sm mb-1"
                                                      rows="2"
                                                      placeholder="Alasan penolakan"
                                                      required></textarea>
                                            <button class="btn btn-danger btn-sm">Kirim Penolakan</button>
                                        </form>
                                    </div>
                                <?php elseif ($row['status'] === 'menunggu_pemberian_poin'): ?>
                                    <button class="btn btn-warning btn-sm"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#finalForm<?= $row['id'] ?>">
                                        Beri Poin
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#tolakPoinForm<?= $row['id'] ?>">
                                        Tolak Poin
                                    </button>
                                    <div class="collapse mt-2" id="finalForm<?= $row['id'] ?>">
                                        <form method="post"
                                              action="<?= base_url('penjemputan/finalisasi-poin/'.$row['id']) ?>">
                                            <p class="small mb-1">Poin akan dihitung otomatis: berat × coin/kategori.</p>
                                            <button class="btn btn-success btn-sm">Finalisasi & Kirim Poin</button>
                                        </form>
                                    </div>
                                    <div class="collapse mt-2" id="tolakPoinForm<?= $row['id'] ?>">
                                        <form method="post"
                                              action="<?= base_url('penjemputan/tolak-poin/'.$row['id']) ?>">
                                            <textarea name="alasan_penolakan"
                                                      class="form-control form-control-sm mb-1"
                                                      rows="2"
                                                      placeholder="Alasan pembatalan poin"
                                                      required></textarea>
                                            <button class="btn btn-danger btn-sm">Kirim</button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small">—</span>
                                <?php endif; ?>

                            <?php elseif ($role === 'banksampah'): ?>
                                <?php if ($row['status'] === 'disetujui'): ?>
                                    <form method="post"
                                          action="<?= base_url('penjemputan/konfirmasi-selesai/'.$row['id']) ?>"
                                          class="d-inline">
                                        <button class="btn btn-success btn-sm">
                                            Konfirmasi Selesai
                                        </button>
                                    </form>
                                    <a href="https://www.google.com/maps?q=<?= $row['latitude'] ?>,<?= $row['longitude'] ?>"
                                       target="_blank"
                                       class="btn btn-info btn-sm">
                                        Lihat Peta
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">Sudah dikonfirmasi</span>
                                <?php endif; ?>

                            <?php else: ?>
                                <!-- User -->
                                <span class="text-muted small">Lihat detail di notifikasi</span>
                            <?php endif; ?>
                        </td>
                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="8" class="text-center">
                        Belum ada data penjemputan
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>
</div>

<?= $this->endSection() ?>