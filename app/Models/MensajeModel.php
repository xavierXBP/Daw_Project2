<?php

namespace App\Models;

use CodeIgniter\Model;

class MensajeModel extends Model
{
    protected $table = 'mensajes';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'codigo', 'titulo', 'mensaje', 'tipo', 'activo'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $returnType = 'array';

    /**
     * Get inactive messages
     */
    public function getInactiveMessages(): array
    {
        return $this->where('activo', 0)->findAll();
    }

    /**
     * Get active messages by type
     */
    public function getActiveMessages(string $tipo = null): array
    {
        $builder = $this->where('activo', 1);
        
        if ($tipo) {
            $builder->where('tipo', $tipo);
        }
        
        return $builder->findAll();
    }

    /**
     * Get message by code
     */
    public function getMessageByCode(string $codigo): ?array
    {
        return $this->where('codigo', $codigo)
                    ->where('activo', 1)
                    ->first();
    }

    /**
     * Get quick messages for validation
     */
    public function getQuickMessages(): array
    {
        return $this->select('mensaje')
                    ->where('activo', 1)
                    ->whereIn('tipo', ['warning', 'error'])
                    ->findAll();
    }

    /**
     * Initialize default messages
     */
    public function initializeDefaultMessages(): void
    {
        $defaultMessages = [
            [
                'codigo' => 'DOC_INCOMPLETA',
                'titulo' => 'Documentación incompleta',
                'mensaje' => 'Falta documentación obligatoria.',
                'tipo' => 'warning',
                'activo' => 1
            ],
            [
                'codigo' => 'DNI_INCORRECTO',
                'titulo' => 'DNI incorrecto',
                'mensaje' => 'El DNI no es válido.',
                'tipo' => 'error',
                'activo' => 1
            ],
            [
                'codigo' => 'DATOS_INCOMPLETOS',
                'titulo' => 'Datos incompletos',
                'mensaje' => 'Faltan datos personales.',
                'tipo' => 'warning',
                'activo' => 1
            ],
            [
                'codigo' => 'REVISAR_ADJUNTOS',
                'titulo' => 'Revisar adjuntos',
                'mensaje' => 'Los archivos están corruptos.',
                'tipo' => 'warning',
                'activo' => 1
            ],
            [
                'codigo' => 'MATRICULA_VALIDADA',
                'titulo' => 'Matrícula validada',
                'mensaje' => 'Matrícula correcta.',
                'tipo' => 'success',
                'activo' => 1
            ]
        ];

        foreach ($defaultMessages as $message) {
            $existing = $this->where('codigo', $message['codigo'])->first();
            if (!$existing) {
                $this->insert($message);
            }
        }
    }
}
