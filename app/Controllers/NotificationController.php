<?php

namespace App\Controllers;

use App\Models\NotificationModel;

/**
 * NotificationController — handle aksi notifikasi.
 *
 * Semua route pakai POST + CSRF (lihat app/Config/Routes.php) — konsisten
 * dengan pola logout (POST) yang sudah ditetapkan di proyek ini. Tidak
 * ada state-changing action lewat GET murni.
 *
 *   POST /notification/mark/(:num)   : user klik 1 notifikasi → mark-read
 *   POST /notification/mark-all      : tandai semua notifikasi user jadi read
 *
 * Route filter: 'auth' (harus login). Tidak pakai 'role' — endpoint dipakai semua role.
 */
class NotificationController extends BaseController
{
    protected $notifModel;

    public function __construct()
    {
        $this->notifModel = new NotificationModel();
    }

    /**
     * POST /notification/mark/$id
     * Tandai SATU notifikasi sebagai sudah dibaca (idempotent).
     * Validasi ownership ketat (hanya penerima boleh mark-read).
     *
     * Setelah sukses, redirect ke referer (kalau ada), fallback ke dashboard sesuai role.
     */
    public function markRead($id)
    {
        $userId = (int) (session('user_id') ?? 0);
        if ($userId <= 0) {
            return redirect()->to('/login');
        }

        $ok = $this->notifModel->markRead((int) $id, $userId);
        if (! $ok) {
            return redirect()->back()->with('error', 'Notifikasi tidak ditemukan atau bukan milik Anda.');
        }

        return $this->redirectBackOrToOwnDashboard();
    }

    /**
     * POST /notification/mark-all
     * Tandai semua notifikasi user ini jadi sudah dibaca.
     */
    public function markAllRead()
    {
        $userId = (int) (session('user_id') ?? 0);
        if ($userId <= 0) {
            return redirect()->to('/login');
        }

        $this->notifModel
            ->where('recipient_user_id', $userId)
            ->where('is_read', 0)
            ->set(['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')])
            ->update();

        return redirect()->back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    /**
     * Redirect ke referer (kalau ada & dari host yang sama), fallback ke dashboard.
     */
    private function redirectBackOrToOwnDashboard()
    {
        $referer = $this->request->getServer('HTTP_REFERER');
        $baseUrl  = base_url();
        if ($referer && str_contains($referer, $baseUrl)) {
            return redirect()->to($referer);
        }
        $role = session('role') ?? 'user';
        switch ($role) {
            case 'admin':       return redirect()->to('/dashboard/admin');
            case 'banksampah': return redirect()->to('/dashboard/banksampah');
            default:           return redirect()->to('/dashboard/user');
        }
    }
}