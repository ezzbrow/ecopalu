<?= $this->include('layouts/header') ?>
<?= $this->include('layouts/sidebar') ?>

<div class="d-flex justify-content-between mb-3">
    <h2>Data User</h2>

    <a href="<?= base_url('users/create') ?>"
       class="btn btn-eco">
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

            <a href="<?= base_url('users/edit/'.$user['id']) ?>"
               class="btn btn-warning btn-sm">
               Edit
            </a>

            <a href="<?= base_url('users/delete/'.$user['id']) ?>"
               class="btn btn-danger btn-sm">
               Hapus
            </a>

        </td>
    </tr>

    <?php endforeach; ?>

    </tbody>

</table>

<?= $this->include('layouts/footer') ?>