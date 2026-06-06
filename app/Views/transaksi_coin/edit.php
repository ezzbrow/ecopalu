<?= $this->include('layouts/header') ?>
<?= $this->include('layouts/sidebar') ?>

<h2>Edit Transaksi Coin</h2>

<form action="<?= base_url('transaksi-coin/update/'.$transaksi['id']) ?>" method="post">

    <div class="mb-3">
        <label>Coin Masuk</label>

        <input type="number"
               name="coin_masuk"
               value="<?= $transaksi['coin_masuk'] ?>"
               class="form-control">
    </div>

    <div class="mb-3">
        <label>Coin Keluar</label>

        <input type="number"
               name="coin_keluar"
               value="<?= $transaksi['coin_keluar'] ?>"
               class="form-control">
    </div>

    <div class="mb-3">
        <label>Saldo Akhir</label>

        <input type="number"
               name="saldo_akhir"
               value="<?= $transaksi['saldo_akhir'] ?>"
               class="form-control">
    </div>

    <div class="mb-3">
        <label>Keterangan</label>

        <textarea name="keterangan"
                  class="form-control"><?= $transaksi['keterangan'] ?></textarea>
    </div>

    <button class="btn btn-eco">
        Update
    </button>

</form>

<?= $this->include('layouts/footer') ?>