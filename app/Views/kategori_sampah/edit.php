<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<h2>Edit Kategori Sampah</h2>

<form action="<?= base_url('kategori-sampah/update/'.$kategori['id']) ?>" method="post">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label class="form-label">Nama Kategori</label>

        <input type="text"
               name="nama_kategori"
               value="<?= $kategori['nama_kategori'] ?>"
               class="form-control"
               required>
    </div>

    <div class="mb-3">
        <label class="form-label">Nilai Coin</label>

        <input type="number"
               name="coin_value"
               value="<?= $kategori['coin_value'] ?>"
               class="form-control"
               required>
    </div>

    <button type="submit" class="btn btn-eco">
        Update
    </button>

    <a href="<?= base_url('kategori-sampah') ?>"
       class="btn btn-secondary">
       Kembali
    </a>

</form>

<?= $this->endSection() ?>