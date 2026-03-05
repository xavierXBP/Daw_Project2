<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CursMigration extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_curs' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'Nom_curs' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'codigo_curs' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'unique'     => true,
            ],
            'precio' => [
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

        $this->forge->addKey('id_curs', true);
        $this->forge->createTable('curs');
    }

    public function down()
    {
        $this->forge->dropTable('curs');
    }
}
