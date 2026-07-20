<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'recipient_user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'recipient_role' => [
                'type'       => 'ENUM',
                'constraint' => ['user', 'admin', 'banksampah'],
            ],
            'judul' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'pesan' => [
                'type' => 'TEXT',
            ],
            'tipe' => [
                'type'       => 'ENUM',
                'constraint' => ['penjemputan', 'pencairan', 'sistem', 'poin'],
                'default'    => 'sistem',
            ],
            'ref_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'is_read' => [
                'type'    => 'TINYINT',
                'default' => 0,
            ],
            'read_at' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->addKey(['recipient_user_id', 'is_read']);

        $this->forge->addForeignKey(
            'recipient_user_id',
            'users',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('notifications');
    }

    public function down()
    {
        $this->forge->dropTable('notifications');
    }
}