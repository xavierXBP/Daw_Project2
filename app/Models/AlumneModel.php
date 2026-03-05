<?php

namespace App\Models;

use CodeIgniter\Model;

class AlumneModel extends Model
{
    protected $table = 'alumne';
    protected $primaryKey = 'id_alumne';
    protected $allowedFields = [
        'nombre',
        'apellidos',
        'dni',
        'tsi',
        'mutua',
        'fecha_nacimiento',
        'lugar_nacimiento',
        'direccion',
        'municipio',
        'codigo_postal',
        'telefono_familiar',
        'telefono_alumno',
        'email_alumno',
        'estructura_curso_id',
        'estado',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}