<?= $this->extend('privat/layout') ?>

<?= $this->section('content') ?>

<div class="container py-4">
    <h1 class="mb-4">Árbol de niveles, estructuras y asignaturas</h1>

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

    <div class="mb-3">
    <input id="search" type="text" class="form-control" placeholder="Buscar estructuras o asignaturas...">
    <div id="search-results" class="list-group mt-1"></div>
    <div id="path-display" class="mt-2 fw-bold"></div>
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
</style>

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
                const nombreAsig = prompt('Nombre de la nueva asignatura:');
                if (!nombreAsig) return;
                const horas = prompt('Horas semanales (opcional, número):', '0');
                try {
                    await postJson('<?= base_url('matricula/asignatura/save') ?>', {
                        nombre: nombreAsig,
                        horas_semanales: parseInt(horas || '0', 10),
                        estructura_id: item.id
                    });
                    // recargar asignaturas del curso
                    const inner = det.querySelector('.inner-children');
                    if (inner) {
                        inner.dataset.loaded = '';
                        inner.innerHTML = '';
                        await loadCursoAsignaturas(inner, item.id);
                    }
                } catch (err) {
                    alert('No se ha podido crear la asignatura: ' + err.message);
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
                await loadCursoAsignaturas(inner, item.id);
            } else {
                await loadStructs(inner, { parent: item.id });
            }
        });
    });
}

async function loadCursoAsignaturas(container, estructuraId) {
    const asigs = await fetchJson('<?= base_url('matricula/asignaturas') ?>?estructura=' + estructuraId);
    container.innerHTML = '';
    if (!asigs.length) return;
    const ul = document.createElement('ul');
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
        bEdit.addEventListener('click', async (e) => {
            e.stopPropagation();
            const nuevo = prompt('Nuevo nombre para la asignatura:', a.nombre);
            if (!nuevo) return;
            const horas = prompt('Horas semanales (opcional, número):', a.horas_semanales || '0');
            try {
                const saved = await postJson('<?= base_url('matricula/asignatura/save') ?>', {
                    id: a.id,
                    nombre: nuevo,
                    horas_semanales: parseInt(horas || '0', 10),
                    estructura_id: estructuraId
                });
                a.nombre = saved.nombre;
                a.horas_semanales = saved.horas_semanales;
                span.textContent = saved.nombre;
            } catch (err) {
                alert('No se ha podido guardar la asignatura: ' + err.message);
            }
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
    container.appendChild(ul);
}

// expandPath queda para uso manual si se desea, pero el buscador ya no lo invoca
async function expandPath(path) {
    console.log('expandPath called', path);
}

function renderResults(results) {
    const resDiv = document.getElementById('search-results');
    const display = document.getElementById('path-display');
    resDiv.innerHTML = '';
    display.innerHTML = '';
    results.forEach(path => {
        // build string with breaks when names repeat consecutively
        let text = '';
        for (let i = 0; i < path.length; i++) {
            if (i > 0) text += ' &gt; ';
            if (i > 0 && path[i].nombre === path[i - 1].nombre) {
                text += '<br>'; // line break for duplicate segment
            }
            text += path[i].nombre;
        }
        const a = document.createElement('a');
        a.href = '#';
        a.className = 'list-group-item list-group-item-action';
        a.innerHTML = text;
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
    if (q.length < 3) {
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
