<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Entities\User;
use App\Models\UserModel;

class UserSeeder extends Seeder
{
    public function run()
    {
        $model = new UserModel();

        for ($i = 1; $i <= 5; $i++) {
            $user = new User();
            $user->name     = "Usuari Demo $i";
            $user->email    = "user$i@example.com";
            $user->password = "1234"; // L'Entitat l'encriptarà automàticament

            $model->save($user);
        }
    }
}