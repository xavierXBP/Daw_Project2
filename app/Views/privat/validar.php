<?= $this->extend('privat/layout') ?>
<?= $this->section('content') ?>

<div class="container">
  <h3>Validar matrícula</h3>

  <?php if (isset($lock_warning)): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
      <strong>Atención:</strong> <?= esc($lock_warning) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <?php if (isset($matricula)): ?>
    <div class="card mb-4 shadow-sm">
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

        <!-- Botones de acción -->
        <hr>
        <div class="d-flex justify-content-between align-items-center">
          <div class="text-muted small">
            Estado actual: <span class="fw-semibold"><?= esc($matricula['estat']) ?></span>
          </div>
          <div class="btn-group">
            <form method="post" action="<?= base_url('privat/validar/aprobar') ?>" class="me-2">
              <input type="hidden" name="student_id" value="<?= $obfuscated_id ?? '' ?>">
              <button type="submit" class="btn btn-success">
                Validar
              </button>
            </form>

            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#anularModal">
              Anular
            </button>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    <div class="alert alert-danger">No se ha encontrado la información de la matrícula.</div>
  <?php endif; ?>
</div>

<!-- Modal para anular matrícula con mensaje -->
<div class="modal fade" id="anularModal" tabindex="-1" aria-labelledby="anularModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="anularModalLabel">Anular matrícula</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form method="post" action="<?= base_url('privat/validar/anular') ?>">
        <div class="modal-body">
          <input type="hidden" name="student_id" value="<?= $obfuscated_id ?? '' ?>">
          <p class="fw-semibold mb-2">
            Alumno: <?= esc($matricula['alumne']['nom_complet']) ?> (<?= esc($matricula['alumne']['dni']) ?>)
          </p>

          <div class="mb-2">
            <label class="form-label">Mensajes</label>
            <select id="anularPreset" class="form-select mb-2">
              <option value="">-- Selecciona un mensaje --</option>
              <?php if (isset($mensajes)): ?>
                <?php foreach ($mensajes as $mensaje): ?>
                  <option value="<?= esc($mensaje['mensaje']) ?>"><?= esc($mensaje['titulo']) ?> - <?= esc($mensaje['mensaje']) ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>

          <div class="mb-2">
            <label class="form-label">Mensaje al alumno (se enviaría por email)</label>
            <textarea id="anularMensaje" name="mensaje" class="form-control" rows="4" placeholder="Explica el motivo de la anulación..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-danger">Confirmar anulación</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const preset = document.getElementById('anularPreset');
    const mensaje = document.getElementById('anularMensaje');
    if (preset && mensaje) {
      preset.addEventListener('change', function () {
        if (this.value) {
          mensaje.value = this.value;
        }
      });
    }

    // Liberar bloqueo al salir de la página
    window.addEventListener('beforeunload', function() {
      navigator.sendBeacon('/api/unlock-student/<?= $obfuscated_id ?? '' ?>', '');
    });

    // Liberar bloqueo al navegar hacia atrás
    window.addEventListener('popstate', function() {
      navigator.sendBeacon('/api/unlock-student/<?= $obfuscated_id ?? '' ?>', '');
    });
  });
</script>

<?= $this->endSection() ?>