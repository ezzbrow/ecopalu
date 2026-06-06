<?= $this->include('layouts/header') ?>
<?= $this->include('layouts/sidebar') ?>

<h2>Tambah Transaksi Coin</h2>

<form action="<?= base_url('transaksi-coin/store') ?>" method="post">

    <div class="mb-3">
        <label>User ID</label>
        <input type="number"
               name="user_id"
               class="form-control">
    </div>

    <div class="mb-3">
        <label>Coin Masuk</label>
        <input type="number"
               name="coin_masuk"
               class="form-control">
    </div>

    <div class="mb-3">
        <label>Coin Keluar</label>
        <input type="number"
               name="coin_keluar"
               class="form-control">
    </div>

    <div class="mb-3">
        <label>Saldo Akhir</label>
        <input type="number"
               name="saldo_akhir"
               class="form-control">
    </div>

    <div class="mb-3">
        <label>Keterangan</label>
        <textarea name="keterangan"
                  class="form-control"></textarea>
    </div>

    <button class="btn btn-eco">
        Simpan
    </button>

</form>

<?= $this->include('layouts/footer') ?>