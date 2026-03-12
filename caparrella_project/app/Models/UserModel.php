<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\User;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = User::class; // Retorna objectes User
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['name', 'email', 'password'];
    protected $useTimestamps    = true;
}