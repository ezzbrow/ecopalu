<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AuthFilter — gate sederhana: kalau session('user_id') tidak ada,
 * redirect ke /login dengan flash error.
 *
 * Tidak cek role — itu tugas RoleFilter.
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('user_id')) {
            return redirect()->to('/login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }
        // Return null = continue ke controller
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada action after
    }
}