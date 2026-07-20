<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFinalizedByToPenjemputanTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('penjemputan', [
            'finalized_by' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'after'    => 'coin_awarded_at',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('penjemputan', 'finalized_by');
    }
}