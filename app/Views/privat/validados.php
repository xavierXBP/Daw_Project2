<?= $this->extend('privat/layout') ?>
<?= $this->section('content') ?>

  <div class="container">
    <h3>Validados</h3>

    <!-- Buscador y Filtros -->
    <div class="card mb-4 p-4" style="border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
      <div class="row mb-3">
        <!-- Buscador -->
        <div class="col-md-6">
          <label class="form-label fw-semibold">Buscar por Nombre, Apellido o DNI</label>
          <input type="text" id="searchInput" class="form-control" placeholder="Ej: Juan, García, 12345678A">
        </div>

        <!-- Filtro por Curso -->
        <div class="col-md-4">
          <label class="form-label fw-semibold">Filtrar por Curso</label>
          <select id="courseFilter" class="form-select">
            <option value="">-- Todos los cursos --</option>
            <?php foreach ($cursos as $curso): ?>
              <option value="<?= esc($curso['nombre']) ?>"><?= esc($curso['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Botón Buscar -->
        <div class="col-md-2 d-flex align-items-end">
          <button class="btn btn-primary w-100" id="searchBtn">Buscar</button>
        </div>
      </div>
    </div>

    <!-- Botones de Filtro por Estado -->
    <div class="mb-3">
      <h6 class="fw-semibold">Filtrar por Estado:</h6>
      <div class="btn-group" role="group">
        <?php foreach ($filtros_estado as $filtro): ?>
          <button type="button" class="btn btn-<?= esc($filtro['clase']) ?> state-filter" data-state="<?= esc($filtro['codigo']) ?>">
            <?= esc($filtro['texto']) ?>
          </button>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Grid / Tabla de Resultados -->
    <div class="table-responsive">
      <table class="table table-hover table-striped align-middle">
        <thead class="table-light">
          <tr>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>DNI</th>
            <th>Curso</th>
            <th>Estado</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody id="resultsTable">
          <?php if (!empty($alumnos)): ?>
          <?php foreach ($alumnos as $alumno): ?>
            <tr data-id="<?= esc($alumno['id']) ?>" data-nombre="<?= esc($alumno['nombre'] . ' ' . $alumno['apellidos']) ?>">
              <td><?= esc($alumno['nombre']) ?></td>
              <td><?= esc($alumno['apellidos']) ?></td>
              <td><?= esc($alumno['dni']) ?></td>
              <td><?= esc($alumno['curso']) ?></td>
              <td><span class="badge <?= esc($alumno['estado_clase']) ?>"><?= esc($alumno['estado_texto']) ?></span></td>
              <td class="text-center">
                <form method="post" action="<?= base_url('privat/validar') ?>" style="display:inline;">
                  <input type="hidden" name="student_id" value="<?= \App\Libraries\IdObfuscator::generateUrlSegment($alumno['id']) ?>">
                  <button type="submit" class="btn btn-sm btn-success" title="Validar">V</button>
                </form>
                <a href="#" class="btn btn-sm btn-info btn-msg" title="Editar" data-id="<?= esc($alumno['id']) ?>" data-nombre="<?= esc($alumno['nombre'] . ' ' . $alumno['apellidos']) ?>">E</a>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-center">No hay alumnos para mostrar.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
      <div id="noResults" class="alert alert-info" style="display:none;">
        No se encontraron resultados.
      </div>
    </div>
  </div>

  <style>
    .state-filter { cursor: pointer; }
    .state-filter.active { box-shadow: 0 0 0 3px rgba(0,0,0,0.2); }
  </style>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const searchBtn = document.getElementById('searchBtn');
      const searchInput = document.getElementById('searchInput');
      const courseFilter = document.getElementById('courseFilter');
      const stateFilters = document.querySelectorAll('.state-filter');
      const resultsTable = document.getElementById('resultsTable');
      const noResults = document.getElementById('noResults');

      let selectedState = null;

      // Función para guardar filtros en sessionStorage
      function saveFilters() {
        const filters = {
          q: searchInput.value.trim(),
          curso: courseFilter.value,
          estado: selectedState
        };
        sessionStorage.setItem('validadosFilters', JSON.stringify(filters));
      }

      // Función para cargar filtros desde sessionStorage
      function loadFilters() {
        const saved = sessionStorage.getItem('validadosFilters');
        if (saved) {
          const filters = JSON.parse(saved);
          return filters;
        }
        return {};
      }

      // Inicializar filtros desde servidor y sessionStorage
      const serverFilters = <?= json_encode($current_filters ?? []) ?>;
      const clientFilters = loadFilters();
      
      // Priorizar filtros del servidor sobre los del cliente
      const initialFilters = {
          q: serverFilters.q || clientFilters.q || '',
          curso: serverFilters.curso || clientFilters.curso || '',
          estado: serverFilters.estado || clientFilters.estado || ''
      };

      if (initialFilters.q) searchInput.value = initialFilters.q;
      if (initialFilters.curso) courseFilter.value = initialFilters.curso;
      
      // Sincronizar sessionStorage con filtros del servidor
      saveFilters();

      // Filtrar por estado (botones PV, V, E, AN, ALL) - ahora recarga desde servidor
      stateFilters.forEach(btn => {
        btn.addEventListener('click', function() {
          stateFilters.forEach(b => b.classList.remove('active'));
          this.classList.add('active');
          selectedState = this.dataset.state;
          applyFiltersAndReload();
        });

        if (initialFilters.estado && btn.dataset.state === initialFilters.estado) {
          btn.classList.add('active');
          selectedState = initialFilters.estado;
        }
      });

      // Búsqueda y filtros - ahora recarga desde servidor
      function applyFiltersAndReload() {
        saveFilters();
        
        // Enviar filtros al servidor y recargar página
        const filters = loadFilters();
        fetch('<?= base_url('privat/saveFilters') ?>', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify(filters)
        }).then(() => {
          window.location.reload();
        }).catch(() => {
          // Si falla, recargar de todos modos
          window.location.reload();
        });
      }

      searchBtn.addEventListener('click', applyFiltersAndReload);
      courseFilter.addEventListener('change', applyFiltersAndReload);
      
      // Para el campo de búsqueda, esperar un poco antes de recargar
      let searchTimeout;
      searchInput.addEventListener('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFiltersAndReload, 500);
      });

      // Navegar a validar manteniendo filtros (ahora usa sessionStorage + servidor)
      document.querySelectorAll('.btn-validar').forEach(btn => {
        btn.addEventListener('click', function(e) {
          e.preventDefault();
          saveFilters(); // Guardar filtros en sessionStorage
          
          // Enviar filtros al servidor para guardar en sesión
          const filters = loadFilters();
          fetch('<?= base_url('privat/saveFilters') ?>', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(filters)
          }).then(() => {
            window.location.href = this.getAttribute('href');
          }).catch(() => {
            // Si falla, navegar de todos modos
            window.location.href = this.getAttribute('href');
          });
        });
      });

      // POPUP MENSAJES (solo vista)
      const modal = new bootstrap.Modal(document.getElementById('msgModal'));

      document.querySelectorAll('.btn-msg').forEach(btn => {
        btn.addEventListener('click', () => {
          document.getElementById('alumnoId').value = btn.dataset.id;
          document.getElementById('alumnoNombre').innerText = btn.dataset.nombre;
          document.getElementById('msgText').value = '';
          document.getElementById('msgPreset').value = '';
          modal.show();
        });
      });

      document.getElementById('msgPreset').addEventListener('change', function () {
        document.getElementById('msgText').value = this.value;
      });
    });
  </script>

<div class="modal fade" id="msgModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Enviar mensaje</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p id="alumnoNombre" class="fw-bold"></p>

        <label class="form-label">Mensajes</label>
        <select id="msgPreset" class="form-select mb-3">
          <option value="">-- Selecciona un mensaje --</option>
          <?php if (isset($mensajes)): ?>
            <?php foreach ($mensajes as $mensaje): ?>
              <option value="<?= esc($mensaje['mensaje']) ?>"><?= esc($mensaje['titulo']) ?> - <?= esc($mensaje['mensaje']) ?></option>
            <?php endforeach; ?>
          <?php endif; ?>
        </select>

        <label class="form-label">Mensaje personalizado</label>
        <textarea id="msgText" class="form-control" rows="4" placeholder="Escribe el mensaje..."></textarea>

        <input type="hidden" id="alumnoId">
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary">Enviar</button>
      </div>

    </div>
  </div>
</div>

<?= $this->endSection() ?>