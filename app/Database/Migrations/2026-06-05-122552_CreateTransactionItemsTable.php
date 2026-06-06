<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransactionItemsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true
            ],
            'transaction_id' => [
                'type' => 'INT',
                'constraint' => 11
            ],
            'product_id' => [
                'type' => 'INT',
                'constraint' => 11
            ],
            'qty' => [
                'type' => 'INT',
                'constraint' => 11
            ],
            'price' => [
                'type' => 'INT',
                'constraint' => 11
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('transaction_items', true);
    }

    public function down()
    {
        $this->forge->dropTable('transaction_items', true);
    }
}