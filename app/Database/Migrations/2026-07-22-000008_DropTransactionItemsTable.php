<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: drop tabel `transaction_items`.
 *
 * Konteks (cleanup Langkah C-2):
 *   Tabel `transaction_items` dibuat oleh migration 2026-06-05-122552 tapi
 *   tidak pernah dipakai di manapun di codebase (orphan). 0 rows saat ini.
 *   Untuk menjaga integritas history, tabel di-drop via migration baru
 *   ini (bukan drop langsung via SQL).
 *
 * Catatan:
 *   - Tidak bisa pakai re-running migration lama (akan re-create tabel).
 *   - Tidak ada FK constraint yang refer ke tabel ini.
 */
class DropTransactionItemsTable extends Migration
{
    public function up()
    {
        $this->forge->dropTable('transaction_items', true);
    }

    public function down()
    {
        // Untuk rollback: re-create tabel dengan struktur yang sama
        // seperti migration CreateTransactionItemsTable original.
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'transaction_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'product_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'qty' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'price' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('transaction_items', true);
    }
}