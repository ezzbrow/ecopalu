<?php

namespace App\Controllers;

/**
 * EdukasiController — handle halaman Edukasi (panduan memilah sampah, dll).
 *
 * Method:
 *   - GET /edukasi : tampilkan view edukasi
 *
 * Route filter: 'auth' (harus login, bisa diakses semua role).
 *
 * Catatan: konten edukasi masih kerangka awal — bisa diperkaya nanti
 * (artikel detail, infografis, video, dll).
 */
class EdukasiController extends BaseController
{
    public function index()
    {
        return view('edukasi/index');
    }
}