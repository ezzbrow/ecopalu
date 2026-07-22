<?php

/**
 * Helper notifikasi — dipanggil dari controller setiap event bisnis.
 * Insert ke tabel `notifications` via NotificationModel.
 */

if (! function_exists('insertNotifikasi')) {
    /**
     * @param int    $recipientUserId  id user penerima
     * @param string $recipientRole    'user' | 'admin' | 'banksampah'
     * @param string $judul            judul pendek
     * @param string $pesan            pesan lengkap
     * @param string $tipe             'penjemputan' | 'pencairan' | 'sistem' | 'poin'
     * @param int|null $refId          id referensi (mis. id penjemputan/pencairan)
     */
    function insertNotifikasi(
        int $recipientUserId,
        string $recipientRole,
        string $judul,
        string $pesan,
        string $tipe = 'sistem',
        ?int $refId = null
    ): bool {
        $model = new \App\Models\NotificationModel();
        return $model->insert([
            'recipient_user_id' => $recipientUserId,
            'recipient_role'    => $recipientRole,
            'judul'             => $judul,
            'pesan'             => $pesan,
            'tipe'              => $tipe,
            'ref_id'            => $refId,
            'is_read'           => 0,
            'created_at'        => date('Y-m-d H:i:s'),
        ]);
    }
}

if (! function_exists('isHariPenjemputan')) {
    /**
     * Mengecek apakah tanggal tertentu jatuh di hari penjemputan Bank Sampah.
     * Hari penjemputan = Rabu (3) & Sabtu (6). N: 1 (Senin) – 7 (Minggu).
     *
     * Dipakai untuk validasi tanggal_jemput saat User mengajukan penjemputan.
     * Admin EcoPalu (override koreksi) tidak memakai validasi ini.
     */
    function isHariPenjemputan(string $date): bool
    {
        $ts = strtotime($date);
        if ($ts === false) {
            return false;
        }
        $n = (int) date('N', $ts);
        return $n === 3 || $n === 6;
    }
}

if (! function_exists('markNotifikasiRead')) {
    /**
     * Tandai SATU notifikasi sebagai sudah dibaca (idempotent).
     * @param int $idNotif
     * @param int $idUserExpected  user_id penerima (untuk validasi ownership)
     * @return bool sukses update
     */
    function markNotifikasiRead(int $idNotif, int $idUserExpected): bool
    {
        $model = new \App\Models\NotificationModel();
        $row = $model->find($idNotif);
        if (! $row) {
            return false;
        }
        // Validasi ownership: hanya penerima yang boleh mark-read
        if ((int) $row['recipient_user_id'] !== $idUserExpected) {
            return false;
        }
        // Idempotent: kalau sudah read, return true tanpa update ulang
        if ((int) $row['is_read'] === 1) {
            return true;
        }
        return (bool) $model->update($idNotif, [
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s'),
        ]);
    }
}

if (! function_exists('rateCoinToRupiah')) {
    /**
     * Rate konversi coin ke rupiah.
     * Spec: 1 coin = Rp 400.
     */
    function rateCoinToRupiah(): int
    {
        return 400;
    }
}

if (! function_exists('notifTargetUrl')) {
    /**
     * Tentukan URL target untuk sebuah notifikasi berdasarkan tipe & ref_id.
     * Dipakai untuk navigasi saat user klik notifikasi di topbar.
     *
     * @param array $notif  row notifikasi (recipient_role, tipe, ref_id)
     * @return string       URL target
     */
    function notifTargetUrl(array $notif): string
    {
        $role   = (string) ($notif['recipient_role'] ?? '');
        $tipe   = (string) ($notif['tipe'] ?? '');
        $refId  = (int) ($notif['ref_id'] ?? 0);

        // Default: dashboard sesuai role
        $default = match ($role) {
            'admin'       => '/dashboard/admin',
            'banksampah'  => '/dashboard/banksampah',
            default       => '/dashboard/user',
        };

        if ($refId <= 0) {
            return $default;
        }

        return match ($tipe) {
            'penjemputan' => '/penjemputan?role=' . $role,
            'pencairan'   => $role === 'admin' ? '/pencairan/admin' : '/pencairan',
            'poin'        => '/penjemputan?role=' . $role,
            default       => $default,
        };
    }
}

if (! function_exists('formatTanggalIndonesia')) {
    function formatTanggalIndonesia(string $date): string
    {
        if ($date === '' || $date === null) {
            return '-';
        }
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $ts = strtotime($date);
        if ($ts === false) {
            return $date;
        }
        return date('d', $ts) . ' ' . $bulan[(int) date('m', $ts)] . ' ' . date('Y', $ts);
    }
}