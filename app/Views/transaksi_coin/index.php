<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">

    <h2>Transaksi Coin</h2>

    <a href="<?= base_url('transaksi-coin/create') ?>"
       class="btn btn-success">
       Tambah Transaksi
    </a>

</div>

<table class="table table-bordered table-striped">

    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>User ID</th>
            <th>Kategori Sampah ID</th>
            <th>Berat (Kg)</th>
            <th>Total Coin</th>
            <th width="180">Aksi</th>
        </tr>
    </thead>

    <tbody>

    <?php if(!empty($transaksi)): ?>

        <?php foreach($transaksi as $item): ?>

        <tr>

            <td><?= $item['id'] ?></td>
            <td><?= $item['user_id'] ?></td>
            <td><?= $item['kategori_sampah_id'] ?></td>
            <td><?= $item['berat'] ?></td>
            <td><?= $item['total_coin'] ?></td>

            <td>
                <a href="<?= base_url('transaksi-coin/edit/'.$item['id']) ?>"
                   class="btn btn-warning btn-sm">
                   Edit
                </a>

                <form action="<?= base_url('transaksi-coin/delete/'.$item['id']) ?>" method="post" class="d-inline"
                      onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                    <?= csrf_field() ?>
                    <button class="btn btn-danger btn-sm" type="submit">Hapus</button>
                </form>
            </td>

        </tr>

        <?php endforeach; ?>

    <?php else: ?>

        <tr>
            <td colspan="6" class="text-center">
                Belum ada data transaksi coin
            </td>
        </tr>

    <?php endif; ?>

    </tbody>

</table>

<?= $this->endSection() ?>