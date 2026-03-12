<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MatriculaMigration extends Migration
{
    public function up()
    {

        $this->forge->addField([

            'id_matricula'=>[
                'type'=>'INT',
                'constraint'=>11,
                'unsigned'=>true,
                'auto_increment'=>true
            ],

            'id_alumne'=>[
                'type'=>'INT',
                'unsigned'=>true
            ],

            'id_curs'=>[
                'type'=>'INT',
                'unsigned'=>true
            ],

            'estado'=>[
                'type'=>'VARCHAR',
                'constraint'=>50,
                'default'=>'pendiente'
            ],

            'pagado'=>[
                'type'=>'BOOLEAN',
                'default'=>false
            ],

            'created_at'=>[
                'type'=>'DATETIME',
                'null'=>true
            ]

        ]);

        $this->forge->addKey('id_matricula',true);

        $this->forge->createTable('matricula');
    }

    public function down()
    {
        $this->forge->dropTable('matricula');
    }
}