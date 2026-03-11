<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddHorasToOptativas extends Migration
{
    public function up()
    {
        $this->forge->addColumn('optativas', [
            'horas_semanales' => [
                'type'       => 'INT',
                'constraint' => 2,
                'default'    => 0,
                'after'      => 'nombre'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('optativas', 'horas_semanales');
    }
}
