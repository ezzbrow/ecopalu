<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h2>Tambah Transaksi Coin</h2>

<form action="<?= base_url('transaksi-coin/store') ?>" method="post">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label class="form-label">User ID</label>
        <input type="number"
               name="user_id"
               class="form-control"
               required>
    </div>

    <div class="mb-3">
        <label class="form-label">Kategori Sampah ID</label>
        <input type="number"
               name="kategori_sampah_id"
               class="form-control"
               required>
    </div>

    <div class="mb-3">
        <label class="form-label">Berat (Kg)</label>
        <input type="number"
               step="0.01"
               name="berat"
               class="form-control"
               required>
    </div>

    <div class="mb-3">
        <label class="form-label">Total Coin</label>
        <input type="number"
               name="total_coin"
               class="form-control"
               required>
    </div>

    <button type="submit" class="btn btn-eco">
        Simpan
    </button>

    <a href="<?= base_url('transaksi-coin') ?>"
       class="btn btn-secondary">
       Kembali
    </a>

</form>

<?= $this->endSection() ?>