<?php

namespace App\Controllers;

/**
 * DashboardController — STUB SEMENTARA.
 * Nanti akan diganti dengan DashboardController penuh per role
 * (sesuai Langkah Prompt #2 — RBAC & per-role dashboard).
 *
 * Untuk saat ini, view stub 'dashboard/{role}.php' cukup menampilkan
 * session info + tombol logout. View ini TIDAK dilindungi filter
 * Auth/Role — itu Langkah C terpisah.
 */
class DashboardController extends BaseController
{
    public function user()
    {
        return view('dashboard/user');
    }

    public function admin()
    {
        return view('dashboard/admin');
    }

    public function banksampah()
    {
        return view('dashboard/banksampah');
    }
}