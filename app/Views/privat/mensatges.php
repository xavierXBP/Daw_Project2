<?= $this->extend('privat/layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Gestión de Mensajes</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevo">
            Nuevo Mensaje
        </button>
    </div>

    <div class="container py-4">
    <h1 class="mb-4">Todos los Mensajes</h1>
    
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Código</th>
                    <th>Título</th>
                    <th>Mensaje</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($mensajes) && count($mensajes) > 0): ?>
                    <?php foreach ($mensajes as $msg): ?>
                        <tr>
                            <td>#<?= $msg['id'] ?></td>
                            <td><code><?= esc($msg['codigo']) ?></code></td>
                            <td><?= esc($msg['titulo']) ?></td>
                            <td><?= esc($msg['mensaje']) ?></td>
                            <td>
                                <span class="badge bg-<?= $msg['tipo'] === 'error' ? 'danger' : ($msg['tipo'] === 'warning' ? 'warning' : ($msg['tipo'] === 'success' ? 'success' : 'info')) ?>">
                                    <?= esc($msg['tipo']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $msg['activo'] ? 'success' : 'secondary' ?>">
                                    <?= $msg['activo'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if (!$msg['activo']): ?>
                                    <button class="btn btn-sm btn-outline-success" onclick="activarMensaje(<?= $msg['id'] ?>)">
                                        Activar
                                    </button>
                                <?php endif; ?>
                                <button class="btn btn-sm btn-outline-warning"
                                    onclick='editarMensaje(<?= json_encode($msg) ?>)'>
                                    Editar
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="eliminarMensaje(<?= $msg['id'] ?>)">Eliminar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            No hay mensajes configurados. 
                            <button class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#modalNuevo">
                                Crear primer mensaje
                            </button>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL EDITAR -->
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Editar mensaje</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="formEditar">
                    <input type="hidden" id="editId">

                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" id="editTitulo" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipo</label>
                        <select id="editTipo" class="form-control">
                            <option value="info">Info</option>
                            <option value="warning">Warning</option>
                            <option value="error">Error</option>
                            <option value="success">Success</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mensaje</label>
                        <textarea id="editMensaje" class="form-control" rows="4"></textarea>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="editActivo">
                        <label class="form-check-label">Activo</label>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-warning" onclick="guardarCambios()">Guardar</button>
                

            </div>
        </div>
    </div>
</div>

<!-- MODAL NUEVO -->
<div class="modal fade" id="modalNuevo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Nuevo mensaje</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="formNuevo">
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" id="nuevoTitulo" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipo</label>
                        <select id="nuevoTipo" class="form-control">
                            <option value="info">Info</option>
                            <option value="warning">Warning</option>
                            <option value="error">Error</option>
                            <option value="success">Success</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mensaje</label>
                        <textarea id="nuevoMensaje" class="form-control" rows="4"></textarea>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="nuevoActivo" checked>
                        <label class="form-check-label">Activo</label>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary" onclick="crearMensaje()">Guardar</button>
            </div>
        </div>
    </div>
</div>


<script>
function activarMensaje(id) {
    fetch(`/api/mensajes/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            activo: 1
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            location.reload();
        } else {
            alert('Error: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al activar mensaje');
    });
}

function toggleEstado(id, nuevoEstado) {
    // Obtener datos actuales del formulario de edición
    const idInput = document.getElementById('editId');
    const tituloInput = document.getElementById('editTitulo');
    const mensajeInput = document.getElementById('editMensaje');
    const tipoSelect = document.getElementById('editTipo');
    
    fetch(`/api/mensajes/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            codigo: 'CUSTOM_' + id,
            titulo: tituloInput.value,
            mensaje: mensajeInput.value,
            tipo: tipoSelect.value,
            activo: nuevoEstado
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            location.reload();
        } else {
            alert('Error: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al cambiar estado');
    });
}

function editarMensaje(msg) {
    document.getElementById('editId').value = msg.id;
    document.getElementById('editTitulo').value = msg.titulo;
    document.getElementById('editMensaje').value = msg.mensaje;
    document.getElementById('editTipo').value = msg.tipo;
    document.getElementById('editActivo').checked = msg.activo;

    new bootstrap.Modal(document.getElementById('modalEditar')).show();
}

function guardarCambios() {
    const id = document.getElementById('editId').value;
    const data = {
        codigo: 'CUSTOM_' + id,
        titulo: document.getElementById('editTitulo').value,
        mensaje: document.getElementById('editMensaje').value,
        tipo: document.getElementById('editTipo').value,
        activo: document.getElementById('editActivo').checked ? 1 : 0
    };

    fetch(`/api/mensajes/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            location.reload();
        } else {
            alert('Error: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al guardar mensaje');
    });
}

function crearMensaje() {
    const data = {
        codigo: 'CUSTOM_' + Date.now(),
        titulo: document.getElementById('nuevoTitulo').value,
        mensaje: document.getElementById('nuevoMensaje').value,
        tipo: document.getElementById('nuevoTipo').value,
        activo: document.getElementById('nuevoActivo').checked ? 1 : 0
    };

    fetch('/api/mensajes', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            location.reload();
        } else {
            alert('Error: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al crear mensaje');
    });
}

function eliminarMensaje(id) {
    if (confirm('¿Estás seguro de eliminar este mensaje?')) {
        fetch(`/api/mensajes/${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(result => {
            if (result.status === 'success') {
                location.reload();
            } else {
                alert('Error: ' + result.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar mensaje');
        });
    }
}
</script>

<?= $this->endSection() ?>
