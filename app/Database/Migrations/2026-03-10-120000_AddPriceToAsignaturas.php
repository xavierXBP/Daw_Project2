<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPriceToAsignaturas extends Migration
{
    public function up()
    {
        $this->forge->addColumn('asignaturas', [
            'precio' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
                'null'       => false,
                'after'      => 'horas_semanales'
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'precio'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('asignaturas', 'precio');
        $this->forge->dropColumn('asignaturas', 'updated_at');
    }
}
