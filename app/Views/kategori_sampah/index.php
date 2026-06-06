<?= $this->include('layouts/header') ?>
<?= $this->include('layouts/sidebar') ?>

<div class="d-flex justify-content-between mb-3">
    <h2>Kategori Sampah</h2>

    <a href="<?= base_url('kategori-sampah/create') ?>"
       class="btn btn-eco">
       Tambah Kategori
    </a>
</div>

<table class="table table-bordered">

<thead>
<tr>
    <th>ID</th>
    <th>Nama Kategori</th>
    <th>Coin</th>
    <th>Aksi</th>
</tr>
</thead>

<tbody>

<?php foreach($kategori as $item): ?>

<tr>
    <td><?= $item['id'] ?></td>
    <td><?= $item['nama_kategori'] ?></td>
    <td><?= $item['coin_value'] ?></td>

    <td>
        <a href="<?= base_url('kategori-sampah/edit/'.$item['id']) ?>"
            class="btn btn-warning btn-sm">
            Edit
        </a>

        <a href="<?= base_url('kategori-sampah/delete/'.$item['id']) ?>"
            class="btn btn-danger btn-sm">
            Hapus
        </a>
    </td>

</tr>

<?php endforeach; ?>

</tbody>
</table>

<?= $this->include('layouts/footer') ?>