<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TandadaMigration extends Migration
{
       public function up()
    {
        $this->forge->addField([
            'id_tandada' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nom_tandada' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'fecha_inici' => [
                'type' => 'DATE',
            ],
            'fecha_fin' => [
                'type' => 'DATE',
            ],
            'estado' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_tandada', true);
        $this->forge->createTable('tandadas');
    }

    public function down()
    {
        $this->forge->dropTable('tandadas');
    }
}
