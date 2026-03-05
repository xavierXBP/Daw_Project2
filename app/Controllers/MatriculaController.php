<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use CodeIgniter\API\ResponseTrait;
use App\Models\AlumneModel;
use App\Models\EstructurasModel;

class MatriculaController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $title = "MATRICULACION DE CURSO ";
    }

      public function expedientes_view(){
          return view('privat/expedient'); 
    }
   public function validados_view(){
      $alumneModel = new AlumneModel();
      $db = \Config\Database::connect();

      // Obtener alumnos con su curso
      $builder = $db->table('alumne a')
          ->select('a.id_alumne as id, a.nombre, a.apellidos, a.dni, a.estado, e.nombre as curso')
          ->join('estructuras e', 'e.id = a.estructura_curso_id', 'left');

      $alumnos = $builder->get()->getResultArray();

      // Mapear estado a clases y códigos para la vista
      foreach ($alumnos as &$alumno) {
          $estado = strtolower($alumno['estado'] ?? '');
          if ($estado === 'validado') {
              $alumno['estado_codigo'] = 'V';
              $alumno['estado_clase'] = 'bg-success';
              $alumno['estado_texto'] = 'Validado';
          } elseif ($estado === 'anulado') {
              $alumno['estado_codigo'] = 'AN';
              $alumno['estado_clase'] = 'bg-danger';
              $alumno['estado_texto'] = 'Anulado';
          } else {
              // En revisión por defecto
              $alumno['estado_codigo'] = 'E';
              $alumno['estado_clase'] = 'bg-warning text-dark';
              $alumno['estado_texto'] = 'En Revisión';
          }
      }
      unset($alumno);

      // Cursos disponibles para el filtro
      $cursos = $db->table('estructuras')
          ->where('tipo', 'curso')
          ->orderBy('nombre')
          ->get()
          ->getResultArray();

      $filtros_estado = [
          ['codigo' => 'V', 'clase' => 'outline-success', 'texto' => 'V (Validado)'],
          ['codigo' => 'E', 'clase' => 'outline-warning', 'texto' => 'E (En Revisión)'],
          ['codigo' => 'AN', 'clase' => 'outline-secondary', 'texto' => 'AN (Anulado)'],
          ['codigo' => 'ALL', 'clase' => 'outline-dark', 'texto' => 'TODOS'],
      ];

      $missatges_rapids = [
          'Falta documentación.',
          'DNI incorrecto.',
          'Datos incompletos.',
          'Revisa los archivos adjuntos.',
      ];

      return view('privat/validados', [
          'alumnos' => $alumnos,
          'cursos' => $cursos,
          'filtros_estado' => $filtros_estado,
          'missatges_rapids' => $missatges_rapids,
      ]);
      //rutas post para recivir de validar el id de alumno y el mensaje para enviar al alumno
  }
   public function validados_view_2($id): string{
      // Si más adelante quieres manejar POST con ID concreto,
      // puedes implementar la lógica aquí. De momento no se usa.
      return $this->validados_view();
   }

      public function validar_view($id){
          $alumneModel = new AlumneModel();
          $estructurasModel = new EstructurasModel();

          // Alumno
          $alumne = $alumneModel->find($id);
          if (!$alumne) {
              throw new \CodeIgniter\Exceptions\PageNotFoundException('Alumno no encontrado');
          }

          // Curso al que pertenece
          $curso = $estructurasModel->find($alumne['estructura_curso_id']);
          $cicleId = $curso['parent_id'] ?? null;

          $cicleNombre = 'Desconocido';
          if ($cicleId) {
              $cicle = $estructurasModel->find($cicleId);
              if ($cicle) {
                  $cicleNombre = $cicle['nombre'];
              }
          }

          $data['matricula'] = [
              'any_escolar' => '2025 / 2026',
              'curs' => $curso['nombre'] ?? 'Desconocido',
              'cicle' => $cicleNombre,
              'estat' => $alumne['estado'] ?? 'En Revisión',
              'estat_clase' => ($alumne['estado'] ?? '') === 'Anulado'
                  ? 'bg-danger text-white'
                  : (($alumne['estado'] ?? '') === 'Validado'
                      ? 'bg-success text-white'
                      : 'bg-warning text-dark'),
              'alumne' => [
                  'nom_complet' => $alumne['apellidos'] . ', ' . $alumne['nombre'],
                  'dni' => $alumne['dni'],
                  'data_naixement' => date('d/m/Y', strtotime($alumne['fecha_nacimiento'])),
                  'domicili' => $alumne['direccion'],
                  'municipi' => $alumne['municipio'],
                  'cp' => $alumne['codigo_postal'],
                  'telefon' => $alumne['telefono_alumno'],
                  'email' => $alumne['email_alumno'],
                  'poblacio_naixement' => $alumne['lugar_nacimiento'],
                  'id' => $alumne['id_alumne'],
              ],
          ];

          $data['missatges_rapids'] = [
              'Falta documentación.',
              'DNI incorrecto.',
              'Datos incompletos.',
              'Revisa los archivos adjuntos.',
          ];

          return view('privat/validar', $data);
      }
     public function mensatges_view(){
          return view('privat/mensatges'); 
    }
    
    public function matricula_post(){
     helper('form');
     $check1 = $this->request->getPost('check1');
     $check2 =$this->request->getPost('check2');
     $check3 = $this->request->getPost('check3');
     $check4 = $this->request->getPost('check4');

     $validation_rules = [
        'check1'=> 'required',
        'check2'=> 'required',
        'check3'=> 'required',
        'check4'=> 'required'
     ];
     if($this->validate($validation_rules)){
        
        return view('matricula/matricula1');
     }else{

        redirect()->to('matricula/matricula')->withInput()->with('error de validacion ',$validation_rules);

     }
    }
    public function m_alumne_view(){

    }
    public function m_alumne_post(){

    }
    public function m_pagament_view(){

    }
    public function m_pagament_post(){
        
    }

   public function historial_view()
   {
      return view('privat/historial');
   }

   /**
    * Muestra la página con selects encadenados para niveles/estructuras/asignaturas.
    */
   public function education_dropdowns()
   {
       helper('url'); // necesario para usar base_url() en la vista

       $db = \Config\Database::connect();
       $niveles = $db->table('niveles')->orderBy('id')->get()->getResult();
       return view('privat/education_dropdowns', ['niveles' => $niveles]);
   }

   /**
    * API JSON para devolver estructuras según nivel y/o padre.
    * Consulta opcional ?nivel=X&parent=Y
    */
   public function estructuras()
   {
       $db = \Config\Database::connect();
       $builder = $db->table('estructuras');
       $nivel = $this->request->getGet('nivel');
       $parent = $this->request->getGet('parent');
       if ($nivel !== null) {
           $builder->where('nivel_id', $nivel);
       }
       if ($parent !== null) {
           $builder->where('parent_id', $parent);
       }
       $estructuras = $builder->orderBy('nombre')->get()->getResult();
       return $this->response->setJSON($estructuras);
   }

   /**
    * API JSON para devolver asignaturas de una estructura.
    */
   public function asignaturas()
   {
       $db = \Config\Database::connect();
       $estructura = $this->request->getGet('estructura');
       if ($estructura === null) {
           return $this->fail('Parámetro estructura requerido', 400);
       }
       $asignaturas = $db->table('asignaturas')
           ->where('estructura_id', $estructura)
           ->orderBy('nombre')
           ->get()
           ->getResult();
       return $this->response->setJSON($asignaturas);
   }

   /**
    * Crea o actualiza una estructura (grado, familia, curso).
    */
   public function saveEstructura()
   {
       $data = $this->request->getJSON(true) ?? $this->request->getPost();
       if (!$data) {
           return $this->fail('Datos no válidos', 400);
       }

       $model = new EstructurasModel();

       $id = $data['id'] ?? null;
       $record = [
           'nombre'   => $data['nombre']   ?? '',
           'tipo'     => $data['tipo']     ?? '',
           'nivel_id' => $data['nivel_id'] ?? null,
           'parent_id'=> $data['parent_id']?? null,
       ];

       if (trim($record['nombre']) === '' || trim($record['tipo']) === '') {
           return $this->fail('Nombre y tipo son obligatorios', 400);
       }

       if ($id) {
           $model->update($id, $record);
       } else {
           $id = $model->insert($record);
       }

       $saved = $model->find($id);
       return $this->response->setJSON($saved);
   }

   /**
    * Elimina una estructura (y en cascada sus hijas/asignaturas).
    */
   public function deleteEstructura($id)
   {
       $model = new EstructurasModel();
       $model->delete($id);
       return $this->response->setJSON(['status' => 'ok']);
   }

   /**
    * Crea o actualiza una asignatura.
    */
   public function saveAsignatura()
   {
       $data = $this->request->getJSON(true) ?? $this->request->getPost();
       if (!$data) {
           return $this->fail('Datos no válidos', 400);
       }

       $nombre = $data['nombre'] ?? '';
       $estructuraId = $data['estructura_id'] ?? null;
       $horas = isset($data['horas_semanales']) ? (int)$data['horas_semanales'] : 0;

       if (trim($nombre) === '' || !$estructuraId) {
           return $this->fail('Nombre y estructura_id son obligatorios', 400);
       }

       $db = \Config\Database::connect();
       $builder = $db->table('asignaturas');
       $id = $data['id'] ?? null;

       $record = [
           'nombre'          => $nombre,
           'horas_semanales' => $horas,
           'estructura_id'   => $estructuraId,
       ];

       if ($id) {
           $builder->where('id', $id)->update($record);
       } else {
           $builder->insert($record);
           $id = $db->insertID();
       }

       $asig = $db->table('asignaturas')->where('id', $id)->get()->getRowArray();
       return $this->response->setJSON($asig);
   }

   /**
    * Elimina una asignatura.
    */
   public function deleteAsignatura($id)
   {
       $db = \Config\Database::connect();
       $db->table('asignaturas')->where('id', $id)->delete();
       return $this->response->setJSON(['status' => 'ok']);
   }

   /**
    * Busca por nombre en estructuras y asignaturas y devuelve las rutas
    * completas de los elementos encontrados (array de segmentos).
    */
   public function buscar()
   {
       $q = $this->request->getGet('q');
       if (!$q) {
           return $this->response->setJSON([]);
       }
       $db = \Config\Database::connect();
       $results = [];

       // estructuras coincidentes
       $estrs = $db->table('estructuras')
           ->like('nombre', $q)
           ->get()
           ->getResult();
       foreach ($estrs as $e) {
           $results[] = $this->buildPath($e);
       }

       // asignaturas coincidentes
       $asigs = $db->table('asignaturas')
           ->like('nombre', $q)
           ->get()
           ->getResult();
       foreach ($asigs as $a) {
           $estructura = $db->table('estructuras')->where('id', $a->estructura_id)->get()->getRow();
           if ($estructura) {
               $path = $this->buildPath($estructura);
               $path[] = (object)[
                   'id' => $a->id,
                   'nombre' => $a->nombre,
                   'tipo' => 'asignatura',
               ];
               $results[] = $path;
           }
       }

       return $this->response->setJSON($results);
   }

   /**
    * Construye un array de segmentos desde el nivel hasta el nodo dado.
    */
   private function buildPath($node)
   {
       $db = \Config\Database::connect();
       $path = [];
       // ascendiendo hasta llegar al nivel
       $current = $node;
       while ($current) {
           $path[] = (object)[
               'id' => $current->id,
               'nombre' => $current->nombre,
               'tipo' => $current->tipo,
           ];
           if ($current->parent_id) {
               $current = $db->table('estructuras')->where('id', $current->parent_id)->get()->getRow();
           } else {
               // reached top; agregar nivel
               $nivel = $db->table('niveles')->where('id', $current->nivel_id)->get()->getRow();
               if ($nivel) {
                   array_unshift($path, (object)[
                       'id' => $nivel->id,
                       'nombre' => $nivel->nombre,
                       'tipo' => 'nivel',
                   ]);
               }
               break;
           }
       }
       return $path;
   }
}
