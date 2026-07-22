<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: tambah kolom `reference_number` ke tabel `pencairan_reward`.
 *
 * Konteks:
 *   Sebelumnya reference number pencairan disimpan di kolom `alasan_penolakan`
 *   (workaround karena tabel belum punya kolom khusus). Sekarang
 *   tambah kolom dedicated VARCHAR(50) NULL, dan rename logic
 *   yang lama supaya pakai kolom baru ini.
 *
 *   Kolom `alasan_penolakan` tetap dipakai untuk alasan reject (text).
 *
 * Eksekusi:
 *   - UP: tambah kolom reference_number VARCHAR(50) NULL
 *   - DOWN: drop kolom reference_number
 *
 * Data migration:
 *   - Copy nilai dari `alasan_penolakan` ke `reference_number`
 *     untuk row yang ada reference (MDTR-*) — hanya untuk record
 *     yang status='berhasil' atau 'diproses' (yaitu pencairan
 *     yang dibuat via SimulatedPayoutService).
 *   - Untuk record 'ditolak' / 'menunggu', alasan_penolakan berisi
 *     alasan reject, bukan reference — biarkan reference_number NULL.
 *
 *   UPDATE pencairan_reward
 *   SET reference_number = TRIM(alasan_penolakan)
 *   WHERE status IN ('berhasil', 'diproses')
 *     AND alasan_penolakan LIKE 'MDTR-%';
 */
class AddReferenceNumberToPencairanReward extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pencairan_reward', [
            'reference_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'nomor_ewallet',
                'comment'    => 'Nomor referensi transfer (format MDTR-XXXXXXXXXX)',
            ],
        ]);

        // Migrate data: pindahkan reference number yang tersimpan di
        // alasan_penolakan ke kolom reference_number (hanya untuk record
        // yang dihasilkan oleh SimulatedPayoutService, prefix 'MDTR-').
        $this->db->query(
            "UPDATE pencairan_reward
             SET reference_number = TRIM(alasan_penolakan)
             WHERE status IN ('berhasil', 'diproses')
               AND alasan_penolakan LIKE 'MDTR-%'"
        );
    }

    public function down()
    {
        // Sebelum drop kolom, kembalikan reference_number ke alasan_penolakan
        // (supaya tidak ada data hilang). Hanya untuk record yang reference_number
        // dimulai dengan 'MDTR-' (avoid overriding alasan_penolakan yang berisi
        // alasan reject).
        $this->db->query(
            "UPDATE pencairan_reward
             SET alasan_penolakan = TRIM(reference_number)
             WHERE reference_number LIKE 'MDTR-%'
               AND (alasan_penolakan IS NULL OR alasan_penolakan = '')"
        );

        $this->forge->dropColumn('pencairan_reward', 'reference_number');
    }
}