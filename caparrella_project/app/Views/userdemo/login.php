<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Login d'Usuari</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        form { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; width: 300px; }
        label { display: block; margin-top: 10px; }
        button { margin-top: 15px; cursor: pointer; }
    </style>
</head>
<body>

    <h2>Accés Privat</h2>

    <?php if (session()->getFlashdata('msg')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('msg') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-error">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('userdemo/auth') ?>" method="post">
        <?= csrf_field() ?>
        
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?= old('email') ?>" required>
        
        <label for="password">Contrasenya:</label>
        <input type="password" name="password" id="password" required>
        
        <button type="submit">Entrar</button>
    </form>

</body>
</html>