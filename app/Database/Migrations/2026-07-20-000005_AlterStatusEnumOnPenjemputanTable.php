<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterStatusEnumOnPenjemputanTable extends Migration
{
    public function up()
    {
        // Ubah enum `status` dari 3 nilai (pending/diproses/selesai) ke
        // 5 nilai sesuai flow controller PenjemputanController:
        //   menunggu → disetujui → menunggu_pemberian_poin → selesai
        //   (atau ditolak kapan saja)
        //
        // CATATAN: migrasi ini TIDAK menyentuh nilai existing di baris.
        // Jika ada baris dengan status 'pending' atau 'diproses' (nilai
        // enum lama yang tidak ada di 5 nilai baru), MySQL akan menolak
        // ALTER dengan error "Data truncated for column 'status'".
        // Sebelum menjalankan migrate, baris-baris tersebut harus
        // dimapping manual ke nilai baru oleh developer.
        $this->forge->modifyColumn('penjemputan', [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => [
                    'menunggu',
                    'disetujui',
                    'menunggu_pemberian_poin',
                    'selesai',
                    'ditolak',
                ],
                'default' => 'menunggu',
            ],
        ]);
    }

    public function down()
    {
        // Kembalikan ke enum awal (3 nilai) — akan GAGAL jika ada baris
        // dengan nilai baru yang tidak ada di 3 nilai awal.
        $this->forge->modifyColumn('penjemputan', [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => [
                    'pending',
                    'diproses',
                    'selesai',
                ],
                'default' => 'pending',
            ],
        ]);
    }
}