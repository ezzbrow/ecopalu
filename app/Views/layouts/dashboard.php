<?php
/**
 * Layout: layouts/dashboard.php
 *
 * Wrapper SHARED untuk 3 dashboard (User, BankSampah, Admin).
 *
 * Struktur:
 *   - Sidebar kiri (role-aware menu)
 *   - Topbar: greeting + role badge + notifikasi icon (counter) + user dropdown (nama + logout)
 *   - Main content (rendered dari child view via $this->renderSection('content'))
 *
 * Cara pakai di child view:
 *   <?= $this->extend('layouts/dashboard') ?>
 *   <?= $this->section('content') ?>
 *   ... HTML content dashboard ...
 *   <?= $this->endSection() ?>
 *
 * Data yang dibutuhkan dari controller:
 *   - $title              string  (page title, muncul di <title>)
 *   - $role               string  (session role, untuk filter sidebar — sudah di session)
 *   - $nama               string  (session nama, untuk topbar greeting)
 *   - $unread_count       int     (jumlah notifikasi belum dibaca, untuk badge topbar)
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Dashboard EcoPalu') ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">

    <style>
        :root {
            --eco-green: #22C55E;
            --eco-green-light: #DCFCE7;
            --eco-green-dark: #16A34A;
            --sidebar-w: 260px;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
        }
        .db-sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: white;
            box-shadow: 2px 0 12px rgba(0, 0, 0, 0.04);
            padding: 20px 16px;
            overflow-y: auto;
            z-index: 1000;
        }
        .db-sidebar .brand {
            color: var(--eco-green);
            font-weight: 700;
            font-size: 1.3rem;
            margin-bottom: 4px;
        }
        .db-sidebar .role-tag {
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 600;
            background: var(--eco-green-light);
            color: var(--eco-green-dark);
            padding: 2px 8px;
            border-radius: 10px;
            margin-bottom: 24px;
            text-transform: uppercase;
        }
        .db-sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #475569;
            font-weight: 500;
            font-size: 0.92rem;
            padding: 10px 14px;
            border-radius: 10px;
            margin-bottom: 4px;
            transition: 0.2s;
        }
        .db-sidebar .nav-link:hover,
        .db-sidebar .nav-link.active {
            background: var(--eco-green-light);
            color: var(--eco-green-dark);
        }
        .db-sidebar .nav-link i {
            font-size: 1.1rem;
        }
        .db-sidebar .nav-section {
            font-size: 0.7rem;
            text-transform: uppercase;
            color: #94a3b8;
            font-weight: 600;
            margin: 16px 0 6px 14px;
            letter-spacing: 0.5px;
        }
        .db-topbar {
            position: sticky;
            top: 0;
            margin-left: var(--sidebar-w);
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 28px;
            z-index: 900;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .db-topbar .greeting {
            font-size: 0.95rem;
            color: #475569;
        }
        .db-topbar .greeting strong {
            color: #0f172a;
        }
        .db-topbar .notif-btn {
            position: relative;
            background: none;
            border: none;
            font-size: 1.25rem;
            color: #475569;
            padding: 6px 10px;
            border-radius: 8px;
        }
        .db-topbar .notif-btn:hover {
            background: #f1f5f9;
        }
        .db-topbar .notif-badge {
            position: absolute;
            top: 2px;
            right: 2px;
            background: #ef4444;
            color: white;
            font-size: 0.65rem;
            font-weight: 700;
            border-radius: 10px;
            padding: 1px 5px;
            min-width: 16px;
            text-align: center;
        }
        .db-main {
            margin-left: var(--sidebar-w);
            padding: 28px;
            min-height: calc(100vh - 60px);
        }
        @media (max-width: 768px) {
            .db-sidebar { transform: translateX(-100%); transition: 0.3s; }
            .db-sidebar.show { transform: translateX(0); }
            .db-topbar, .db-main { margin-left: 0; }
        }
    </style>
</head>
<body>

<?php
    // Helper closure: cek apakah URL saat ini cocok dengan $href untuk active state
    $currentUrl = current_url();
    $isActive = static function (string $href) use ($currentUrl): bool {
        return str_contains($currentUrl, $href);
    };
    $userRole  = session('role') ?? 'user';
    $userName  = session('nama') ?? 'Pengguna';
    $unreadCnt = $unread_count ?? 0;
?>

<!-- ============== SIDEBAR (role-aware) ============== -->
<aside class="db-sidebar" id="dbSidebar">
    <div class="brand">♻ EcoPalu</div>
    <div class="role-tag"><?= esc($userRole) ?></div>

    <?php if ($userRole === 'user'): ?>
        <!-- ====== MENU USER (EcoFriend) ====== -->
        <a class="nav-link <?= $isActive('/dashboard/user') ? 'active' : '' ?>" href="<?= base_url('dashboard/user') ?>">
            <i class="bi bi-house"></i> Beranda
        </a>
        <a class="nav-link <?= $isActive('/penjemputan/create') ? 'active' : '' ?>" href="<?= base_url('penjemputan/create') ?>">
            <i class="bi bi-truck"></i> Ajukan Penjemputan
        </a>
        <a class="nav-link <?= $isActive('/penjemputan') && !$isActive('/penjemputan/create') && !$isActive('/penjemputan/edit') ? 'active' : '' ?>" href="<?= base_url('penjemputan?role=user') ?>">
            <i class="bi bi-list-check"></i> Status Pengajuan
        </a>
        <div class="nav-section">Reward</div>
        <a class="nav-link <?= $isActive('/pencairan') && !$isActive('/pencairan/admin') ? 'active' : '' ?>" href="<?= base_url('pencairan') ?>">
            <i class="bi bi-coin"></i> Reward & Poin
        </a>
        <div class="nav-section">Akun</div>
        <a class="nav-link" href="#edukasi">
            <i class="bi bi-book"></i> Edukasi
        </a>
        <a class="nav-link" href="#kalender">
            <i class="bi bi-calendar-week"></i> Kalender
        </a>
        <a class="nav-link" href="#ubah-password">
            <i class="bi bi-key"></i> Ubah Password
        </a>

    <?php elseif ($userRole === 'banksampah'): ?>
        <!-- ====== MENU BANK SAMPAH ====== -->
        <a class="nav-link <?= $isActive('/dashboard/banksampah') ? 'active' : '' ?>" href="<?= base_url('dashboard/banksampah') ?>">
            <i class="bi bi-house"></i> Beranda
        </a>
        <a class="nav-link <?= $isActive('/penjemputan') && !$isActive('/penjemputan/create') ? 'active' : '' ?>" href="<?= base_url('penjemputan?role=banksampah') ?>">
            <i class="bi bi-truck"></i> Penjemputan
        </a>
        <div class="nav-section">Akun</div>
        <a class="nav-link" href="#kalender">
            <i class="bi bi-calendar-week"></i> Kalender
        </a>
        <a class="nav-link" href="#ubah-password">
            <i class="bi bi-key"></i> Ubah Password
        </a>

    <?php elseif ($userRole === 'admin'): ?>
        <!-- ====== MENU ADMIN ECOPALU ====== -->
        <a class="nav-link <?= $isActive('/dashboard/admin') ? 'active' : '' ?>" href="<?= base_url('dashboard/admin') ?>">
            <i class="bi bi-house"></i> Beranda
        </a>
        <div class="nav-section">Penjemputan</div>
        <a class="nav-link <?= $isActive('/penjemputan') && !$isActive('/penjemputan/create') ? 'active' : '' ?>" href="<?= base_url('penjemputan?role=admin') ?>">
            <i class="bi bi-truck"></i> Daftar Penjemputan
        </a>
        <div class="nav-section">Reward & Pencairan</div>
        <a class="nav-link <?= $isActive('/pencairan/admin') || $isActive('/pencairan') ? 'active' : '' ?>" href="<?= base_url('pencairan/admin') ?>">
            <i class="bi bi-cash-stack"></i> Pencairan Reward
        </a>
        <div class="nav-section">Manajemen</div>
        <a class="nav-link <?= $isActive('/users') ? 'active' : '' ?>" href="<?= base_url('users') ?>">
            <i class="bi bi-people"></i> User
        </a>
        <a class="nav-link <?= $isActive('/kategori-sampah') ? 'active' : '' ?>" href="<?= base_url('kategori-sampah') ?>">
            <i class="bi bi-recycle"></i> Kategori Sampah
        </a>
        <a class="nav-link <?= $isActive('/transaksi-coin') ? 'active' : '' ?>" href="<?= base_url('transaksi-coin') ?>">
            <i class="bi bi-coin"></i> Transaksi Coin
        </a>
        <div class="nav-section">Akun</div>
        <a class="nav-link" href="<?= base_url('dashboard/admin') ?>#kalender">
            <i class="bi bi-calendar-week"></i> Kalender
        </a>
        <a class="nav-link <?= $isActive('/password/change') ? 'active' : '' ?>" href="<?= base_url('password/change') ?>">
            <i class="bi bi-key"></i> Ubah Password
        </a>
    <?php endif; ?>

    <div class="nav-section">&nbsp;</div>
    <a class="nav-link" href="<?= base_url('/') ?>" style="color: #94a3b8;">
        <i class="bi bi-arrow-left"></i> Landing Page
    </a>
</aside>

<!-- ============== TOPBAR ============== -->
<header class="db-topbar">
    <div class="greeting">
        Halo, <strong><?= esc($userName) ?></strong>
        <?php if (in_array($userRole, ['admin', 'banksampah'], true)): ?>
            <span class="role-tag ms-2"><?= esc($userRole) ?></span>
        <?php endif; ?>
    </div>
    <div class="d-flex align-items-center gap-2" style="position: relative;">
        <button class="notif-btn" type="button" id="notifBtn" title="Notifikasi">
            <i class="bi bi-bell"></i>
            <?php if ($unreadCnt > 0): ?>
                <span class="notif-badge" id="notifBadge"><?= (int) $unreadCnt ?></span>
            <?php else: ?>
                <span class="notif-badge" id="notifBadge" style="display:none;">0</span>
            <?php endif; ?>
        </button>
        <?= $this->include('dashboard/_partial_notifikasi', ['notifList' => $notifList ?? []]) ?>
        <form action="<?= base_url('logout') ?>" method="post" class="m-0">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>
</header>

<!-- ============== MAIN CONTENT (rendered from child view) ============== -->
<main class="db-main">
    <?= $this->renderSection('content') ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Toggle sidebar di mobile
document.getElementById('dbSidebarToggle')?.addEventListener('click', () => {
    document.getElementById('dbSidebar')?.classList.toggle('show');
});

// Notifikasi button (akan di-handle di Langkah #3)
document.getElementById('notifBtn')?.addEventListener('click', () => {
    if (window.toggleNotifDropdown) window.toggleNotifDropdown();
});
</script>
</body>
</html>