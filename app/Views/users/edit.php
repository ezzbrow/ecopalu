<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h2>Edit User</h2>

<form action="<?= base_url('users/update/'.$user['id']) ?>" method="post">

    <div class="mb-3">
        <label>Nama</label>
        <input type="text" name="name"
               value="<?= $user['name'] ?>"
               class="form-control">
    </div>

    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email"
               value="<?= $user['email'] ?>"
               class="form-control">
    </div>

    <div class="mb-3">
        <label>Role</label>
        <select name="role" class="form-control">

            <option value="user" <?= $user['role']=='user'?'selected':'' ?>>
                User
            </option>

            <option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>
                Admin
            </option>

        </select>
    </div>

    <button class="btn btn-eco">Update</button>

</form>

<?= $this->endSection() ?>