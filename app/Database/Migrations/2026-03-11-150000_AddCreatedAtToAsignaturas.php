<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCreatedAtToAsignaturas extends Migration
{
    public function up()
    {
        $this->forge->addColumn('asignaturas', [
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'estructura_id'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('asignaturas', 'created_at');
    }
}
