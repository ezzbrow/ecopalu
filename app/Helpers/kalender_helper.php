<?php

/**
 * Helper kalender — untuk partial dashboard.
 *
 * Dipakai oleh:
 *   - app/Views/dashboard/_partial_kalender.php
 *
 * Logic: hitung cell kalender bulanan (Senin–Minggu × N baris),
 * termasuk hari "di luar bulan" (prev/next month) untuk fill cell kosong.
 *
 * Hari operasional penjemputan Bank Sampah: Rabu & Sabtu (sesuai spec).
 */

if (! function_exists('isHariOperasional')) {
    /**
     * Cek apakah tanggal jatuh di hari penjemputan (Rabu=3 atau Sabtu=6).
     * N: 1 (Senin) – 7 (Minggu).
     */
    function isHariOperasional(DateTime $date): bool
    {
        $n = (int) $date->format('N');
        return $n === 3 || $n === 6;
    }
}

if (! function_exists('getKalenderBulan')) {
    /**
     * Bangun array kalender untuk bulan & tahun tertentu.
     * Mengikuti ISO: minggu dimulai dari Senin (N=1).
     *
     * Return: array of weeks. Tiap week adalah array of 7 days dengan key:
     *   - 'date'      string 'Y-m-d'
     *   - 'day'       int    1..31 (tanggal)
     *   - 'in_month'  bool   true kalau tanggal di bulan yang dimaksud
     *   - 'is_today'  bool   true kalau tanggal == hari ini
     *   - 'is_operasional' bool true kalau Rabu/Sabtu
     */
    function getKalenderBulan(int $tahun, int $bulan): array
    {
        // Hari pertama bulan
        $first   = new DateTime(sprintf('%04d-%02d-01', $tahun, $bulan));
        // Hari terakhir bulan
        $last    = new DateTime(sprintf('%04d-%02d-%02d', $tahun, $bulan, (int) $first->format('t')));
        // ISO weekday bulan pertama (1=Senin..7=Minggu)
        $firstDow = (int) $first->format('N');
        // Mundur ke Senin dari minggu yang mengandung tanggal 1
        $gridStart = (clone $first)->modify('-' . ($firstDow - 1) . ' days');
        // Maju ke Minggu dari minggu yang mengandung tanggal terakhir
        $lastDow   = (int) $last->format('N');
        $gridEnd   = (clone $last)->modify('+' . (7 - $lastDow) . ' days');

        $today = new DateTime('today');

        $weeks = [];
        $cursor = clone $gridStart;
        while ($cursor <= $gridEnd) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $dateStr = $cursor->format('Y-m-d');
                $week[] = [
                    'date'           => $dateStr,
                    'day'            => (int) $cursor->format('j'),
                    'in_month'       => (int) $cursor->format('n') === $bulan,
                    'is_today'       => $cursor->format('Y-m-d') === $today->format('Y-m-d'),
                    'is_operasional' => isHariOperasional($cursor),
                ];
                $cursor->modify('+1 day');
            }
            $weeks[] = $week;
        }
        return $weeks;
    }
}

if (! function_exists('namaBulanIndonesia')) {
    /**
     * Nama bulan dalam Bahasa Indonesia.
     */
    function namaBulanIndonesia(int $bulan): string
    {
        $nama = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        return $nama[$bulan] ?? '';
    }
}
