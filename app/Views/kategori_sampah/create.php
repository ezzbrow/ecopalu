<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<h2>Tambah Kategori Sampah</h2>

<form action="<?= base_url('kategori-sampah/store') ?>" method="post">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label class="form-label">Nama Kategori</label>
        <input type="text"
               name="nama_kategori"
               class="form-control"
               required>
    </div>

    <div class="mb-3">
        <label class="form-label">Nilai Coin</label>
        <input type="number"
               name="coin_value"
               class="form-control"
               required>
    </div>

    <button type="submit" class="btn btn-eco">
        Simpan
    </button>

    <a href="<?= base_url('kategori-sampah') ?>"
       class="btn btn-secondary">
       Kembali
    </a>

</form>

<?= $this->endSection() ?>