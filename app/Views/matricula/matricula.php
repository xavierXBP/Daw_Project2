<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Matricula </title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #f4f6f9;
      font-family: "Segoe UI", Arial, sans-serif;
    }

    .main-card {
      max-width: 900px;
      border-radius: 12px;
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
    }

    .left-panel {
      background-color: #0d6efd;
      color: white;
      border-radius: 12px 0 0 12px;
    }

    .left-panel img {
      max-width: 180px;
    }

    .form-control {
      padding: 12px;
    }

    .btn-primary {
      font-weight: 600;
      padding: 12px;
    }

    .footer-text {
      font-size: 12px;
      color: #9aa4b2;
    }
  </style>
</head>

<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <a class="navbar-brand fw-semibold" href="https://www.iescaparrella.cat/" target="_blank">
        Instituto Educativo
      </a>
      
      <div class="collapse navbar-collapse justify-content-end">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="https://www.iescaparrella.cat/ca/educacio/iescaparrella/preinscripcions-i-matriculacions/171280.html" target="_blank">AYUDA ? </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 56px);">
    <div class="card main-card w-100">
      <div class="row g-0">

        <div class="col-md-5 left-panel d-flex flex-column justify-content-center align-items-center p-4 text-center">
          <img src="/img/logo.jpg" alt="Logo" class="mb-4">
          <h4 class="fw-semibold">Matricula Riquisitos </h4>
          <p class="mt-3">
          </p>
        </div>

        <div class="col-md-7 p-5">
          <h4 class="mb-4">Acceso para matricularse</h4>

          <form action="<?php  base_url('matricula')?>" method="post" >
          <?= csrf_field(); ?>
          
          <div class="mb-3">
            <label for="email" class="form-label">Tienes la foto del DNI 2 caras  </label>
            <input type="checkbox"  id="check1" name="check1" >
          </div>

          <div class="mb-4">
            <label for="code" class="form-label">tienes familia numerosa  </label>
            <input type="checkbox"  id="check2" name="check2" placeholder="Ingrese"><br>
          </div>

          <div class="mb-4">
            <label for="code" class="form-label">Tienes alguna discapacidad </label>
            <input type="checkbox"  id="check3" name="check3" placeholder="Ingrese"><br>
          </div>

          <div class="mb-4">
            <label for="code" class="form-label">Tienes ............ </label>
            <input type="checkbox"  id="check4" name="check4" placeholder="Ingrese"><br>
          </div>

          <div class="d-grid">
            <button class="btn btn-primary btn-lg" >Empezar Matriculacion  </button>
          </div>
          </form>
          <div class="footer-text mt-4">
            © 2026 · Instituto Educativo · Todos los derechos reservados
          </div>
        </div>

      </div>
    </div>
  </div>


</body>
</html>
