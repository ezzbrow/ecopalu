<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — EcoPalu</title>

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
        .role-tabs {
            display: flex;
            background: #f1f5f9;
            border-radius: 12px;
            padding: 4px;
            margin-bottom: 24px;
        }
        .role-tab {
            flex: 1;
            text-align: center;
            padding: 10px 12px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 500;
            font-size: 14px;
            color: #64748b;
            transition: all 0.2s;
            user-select: none;
        }
        .role-tab.active {
            background: white;
            color: #22C55E;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
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
    <div class="auth-subtitle">Masuk ke akun Anda</div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <!-- Tab toggle User / Bank Sampah -->
    <div class="role-tabs">
        <div class="role-tab active" data-role="user" onclick="switchRole('user', this)">
            <i class="bi bi-person"></i> User
        </div>
        <div class="role-tab" data-role="banksampah" onclick="switchRole('banksampah', this)">
            <i class="bi bi-truck"></i> Bank Sampah
        </div>
    </div>

    <form action="<?= base_url('login/attempt') ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="role_toggle" id="role_toggle" value="user">

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email"
                   name="email"
                   class="form-control"
                   placeholder="nama@email.com"
                   value="<?= esc(old('email')) ?>"
                   required>
        </div>

        <div class="mb-4">
            <label class="form-label">Password</label>
            <input type="password"
                   name="password"
                   class="form-control"
                   placeholder="Masukkan password"
                   required>
        </div>

        <button type="submit" class="btn btn-eco w-100 py-2">
            Masuk
        </button>
    </form>

    <div class="auth-footer">
        Belum punya akun? <a href="<?= base_url('register') ?>">Daftar sebagai User</a>
    </div>
</div>

<script>
function switchRole(role, el) {
    document.getElementById('role_toggle').value = role;
    document.querySelectorAll('.role-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
}
</script>

</body>
</html>