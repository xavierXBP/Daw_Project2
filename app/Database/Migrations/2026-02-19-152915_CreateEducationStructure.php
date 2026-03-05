<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEducationStructure extends Migration
{
    public function up()
    {
        // ------------------------------
        // 1. Tabla niveles
        // ------------------------------
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('niveles', true);

        // ------------------------------
        // 2. Tabla estructuras
        // ------------------------------
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
            ],
            'tipo' => [
                'type'       => 'ENUM',
                'constraint' => ['grado', 'familia', 'curso'],
            ],
            'nivel_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'parent_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('nivel_id');
        $this->forge->addKey('parent_id');

        $this->forge->addForeignKey('nivel_id', 'niveles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('parent_id', 'estructuras', 'id', 'CASCADE', 'SET NULL');

        $this->forge->createTable('estructuras', true);

        // ------------------------------
        // 3. Tabla asignaturas
        // ------------------------------
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'horas_semanales' => [
                'type'       => 'INT',
                'constraint' => 2,
                'default'    => 0,
            ],
            'estructura_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('estructura_id');
        $this->forge->addForeignKey('estructura_id', 'estructuras', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('asignaturas', true);
    }

    public function down()
    {
        $this->forge->dropTable('asignaturas', true);
        $this->forge->dropTable('estructuras', true);
        $this->forge->dropTable('niveles', true);
    }
}