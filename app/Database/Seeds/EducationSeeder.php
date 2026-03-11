<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EducationSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Limpiar tablas para evitar duplicados
        $db->table('optativas')->emptyTable();
        $db->table('asignaturas')->emptyTable();
        $db->table('estructuras')->emptyTable();
        $db->table('niveles')->emptyTable();

        // 1. Insertar niveles
        $niveles = [
            ['nombre' => 'ESO'],
            ['nombre' => 'Bachillerato'],
            ['nombre' => 'FP grado superior'],
            ['nombre' => 'FP grado medio'],
        ];
        $db->table('niveles')->insertBatch($niveles);
        $nivelesAll = $db->table('niveles')->get()->getResultArray();
        $nivelesMap = [];
        foreach ($nivelesAll as $n) {
            $nivelesMap[$n['nombre']] = $n['id'];
        }

        // 2. Insertar familias (tipo 'familia')
        $familias = [
            // ESO
            ['nombre' => 'ESO Científico-Tecnológico', 'tipo' => 'familia', 'nivel_id' => $nivelesMap['ESO'], 'parent_id' => null],
            ['nombre' => 'ESO Social', 'tipo' => 'familia', 'nivel_id' => $nivelesMap['ESO'], 'parent_id' => null],
            // Bachillerato
            ['nombre' => 'Bachillerato Científico', 'tipo' => 'familia', 'nivel_id' => $nivelesMap['Bachillerato'], 'parent_id' => null],
            ['nombre' => 'Bachillerato Social', 'tipo' => 'familia', 'nivel_id' => $nivelesMap['Bachillerato'], 'parent_id' => null],
            // FP grado superior
            ['nombre' => 'Informática', 'tipo' => 'familia', 'nivel_id' => $nivelesMap['FP grado superior'], 'parent_id' => null],
            ['nombre' => 'Estética', 'tipo' => 'familia', 'nivel_id' => $nivelesMap['FP grado superior'], 'parent_id' => null],
            // FP grado medio
            ['nombre' => 'Informática', 'tipo' => 'familia', 'nivel_id' => $nivelesMap['FP grado medio'], 'parent_id' => null],
        ];
        $db->table('estructuras')->insertBatch($familias);

        // Mapear familias
        $familiasAll = $db->table('estructuras')->where('tipo', 'familia')->get()->getResultArray();
        $familiasMap = [];
        foreach ($familiasAll as $f) {
            $familiasMap[$f['nombre'].'_'.$f['nivel_id']] = $f['id'];
        }

        // 3. Insertar cursos (tipo 'curso') por familia
        $cursos = [
            // ESO
            ['nombre' => '1º ESO', 'tipo' => 'curso', 'nivel_id' => $nivelesMap['ESO'], 'parent_id' => $familiasMap['ESO Científico-Tecnológico_'.$nivelesMap['ESO']]],
            ['nombre' => '2º ESO', 'tipo' => 'curso', 'nivel_id' => $nivelesMap['ESO'], 'parent_id' => $familiasMap['ESO Científico-Tecnológico_'.$nivelesMap['ESO']]],
            ['nombre' => '3º ESO', 'tipo' => 'curso', 'nivel_id' => $nivelesMap['ESO'], 'parent_id' => $familiasMap['ESO Científico-Tecnológico_'.$nivelesMap['ESO']]],
            ['nombre' => '4º ESO', 'tipo' => 'curso', 'nivel_id' => $nivelesMap['ESO'], 'parent_id' => $familiasMap['ESO Científico-Tecnológico_'.$nivelesMap['ESO']]],
            ['nombre' => '1º ESO Social', 'tipo' => 'curso', 'nivel_id' => $nivelesMap['ESO'], 'parent_id' => $familiasMap['ESO Social_'.$nivelesMap['ESO']]],
            ['nombre' => '2º ESO Social', 'tipo' => 'curso', 'nivel_id' => $nivelesMap['ESO'], 'parent_id' => $familiasMap['ESO Social_'.$nivelesMap['ESO']]],
            ['nombre' => '3º ESO Social', 'tipo' => 'curso', 'nivel_id' => $nivelesMap['ESO'], 'parent_id' => $familiasMap['ESO Social_'.$nivelesMap['ESO']]],
            ['nombre' => '4º ESO Social', 'tipo' => 'curso', 'nivel_id' => $nivelesMap['ESO'], 'parent_id' => $familiasMap['ESO Social_'.$nivelesMap['ESO']]],
            // Bachillerato
            ['nombre' => '1º Científico', 'tipo' => 'curso', 'nivel_id' => $nivelesMap['Bachillerato'], 'parent_id' => $familiasMap['Bachillerato Científico_'.$nivelesMap['Bachillerato']]],
            ['nombre' => '2º Científico', 'tipo' => 'curso', 'nivel_id' => $nivelesMap['Bachillerato'], 'parent_id' => $familiasMap['Bachillerato Científico_'.$nivelesMap['Bachillerato']]],
            ['nombre' => '1º Social', 'tipo' => 'curso', 'nivel_id' => $nivelesMap['Bachillerato'], 'parent_id' => $familiasMap['Bachillerato Social_'.$nivelesMap['Bachillerato']]],
            ['nombre' => '2º Social', 'tipo' => 'curso', 'nivel_id' => $nivelesMap['Bachillerato'], 'parent_id' => $familiasMap['Bachillerato Social_'.$nivelesMap['Bachillerato']]],
            // FP grado superior
            ['nombre' => 'DAW1', 'tipo' => 'curso', 'nivel_id' => $nivelesMap['FP grado superior'], 'parent_id' => $familiasMap['Informática_'.$nivelesMap['FP grado superior']]],
            ['nombre' => 'DAW2', 'tipo' => 'curso', 'nivel_id' => $nivelesMap['FP grado superior'], 'parent_id' => $familiasMap['Informática_'.$nivelesMap['FP grado superior']]],
            ['nombre' => 'Estética1', 'tipo' => 'curso', 'nivel_id' => $nivelesMap['FP grado superior'], 'parent_id' => $familiasMap['Estética_'.$nivelesMap['FP grado superior']]],
            ['nombre' => 'Estética2', 'tipo' => 'curso', 'nivel_id' => $nivelesMap['FP grado superior'], 'parent_id' => $familiasMap['Estética_'.$nivelesMap['FP grado superior']]],
            // FP grado medio
            ['nombre' => 'Asix1', 'tipo' => 'curso', 'nivel_id' => $nivelesMap['FP grado medio'], 'parent_id' => $familiasMap['Informática_'.$nivelesMap['FP grado medio']]],
            ['nombre' => 'Asix2', 'tipo' => 'curso', 'nivel_id' => $nivelesMap['FP grado medio'], 'parent_id' => $familiasMap['Informática_'.$nivelesMap['FP grado medio']]],
        ];
        $db->table('estructuras')->insertBatch($cursos);

        // Mapear cursos
        $cursosAll = $db->table('estructuras')->where('tipo', 'curso')->get()->getResultArray();
        $cursosMap = [];
        foreach ($cursosAll as $c) {
            $cursosMap[$c['nombre']] = $c['id'];
        }

        // 4. Insertar asignaturas
        $asignaturas = [
            // ESO
            ['nombre' => 'Matemáticas 1', 'horas_semanales' => 5, 'estructura_id' => $cursosMap['1º ESO']],
            ['nombre' => 'Lengua', 'horas_semanales' => 4, 'estructura_id' => $cursosMap['1º ESO']],
            ['nombre' => 'Inglés', 'horas_semanales' => 3, 'estructura_id' => $cursosMap['1º ESO']],
            ['nombre' => 'Educación Física', 'horas_semanales' => 2, 'estructura_id' => $cursosMap['1º ESO']],
            ['nombre' => 'Català', 'horas_semanales' => 2, 'estructura_id' => $cursosMap['1º ESO']],

            ['nombre' => 'Matemáticas 2', 'horas_semanales' => 5, 'estructura_id' => $cursosMap['2º ESO']],
            ['nombre' => 'Lengua 2', 'horas_semanales' => 4, 'estructura_id' => $cursosMap['2º ESO']],
            ['nombre' => 'Inglés', 'horas_semanales' => 3, 'estructura_id' => $cursosMap['2º ESO']],
            ['nombre' => 'Educación Física', 'horas_semanales' => 2, 'estructura_id' => $cursosMap['2º ESO']],
            ['nombre' => 'Català 2', 'horas_semanales' => 2, 'estructura_id' => $cursosMap['2º ESO']],
            ['nombre' => 'Geografía', 'horas_semanales' => 3, 'estructura_id' => $cursosMap['2º ESO']],

            ['nombre' => 'Matemáticas 3', 'horas_semanales' => 5, 'estructura_id' => $cursosMap['3º ESO']],
            ['nombre' => 'Lengua 3', 'horas_semanales' => 4, 'estructura_id' => $cursosMap['3º ESO']],
            ['nombre' => 'Inglés Avanzado', 'horas_semanales' => 3, 'estructura_id' => $cursosMap['3º ESO']],
            ['nombre' => 'Educación Física', 'horas_semanales' => 2, 'estructura_id' => $cursosMap['3º ESO']],
            ['nombre' => 'Català 3', 'horas_semanales' => 2, 'estructura_id' => $cursosMap['3º ESO']],
            ['nombre' => 'Física y Química', 'horas_semanales' => 4, 'estructura_id' => $cursosMap['3º ESO']],

            ['nombre' => 'Matemáticas 3', 'horas_semanales' => 5, 'estructura_id' => $cursosMap['4º ESO']],
            ['nombre' => 'Lengua 3', 'horas_semanales' => 4, 'estructura_id' => $cursosMap['4º ESO']],
            ['nombre' => 'Inglés Avanzado', 'horas_semanales' => 3, 'estructura_id' => $cursosMap['4º ESO']],
            ['nombre' => 'Educación Física', 'horas_semanales' => 2, 'estructura_id' => $cursosMap['4º ESO']],
            ['nombre' => 'Català 3', 'horas_semanales' => 2, 'estructura_id' => $cursosMap['4º ESO']],
            ['nombre' => 'Física y Química', 'horas_semanales' => 4, 'estructura_id' => $cursosMap['4º ESO']],

            // Bachillerato Científico
            ['nombre' => 'Lengua', 'horas_semanales' => 4, 'estructura_id' => $cursosMap['1º Científico']],
            ['nombre' => 'Matemáticas', 'horas_semanales' => 5, 'estructura_id' => $cursosMap['1º Científico']],
            ['nombre' => 'Física', 'horas_semanales' => 4, 'estructura_id' => $cursosMap['1º Científico']],

            ['nombre' => 'Lengua 2', 'horas_semanales' => 4, 'estructura_id' => $cursosMap['2º Científico']],
            ['nombre' => 'Matemáticas 2', 'horas_semanales' => 5, 'estructura_id' => $cursosMap['2º Científico']],
            ['nombre' => 'Física 2', 'horas_semanales' => 4, 'estructura_id' => $cursosMap['2º Científico']],

            // Bachillerato Social
            ['nombre' => 'Lengua', 'horas_semanales' => 4, 'estructura_id' => $cursosMap['1º Social']],
            ['nombre' => 'Catalán', 'horas_semanales' => 3, 'estructura_id' => $cursosMap['1º Social']],
            ['nombre' => 'Inglés', 'horas_semanales' => 3, 'estructura_id' => $cursosMap['1º Social']],

            ['nombre' => 'Lengua 2', 'horas_semanales' => 4, 'estructura_id' => $cursosMap['2º Social']],
            ['nombre' => 'Catalán 2', 'horas_semanales' => 3, 'estructura_id' => $cursosMap['2º Social']],
            ['nombre' => 'Inglés 2', 'horas_semanales' => 3, 'estructura_id' => $cursosMap['2º Social']],

            // FP grado superior - Informática
            ['nombre' => 'Java', 'horas_semanales' => 5, 'estructura_id' => $cursosMap['DAW1']],
            ['nombre' => 'SQL', 'horas_semanales' => 4, 'estructura_id' => $cursosMap['DAW1']],
            ['nombre' => 'PHP', 'horas_semanales' => 5, 'estructura_id' => $cursosMap['DAW2']],
            ['nombre' => 'JavaScript', 'horas_semanales' => 4, 'estructura_id' => $cursosMap['DAW2']],

            // FP grado medio - Informática
            ['nombre' => 'Java', 'horas_semanales' => 5, 'estructura_id' => $cursosMap['Asix1']],
            ['nombre' => 'SQL', 'horas_semanales' => 4, 'estructura_id' => $cursosMap['Asix1']],
            ['nombre' => 'Routers', 'horas_semanales' => 3, 'estructura_id' => $cursosMap['Asix2']],
            ['nombre' => 'Cables', 'horas_semanales' => 3, 'estructura_id' => $cursosMap['Asix2']],
        ];

        $db->table('asignaturas')->insertBatch($asignaturas);

        echo "Seeder completo ejecutado con ESO, Bachillerato, FP grado superior y FP grado medio.";
    }
}