<?= $this->extend('privat/layout') ?>
<?= $this->section('content') ?>

<div class="container">
  <h3>Explorador de Expedientes</h3>
  
  <!-- Estadísticas -->
  <div class="row mb-4">
    <div class="col-md-6">
      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title"><?= $total_folders ?></h5>
          <p class="card-text">Total de Alumnos</p>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title"><?= $total_pdfs ?></h5>
          <p class="card-text">Total de PDFs Generados</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Lista de carpetas -->
  <?php if (!empty($folders)): ?>
    <div class="accordion" id="expedientesAccordion">
      <?php foreach ($folders as $index => $folder): ?>
        <div class="accordion-item">
          <h2 class="accordion-header" id="heading<?= $index ?>">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#collapse<?= $index ?>" aria-expanded="false" aria-controls="collapse<?= $index ?>">
              <div class="d-flex justify-content-between align-items-center w-100">
                <div>
                  <strong><?= esc($folder['name']) ?></strong>
                  <span class="badge bg-primary ms-2"><?= $folder['pdf_count'] ?> PDFs</span>
                </div>
                <small class="text-muted">
                  Creado: <?= date('d/m/Y H:i', $folder['created']) ?>
                </small>
              </div>
            </button>
          </h2>
          <div id="collapse<?= $index ?>" class="accordion-collapse collapse" 
               aria-labelledby="heading<?= $index ?>" data-bs-parent="#expedientesAccordion">
            <div class="accordion-body">
              <?php if (!empty($folder['pdfs'])): ?>
                <div class="list-group">
                  <?php foreach ($folder['pdfs'] as $pdf): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                      <div>
                        <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                        <strong><?= esc($pdf['name']) ?></strong>
                        <small class="text-muted ms-2">
                          (<?= number_format($pdf['size'] / 1024, 2) ?> KB)
                        </small>
                      </div>
                      <div>
                        <small class="text-muted me-3">
                          <?= date('d/m/Y H:i', $pdf['modified']) ?>
                        </small>
                        <a href="<?= base_url('privat/expedientes/pdf/' . $folder['name'] . '/' . $pdf['name']) ?>" 
                           target="_blank" class="btn btn-sm btn-outline-primary me-1" title="Ver PDF">
                          <i class="bi bi-eye"></i>
                        </a>
                        <a href="<?= base_url('privat/expedientes/pdf/' . $folder['name'] . '/' . $pdf['name']) ?>" 
                           download="<?= $pdf['name'] ?>" class="btn btn-sm btn-outline-success" title="Descargar PDF">
                          <i class="bi bi-download"></i>
                        </a>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <p class="text-muted">No hay PDFs en esta carpeta.</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="alert alert-info">
      <h4 class="alert-heading">No hay expedientes</h4>
      <p>No se encontraron carpetas de expedientes. Los expedientes se crearán automáticamente cuando valides alumnos.</p>
      <hr>
      <p class="mb-0">
        <a href="<?= base_url('privat/validados') ?>" class="btn btn-primary">
          Ir a Validar Alumnos
        </a>
      </p>
    </div>
  <?php endif; ?>
</div>

<style>
.accordion-button:not(.collapsed) {
  background-color: #f8f9fa;
  color: #495057;
}

.accordion-item {
  border: 1px solid #dee2e6;
  margin-bottom: 10px;
  border-radius: 8px !important;
  overflow: hidden;
}

.list-group-item {
  border-left: none;
  border-right: none;
}

.list-group-item:first-child {
  border-top: none;
}

.list-group-item:last-child {
  border-bottom: none;
}

.card {
  border: none;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-body {
  padding: 1.5rem;
}

.card-title {
  font-size: 2rem;
  font-weight: bold;
  color: #0d6efd;
}

.card-text {
  font-size: 0.9rem;
  color: #6c757d;
}

.btn-outline-primary:hover,
.btn-outline-success:hover {
  transform: translateY(-1px);
  transition: all 0.2s ease;
}

.bi-file-earmark-pdf {
  font-size: 1.2rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Auto-expand first folder if there are folders
  const firstAccordion = document.querySelector('.accordion-button');
  if (firstAccordion && firstAccordion.classList.contains('collapsed')) {
    firstAccordion.click();
  }
});
</script>

<?= $this->endSection() ?>
