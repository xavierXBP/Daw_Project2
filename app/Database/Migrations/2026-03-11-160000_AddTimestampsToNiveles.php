<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTimestampsToNiveles extends Migration
{
    public function up()
    {
        $this->forge->addColumn('niveles', [
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'nombre'
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'created_at'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('niveles', 'created_at');
        $this->forge->dropColumn('niveles', 'updated_at');
    }
}
