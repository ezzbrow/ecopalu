<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h2 class="mb-4">Dashboard EcoPalu</h2>

<div class="row">

    <div class="col-md-12">

        <div class="card shadow-sm">

            <div class="card-header bg-success text-white">
                Data Kategori Sampah
            </div>

            <div class="card-body">

                <table class="table table-bordered table-striped">

                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nama Kategori</th>
                            <th>Coin Value</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if(!empty($kategori)): ?>

                        <?php foreach ($kategori as $row): ?>

                        <tr>
                            <td><?= $row['id']; ?></td>
                            <td><?= $row['nama_kategori']; ?></td>
                            <td><?= $row['coin_value']; ?></td>
                        </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="3" class="text-center">
                                Belum ada data kategori sampah
                            </td>
                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<?= $this->endSection() ?>