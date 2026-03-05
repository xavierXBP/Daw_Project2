<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEducationSystem extends Migration
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

        // ------------------------------
        // 4. Tabla alumne
        // ------------------------------
        $this->forge->addField([
            'id_alumne' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'apellidos' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'dni' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'unique'     => true,
            ],
            'tsi' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'mutua' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'fecha_nacimiento' => [
                'type' => 'DATE',
            ],
            'lugar_nacimiento' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
            ],
            'direccion' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'municipio' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
            ],
            'codigo_postal' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            'telefono_familiar' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'telefono_alumno' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'email_alumno' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'estructura_curso_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            // Estado de la matrícula del alumno (pendiente, validado, anulado, etc.)
            'estado' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'En revisión',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_alumne', true);
        $this->forge->addKey('estructura_curso_id');
        $this->forge->addForeignKey(
            'estructura_curso_id',
            'estructuras',
            'id',
            'RESTRICT',
            'CASCADE'
        );
        $this->forge->createTable('alumne', true);
    }

    public function down()
    {
        $this->forge->dropTable('alumne', true);
        $this->forge->dropTable('asignaturas', true);
        $this->forge->dropTable('estructuras', true);
        $this->forge->dropTable('niveles', true);
    }
}