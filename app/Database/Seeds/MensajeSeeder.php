<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MensajeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'codigo' => 'DOC_INCOMPLETA',
                'titulo' => 'Documentación incompleta',
                'mensaje' => 'Falta documentación obligatoria.',
                'tipo' => 'warning',
                'activo' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'codigo' => 'DNI_INCORRECTO',
                'titulo' => 'DNI incorrecto',
                'mensaje' => 'El DNI no es válido.',
                'tipo' => 'error',
                'activo' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'codigo' => 'DATOS_INCOMPLETOS',
                'titulo' => 'Datos incompletos',
                'mensaje' => 'Faltan datos personales.',
                'tipo' => 'warning',
                'activo' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'codigo' => 'REVISAR_ADJUNTOS',
                'titulo' => 'Revisar adjuntos',
                'mensaje' => 'Los archivos están corruptos.',
                'tipo' => 'warning',
                'activo' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'codigo' => 'MATRICULA_VALIDADA',
                'titulo' => 'Matrícula validada',
                'mensaje' => 'Matrícula correcta.',
                'tipo' => 'success',
                'activo' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'codigo' => 'PAGO_PENDIENTE',
                'titulo' => 'Pago pendiente',
                'mensaje' => 'El pago de la matrícula está pendiente.',
                'tipo' => 'warning',
                'activo' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'codigo' => 'PLAZO_FINALIZADO',
                'titulo' => 'Plazo finalizado',
                'mensaje' => 'El plazo de matriculación ha finalizado.',
                'tipo' => 'error',
                'activo' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('mensajes')->insertBatch($data);
    }
}
