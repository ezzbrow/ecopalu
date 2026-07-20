<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToPenjemputanTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('penjemputan', [
            'kategori_sampah_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'user_id',
            ],
            'berat' => [
                'type'       => 'FLOAT',
                'null'       => true,
                'after'      => 'kategori_sampah_id',
            ],
            'latitude' => [
                'type'       => 'DECIMAL',
                'constraint' => [10, 7],
                'null'       => true,
                'after'      => 'alamat',
            ],
            'longitude' => [
                'type'       => 'DECIMAL',
                'constraint' => [10, 7],
                'null'       => true,
                'after'      => 'latitude',
            ],
        ]);

        $this->forge->addForeignKey(
            'kategori_sampah_id',
            'kategori_sampah',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->forge->dropForeignKey('penjemputan', 'penjemputan_kategori_sampah_id_foreign');

        $this->forge->dropColumn('penjemputan', [
            'kategori_sampah_id',
            'berat',
            'latitude',
            'longitude',
        ]);
    }
}