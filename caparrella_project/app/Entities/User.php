<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class User extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at'];
    protected $casts   = [];

    // Mutator automàtic: Quan assignem $user->password = 'secret', s'encripta sol.
    public function setPassword(string $pass)
    {
        $this->attributes['password'] = password_hash($pass, PASSWORD_DEFAULT);
        return $this;
    }
    
    // Helper per verificar contrasenyes
    public function verifyPassword(string $inputPassword): bool
    {
        return password_verify($inputPassword, $this->attributes['password']);
    }
}