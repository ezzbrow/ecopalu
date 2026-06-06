<?= $this->include('layouts/header') ?>
<?= $this->include('layouts/sidebar') ?>

<h2>Tambah Penjemputan</h2>

<form action="<?= base_url('penjemputan/store') ?>" method="post">

    <div class="mb-3">
        <label>User ID</label>
        <input type="number"
               name="user_id"
               class="form-control">
    </div>

    <div class="mb-3">
        <label>Kategori Sampah ID</label>
        <input type="number"
               name="kategori_id"
               class="form-control">
    </div>

    <div class="mb-3">
        <label>Berat Sampah (Kg)</label>
        <input type="number"
               step="0.01"
               name="berat_sampah"
               class="form-control">
    </div>

    <div class="mb-3">
        <label>Tanggal Jemput</label>
        <input type="date"
               name="tanggal_jemput"
               class="form-control">
    </div>

    <div class="mb-3">
        <label>Status</label>

        <select name="status"
                class="form-control">

            <option value="Menunggu">
                Menunggu
            </option>

            <option value="Diproses">
                Diproses
            </option>

            <option value="Selesai">
                Selesai
            </option>

        </select>
    </div>

    <button class="btn btn-eco">
        Simpan
    </button>

</form>

<?= $this->include('layouts/footer') ?>