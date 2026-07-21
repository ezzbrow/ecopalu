<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * RoleFilter — harus dipasang SETELAH AuthFilter (jadi session user_id
 * sudah pasti ada).
 *
 * Cara pakai di Routes.php:
 *   $routes->get('/dashboard/admin', '...', ['filter' => 'role:admin']);
 *   $routes->get('/penjemputan',     '...', ['filter' => 'role:admin,banksampah']);
 *
 * Argument $arguments adalah array of role yang diizinkan. Kalau session
 * role user tidak ada dalam list → redirect ke dashboard sesuai role-nya
 * dengan flash error.
 */
class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Safety: kalau AuthFilter belum jalan, perlakukan sebagai belum login
        if (! session()->get('user_id')) {
            return redirect()->to('/login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // Default: kalau tidak ada argumen role, lewati (anggap OK)
        if (empty($arguments)) {
            return null;
        }

        $userRole = (string) session()->get('role');

        // Cek apakah role user ada di list role yang diizinkan
        if (! in_array($userRole, $arguments, true)) {
            // Redirect ke dashboard sesuai role-nya (bukan 403/blank)
            return $this->redirectToOwnDashboard($userRole)
                ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        return null; // Lanjut
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada action after
    }

    /**
     * Helper: redirect ke dashboard sesuai role user.
     * Duplicate logic dari AuthController::redirectToDashboard() — kalau
     * Anda nanti refactor ke satu helper, sync kedua tempat.
     */
    private function redirectToOwnDashboard(string $role)
    {
        switch ($role) {
            case 'admin':
                return redirect()->to('/dashboard/admin');
            case 'banksampah':
                return redirect()->to('/dashboard/banksampah');
            case 'user':
            default:
                return redirect()->to('/dashboard/user');
        }
    }
}