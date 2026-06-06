<?= $this->include('layouts/header') ?>
<?= $this->include('layouts/sidebar') ?>

<h2>Edit Kategori Sampah</h2>

<form action="<?= base_url('kategori-sampah/update/'.$kategori['id']) ?>" method="post">

    <div class="mb-3">
        <label>Nama Kategori</label>

        <input type="text"
               name="nama_kategori"
               value="<?= $kategori['nama_kategori'] ?>"
               class="form-control">
    </div>

    <div class="mb-3">
        <label>Coin</label>

        <input type="number"
               name="coin_value"
               value="<?= $kategori['coin_value'] ?>"
               class="form-control">
    </div>

    <button class="btn btn-eco">
        Update
    </button>

</form>

<?= $this->include('layouts/footer') ?>