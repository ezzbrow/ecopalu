<?php

namespace App\Controllers;

use App\Models\NotificationModel;

/**
 * DashboardController — 3 method (user, admin, banksampah).
 * Pakai layout 'layouts/dashboard' (role-aware sidebar + topbar notifikasi).
 *
 * Data yang dikirim ke view:
 *   - $title         string  — page title
 *   - $unread_count  int     — jumlah notifikasi belum dibaca untuk badge topbar
 */
class DashboardController extends BaseController
{
    protected $notifModel;

    public function __construct()
    {
        $this->notifModel = new NotificationModel();
    }

    /**
     * Hitung jumlah notifikasi belum dibaca untuk user session saat ini.
     * Dipakai untuk badge di topbar (semua 3 role).
     */
    private function getUnreadCount(): int
    {
        $userId = (int) (session('user_id') ?? 0);
        if ($userId <= 0) {
            return 0;
        }
        return $this->notifModel
            ->where('recipient_user_id', $userId)
            ->where('is_read', 0)
            ->countAllResults();
    }

    public function user()
    {
        $data = [
            'title'        => 'Dashboard User — EcoPalu',
            'unread_count' => $this->getUnreadCount(),
            'notifList'    => $this->getRecentNotif(),
        ];
        return view('dashboard/user', $data);
    }

    public function admin()
    {
        $data = [
            'title'        => 'Dashboard Admin — EcoPalu',
            'unread_count' => $this->getUnreadCount(),
            'notifList'    => $this->getRecentNotif(),
        ];
        return view('dashboard/admin', $data);
    }

    public function banksampah()
    {
        $data = [
            'title'        => 'Dashboard Bank Sampah — EcoPalu',
            'unread_count' => $this->getUnreadCount(),
            'notifList'    => $this->getRecentNotif(),
        ];
        return view('dashboard/banksampah', $data);
    }

    /**
     * Ambil 10 notifikasi terbaru untuk topbar dropdown.
     * Sesuai spec: unread di atas, lalu created_at DESC.
     */
    private function getRecentNotif(int $limit = 10): array
    {
        $userId = (int) (session('user_id') ?? 0);
        if ($userId <= 0) {
            return [];
        }
        return $this->notifModel->getForUser($userId, $limit);
    }
}