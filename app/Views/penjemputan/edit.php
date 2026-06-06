<?= $this->include('layouts/header') ?>
<?= $this->include('layouts/sidebar') ?>

<h2>Edit Penjemputan</h2>

<form action="<?= base_url('penjemputan/update/'.$penjemputan['id']) ?>" method="post">

    <div class="mb-3">
        <label>User ID</label>

        <input type="number"
               name="user_id"
               value="<?= $penjemputan['user_id'] ?>"
               class="form-control">
    </div>

    <div class="mb-3">
        <label>Kategori ID</label>

        <input type="number"
               name="kategori_id"
               value="<?= $penjemputan['kategori_id'] ?>"
               class="form-control">
    </div>

    <div class="mb-3">
        <label>Berat Sampah</label>

        <input type="number"
               step="0.01"
               name="berat_sampah"
               value="<?= $penjemputan['berat_sampah'] ?>"
               class="form-control">
    </div>

    <div class="mb-3">
        <label>Tanggal Jemput</label>

        <input type="date"
               name="tanggal_jemput"
               value="<?= $penjemputan['tanggal_jemput'] ?>"
               class="form-control">
    </div>

    <div class="mb-3">
        <label>Status</label>

        <input type="text"
               name="status"
               value="<?= $penjemputan['status'] ?>"
               class="form-control">
    </div>

    <button class="btn btn-eco">
        Update
    </button>

</form>

<?= $this->include('layouts/footer') ?>