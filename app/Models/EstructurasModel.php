<?php

namespace App\Models;

use CodeIgniter\Model;

class EstructurasModel extends Model
{
    protected $table = 'estructuras';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nombre','tipo','nivel_id','parent_id'];
}