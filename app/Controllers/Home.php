<?php

namespace App\Controllers;

use App\Models\KategoriSampahModel;

class Home extends BaseController
{
    /**
     * Landing page publik EcoPalu.
     * Route: GET / (publik, tidak ada filter auth/role).
     *
     * View: app/Views/home.php
     * Data dikirim: $kategori (jenis sampah, DISTINCT by nama_kategori
     * supaya 5 kategori tidak dobel walau DB ada 10 row duplikat).
     */
    public function index()
    {
        $model = new KategoriSampahModel();

        // DISTINCT nama_kategori + ambil coin_value representative (MIN
        // atau MAX, karena semua row dengan nama sama punya coin sama).
        // Disort ascending by nama biar konsisten di UI.
        $kategori = $model
            ->select('nama_kategori, MIN(coin_value) AS coin_value, COUNT(*) AS total')
            ->groupBy('nama_kategori')
            ->orderBy('nama_kategori', 'ASC')
            ->findAll();

        $data = [
            'kategori' => $kategori,
        ];

        return view('home', $data);
    }
}