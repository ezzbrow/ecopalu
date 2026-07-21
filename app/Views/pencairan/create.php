<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="mb-0">Ajukan Pencairan</h3>
        <a href="<?= base_url('pencairan') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Saldo reminder -->
    <div class="card stat-card stat-poin mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div>
                    <small>Saldo Tersedia</small>
                    <h3 class="mb-0"><?= number_format((int) ($saldo_tersedia ?? 0), 0, ',', '.') ?> <small class="text-muted fs-6">coin</small></h3>
                </div>
                <div class="text-end">
                    <small>Setara</small>
                    <h4 class="mb-0 text-success">Rp <?= number_format((int) ($saldo_rupiah ?? 0), 0, ',', '.') ?></h4>
                    <small class="text-muted">@ Rp <?= number_format((int) $rate, 0, ',', '.') ?>/coin</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="card section-card">
        <div class="card-body">
            <form action="<?= base_url('pencairan/store') ?>" method="post">
                <?= csrf_field() ?>

                <h5 class="mb-3">Pilih Nominal Pencairan</h5>
                <p class="text-muted small">
                    Pilih salah satu preset di bawah. Pencairan diproses manual oleh Admin EcoPalu.
                </p>

                <div class="row g-3 mb-4">
                    <?php foreach ($preset as $rupiah): ?>
                        <?php $coin = intdiv($rupiah, $rate); ?>
                        <?php $disabled = ($rupiah > ($saldo_rupiah ?? 0)); ?>
                        <div class="col-md-6 col-lg-3">
                            <input type="radio" class="btn-check" name="nominal_rupiah"
                                   id="preset_<?= $rupiah ?>" value="<?= $rupiah ?>"
                                   autocomplete="off" <?= $disabled ? 'disabled' : '' ?> required>
                            <label class="btn btn-outline-eco w-100 py-3" for="preset_<?= $rupiah ?>">
                                <div class="fw-bold">Rp <?= number_format($rupiah, 0, ',', '.') ?></div>
                                <small class="d-block"><?= number_format($coin, 0, ',', '.') ?> coin</small>
                                <?php if ($disabled): ?>
                                    <small class="d-block text-muted">(saldo kurang)</small>
                                <?php endif; ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <hr>

                <h5 class="mb-3">Detail E-Wallet</h5>
                <div class="mb-3">
                    <label class="form-label">Jenis E-Wallet</label>
                    <select name="jenis_ewallet" class="form-select" required>
                        <option value="">-- Pilih E-Wallet --</option>
                        <option value="dana">DANA</option>
                        <option value="ovo">OVO</option>
                        <option value="gopay">GoPay</option>
                        <option value="shopeepay">ShopeePay</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label">Nomor E-Wallet (HP)</label>
                    <input type="text" name="nomor_ewallet" class="form-control"
                           placeholder="08xxxxxxxxxx" minlength="8" maxlength="20" required>
                    <small class="text-muted">Pastikan nomor sudah terdaftar di e-wallet pilihan.</small>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-eco btn-lg" <?= ($saldo_tersedia ?? 0) < $min_coin ? 'disabled' : '' ?>>
                        <i class="bi bi-send"></i> Kirim Pengajuan
                    </button>
                    <a href="<?= base_url('pencairan') ?>" class="btn btn-outline-secondary btn-lg">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Info tambahan -->
    <div class="alert alert-light border mt-3 small text-muted">
        <i class="bi bi-info-circle"></i>
        <strong>Catatan:</strong>
        Setelah pengajuan, Admin EcoPalu akan memverifikasi dan mentransfer manual ke e-wallet Anda.
        Status pencairan dapat dipantau di halaman Riwayat Penukaran.
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
    .stat-card h3, .stat-card h4 { color: #0f172a; font-weight: 700; margin: 4px 0 0; }
    .stat-poin { border-top: 4px solid #22c55e; }
    .btn-outline-eco { border: 2px solid #22c55e; color: #22c55e; background: white; font-weight: 500; }
    .btn-check:checked + .btn-outline-eco { background: #22c55e; color: white; }
    .section-card h5 { color: #0f172a; font-weight: 700; }
</style>