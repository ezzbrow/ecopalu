<?php

namespace App\Services;

use App\Models\PencairanModel;

/**
 * SimulatedPayoutService — simulasi Midtrans payout API untuk demo offline.
 *
 * 100% deterministik, TIDAK ada HTTP call ke Midtrans. Pembayaran auto-
 * settled oleh JavaScript di frontend (polling endpoint /pencairan/status/{id})
 * yang trigger POST ke endpoint auto-settle setelah delay 3 detik.
 *
 * Method:
 *   - generateReference()         — bikin nomor referensi format Midtrans-like
 *   - isOwnedBy(int $id, int $userId) — cek ownership pencairan
 *
 * Kenapa tidak ada method executePayout/sendPayout?
 *   Karena ini SIMULASI, bukan API call sungguhan. Settlement dilakukan
 *   oleh user (via JS) trigger endpoint admin (auto-settle). Pendekatan ini
 *   menjamin 100% deterministik untuk demo tanpa internet.
 *
 * Untuk upgrade ke Midtrans sungguhan di production:
 *   1. Implement MidtransSnap::createTransaction() di method baru
 *   2. Ganti mekanisme auto-settle dengan Midtrans webhook callback
 *   3. Hapus endpoint /pencairan/{id}/auto-settle (atau non-aktifkan)
 */
class SimulatedPayoutService
{
    /**
     * Generate nomor referensi mirip format Midtrans.
     * Format: MDTR- + 10 hex chars (uppercase).
     * Contoh: MDTR-A1B2C3D4E5
     */
    public function generateReference(): string
    {
        return 'MDTR-' . strtoupper(bin2hex(random_bytes(5)));
    }

    /**
     * Cek apakah pencairan dengan ID tertentu milik user tertentu.
     * Dipakai untuk validasi ownership endpoint auto-settle (mencegah
     * user lain iseng settle punya orang lain).
     */
    public function isOwnedBy(int $pencairanId, int $userId): bool
    {
        $model = new PencairanModel();
        $row = $model->find($pencairanId);
        if (! $row) {
            return false;
        }
        return (int) $row['user_id'] === $userId;
    }
}