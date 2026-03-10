<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateValidationLocksTable extends Migration
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
            'alumno_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'usuario_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'usuario_nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'fecha_inicio' => [
                'type' => 'DATETIME',
            ],
            'fecha_expiracion' => [
                'type' => 'DATETIME',
            ],
            'activo' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('alumno_id');
        $this->forge->addKey('usuario_id');
        $this->forge->addKey('fecha_expiracion');
        $this->forge->addForeignKey('alumno_id', 'alumne', 'id_alumne', 'CASCADE', 'CASCADE');
        $this->forge->createTable('validation_locks', true);
    }

    public function down()
    {
        $this->forge->dropTable('validation_locks', true);
    }
}
