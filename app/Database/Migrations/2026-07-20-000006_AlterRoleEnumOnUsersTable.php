<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterRoleEnumOnUsersTable extends Migration
{
    public function up()
    {
        // Ubah enum `users.role` dari 2 nilai (admin/user) ke 3 nilai
        // untuk support Bank Sampah sebagai role ketiga (Q1 Opsi B:
        // Bank Sampah dibuat Admin via CRUD users, tidak self-register).
        //
        // Tiga role: admin (EcoPalu), banksampah (Bank Sampah Kabelotapura),
        // user (EcoFriend).
        //
        // CATATAN: migrasi ini TIDAK menyentuh nilai existing di baris.
        // Saat ini users hanya berisi role 'admin' (1 row) dan 'user'
        // (2 rows) — keduanya anggota dari enum baru, jadi ALTER aman.
        // Jika ada baris dengan role di luar 3 nilai baru, MySQL akan
        // menolak ALTER.
        $this->forge->modifyColumn('users', [
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['admin', 'banksampah', 'user'],
                'default'    => 'user',
            ],
        ]);
    }

    public function down()
    {
        // Kembalikan ke enum awal (2 nilai) — akan GAGAL jika ada baris
        // dengan role 'banksampah'.
        $this->forge->modifyColumn('users', [
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['admin', 'user'],
                'default'    => 'user',
            ],
        ]);
    }
}