<?php

namespace App\Controllers;

use App\Models\NotificationModel;

/**
 * NotificationController — handle aksi notifikasi.
 *
 *   GET  /notification/mark/(:num)?redirect=...  : user klik notifikasi →
 *       tandai sudah dibaca (idempotent) + redirect ke target URL
 *   GET  /notification/mark-all?redirect=...     : tandai semua notifikasi
 *       user jadi read + redirect ke target
 *
 * Pakai GET (bukan POST) untuk simplicity — aksi cuma update `is_read=1`,
 * no state-changing penting. Validasi ownership ketat di model.
 */
class NotificationController extends BaseController
{
    protected $notifModel;

    public function __construct()
    {
        $this->notifModel = new NotificationModel();
    }

    /**
     * GET /notification/mark/(:num)
     * Tandai SATU notifikasi sebagai sudah dibaca (idempotent).
     * Validasi ownership ketat (hanya penerima boleh mark-read).
     * Redirect ke:
     *   1. ?redirect= param (jika ada & valid)
     *   2. notifTargetUrl() untuk tipe-specific (mis. /penjemputan)
     *   3. dashboard sesuai role sebagai fallback
     */
    public function markRead($id)
    {
        $userId = (int) (session('user_id') ?? 0);
        if ($userId <= 0) {
            return redirect()->to('/login');
        }

        $row = $this->notifModel->find($id);
        $ok = $this->notifModel->markRead((int) $id, $userId);
        if (! $ok) {
            return redirect()->to('/login')
                ->with('error', 'Notifikasi tidak ditemukan atau bukan milik Anda.');
        }

        // Tentukan target redirect: ?redirect= > notifTargetUrl() > dashboard
        $redirect = trim((string) $this->request->getGet('redirect'));
        if ($redirect !== '' && str_starts_with($redirect, '/')) {
            return redirect()->to($redirect);
        }
        if ($row) {
            $target = notifTargetUrl($row);
            if ($target !== '' && str_starts_with($target, '/')) {
                return redirect()->to($target);
            }
        }
        return $this->redirectToOwnDashboard();
    }

    /**
     * GET /notification/mark-all
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

        $redirect = trim((string) $this->request->getGet('redirect'));
        if ($redirect !== '' && str_starts_with($redirect, '/')) {
            return redirect()->to($redirect)->with('success', 'Semua notifikasi ditandai sudah dibaca.');
        }
        // Fallback: redirect ke dashboard role sendiri (bukan redirect()->back()
        // karena CI4 previousURL bisa unpredictable).
        return $this->redirectToOwnDashboard()
            ->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    private function redirectToOwnDashboard()
    {
        $role = session('role') ?? 'user';
        return match ($role) {
            'admin'       => redirect()->to('/dashboard/admin'),
            'banksampah'  => redirect()->to('/dashboard/banksampah'),
            default       => redirect()->to('/dashboard/user'),
        };
    }
}