<?= $this->extend('privat/layout') ?>
<?= $this->section('content') ?>

<?php
$mensajes_predefinidos = [
    ['id'=>1,'titulo'=>'Documentación incompleta','mensaje'=>'Falta documentación obligatoria.','activo'=>true,'fecha_creacion'=>'15/01/2025'],
    ['id'=>2,'titulo'=>'DNI incorrecto','mensaje'=>'El DNI no es válido.','activo'=>true,'fecha_creacion'=>'20/01/2025'],
    ['id'=>3,'titulo'=>'Datos incompletos','mensaje'=>'Faltan datos personales.','activo'=>true,'fecha_creacion'=>'10/01/2025'],
    ['id'=>4,'titulo'=>'Revisar adjuntos','mensaje'=>'Los archivos están corruptos.','activo'=>true,'fecha_creacion'=>'05/02/2025'],
    ['id'=>5,'titulo'=>'Matrícula validada','mensaje'=>'Matrícula correcta.','activo'=>true,'fecha_creacion'=>'01/01/2025'],
];
?>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Gestión de Mensajes</h2>
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevo">
    Nuevo Mensaje
</button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Mensaje</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mensajes_predefinidos as $msg): ?>
                            <tr>
                                <td>#<?= $msg['id'] ?></td>
                                <td><?= esc($msg['titulo']) ?></td>
                                <td><?= esc($msg['mensaje']) ?></td>
                                <td><?= $msg['activo'] ? 'Activo' : 'Inactivo' ?></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-warning"
                                        onclick='editarMensaje(<?= json_encode($msg) ?>)'>
                                        Editar
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
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
function editarMensaje(msg) {
    document.getElementById('editId').value = msg.id;
    document.getElementById('editTitulo').value = msg.titulo;
    document.getElementById('editMensaje').value = msg.mensaje;
    document.getElementById('editActivo').checked = msg.activo;

    new bootstrap.Modal(document.getElementById('modalEditar')).show();
}

function guardarCambios() {
    const data = {
        id: document.getElementById('editId').value,
        titulo: document.getElementById('editTitulo').value,
        mensaje: document.getElementById('editMensaje').value,
        activo: document.getElementById('editActivo').checked ? 1 : 0
    };

    console.log('Datos a guardar:', data);

    // Aquí irá el AJAX al backend

    bootstrap.Modal.getInstance(document.getElementById('modalEditar')).hide();
}

function crearMensaje() {
    const data = {
        titulo: document.getElementById('nuevoTitulo').value,
        mensaje: document.getElementById('nuevoMensaje').value,
        activo: document.getElementById('nuevoActivo').checked ? 1 : 0
    };

    console.log('Nuevo mensaje:', data);

    // Aquí irá el AJAX al backend

    bootstrap.Modal.getInstance(document.getElementById('modalNuevo')).hide();
}


</script>

<?= $this->endSection() ?>
