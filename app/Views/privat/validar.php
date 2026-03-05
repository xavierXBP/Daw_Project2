<?= $this->extend('privat/layout') ?>
<?= $this->section('content') ?>

<?php
// Espera recibir en $matricula:
// - any_escolar, curs, cicle, estat, estat_clase
// - alumne: [nom_complet, dni, data_naixement, domicili, municipi, cp, telefon, email, poblacio_naixement, id]
// Y $missatges_rapids para posibles mensajes rápidos.
?>

<div class="container">
  <h3>Validar matrícula</h3>

  <?php if (isset($matricula)): ?>
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div>
          <span class="fw-semibold">Curso:</span> <?= esc($matricula['curs']) ?> |
          <span class="fw-semibold">Ciclo:</span> <?= esc($matricula['cicle']) ?> |
          <span class="fw-semibold">Año escolar:</span> <?= esc($matricula['any_escolar']) ?>
        </div>
        <span class="badge <?= esc($matricula['estat_clase']) ?>">
          <?= esc($matricula['estat']) ?>
        </span>
      </div>
      <div class="card-body">
        <h5 class="card-title mb-3">Datos del alumno</h5>
        <div class="row mb-2">
          <div class="col-md-6">
            <strong>Nombre completo:</strong> <?= esc($matricula['alumne']['nom_complet']) ?>
          </div>
          <div class="col-md-3">
            <strong>DNI:</strong> <?= esc($matricula['alumne']['dni']) ?>
          </div>
          <div class="col-md-3">
            <strong>Fecha nacimiento:</strong> <?= esc($matricula['alumne']['data_naixement']) ?>
          </div>
        </div>
        <div class="row mb-2">
          <div class="col-md-6">
            <strong>Domicilio:</strong> <?= esc($matricula['alumne']['domicili']) ?>
          </div>
          <div class="col-md-3">
            <strong>Municipio:</strong> <?= esc($matricula['alumne']['municipi']) ?>
          </div>
          <div class="col-md-3">
            <strong>Código postal:</strong> <?= esc($matricula['alumne']['cp']) ?>
          </div>
        </div>
        <div class="row mb-2">
          <div class="col-md-4">
            <strong>Teléfono:</strong> <?= esc($matricula['alumne']['telefon']) ?>
          </div>
          <div class="col-md-4">
            <strong>Email:</strong> <?= esc($matricula['alumne']['email']) ?>
          </div>
          <div class="col-md-4">
            <strong>Población nacimiento:</strong> <?= esc($matricula['alumne']['poblacio_naixement']) ?>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    <div class="alert alert-danger">No se ha encontrado la información de la matrícula.</div>
  <?php endif; ?>
</div>

<?= $this->endSection() ?>