<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class UserDemo extends BaseController
{
    // Mostra el formulari
    public function login()
    {
        if (session()->get('loggedIn')) {
            return redirect()->to('/userdemo/dashboard');
        }
        return view('userdemo/login');
    }

    // Processa el POST
    public function auth()
    {
        $email = $this->request->getPost('email');
        $pass  = $this->request->getPost('password');

        $model = new UserModel();
        
        // Busquem l'usuari (retorna una Entitat User)
        $user = $model->where('email', $email)->first();

        if ($user) {
            // Verifiquem la contrasenya usant el mètode de l'Entitat
            if ($user->verifyPassword($pass)) {
                
                // Creem la sessió
                session()->set([
                    'id'       => $user->id,
                    'name'     => $user->name,
                    'email'    => $user->email,
                    'loggedIn' => true,
                ]);

                return redirect()->to('/userdemo/dashboard');
            }
        }

        return redirect()->back()->withInput()->with('error', 'Credencials incorrectes');
    }

    public function dashboard()
    {
        return view('userdemo/dashboard');
    }

    public function logout()
    {
        // 1. Destruïm la sessió actual
        session()->destroy();

        // 2. Redirigim al login enviant un missatge Flash
        // El primer paràmetre 'msg' és la clau, el segon el text.
        return redirect()->to('/userdemo/login')
                         ->with('msg', 'Has tancat la sessió correctament. Fins aviat!');
    }
}