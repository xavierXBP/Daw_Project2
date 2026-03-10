<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMessagesTable extends Migration
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
            'codigo' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'unique'     => true,
            ],
            'titulo' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
            ],
            'mensaje' => [
                'type' => 'TEXT',
            ],
            'tipo' => [
                'type'       => 'ENUM',
                'constraint' => ['info', 'warning', 'error', 'success'],
                'default'    => 'info',
            ],
            'activo' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
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
        $this->forge->createTable('mensajes', true);
    }

    public function down()
    {
        $this->forge->dropTable('mensajes', true);
    }
}
