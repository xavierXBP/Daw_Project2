<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOptativasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nombre' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'precio' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],
            'estructura_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
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
        $this->forge->addForeignKey('estructura_id', 'estructuras', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('optativas');
    }

    public function down()
    {
        $this->forge->dropTable('optativas');
    }
}
