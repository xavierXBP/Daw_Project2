<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Matricula Estudis</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

.stepper-wrapper{
display:flex;
justify-content:space-between;
position:relative;
}

.stepper-wrapper::before{
content:"";
position:absolute;
top:22px;
left:0;
width:100%;
height:3px;
background:#e2e8f0;
}

.step{
text-align:center;
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
font-weight:600;
margin:auto;
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
<span class="navbar-brand">Proces de Matricula</span>
</div>
</nav>

<div class="container py-5">

<div class="card shadow-lg border-0 rounded-4">

<div class="stepper-wrapper mb-5">

<div class="step completed">
<div class="step-circle">1</div>
<div>Datos Alumno</div>
</div>

<div class="step active">
<div class="step-circle">2</div>
<div>Datos Curso</div>
</div>

<div class="step">
<div class="step-circle">3</div>
<div>Pago</div>
</div>

</div>

<div class="card-body p-5">

<h4 class="text-primary mb-4">Datos del Curso</h4>

<form action="<?= base_url('matricula/datos_curs') ?>" method="post">

<?= csrf_field() ?>

<?= validation_list_errors() ?>

<div class="row g-3 mb-4">

<div class="col-md-6">

<label class="form-label">Ciclo formativo</label>

<select class="form-select" name="Nom_curs">

<option value="">Seleccione</option>

<option value="DAW">DAW</option>
<option value="DAM">DAM</option>
<option value="ASIX">ASIX</option>

</select>

</div>

<div class="col-md-6">

<label class="form-label">Código del curso</label>

<input type="text" class="form-control" name="codigo_curs">

</div>

</div>

<div class="row g-3 mb-4">

<div class="col-md-6">

<label class="form-label">Tipo matrícula</label>

<select class="form-select" name="tipo_matricula">

<option value="normal">Normal</option>
<option value="continuidad">Continuidad</option>

</select>

</div>

<div class="col-md-6">

<label class="form-label">Precio matrícula</label>

<input type="number" step="0.01" class="form-control" name="precio">

</div>

</div>

<div class="text-end">

<button class="btn btn-primary btn-lg">

Continuar al pago

</button>

</div>

</form>

</div>

</div>

</div>

</body>
</html>