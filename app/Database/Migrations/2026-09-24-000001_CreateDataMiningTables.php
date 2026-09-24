<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDataMiningTables extends Migration
{
    public function up()
    {
        // 1. Tabel hasil_klaster (K-Means)
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
            'nama_nasabah' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'frekuensi' => [
                'type'    => 'INT',
                'default' => 0,
            ],
            'total_berat' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'total_poin' => [
                'type'    => 'INT',
                'default' => 0,
            ],
            'recency_hari' => [
                'type'    => 'INT',
                'default' => 0,
            ],
            'cluster_id' => [
                'type'    => 'INT',
                'default' => 0,
            ],
            'cluster_label' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'rekomendasi' => [
                'type' => 'TEXT',
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
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->createTable('hasil_klaster', true);

        // 2. Tabel hasil_asosiasi (Apriori)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'antecedents' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'consequents' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'support' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,4',
                'default'    => 0.0000,
            ],
            'confidence' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,4',
                'default'    => 0.0000,
            ],
            'lift' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,4',
                'default'    => 0.0000,
            ],
            'keterangan' => [
                'type' => 'TEXT',
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
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('hasil_asosiasi', true);
    }

    public function down()
    {
        $this->forge->dropTable('hasil_asosiasi', true);
        $this->forge->dropTable('hasil_klaster', true);
    }
}
