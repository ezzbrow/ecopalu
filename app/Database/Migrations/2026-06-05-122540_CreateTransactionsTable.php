<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransaksiCoinTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'kategori_sampah_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'berat' => [
                'type' => 'FLOAT',
            ],
            'total_coin' => [
                'type' => 'INT',
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

        $this->forge->addForeignKey(
            'user_id',
            'users',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->addForeignKey(
            'kategori_sampah_id',
            'kategori_sampah',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('transaksi_coin');
    }

    public function down()
    {
        $this->forge->dropTable('transaksi_coin');
    }
}