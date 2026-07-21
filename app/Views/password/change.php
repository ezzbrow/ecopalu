<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

    <!-- ============== UBAH PASSWORD ============== -->
    <h3 class="mb-4">Ubah Password</h3>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?php $errors = session()->getFlashdata('errors') ?? []; ?>
    <?php if (! empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                <?php foreach ($errors as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm" style="border-radius:16px;">
        <div class="card-body p-4">
            <p class="text-muted small mb-3">
                Untuk keamanan, masukkan password lama Anda terlebih dahulu. Password baru minimal 8 karakter.
            </p>

            <form action="<?= base_url('password/change') ?>" method="post">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label">Password Lama</label>
                    <input type="password"
                           name="password_lama"
                           class="form-control"
                           required
                           autocomplete="current-password">
                </div>

                <div class="mb-3">
                    <label class="form-label">Password Baru</label>
                    <input type="password"
                           name="password_baru"
                           class="form-control"
                           required
                           minlength="8"
                           autocomplete="new-password">
                    <small class="text-muted">Minimal 8 karakter.</small>
                </div>

                <div class="mb-4">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input type="password"
                           name="password_baru_konfirm"
                           class="form-control"
                           required
                           minlength="8"
                           autocomplete="new-password">
                </div>

                <button type="submit" class="btn btn-eco">
                    <i class="bi bi-key"></i> Ubah Password
                </button>
            </form>
        </div>
    </div>

<?= $this->endSection() ?>