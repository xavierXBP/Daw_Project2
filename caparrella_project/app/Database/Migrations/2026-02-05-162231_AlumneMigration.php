<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlumneMigration extends Migration
{
     public function up()
    {
        $this->forge->addField([
            'id_alumne' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'Nom_alumne' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'Dni_alumne' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'unique'     => true,
            ],
            'Pass_login' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'correo_alumne' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],'tsi' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],

            'poblacio' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],

            'data_naixement' => [
                'type' => 'DATE',
            ],

            'domicili' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],

            'tlf_familiar' => [
                'type' => 'VARCHAR',
                'constraint' => 15,
            ],

            'municipi' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],

            'codi_postal' => [
                'type' => 'VARCHAR',
                'constraint' => 5,
            ],

            'tlf_alumne' => [
                'type' => 'VARCHAR',
                'constraint' => 15,
                'null' => true,
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
        $this->forge->createTable('alumne');
    }

    public function down()
    {
        $this->forge->dropTable('alumne');
    }
}
