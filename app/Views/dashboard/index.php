<?= $this->include('layouts/header') ?>
<?= $this->include('layouts/sidebar') ?>

<h2 class="mb-4">Dashboard EcoPalu</h2>

<div class="row">

    <div class="col-md-3">
        <div class="card card-custom p-3">
            <h5>Total User</h5>
            <h3><?= $totalUser ?? 0 ?></h3>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-custom p-3">
            <h5>Kategori Sampah</h5>
            <h3><?= $totalKategori ?? 0 ?></h3>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-custom p-3">
            <h5>Penjemputan</h5>
            <h3><?= $totalPenjemputan ?? 0 ?></h3>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-custom p-3">
            <h5>Total Coin</h5>
            <h3><?= $totalCoin ?? 0 ?></h3>
        </div>
    </div>

</div>

<?= $this->include('layouts/footer') ?>