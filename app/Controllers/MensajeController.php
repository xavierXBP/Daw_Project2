<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MensajeModel;

class MensajeController extends BaseController
{
    private $mensajeModel;

    public function __construct()
    {
        $this->mensajeModel = new MensajeModel();
    }

    public function create()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        
        $mensaje = [
            'codigo' => $data['codigo'] ?? '',
            'titulo' => $data['titulo'] ?? '',
            'mensaje' => $data['mensaje'] ?? '',
            'tipo' => $data['tipo'] ?? 'info',
            'activo' => isset($data['activo']) ? (int)$data['activo'] : 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $id = $this->mensajeModel->insert($mensaje);
        
        if ($id) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Mensaje creado correctamente',
                'data' => ['id' => $id]
            ]);
        }
        
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Error al crear mensaje'
        ], 400);
    }

    public function update($id)
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        
        $mensaje = [
            'codigo' => $data['codigo'] ?? '',
            'titulo' => $data['titulo'] ?? '',
            'mensaje' => $data['mensaje'] ?? '',
            'tipo' => $data['tipo'] ?? 'info',
            'activo' => isset($data['activo']) ? (int)$data['activo'] : 1,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $result = $this->mensajeModel->update($id, $mensaje);
        
        if ($result) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Mensaje actualizado correctamente'
            ]);
        }
        
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Error al actualizar mensaje'
        ], 400);
    }

    public function delete($id)
    {
        $result = $this->mensajeModel->delete($id);
        
        if ($result) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Mensaje eliminado correctamente'
            ]);
        }
        
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Error al eliminar mensaje'
        ], 400);
    }
}
