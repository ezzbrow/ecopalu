<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — EcoPalu</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">

    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #DCFCE7 0%, #f8fafc 100%);
            padding: 20px;
        }
        .auth-card {
            width: 100%;
            max-width: 440px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 40px 32px;
        }
        .auth-logo {
            color: #22C55E;
            font-weight: 700;
            font-size: 28px;
            text-align: center;
            margin-bottom: 4px;
        }
        .auth-subtitle {
            text-align: center;
            color: #64748b;
            margin-bottom: 28px;
            font-size: 14px;
        }
        .form-control:focus {
            border-color: #22C55E;
            box-shadow: 0 0 0 0.2rem rgba(34, 197, 94, 0.15);
        }
        .btn-eco {
            background: #22C55E;
            border: none;
            color: white;
            font-weight: 500;
        }
        .btn-eco:hover {
            background: #16A34A;
            color: white;
        }
        .auth-footer {
            text-align: center;
            margin-top: 20px;
            color: #64748b;
            font-size: 14px;
        }
        .auth-footer a {
            color: #22C55E;
            text-decoration: none;
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="auth-card">
    <div class="auth-logo">♻ EcoPalu</div>
    <div class="auth-subtitle">Daftar sebagai User (EcoFriend)</div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?php $errors = session()->getFlashdata('errors') ?? []; ?>
    <?php if (! empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                <?php foreach ($errors as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('register/attempt') ?>" method="post">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text"
                   name="name"
                   class="form-control"
                   placeholder="Nama Anda"
                   value="<?= esc(old('name')) ?>"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email"
                   name="email"
                   class="form-control"
                   placeholder="nama@email.com"
                   value="<?= esc(old('email')) ?>"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password"
                   name="password"
                   class="form-control"
                   placeholder="Minimal 8 karakter"
                   required>
        </div>

        <div class="mb-4">
            <label class="form-label">Konfirmasi Password</label>
            <input type="password"
                   name="password_confirm"
                   class="form-control"
                   placeholder="Ulangi password"
                   required>
        </div>

        <button type="submit" class="btn btn-eco w-100 py-2">
            Daftar
        </button>
    </form>

    <div class="auth-footer">
        Sudah punya akun? <a href="<?= base_url('login') ?>">Masuk</a>
    </div>
</div>

</body>
</html>