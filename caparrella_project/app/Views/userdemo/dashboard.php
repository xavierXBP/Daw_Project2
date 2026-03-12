<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Àrea Privada</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        
        /* Barra superior */
        .navbar {
            background-color: #333;
            color: #fff;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar h3 { margin: 0; }
        .user-info { font-size: 0.9em; }
        
        /* Contingut principal */
        .container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        /* Botó de logout */
        .btn-logout {
            background-color: #dc3545; /* Vermell per indicar acció de sortida */
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 4px;
            font-size: 0.9em;
            transition: background 0.3s;
        }
        .btn-logout:hover { background-color: #c82333; }
    </style>
</head>
<body>

    <div class="navbar">
        <h3>El Meu Lloc Web</h3>
        <div class="user-info">
            Hola, <strong><?= session()->get('name') ?></strong> 
            &nbsp; | &nbsp; 
            <a href="<?= base_url('userdemo/logout') ?>" class="btn-logout">Tancar Sessió</a>
        </div>
    </div>

    <div class="container">
        <h1>Benvingut, <?= session()->get('name') ?>!</h1>
        <hr>
        
        <p>Has accedit correctament a l'àrea restringida.</p>
        
        <p>
            El teu email registrat és: 
            <code><?= session()->get('email') ?></code>
        </p>

        <div style="margin-top: 30px; padding: 15px; background-color: #e9ecef; border-left: 5px solid #007bff;">
            <strong>Nota tècnica:</strong> 
            Aquesta pàgina està protegida pel filtre <code>AuthFilter</code> (o <code>Autentica</code>). 
            Si intentes accedir directament a la URL sense sessió, seràs redirigit al login.
        </div>
    </div>

</body>
</html>