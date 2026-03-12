<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
<title>Pago Matrícula</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

.stepper-wrapper{
display:flex;
justify-content:space-between;
position:relative;
margin-bottom:40px;
}

.stepper-wrapper::before{
content:"";
position:absolute;
top:22px;
left:0;
width:100%;
height:3px;
background:#e2e8f0;
z-index:0;
}

.step{
text-align:center;
z-index:1;
width:33%;
}

.step-circle{
width:45px;
height:45px;
border-radius:50%;
background:#cbd5e1;
color:white;
display:flex;
align-items:center;
justify-content:center;
margin:auto;
font-weight:bold;
}

.step.active .step-circle{
background:#0d6efd;
}

.step.completed .step-circle{
background:#16c172;
}

</style>

</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-primary">
<div class="container">
<span class="navbar-brand">Proceso de Matrícula</span>
</div>
</nav>

<div class="container py-5">

<div class="card shadow-lg border-0 rounded-4">

<div class="card-body p-5">

<!-- STEPPER -->

<div class="stepper-wrapper">

<div class="step completed">
<div class="step-circle">1</div>
<div>Datos Alumno</div>
</div>

<div class="step completed">
<div class="step-circle">2</div>
<div>Curso</div>
</div>

<div class="step active">
<div class="step-circle">3</div>
<div>Pago</div>
</div>

</div>

<h4 class="text-primary mb-4">Confirmación y pago</h4>

<!-- RESUMEN -->

<div class="card mb-4 border-0 bg-light">

<div class="card-body">

<h5 class="mb-3">Resumen de matrícula</h5>

<ul class="list-group">

<li class="list-group-item d-flex justify-content-between">
<span>Alumno</span>
<strong><?= $matricula->Nom_alumne ?></strong>
</li>

<li class="list-group-item d-flex justify-content-between">
<span>Email</span>
<strong><?= $matricula->correo_alumne ?></strong>
</li>

<li class="list-group-item d-flex justify-content-between">
<span>Curso</span>
<strong><?= $matricula->Nom_curs ?></strong>
</li>

<li class="list-group-item d-flex justify-content-between">
<span>Código curso</span>
<strong><?= $matricula->codigo_curs ?></strong>
</li>

<li class="list-group-item d-flex justify-content-between">
<span>Total a pagar</span>
<strong class="text-success fs-5"><?= $matricula->precio ?> €</strong>
</li>

</ul>

</div>

</div>

<!-- FORMULARIO PAGO -->

<form action="<?= base_url('matricula/pago/'.$id_matricula) ?>" method="post">

<?= csrf_field() ?>

<h5 class="mb-3">Datos de pago</h5>

<div class="row g-3 mb-3">

<div class="col-md-6">

<label class="form-label">Titular de la tarjeta</label>

<input type="text" class="form-control" name="titular" required>

</div>

<div class="col-md-6">

<label class="form-label">Número de tarjeta</label>

<input type="text" class="form-control" name="tarjeta" placeholder="1234 5678 9012 3456" required>

</div>

</div>

<div class="row g-3 mb-4">

<div class="col-md-4">

<label class="form-label">Caducidad</label>

<input type="text" class="form-control" name="caducidad" placeholder="MM/YY" required>

</div>

<div class="col-md-4">

<label class="form-label">CVV</label>

<input type="text" class="form-control" name="cvv" required>

</div>

<div class="col-md-4">

<label class="form-label">DNI titular</label>

<input type="text" class="form-control" name="dni_pago" required>

</div>

</div>

<div class="d-grid">

<button class="btn btn-success btn-lg">

Confirmar pago

</button>

</div>

</form>

</div>

</div>

</div>

</body>

</html>