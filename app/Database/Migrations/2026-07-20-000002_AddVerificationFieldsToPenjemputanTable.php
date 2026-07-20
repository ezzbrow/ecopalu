<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVerificationFieldsToPenjemputanTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('penjemputan', [
            'verified_by' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'status',
            ],
            'verified_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'after'      => 'verified_by',
            ],
            'alasan_penolakan' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'verified_at',
            ],
            'confirmed_by' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'alasan_penolakan',
            ],
            'confirmed_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'after'      => 'confirmed_by',
            ],
            'coin_award' => [
                'type'       => 'INT',
                'null'       => true,
                'after'      => 'confirmed_at',
            ],
            'coin_awarded_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'after'      => 'coin_award',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('penjemputan', [
            'verified_by',
            'verified_at',
            'alasan_penolakan',
            'confirmed_by',
            'confirmed_at',
            'coin_award',
            'coin_awarded_at',
        ]);
    }
}