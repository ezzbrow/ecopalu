<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

    <!-- ============== DASHBOARD USER (EcoFriend) ============== -->
    <h3 class="mb-4">Beranda User</h3>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="card shadow-sm" style="border-radius:16px;">
        <div class="card-body p-4">
            <h4 class="mb-3">Dashboard User — Coming Soon</h4>
            <p class="mb-2">Halo, <strong><?= esc(session()->get('nama')) ?></strong>! Anda login sebagai
                <span class="badge bg-success"><?= esc(session()->get('role')) ?></span>.</p>
            <p class="text-muted mb-0">
                Halaman dashboard User sesuai spec CLAUDE.md (Beranda, Riwayat Penukaran, Ajukan Penjemputan, Status Pengajuan, Notifikasi, Reward & Poin, Ubah Password, Edukasi, Kalender) akan dibangun di langkah selanjutnya.
            </p>
        </div>
    </div>

    <div class="mt-3">
        <small class="text-muted">
            Session aktif — user_id: <?= esc(session()->get('user_id')) ?> &middot; role: <?= esc(session()->get('role')) ?>
        </small>
    </div>

    <!-- Kalender (highlight hari operasional penjemputan) -->
    <div class="mt-4" id="kalender">
        <?= $this->include('dashboard/_partial_kalender') ?>
    </div>

<?= $this->endSection() ?>