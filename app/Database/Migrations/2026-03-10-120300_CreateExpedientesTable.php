<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExpedientesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'numero_expediente' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'unique'     => true,
            ],
            'alumno_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'fecha_generacion' => [
                'type' => 'DATETIME',
            ],
            'ruta_pdf' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
            ],
            'any_academico' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => '2025-2026',
            ],
            'estado' => [
                'type'       => 'ENUM',
                'constraint' => ['generado', 'error', 'anulado'],
                'default'    => 'generado',
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
        $this->forge->addKey('alumno_id');
        $this->forge->addForeignKey('alumno_id', 'alumne', 'id_alumne', 'CASCADE', 'CASCADE');
        $this->forge->createTable('expedientes', true);
    }

    public function down()
    {
        $this->forge->dropTable('expedientes', true);
    }
}
