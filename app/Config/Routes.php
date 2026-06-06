<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// =====================
// USER
// =====================
$routes->get('/users', 'UserController::index');
$routes->get('/users/create', 'UserController::create');
$routes->post('/users/store', 'UserController::store');
$routes->get('/users/edit/(:num)', 'UserController::edit/$1');
$routes->post('/users/update/(:num)', 'UserController::update/$1');
$routes->get('/users/delete/(:num)', 'UserController::delete/$1');


// =====================
// KATEGORI SAMPAH
// =====================
$routes->get('/kategori-sampah', 'KategoriSampahController::index');
$routes->get('/kategori-sampah/create', 'KategoriSampahController::create');
$routes->post('/kategori-sampah/store', 'KategoriSampahController::store');
$routes->get('/kategori-sampah/edit/(:num)', 'KategoriSampahController::edit/$1');
$routes->post('/kategori-sampah/update/(:num)', 'KategoriSampahController::update/$1');
$routes->get('/kategori-sampah/delete/(:num)', 'KategoriSampahController::delete/$1');


// =====================
// PENJEMPUTAN
// =====================
$routes->get('/penjemputan', 'PenjemputanController::index');
$routes->get('/penjemputan/create', 'PenjemputanController::create');
$routes->post('/penjemputan/store', 'PenjemputanController::store');
$routes->get('/penjemputan/edit/(:num)', 'PenjemputanController::edit/$1');
$routes->post('/penjemputan/update/(:num)', 'PenjemputanController::update/$1');
$routes->get('/penjemputan/delete/(:num)', 'PenjemputanController::delete/$1');


// =====================
// TRANSAKSI COIN
// =====================
$routes->get('/transaksi-coin', 'TransaksiCoinController::index');
$routes->get('/transaksi-coin/create', 'TransaksiCoinController::create');
$routes->post('/transaksi-coin/store', 'TransaksiCoinController::store');
$routes->get('/transaksi-coin/edit/(:num)', 'TransaksiCoinController::edit/$1');
$routes->post('/transaksi-coin/update/(:num)', 'TransaksiCoinController::update/$1');
$routes->get('/transaksi-coin/delete/(:num)', 'TransaksiCoinController::delete/$1');


// =====================
// HOME
// =====================
$routes->get('/', 'Home::index');