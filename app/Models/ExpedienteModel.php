<?php

namespace App\Models;

use CodeIgniter\Model;

class ExpedienteModel extends Model
{
    protected $table = 'expedientes';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'numero_expediente', 'alumno_id', 'fecha_generacion', 
        'ruta_pdf', 'any_academico', 'estado'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $returnType = 'array';

    /**
     * Generate next expediente number
     */
    public function generateNextNumber(): string
    {
        $lastExpediente = $this->orderBy('id', 'DESC')->first();
        
        if ($lastExpediente) {
            $lastNumber = (int) $lastExpediente['numero_expediente'];
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Create expediente for validated student
     */
    public function createExpediente(int $alumnoId, string $anyAcademico = '2025-2026'): array
    {
        $numeroExpediente = $this->generateNextNumber();
        
        $expedienteData = [
            'numero_expediente' => $numeroExpediente,
            'alumno_id' => $alumnoId,
            'fecha_generacion' => date('Y-m-d H:i:s'),
            'any_academico' => $anyAcademico,
            'estado' => 'generado'
        ];

        $expedienteId = $this->insert($expedienteData);
        
        return $this->find($expedienteId);
    }

    /**
     * Get expediente by student ID
     */
    public function getExpedienteByAlumno(int $alumnoId): ?array
    {
        return $this->where('alumno_id', $alumnoId)
                   ->where('estado', 'generado')
                   ->first();
    }

    /**
     * Update PDF path
     */
    public function updatePdfPath(int $expedienteId, string $pdfPath): bool
    {
        return $this->update($expedienteId, ['ruta_pdf' => $pdfPath]);
    }

    /**
     * Get expediente with student data
     */
    public function getExpedienteWithStudent(int $expedienteId): ?array
    {
        return $this->select('expedientes.*, alumne.*')
                   ->join('alumne', 'alumne.id_alumne = expedientes.alumno_id')
                   ->where('expedientes.id', $expedienteId)
                   ->first();
    }
}
