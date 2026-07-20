<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">

    <h2>Kategori Sampah</h2>

    <a href="<?= base_url('kategori-sampah/create') ?>"
       class="btn btn-eco">
       Tambah Kategori
    </a>

</div>

<table class="table table-bordered table-striped">

    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Nama Kategori</th>
            <th>Coin</th>
            <th width="180">Aksi</th>
        </tr>
    </thead>

    <tbody>

    <?php if(!empty($kategori)): ?>

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
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('Yakin ingin menghapus data ini?')">
                   Hapus
                </a>
            </td>

        </tr>

        <?php endforeach; ?>

    <?php else: ?>

        <tr>
            <td colspan="4" class="text-center">
                Belum ada data kategori sampah
            </td>
        </tr>

    <?php endif; ?>

    </tbody>

</table>

<?= $this->endSection() ?>