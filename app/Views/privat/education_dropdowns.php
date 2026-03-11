<?= $this->extend('privat/layout') ?>

<?= $this->section('content') ?>

<div class="container py-4">
    <h1 class="mb-3">Árbol de niveles, estructuras y asignaturas</h1>

    <!-- Buscador mejorado arriba -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-md-4">
                    <label for="search" class="form-label fw-semibold mb-1">
                        Buscar estructuras / asignaturas
                    </label>
                    <input id="search" type="text" class="form-control"
                           placeholder="Ej: ESO 1, Matemáticas, DAM 2...">
                </div>
                <div class="col-md-8">
                    <div id="search-results" class="list-group small"></div>
                    <div id="path-display" class="mt-2 fw-bold text-primary"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="tree">
        <?php foreach ($niveles as $n): ?>
        <details class="card mb-2">
            <summary class="card-header ps-3 d-flex justify-content-between align-items-center">
                <div>
                    <span class="me-2 toggle-icon">▶</span><?= esc($n->nombre) ?>
                </div>
                <!-- Aquí podrías añadir CRUD de niveles si lo necesitas en el futuro -->
            </summary>
            <div class="card-body children py-2" data-nivel="<?= esc($n->id) ?>"></div>
        </details>
        <?php endforeach; ?>
    </div>

<p class="text-muted small">Abra los nodos para ver familias, cursos y asignaturas; puede tener varios abiertos a la vez.</p>
</div>

<style>
    #tree details { background:#f8f9fa; }
    #tree summary { cursor: pointer; font-weight: 600; display: flex; align-items: center; }
    #tree summary:hover { background:#e9ecef; }
    #tree .toggle-icon { transition: transform .2s ease; }
    #tree details[open] .toggle-icon { transform: rotate(90deg); }
    #tree ul { list-style: disc inside; margin: .5rem 0 1rem 1rem; }
    #search-results .list-group-item { cursor:pointer; }
</style>

<!-- Modal para alta/edición de asignaturas -->
<div class="modal fade" id="asigModal" tabindex="-1" aria-labelledby="asigModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="asigModalLabel">Asignatura</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2">
          <label class="form-label">Curso</label>
          <input type="text" id="asigCurso" class="form-control" readonly>
        </div>
        <div class="mb-2">
          <label for="asigNombre" class="form-label">Nombre de la asignatura</label>
          <input type="text" id="asigNombre" class="form-control" placeholder="Ej: Matemáticas, Inglés...">
        </div>
        <div class="mb-2">
          <label for="asigHoras" class="form-label">Horas semanales</label>
          <input type="number" min="0" id="asigHoras" class="form-control" value="0">
        </div>
        <div class="mb-2">
          <label for="asigPrecio" class="form-label">Precio (€)</label>
          <input type="number" min="0" step="0.01" id="asigPrecio" class="form-control" value="0.00">
        </div>
        <div id="asigError" class="text-danger small mt-1"></div>
        <input type="hidden" id="asigId">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="guardarAsignaturaDesdeModal()">Guardar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal para alta/edición de optativas -->
<div class="modal fade" id="optModal" tabindex="-1" aria-labelledby="optModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="optModalLabel">Optativa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2">
          <label class="form-label">Curso</label>
          <input type="text" id="optCurso" class="form-control" readonly>
        </div>
        <div class="mb-2">
          <label for="optNombre" class="form-label">Nombre de la optativa</label>
          <input type="text" id="optNombre" class="form-control" placeholder="Ej: Informática, Música...">
        </div>
        <div class="mb-2">
          <label for="optPrecio" class="form-label">Precio (€)</label>
          <input type="number" min="0" step="0.01" id="optPrecio" class="form-control" value="0.00">
        </div>
        <div id="optError" class="text-danger small mt-1"></div>
        <input type="hidden" id="optId">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="guardarOptativaDesdeModal()">Guardar</button>
      </div>
    </div>
  </div>
</div>

<script>
const fetchJson = url => fetch(url).then(r => r.json());

async function postJson(url, data) {
    const res = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify(data || {}),
    });
    if (!res.ok) {
        const text = await res.text();
        throw new Error(text || 'Error en la petición');
    }
    try {
        return await res.json();
    } catch (e) {
        return {};
    }
}

// Contexto global para refrescar después de guardar asignaturas
let asigContext = null;
let optContext = null;

async function loadStructs(container, query) {
    const items = await fetchJson('<?= base_url('matricula/estructuras') ?>?' + new URLSearchParams(query));
    items.forEach(item => {
        const det = document.createElement('details');
        const sum = document.createElement('summary');
        sum.className = 'd-flex justify-content-between align-items-center';

        const nameSpan = document.createElement('span');
        nameSpan.textContent = item.nombre;

        const btnGroup = document.createElement('div');
        btnGroup.className = 'btn-group btn-group-sm';

        const btnEdit = document.createElement('button');
        btnEdit.type = 'button';
        btnEdit.className = 'btn btn-outline-primary';
        btnEdit.textContent = 'Editar';
        btnEdit.addEventListener('click', async (e) => {
            e.stopPropagation();
            const nuevo = prompt('Nuevo nombre para ' + item.nombre + ':', item.nombre);
            if (!nuevo) return;
            try {
                const saved = await postJson('<?= base_url('matricula/estructura/save') ?>', {
                    id: item.id,
                    nombre: nuevo,
                    tipo: item.tipo,
                    nivel_id: item.nivel_id,
                    parent_id: item.parent_id
                });
                item.nombre = saved.nombre;
                nameSpan.textContent = saved.nombre;
            } catch (err) {
                alert('No se ha podido guardar: ' + err.message);
            }
        });

        const btnDelete = document.createElement('button');
        btnDelete.type = 'button';
        btnDelete.className = 'btn btn-outline-danger';
        btnDelete.textContent = 'Borrar';
        btnDelete.addEventListener('click', async (e) => {
            e.stopPropagation();
            if (!confirm('¿Seguro que quieres borrar "' + item.nombre + '" y todo su contenido?')) return;
            try {
                await postJson('<?= base_url('matricula/estructura/delete') ?>/' + item.id, {});
                det.remove();
            } catch (err) {
                alert('No se ha podido borrar: ' + err.message);
            }
        });

        const btnAddChild = document.createElement('button');
        btnAddChild.type = 'button';
        btnAddChild.className = 'btn btn-outline-success';
        btnAddChild.textContent = item.tipo === 'curso' ? 'Añadir asignatura' : 'Añadir hijo';
        btnAddChild.addEventListener('click', async (e) => {
            e.stopPropagation();
            if (item.tipo === 'curso') {
                const inner = det.querySelector('.inner-children');
                // Mostrar menú para elegir entre asignatura u optativa
                const choice = confirm('¿Añadir optativa? (Cancelar para asignatura normal)');
                if (choice) {
                    openOptativaModal('new', { id: item.id, nombre: item.nombre }, null, inner);
                } else {
                    openAsignaturaModal('new', { id: item.id, nombre: item.nombre }, null, inner);
                }
            } else {
                let tipoHijo = 'familia';
                if (item.tipo === 'familia') tipoHijo = 'curso';
                const nombre = prompt('Nombre del nuevo ' + tipoHijo + ':');
                if (!nombre) return;
                try {
                    await postJson('<?= base_url('matricula/estructura/save') ?>', {
                        nombre,
                        tipo: tipoHijo,
                        nivel_id: item.nivel_id,
                        parent_id: item.id
                    });
                    // recargar hijos
                    const inner = det.querySelector('.inner-children');
                    if (inner) {
                        inner.dataset.loaded = '';
                        inner.innerHTML = '';
                        await loadStructs(inner, { parent: item.id });
                    }
                } catch (err) {
                    alert('No se ha podido crear el hijo: ' + err.message);
                }
            }
        });

        btnGroup.appendChild(btnEdit);
        btnGroup.appendChild(btnDelete);
        btnGroup.appendChild(btnAddChild);

        sum.appendChild(nameSpan);
        sum.appendChild(btnGroup);
        det.appendChild(sum);
        const inner = document.createElement('div');
        inner.className = 'inner-children';
        inner.dataset.parent = item.id;
        inner.dataset.tipo = item.tipo;
        det.appendChild(inner);
        container.appendChild(det);

        sum.addEventListener('click', async (e) => {
            // evitar que los clicks de los botones re-lancen el toggle
            if (e.target && e.target.tagName === 'BUTTON') return;
            if (det.dataset.loaded) return;
            det.dataset.loaded = '1';
            if (item.tipo === 'curso') {
                await loadCursoAsignaturas(inner, item.id, item.nombre);
            } else {
                await loadStructs(inner, { parent: item.id });
            }
        });
    });
}

async function loadCursoAsignaturas(container, estructuraId, cursoNombre) {
    const asigs = await fetchJson('<?= base_url('matricula/asignaturas') ?>?estructura=' + estructuraId);
    const optativas = await fetchJson('<?= base_url('matricula/optativas') ?>?estructura=' + estructuraId);
    
    container.innerHTML = '';
    if (!asigs.length && !optativas.length) return;
    
    const ul = document.createElement('ul');
    
    // Cargar asignaturas normales
    asigs.forEach(a => {
        const li = document.createElement('li');
        li.className = 'd-flex justify-content-between align-items-center';

        const span = document.createElement('span');
        span.textContent = a.nombre;

        const btns = document.createElement('div');
        btns.className = 'btn-group btn-group-sm';

        const bEdit = document.createElement('button');
        bEdit.type = 'button';
        bEdit.className = 'btn btn-outline-primary';
        bEdit.textContent = 'Editar';
        bEdit.addEventListener('click', (e) => {
            e.stopPropagation();
            openAsignaturaModal(
                'edit',
                { id: estructuraId, nombre: cursoNombre },
                a,
                container
            );
        });

        const bDel = document.createElement('button');
        bDel.type = 'button';
        bDel.className = 'btn btn-outline-danger';
        bDel.textContent = 'Borrar';
        bDel.addEventListener('click', async (e) => {
            e.stopPropagation();
            if (!confirm('¿Seguro que quieres borrar la asignatura "' + a.nombre + '"?')) return;
            try {
                await postJson('<?= base_url('matricula/asignatura/delete') ?>/' + a.id, {});
                li.remove();
            } catch (err) {
                alert('No se ha podido borrar la asignatura: ' + err.message);
            }
        });

        btns.appendChild(bEdit);
        btns.appendChild(bDel);

        li.appendChild(span);
        li.appendChild(btns);

        ul.appendChild(li);
    });
    
    // Cargar optativas
    optativas.forEach(o => {
        const li = document.createElement('li');
        li.className = 'd-flex justify-content-between align-items-center';

        const span = document.createElement('span');
        span.textContent = o.nombre + ' (op)';

        const btns = document.createElement('div');
        btns.className = 'btn-group btn-group-sm';

        const bEdit = document.createElement('button');
        bEdit.type = 'button';
        bEdit.className = 'btn btn-outline-primary';
        bEdit.textContent = 'Editar';
        bEdit.addEventListener('click', (e) => {
            e.stopPropagation();
            openOptativaModal(
                'edit',
                { id: estructuraId, nombre: cursoNombre },
                o,
                container
            );
        });

        const bDel = document.createElement('button');
        bDel.type = 'button';
        bDel.className = 'btn btn-outline-danger';
        bDel.textContent = 'Borrar';
        bDel.addEventListener('click', async (e) => {
            e.stopPropagation();
            if (!confirm('¿Seguro que quieres borrar la optativa "' + o.nombre + '"?')) return;
            try {
                await postJson('<?= base_url('matricula/optativa/delete') ?>/' + o.id, {});
                li.remove();
            } catch (err) {
                alert('No se ha podido borrar la optativa: ' + err.message);
            }
        });

        btns.appendChild(bEdit);
        btns.appendChild(bDel);

        li.appendChild(span);
        li.appendChild(btns);

        ul.appendChild(li);
    });
    
    container.appendChild(ul);
}

// Modal de alta / edición de optativas
function openOptativaModal(modo, curso, opt, container) {
    const modalEl = document.getElementById('optModal');
    const modal = new bootstrap.Modal(modalEl);

    optContext = {
        modo,
        cursoId: curso.id,
        cursoNombre: curso.nombre,
        container
    };

    document.getElementById('optCurso').value = curso.nombre || '';
    document.getElementById('optId').value = opt && opt.id ? opt.id : '';
    document.getElementById('optNombre').value = opt && opt.nombre ? opt.nombre : '';
    document.getElementById('optPrecio').value = opt && opt.precio ? opt.precio : 0.00;

    document.getElementById('optError').textContent = '';

    const title = document.getElementById('optModalLabel');
    title.textContent = modo === 'edit'
        ? 'Editar optativa de ' + (curso.nombre || '')
        : 'Nueva optativa para ' + (curso.nombre || '');

    modal.show();
}

async function guardarOptativaDesdeModal() {
    if (!optContext) return;
    const id = document.getElementById('optId').value || null;
    const nombre = document.getElementById('optNombre').value.trim();
    const precio = parseFloat(document.getElementById('optPrecio').value || '0.00');

    const errorDiv = document.getElementById('optError');
    errorDiv.textContent = '';

    if (!nombre) {
        errorDiv.textContent = 'El nombre de la optativa es obligatorio.';
        return;
    }

    try {
        await postJson('<?= base_url('matricula/optativa/save') ?>', {
            id,
            nombre,
            precio: precio,
            estructura_id: optContext.cursoId
        });
        if (optContext.container) {
            await loadCursoAsignaturas(
                optContext.container,
                optContext.cursoId,
                optContext.cursoNombre
            );
        }
        const modalEl = document.getElementById('optModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        modal.hide();
    } catch (err) {
        errorDiv.textContent = 'No se ha podido guardar la optativa: ' + err.message;
    }
}

// Modal de alta / edición de asignaturas
function openAsignaturaModal(modo, curso, asig, container) {
    const modalEl = document.getElementById('asigModal');
    const modal = new bootstrap.Modal(modalEl);

    asigContext = {
        modo,
        cursoId: curso.id,
        cursoNombre: curso.nombre,
        container
    };

    document.getElementById('asigCurso').value = curso.nombre || '';
    document.getElementById('asigId').value = asig && asig.id ? asig.id : '';
    document.getElementById('asigNombre').value = asig && asig.nombre ? asig.nombre : '';
    document.getElementById('asigHoras').value = asig && asig.horas_semanales ? asig.horas_semanales : 0;
    document.getElementById('asigPrecio').value = asig && asig.precio ? asig.precio : 0.00;

    document.getElementById('asigError').textContent = '';

    const title = document.getElementById('asigModalLabel');
    title.textContent = modo === 'edit'
        ? 'Editar asignatura de ' + (curso.nombre || '')
        : 'Nueva asignatura para ' + (curso.nombre || '');

    modal.show();
}

async function guardarAsignaturaDesdeModal() {
    if (!asigContext) return;
    const id = document.getElementById('asigId').value || null;
    const nombre = document.getElementById('asigNombre').value.trim();
    const horas = parseInt(document.getElementById('asigHoras').value || '0', 10);
    const precio = parseFloat(document.getElementById('asigPrecio').value || '0.00');

    const errorDiv = document.getElementById('asigError');
    errorDiv.textContent = '';

    if (!nombre) {
        errorDiv.textContent = 'El nombre de la asignatura es obligatorio.';
        return;
    }

    try {
        await postJson('<?= base_url('matricula/asignatura/save') ?>', {
            id,
            nombre,
            horas_semanales: horas,
            precio: precio,
            estructura_id: asigContext.cursoId
        });
        if (asigContext.container) {
            await loadCursoAsignaturas(
                asigContext.container,
                asigContext.cursoId,
                asigContext.cursoNombre
            );
        }
        const modalEl = document.getElementById('asigModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        modal.hide();
    } catch (err) {
        errorDiv.textContent = 'No se ha podido guardar la asignatura: ' + err.message;
    }
}

function renderResults(results) {
    const resDiv = document.getElementById('search-results');
    const display = document.getElementById('path-display');
    resDiv.innerHTML = '';
    display.innerHTML = '';
    results.forEach(path => {
        if (!path.length) return;
        const last = path[path.length - 1];
        let text = '';
        for (let i = 0; i < path.length; i++) {
            if (i > 0) text += ' &gt; ';
            text += path[i].nombre;
        }
        const a = document.createElement('a');
        a.href = '#';
        a.className = 'list-group-item list-group-item-action';
        a.innerHTML = `<span class="fw-semibold">${last.nombre}</span><br><small class="text-muted">${text}</small>`;
        a.addEventListener('click', e => {
            e.preventDefault();
            // sólo mostrar la ruta seleccionada, no expandir nada
            display.innerHTML = text;
        });
        resDiv.appendChild(a);
    });
}

document.getElementById('search').addEventListener('input', async function() {
    const q = this.value.trim();
    if (q.length < 2) {
        document.getElementById('search-results').innerHTML = '';
        return;
    }
    const results = await fetchJson(`<?= base_url('matricula/buscar') ?>?q=` + encodeURIComponent(q));
    renderResults(results);
});


document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('#tree > details').forEach(det => {
        const div = det.querySelector('.children');
        const nivel = div.dataset.nivel;
        det.addEventListener('click', async () => {
            if (div.dataset.loaded) return;
            div.dataset.loaded = '1';
            await loadStructs(div, { nivel });
        });
    });
});
</script>

<?= $this->endSection() ?>
