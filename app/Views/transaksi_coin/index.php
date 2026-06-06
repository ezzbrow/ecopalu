<?= $this->include('layouts/header') ?>
<?= $this->include('layouts/sidebar') ?>

<div class="d-flex justify-content-between mb-3">

    <h2>Transaksi Coin</h2>

    <a href="<?= base_url('transaksi-coin/create') ?>"
       class="btn btn-eco">
       Tambah Transaksi
    </a>

</div>

<table class="table table-bordered">

<thead>
<tr>
    <th>ID</th>
    <th>User ID</th>
    <th>Coin Masuk</th>
    <th>Coin Keluar</th>
    <th>Saldo Akhir</th>
    <th>Keterangan</th>
    <th>Aksi</th>
</tr>
</thead>

<tbody>

<?php foreach($transaksi as $item): ?>

<tr>

    <td><?= $item['id'] ?></td>
    <td><?= $item['user_id'] ?></td>
    <td><?= $item['coin_masuk'] ?></td>
    <td><?= $item['coin_keluar'] ?></td>
    <td><?= $item['saldo_akhir'] ?></td>
    <td><?= $item['keterangan'] ?></td>

    <td>

        <a href="<?= base_url('transaksi-coin/edit/'.$item['id']) ?>"
           class="btn btn-warning btn-sm">
           Edit
        </a>

        <a href="<?= base_url('transaksi-coin/delete/'.$item['id']) ?>"
           class="btn btn-danger btn-sm">
           Hapus
        </a>

    </td>

</tr>

<?php endforeach; ?>

</tbody>
</table>

<?= $this->include('layouts/footer') ?>