<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class OptativaSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Obtener los cursos existentes para asociar las optativas correctamente
        $cursos = $db->table('estructuras')
            ->where('tipo', 'curso')
            ->get()
            ->getResultArray();
        
        $cursosMap = [];
        foreach ($cursos as $curso) {
            $cursosMap[$curso['nombre']] = $curso['id'];
        }
        
        // Si no hay cursos, no podemos crear optativas
        if (empty($cursosMap)) {
            echo "No hay cursos disponibles para crear optativas. Ejecute primero EducationSeeder.\n";
            return;
        }

        $data = [
            [
                'nombre' => 'Informática (op)',
                'precio' => 50.00,
                'horas_semanales' => 0,
                'estructura_id' => $cursosMap['1º ESO'] ?? $cursosMap[array_key_first($cursosMap)],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nombre' => 'Música (op)',
                'precio' => 45.00,
                'horas_semanales' => 0,
                'estructura_id' => $cursosMap['2º ESO'] ?? $cursosMap[array_key_first($cursosMap)],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nombre' => 'Arte Plástico (op)',
                'precio' => 40.00,
                'horas_semanales' => 0,
                'estructura_id' => $cursosMap['3º ESO'] ?? $cursosMap[array_key_first($cursosMap)],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nombre' => 'Segunda Lengua Extranjera (op)',
                'precio' => 60.00,
                'horas_semanales' => 0,
                'estructura_id' => $cursosMap['4º ESO'] ?? $cursosMap[array_key_first($cursosMap)],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nombre' => 'Tecnología Industrial (op)',
                'precio' => 55.00,
                'horas_semanales' => 0,
                'estructura_id' => $cursosMap['1º Científico'] ?? $cursosMap[array_key_first($cursosMap)],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nombre' => 'Educación Física (op)',
                'precio' => 35.00,
                'horas_semanales' => 0,
                'estructura_id' => $cursosMap['2º Científico'] ?? $cursosMap[array_key_first($cursosMap)],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('optativas')->insertBatch($data);
    }
}
