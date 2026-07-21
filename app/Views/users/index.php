<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between mb-3">
    <h2>Data User</h2>

    <a href="<?= base_url('users/create') ?>" class="btn btn-eco">
        Tambah User
    </a>
</div>

<table class="table table-bordered">

    <thead>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Role</th>
            <th>Aksi</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach($users as $user): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= $user['name'] ?></td>
            <td><?= $user['email'] ?></td>
            <td><?= $user['role'] ?></td>
            <td>
                <a href="<?= base_url('users/edit/'.$user['id']) ?>" class="btn btn-warning btn-sm">Edit</a>
                <form action="<?= base_url('users/delete/'.$user['id']) ?>" method="post" class="d-inline"
                      onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                    <?= csrf_field() ?>
                    <button class="btn btn-danger btn-sm" type="submit">Hapus</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>

</table>

<?= $this->endSection() ?>