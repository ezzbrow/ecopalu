<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h2>Edit Transaksi Coin</h2>

<form action="<?= base_url('transaksi-coin/update/'.$transaksi['id']) ?>" method="post">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label class="form-label">Kategori Sampah ID</label>

        <input type="number"
               name="kategori_sampah_id"
               value="<?= $transaksi['kategori_sampah_id'] ?>"
               class="form-control"
               required>
    </div>

    <div class="mb-3">
        <label class="form-label">Berat (Kg)</label>

        <input type="number"
               step="0.01"
               name="berat"
               value="<?= $transaksi['berat'] ?>"
               class="form-control"
               required>
    </div>

    <div class="mb-3">
        <label class="form-label">Total Coin</label>

        <input type="number"
               name="total_coin"
               value="<?= $transaksi['total_coin'] ?>"
               class="form-control"
               required>
    </div>

    <button type="submit" class="btn btn-eco">
        Update
    </button>

    <a href="<?= base_url('transaksi-coin') ?>"
       class="btn btn-secondary">
       Kembali
    </a>

</form>

<?= $this->endSection() ?>