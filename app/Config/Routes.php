<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// =====================
// USER (Admin only — Q7)
// =====================
$routes->get('/users', 'UserController::index', ['filter' => ['auth', 'role:admin']]);
$routes->get('/users/create', 'UserController::create', ['filter' => ['auth', 'role:admin']]);
$routes->post('/users/store', 'UserController::store', ['filter' => ['auth', 'role:admin']]);
$routes->get('/users/edit/(:num)', 'UserController::edit/$1', ['filter' => ['auth', 'role:admin']]);
$routes->post('/users/update/(:num)', 'UserController::update/$1', ['filter' => ['auth', 'role:admin']]);
$routes->post('/users/delete/(:num)', 'UserController::delete/$1', ['filter' => ['auth', 'role:admin']]);  // POST (bukan GET) untuk CSRF safety


// =====================
// KATEGORI SAMPAH (Admin only — Q7)
// =====================
$routes->get('/kategori-sampah', 'KategoriSampahController::index', ['filter' => ['auth', 'role:admin']]);
$routes->get('/kategori-sampah/create', 'KategoriSampahController::create', ['filter' => ['auth', 'role:admin']]);
$routes->post('/kategori-sampah/store', 'KategoriSampahController::store', ['filter' => ['auth', 'role:admin']]);
$routes->get('/kategori-sampah/edit/(:num)', 'KategoriSampahController::edit/$1', ['filter' => ['auth', 'role:admin']]);
$routes->post('/kategori-sampah/update/(:num)', 'KategoriSampahController::update/$1', ['filter' => ['auth', 'role:admin']]);
$routes->post('/kategori-sampah/delete/(:num)', 'KategoriSampahController::delete/$1', ['filter' => ['auth', 'role:admin']]);  // POST (bukan GET) untuk CSRF safety


// =====================
// PENJEMPUTAN
// =====================
// Q1: GET list — semua role boleh akses, filter data per role di controller
$routes->get('/penjemputan', 'PenjemputanController::index', ['filter' => ['auth', 'role:admin,banksampah,user']]);
// Q2: create/store — HANYA user
$routes->get('/penjemputan/create', 'PenjemputanController::create', ['filter' => ['auth', 'role:user']]);
$routes->post('/penjemputan/store', 'PenjemputanController::store', ['filter' => ['auth', 'role:user']]);
// Q3: edit/update — admin + user (validasi granular di controller)
$routes->get('/penjemputan/edit/(:num)', 'PenjemputanController::edit/$1', ['filter' => ['auth', 'role:admin,user']]);
$routes->post('/penjemputan/update/(:num)', 'PenjemputanController::update/$1', ['filter' => ['auth', 'role:admin,user']]);
// Hapus: HANYA admin (route delete/$1 tidak ada — sudah dihapus Poin 4)

// Q4: setujui/tolak/finalisasi-poin/tolak-poin — HANYA admin
$routes->post('/penjemputan/setujui/(:num)',          'PenjemputanController::setujui/$1',          ['filter' => ['auth', 'role:admin']]);
$routes->post('/penjemputan/tolak/(:num)',             'PenjemputanController::tolak/$1',             ['filter' => ['auth', 'role:admin']]);
$routes->post('/penjemputan/finalisasi-poin/(:num)',   'PenjemputanController::finalisasiPoin/$1',   ['filter' => ['auth', 'role:admin']]);
$routes->post('/penjemputan/tolak-poin/(:num)',        'PenjemputanController::tolakPoin/$1',        ['filter' => ['auth', 'role:admin']]);
// Q5: konfirmasi-selesai — HANYA banksampah
$routes->post('/penjemputan/konfirmasi-selesai/(:num)', 'PenjemputanController::konfirmasiSelesai/$1', ['filter' => ['auth', 'role:banksampah']]);


// =====================
// TRANSAKSI COIN (Admin only — Q6)
// =====================
$routes->get('/transaksi-coin', 'TransaksiCoinController::index', ['filter' => ['auth', 'role:admin']]);
$routes->get('/transaksi-coin/create', 'TransaksiCoinController::create', ['filter' => ['auth', 'role:admin']]);
$routes->post('/transaksi-coin/store', 'TransaksiCoinController::store', ['filter' => ['auth', 'role:admin']]);
$routes->get('/transaksi-coin/edit/(:num)', 'TransaksiCoinController::edit/$1', ['filter' => ['auth', 'role:admin']]);
$routes->post('/transaksi-coin/update/(:num)', 'TransaksiCoinController::update/$1', ['filter' => ['auth', 'role:admin']]);
$routes->post('/transaksi-coin/delete/(:num)', 'TransaksiCoinController::delete/$1', ['filter' => ['auth', 'role:admin']]);  // POST (bukan GET) untuk CSRF safety


// =====================
// HOME (publik)
// =====================
$routes->get('/', 'Home::index');


// =====================
// AUTH (publik — TIDAK ada filter auth)
// =====================
$routes->get('/login',             'AuthController::login');
$routes->post('/login/attempt',    'AuthController::attemptLogin');
$routes->get('/register',          'AuthController::register');
$routes->post('/register/attempt', 'AuthController::attemptRegister');
$routes->post('/logout',           'AuthController::logout');   // POST (bukan GET) untuk CSRF safety

// =====================
// DASHBOARD (per-role — Q8)
// =====================
$routes->get('/dashboard/user',       'DashboardController::user',       ['filter' => ['auth', 'role:user']]);
$routes->get('/dashboard/admin',      'DashboardController::admin',      ['filter' => ['auth', 'role:admin']]);
$routes->get('/dashboard/banksampah', 'DashboardController::banksampah', ['filter' => ['auth', 'role:banksampah']]);


// =====================
// NOTIFIKASI (read/unread per klik)
// =====================
// GET agar navigasi dari notifikasi langsung redirect ke target (link <a>).
// Aksi cuma update is_read=1, no state-changing penting — validasi
// ownership ketat di model.
$routes->get('/notification/mark/(:num)', 'NotificationController::markRead/$1', ['filter' => 'auth']);
$routes->get('/notification/mark-all',    'NotificationController::markAllRead', ['filter' => 'auth']);


// =====================
// UBAH PASSWORD (semua role yang login)
// =====================
$routes->get('/password/change',  'PasswordController::change',       ['filter' => 'auth']);
$routes->post('/password/change', 'PasswordController::attemptChange', ['filter' => 'auth']);


// =====================
// EDUKASI (semua role yang login)
// =====================
$routes->get('/edukasi', 'EdukasiController::index', ['filter' => 'auth']);


// =====================
// PENCAIRAN REWARD (Step 8-10 alur spec)
// =====================
// User: lihat saldo, ajukan pencairan
$routes->get('/pencairan',          'PencairanController::index',    ['filter' => ['auth', 'role:user']]);
$routes->get('/pencairan/create',   'PencairanController::create',   ['filter' => ['auth', 'role:user']]);
$routes->post('/pencairan/store',   'PencairanController::store',    ['filter' => ['auth', 'role:user']]);
// User: halaman processing (setelah submit) — JS auto-trigger settle 3 detik
$routes->get('/pencairan/processing/(:num)', 'PencairanController::processing/$1', ['filter' => ['auth', 'role:user']]);
$routes->get('/pencairan/success/(:num)',    'PencairanController::success/$1',    ['filter' => ['auth', 'role:user']]);
// Polling JSON endpoint untuk frontend
$routes->get('/pencairan/status/(:num)',     'PencairanController::status/$1',     ['filter' => ['auth', 'role:user']]);
// Auto-settle endpoint (simulasi Midtrans). Ownership check di controller.
$routes->post('/pencairan/(:num)/auto-settle', 'PencairanController::autoSettle/$1', ['filter' => ['auth', 'role:user']]);
// Admin: verifikasi manual (audit trail/fallback)
$routes->get('/pencairan/admin',                                           'PencairanController::adminList',       ['filter' => ['auth', 'role:admin']]);
$routes->post('/pencairan/(:num)/approve',                                 'PencairanController::approve/$1',     ['filter' => ['auth', 'role:admin']]);
$routes->post('/pencairan/(:num)/mark-transferred',                        'PencairanController::markTransferred/$1', ['filter' => ['auth', 'role:admin']]);
$routes->post('/pencairan/(:num)/reject',                                 'PencairanController::reject/$1',      ['filter' => ['auth', 'role:admin']]);
// =====================================================
// DEV-ONLY: Auto-login admin (hanya aktif di development)
// =====================================================
// CI4 otomatis set konstanta ENVIRONMENT dari .env (CI_ENVIRONMENT=development).
// Route ini akan otomatis return 404 kalau ENVIRONMENT !== 'development',
// jadi aman kalau tidak sengaja di-deploy ke production.
if (ENVIRONMENT === 'development') {
    $routes->get('/dev-login-admin', static function () {
        $db = \Config\Database::connect();
        $row = $db->table('users')->where('email', 'al1@gmail.com')->get()->getRowArray();
        if (! $row) {
            return service('response')
                ->setStatusCode(404)
                ->setBody('Admin user (al1@gmail.com) not found.');
        }
        // Set session langsung (bypass AuthController).
        session()->set([
            'user_id' => (int) $row['id'],
            'role'    => $row['role'],
            'nama'    => $row['name'],
        ]);
        return redirect()->to('/dashboard/admin');
    });
}
