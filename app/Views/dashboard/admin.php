<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin — EcoPalu</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body style="font-family:'Poppins',sans-serif; background:#f8fafc; min-height:100vh;">

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="color:#22C55E; margin:0;">♻ EcoPalu</h2>
            <small class="text-muted">Dashboard Admin EcoPalu</small>
        </div>

        <form action="<?= base_url('logout') ?>" method="post" class="m-0">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <div class="card shadow-sm" style="border-radius:16px;">
        <div class="card-body p-4">
            <h4 class="mb-3">Dashboard Admin EcoPalu — Coming Soon</h4>

            <p class="mb-2">Halo, <strong><?= esc(session()->get('nama')) ?></strong>! Anda login sebagai <span class="badge bg-success"><?= esc(session()->get('role')) ?></span>.</p>

            <p class="text-muted mb-0">
                Halaman dashboard Admin sesuai spec CLAUDE.md (Card Disetujui, Grafik Reward Dicairkan, Kalender, verifikasi penjemputan, dll) akan dibangun di langkah selanjutnya.
            </p>
        </div>
    </div>

    <div class="mt-3">
        <small class="text-muted">
            Session aktif — user_id: <?= esc(session()->get('user_id')) ?> &middot; role: <?= esc(session()->get('role')) ?>
        </small>
    </div>
</div>

</body>
</html>