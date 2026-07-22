<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card section-card text-center">
            <div class="card-body p-5">
                <!-- Spinner -->
                <div class="spinner-border text-success mb-4" role="status" style="width: 4rem; height: 4rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>

                <h4 class="mb-3">Sedang Diproses</h4>
                <p class="text-muted mb-1">
                    Pencairan Anda sedang diproses oleh sistem pembayaran.
                </p>
                <p class="text-muted small mb-4">
                    Mohon tunggu beberapa saat. Jangan tutup halaman ini.
                </p>

                <div class="alert alert-light border small text-muted">
                    <i class="bi bi-info-circle"></i>
                    <strong>Simulasi Midtrans:</strong> sistem mensimulasikan transfer
                    otomatis ke e-wallet tujuan. Tidak ada HTTP call ke server eksternal.
                </div>

                <div class="mt-4 text-start">
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
                            <td class="text-muted">Status</td>
                            <td><span class="badge bg-info text-dark">Diproses</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const id    = <?= (int) $pencairan['id'] ?>;
    const start = Date.now();
    const MAX   = 15000; // 15 detik safety timeout

    const interval = setInterval(async () => {
        try {
            const res = await fetch('/pencairan/status/' + id, {
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) throw new Error('Status: ' + res.status);
            const data = await res.json();

            if (data.status === 'berhasil') {
                clearInterval(interval);
                window.location.href = '/pencairan/success/' + id;
                return;
            }
        } catch (err) {
            console.error('Polling error:', err);
        }

        // Safety timeout
        if (Date.now() - start > MAX) {
            clearInterval(interval);
        }
    }, 2000);

    // Trigger auto-settle setelah 3 detik (simulasi Midtrans settlement)
    setTimeout(() => {
        const formData = new FormData();
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        if (csrfMeta) formData.append(csrfMeta.getAttribute('name'), csrfMeta.getAttribute('content'));

        fetch('/pencairan/' + id + '/auto-settle', {
            method: 'POST',
            credentials: 'same-origin',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).catch(err => console.error('Settle error:', err));
    }, 3000);
})();
</script>

<?= $this->endSection() ?>

<style>
    .section-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
    }
</style>