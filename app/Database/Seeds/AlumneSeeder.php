<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AlumneSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Obtener todos los cursos disponibles
        $cursos = $db->table('estructuras')->where('tipo', 'curso')->get()->getResultArray();
        $cursoIds = array_column($cursos, 'id');
        $cursoNombres = array_column($cursos, 'nombre');

        // Generar alumnos de ejemplo
        $alumnos = [];

        $nombres = ['Juan', 'María', 'Pedro', 'Lucía', 'Miguel', 'Laura', 'Carlos', 'Sofía', 'David', 'Elena'];
        $apellidos = ['García', 'Martínez', 'López', 'Sánchez', 'Pérez', 'Gómez', 'Ruiz', 'Fernández', 'Hernández', 'Jiménez'];

        // Crear 50 alumnos distribuidos aleatoriamente en cursos
        for ($i = 1; $i <= 50; $i++) {
            $nombre = $nombres[array_rand($nombres)];
            $apellido = $apellidos[array_rand($apellidos)];
            $dni = 'DNI' . str_pad($i, 3, '0', STR_PAD_LEFT); // Ejemplo simple
            $fecha_nacimiento = date('Y-m-d', strtotime('-' . rand(15, 25) . ' years'));
            $cursoIndex = array_rand($cursoIds);

            // Asignar un estado aleatorio para simular distintos casos
            // Prioriza algunos alumnos "Para validar" para el flujo de trabajo
            $estadosPosibles = ['Para validar', 'En revisión', 'Validado', 'Anulado'];
            $estado = $estadosPosibles[array_rand($estadosPosibles)];

            $alumnos[] = [
                'nombre' => $nombre,
                'apellidos' => $apellido,
                'dni' => $dni,
                'tsi' => null,
                'mutua' => null,
                'fecha_nacimiento' => $fecha_nacimiento,
                'lugar_nacimiento' => 'Ciudad ' . rand(1, 10),
                'direccion' => 'Calle Falsa ' . rand(1, 100),
                'municipio' => 'Municipio ' . rand(1, 10),
                'codigo_postal' => str_pad(rand(10000, 52999), 5, '0', STR_PAD_LEFT),
                'telefono_familiar' => '600' . rand(100000, 999999),
                'telefono_alumno' => '600' . rand(100000, 999999),
                'email_alumno' => strtolower($nombre . '.' . $apellido . $i . '@example.com'),
                'estructura_curso_id' => $cursoIds[$cursoIndex],
                'estado' => $estado,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        // Insertar alumnos
        $db->table('alumne')->insertBatch($alumnos);

        echo "Seeder de alumnos ejecutado: 50 alumnos insertados en cursos aleatorios.";
    }
}