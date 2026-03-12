<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class OptativaMigration extends Migration
{
     public function up()
    {
        $this->forge->addField([
            'id_optativa' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nom_opt' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'codigo_opt' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'unique'     => true,
            ],
            'precio_opt' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
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

        $this->forge->addKey('id_optativa', true);
        $this->forge->createTable('optativa');
    }

    public function down()
    {
        $this->forge->dropTable('optativa');
    }
}
