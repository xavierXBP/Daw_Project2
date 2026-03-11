<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class OptativaSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nombre' => 'Informática',
                'precio' => 50.00,
                'estructura_id' => 1, // Ajustar según el ID del curso correspondiente
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nombre' => 'Música',
                'precio' => 45.00,
                'estructura_id' => 1, // Ajustar según el ID del curso correspondiente
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nombre' => 'Arte Plástico',
                'precio' => 40.00,
                'estructura_id' => 2, // Ajustar según el ID del curso correspondiente
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nombre' => 'Segunda Lengua Extranjera',
                'precio' => 60.00,
                'estructura_id' => 2, // Ajustar según el ID del curso correspondiente
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nombre' => 'Tecnología Industrial',
                'precio' => 55.00,
                'estructura_id' => 3, // Ajustar según el ID del curso correspondiente
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nombre' => 'Educación Física',
                'precio' => 35.00,
                'estructura_id' => 3, // Ajustar según el ID del curso correspondiente
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('optativas')->insertBatch($data);
    }
}
