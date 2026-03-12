<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Autentica implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. Validació bàsica: Està logat?
        if (!session()->get('loggedIn')) {
            return redirect()->to(base_url('userdemo/login'))->with('error', 'Si us plau, inicia sessió.');
        }

        // 2. Validació d'arguments (Noms específics)
        // Si la ruta té arguments, ex: filter:autentica:admin,profe
        if ($arguments !== null) {
            $userName = session()->get('name');
            
            // Si el nom de l'usuari no està a la llista d'arguments permesos
            if (!in_array($userName, $arguments)) {
                return redirect()->back()->with('error', 'Accés no permès per al teu usuari.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No cal fer res després
    }
}