<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<?php
    // Ambil reference_number dari kolom dedicated
    $reference = $pencairan['reference_number'] ?? 'MDTR-UNKNOWN';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card section-card text-center">
            <div class="card-body p-5">
                <!-- Success Icon -->
                <div class="mb-4">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                </div>

                <h3 class="text-success mb-3">Transfer Berhasil!</h3>
                <p class="text-muted mb-4">
                    Pencairan Anda telah berhasil ditransfer ke e-wallet tujuan.
                </p>

                <div class="alert alert-success border small mb-4">
                    <i class="bi bi-check2-circle"></i>
                    <strong>Simulasi Midtrans:</strong> settlement sukses dalam 3 detik.
                    Nomor referensi transfer sudah tercatat di sistem EcoPalu.
                </div>

                <div class="mb-4">
                    <small class="text-muted">Nomor Referensi Transfer</small>
                    <div class="fs-5 fw-bold text-dark font-monospace mt-1">
                        <?= esc($reference) ?>
                    </div>
                </div>

                <div class="text-start mb-4">
                    <table class="table table-sm table-borderless small mb-0">
                        <tr>
                            <td class="text-muted" style="width: 40%;">ID Pencairan</td>
                            <td><strong>#<?= (int) $pencairan['id'] ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nominal</td>
                            <td><strong>Rp <?= number_format((int) $pencairan['nominal_rupiah'], 0, ',', '.') ?></strong>
                                (<?= (int) $pencairan['nominal_coin'] ?> coin)</td>
                        </tr>
                        <tr>
                            <td class="text-muted">E-Wallet</td>
                            <td><strong><?= esc(ucfirst($pencairan['jenis_ewallet'])) ?></strong>
                                (<?= esc($pencairan['nomor_ewallet']) ?>)</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Transfer</td>
                            <td><?= esc(formatTanggalIndonesia(substr($pencairan['tanggal_transfer'], 0, 10))) ?>
                                <?= esc(substr($pencairan['tanggal_transfer'], 11, 5)) ?> WITA</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td><span class="badge bg-success">Berhasil</span></td>
                        </tr>
                    </table>
                </div>

                <div class="d-flex gap-2 justify-content-center">
                    <a href="<?= base_url('pencairan') ?>" class="btn btn-eco">
                        <i class="bi bi-arrow-left"></i> Kembali ke Riwayat
                    </a>
                    <a href="<?= base_url('dashboard/user') ?>" class="btn btn-outline-eco">
                        <i class="bi bi-house"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<style>
    .section-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
    }
</style>