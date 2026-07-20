<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h2>Tambah User</h2>

<form action="<?= base_url('users/store') ?>" method="post">

    <div class="mb-3">
        <label>Nama</label>
        <input type="text" name="name" class="form-control">
    </div>

    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control">
    </div>

    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control">
    </div>

    <div class="mb-3">
        <label>Role</label>
        <select name="role" class="form-control">
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>
    </div>

    <button class="btn btn-eco">Simpan</button>

</form>

<?= $this->endSection() ?>