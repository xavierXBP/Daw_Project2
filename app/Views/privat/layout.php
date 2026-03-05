<?php // Layout con nav lateral persistente ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Panel Privado</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { min-height:100vh; }
    #sidebar { width:250px; }
    .nav-link.active { background-color:#e9ecef; border-radius:6px; }
  </style>
</head>
<body>
  <div class="d-flex">
    <nav id="sidebar" class="bg-light border-end vh-100 p-3">
      <div class="mb-4 text-center">
        <img src="/img/logo.jpg" alt="Logo" style="max-width:140px;" />
      </div>
      <ul class="nav nav-pills flex-column">
        <li class="nav-item mb-1"><a class="nav-link" href="<?= base_url('privat/education') ?>">Estructuras</a></li>
        <li class="nav-item mb-1"><a class="nav-link" href="<?= base_url('privat/historial') ?>">Historial</a></li>
        <li class="nav-item mb-1"><a class="nav-link" href="<?= base_url('privat/expedientes') ?>">Expedientes</a></li>
        <li class="nav-item mb-1"><a class="nav-link" href="<?= base_url('privat/validados') ?>">Validados</a></li>
        <li class="nav-item mb-1"><a class="nav-link" href="<?= base_url('privat/mensatges') ?>">Mensatges</a></li>

    </ul>
       
    </nav>

    <main class="flex-grow-1 p-4">
      <?= $this->renderSection('content') ?>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
