<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePencairanRewardTable extends Migration
{
    /**
     * Migration: tabel `pencairan_reward`
     *
     * Konteks (CLAUDE.md):
     *   Sistem TIDAK menggunakan QRIS generate otomatis. Pencairan poin memakai
     *   sistem manual: user ajukan pencairan nominal tertentu (dipotong dari
     *   koin) → admin EcoPalu yang mentransfer secara manual ke rekening/
     *   e-wallet user → status diverifikasi bertahap.
     *
     * Status enum (3 tahap sesuai spec): menunggu → diproses → berhasil
     *   + ditolak (alternatif jika admin tolak pengajuan)
     *
     * Kolom e-wallet (jenis_ewallet, nomor_ewallet) untuk pencatatan
     * manual transfer oleh Admin. TIDAK ada integrasi API payment
     * gateway (Midtrans/Xendit/dsb) di iterasi ini — keputusan
     * PayoutService integration masih menunggu diskusi tim.
     *
     * TODO (open decision Q3 di CLAUDE.md): aturan minimal & kelipatan
     *   nominal pencairan (berapa minimal poin/rupiah per pengajuan). Saat
     *   ini hanya ada struktur data — aturan validasi akan dipasang di
     *   controller (PencairanController) nanti.
     */
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'nominal_coin' => [
                'type' => 'INT',
            ],
            'nominal_rupiah' => [
                'type'     => 'INT',
                'null'     => true,
                'comment'  => 'Placeholder: konversi coin→rupiah rate belum final',
            ],
            'jenis_ewallet' => [
                'type'     => 'VARCHAR',
                'constraint' => 20,
                'null'     => true,
                'comment'  => 'Manual: dana, ovo, gopay, shopeepay, dst. Validasi di level aplikasi (VARCHAR bukan ENUM supaya tidak perlu ALTER saat nambah provider).',
            ],
            'nomor_ewallet' => [
                'type'     => 'VARCHAR',
                'constraint' => 20,
                'null'     => true,
                'comment'  => 'Nomor HP yang terdaftar di e-wallet. Diverifikasi manual oleh Admin saat transfer.',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['menunggu', 'diproses', 'berhasil', 'ditolak'],
                'default'    => 'menunggu',
            ],
            'alasan_penolakan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'tanggal_transfer' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id', 'status']);

        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('pencairan_reward');
    }

    public function down()
    {
        $this->forge->dropTable('pencairan_reward');
    }
}