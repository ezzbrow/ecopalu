<?= $this->include('layouts/header') ?>
<?= $this->include('layouts/sidebar') ?>

<div class="d-flex justify-content-between mb-3">
    <h2>Data Penjemputan</h2>

    <a href="<?= base_url('penjemputan/create') ?>"
       class="btn btn-eco">
        Tambah Penjemputan
    </a>
</div>

<table class="table table-bordered">

    <thead>
        <tr>
            <th>ID</th>
            <th>User ID</th>
            <th>Kategori ID</th>
            <th>Berat Sampah</th>
            <th>Tanggal Jemput</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>

    <tbody>

    <?php foreach($penjemputan as $item): ?>

        <tr>
            <td><?= $item['id'] ?></td>
            <td><?= $item['user_id'] ?></td>
            <td><?= $item['kategori_id'] ?></td>
            <td><?= $item['berat_sampah'] ?> Kg</td>
            <td><?= $item['tanggal_jemput'] ?></td>
            <td><?= $item['status'] ?></td>

            <td>
                <a href="<?= base_url('penjemputan/edit/'.$item['id']) ?>"
                   class="btn btn-warning btn-sm">
                   Edit
                </a>

                <a href="<?= base_url('penjemputan/delete/'.$item['id']) ?>"
                   class="btn btn-danger btn-sm">
                   Hapus
                </a>
            </td>
        </tr>

    <?php endforeach; ?>

    </tbody>

</table>

<?= $this->include('layouts/footer') ?>