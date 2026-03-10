<?php

namespace App\Models;

use CodeIgniter\Model;

class ValidationLockModel extends Model
{
    protected $table = 'validation_locks';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'alumno_id', 'usuario_id', 'usuario_nombre', 
        'fecha_inicio', 'fecha_expiracion', 'activo'
    ];
    protected $useTimestamps = false;
    protected $returnType = 'array';

    /**
     * Lock a student for validation
     */
    public function lockStudent(int $alumnoId, int $usuarioId = null, string $usuarioNombre = null): bool
    {
        // Clean expired locks first
        $this->cleanExpiredLocks();

        // Check if already locked
        $existingLock = $this->getActiveLock($alumnoId);
        if ($existingLock) {
            return false; // Already locked by someone
        }

        $lockData = [
            'alumno_id' => $alumnoId,
            'usuario_id' => $usuarioId,
            'usuario_nombre' => $usuarioNombre ?? 'Usuario',
            'fecha_inicio' => date('Y-m-d H:i:s'),
            'fecha_expiracion' => date('Y-m-d H:i:s', strtotime('+30 minutes')),
            'activo' => 1
        ];

        return $this->insert($lockData) !== false;
    }

    /**
     * Unlock a student
     */
    public function unlockStudent(int $alumnoId): bool
    {
        return $this->where('alumno_id', $alumnoId)
                   ->where('activo', 1)
                   ->set(['activo' => 0])
                   ->update();
    }

    /**
     * Get active lock for a student
     */
    public function getActiveLock(int $alumnoId): ?array
    {
        $this->cleanExpiredLocks();
        
        return $this->where('alumno_id', $alumnoId)
                   ->where('activo', 1)
                   ->first();
    }

    /**
     * Get all active locks
     */
    public function getActiveLocks(): array
    {
        $this->cleanExpiredLocks();
        
        return $this->select('validation_locks.*, alumne.nombre, alumne.apellidos, alumne.dni')
                   ->join('alumne', 'alumne.id_alumne = validation_locks.alumno_id')
                   ->where('validation_locks.activo', 1)
                   ->findAll();
    }

    /**
     * Check if student is locked
     */
    public function isStudentLocked(int $alumnoId): bool
    {
        return $this->getActiveLock($alumnoId) !== null;
    }

    /**
     * Clean expired locks
     */
    public function cleanExpiredLocks(): void
    {
        $this->where('fecha_expiracion <', date('Y-m-d H:i:s'))
             ->where('activo', 1)
             ->set(['activo' => 0])
             ->update();
    }

    /**
     * Extend lock duration
     */
    public function extendLock(int $alumnoId): bool
    {
        return $this->where('alumno_id', $alumnoId)
                   ->where('activo', 1)
                   ->set([
                       'fecha_expiracion' => date('Y-m-d H:i:s', strtotime('+30 minutes'))
                   ])
                   ->update();
    }
}
