<?= $this->include('layouts/header') ?>
<?= $this->include('layouts/sidebar') ?>

<h2>Tambah Kategori Sampah</h2>

<form action="<?= base_url('kategori-sampah/store') ?>" method="post">

    <div class="mb-3">
        <label>Nama Kategori</label>
        <input type="text"
               name="nama_kategori"
               class="form-control">
    </div>

    <div class="mb-3">
        <label>Nilai Coin</label>
        <input type="number"
               name="coin_value"
               class="form-control">
    </div>

    <button class="btn btn-eco">
        Simpan
    </button>

</form>

<?= $this->include('layouts/footer') ?>