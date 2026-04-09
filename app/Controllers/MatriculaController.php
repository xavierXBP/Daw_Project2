<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use CodeIgniter\API\ResponseTrait;
use App\Models\AlumneModel;
use App\Models\EstructurasModel;
use App\Models\MensajeModel;
use App\Models\ValidationLockModel;
use App\Models\ExpedienteModel;
use App\Libraries\IdObfuscator;
use App\Libraries\PdfGenerator;

class MatriculaController extends BaseController
{
    use ResponseTrait;
    
    private $mensajeModel;
    private $validationLockModel;
    private $expedienteModel;
    
    public function __construct()
    {
        $this->mensajeModel = new MensajeModel();
        $this->validationLockModel = new ValidationLockModel();
        $this->expedienteModel = new ExpedienteModel();
    }
    
    /**
     * Helper para obtener color de badge según tipo de mensaje
     */
    public function getBadgeColor($tipo)
    {
        $colors = [
            'info' => 'info',
            'warning' => 'warning',
            'error' => 'danger',
            'success' => 'success'
        ];
        return $colors[$tipo] ?? 'secondary';
    }

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

      // Obtener filtros de la sesión
      $sessionFilters = session()->get('validadosFilters') ?? [];

      // Obtener alumnos con su curso aplicando filtros
      $builder = $db->table('alumne a')
          ->select('a.id_alumne as id, a.nombre, a.apellidos, a.dni, a.estado, e.nombre as curso')
          ->join('estructuras e', 'e.id = a.estructura_curso_id', 'left');

      // Aplicar filtro de estado si existe
      if (!empty($sessionFilters['estado']) && $sessionFilters['estado'] !== 'ALL') {
          $estadoMap = [
              'PV' => 'Para validar',
              'V'  => 'Validado',
              'E'  => 'En revisión',
              'AN' => 'Anulado',
          ];
          if (isset($estadoMap[$sessionFilters['estado']])) {
              $builder->where('a.estado', $estadoMap[$sessionFilters['estado']]);
          }
      }

      // Aplicar filtro de curso si existe
      if (!empty($sessionFilters['curso'])) {
          $builder->where('e.nombre', $sessionFilters['curso']);
      }

      // Aplicar filtro de búsqueda si existe
      if (!empty($sessionFilters['q'])) {
          $q = $sessionFilters['q'];
          $builder->groupStart()
              ->like('a.nombre', $q)
              ->orLike('a.apellidos', $q)
              ->orLike('a.dni', $q)
              ->groupEnd();
      }

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
          } elseif ($estado === 'para validar') {
              $alumno['estado_codigo'] = 'PV';
              $alumno['estado_clase'] = 'bg-info text-dark';
              $alumno['estado_texto'] = 'Para validar';
          } else {
              // En revisión por defecto
              $alumno['estado_codigo'] = 'E';
              $alumno['estado_clase'] = 'bg-warning text-dark';
              $alumno['estado_texto'] = 'En revisión';
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
          ['codigo' => 'PV', 'clase' => 'outline-info', 'texto' => 'PV (Para validar)'],
          ['codigo' => 'V', 'clase' => 'outline-success', 'texto' => 'V (Validado)'],
          ['codigo' => 'E', 'clase' => 'outline-warning', 'texto' => 'E (En revisión)'],
          ['codigo' => 'AN', 'clase' => 'outline-secondary', 'texto' => 'AN (Anulado)'],
          ['codigo' => 'ALL', 'clase' => 'outline-dark', 'texto' => 'TODOS'],
      ];

      $mensajeModel = new MensajeModel();
      $mensajeModel->initializeDefaultMessages();
      
      $mensajes = $mensajeModel->getActiveMessages();
      $missatges_rapids = array_column($mensajeModel->getQuickMessages(), 'mensaje');

      return view('privat/validados', [
          'alumnos' => $alumnos,
          'cursos' => $cursos,
          'filtros_estado' => $filtros_estado,
          'missatges_rapids' => $missatges_rapids,
          'mensajes' => $mensajes,
          'current_filters' => $sessionFilters,
      ]);
      //rutas post para recivir de validar el id de alumno y el mensaje para enviar al alumno
  }
   public function validados_view_2($id): string{
      // Si más adelante quieres manejar POST con ID concreto,
      // puedes implementar la lógica aquí. De momento no se usa.
      return $this->validados_view();
   }

   /**
    * Guarda filtros en la sesión vía AJAX
    */
   public function saveFilters()
   {
       if (!$this->request->isAJAX()) {
           return $this->response->setJSON(['status' => 'error', 'message' => 'Ajax required'], 400);
       }

       $filters = $this->request->getJSON(true);
       if (!$filters) {
           return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid JSON'], 400);
       }

       // Guardar filtros en sesión
       session()->set('validadosFilters', [
           'q' => $filters['q'] ?? '',
           'curso' => $filters['curso'] ?? '',
           'estado' => $filters['estado'] ?? '',
       ]);

       return $this->response->setJSON(['status' => 'success']);
   }

      public function validar_view($obfuscatedId = null){
          // Manejar both POST (sin ID en URL) y GET (con ID en URL)
          if ($obfuscatedId === null) {
              // Viene de POST - obtener ID desde POST
              $obfuscatedId = $this->request->getPost('student_id');
              if (!$obfuscatedId) {
                  throw new \CodeIgniter\Exceptions\PageNotFoundException('ID de alumno requerido');
              }
          }
          
          try {
              $id = IdObfuscator::extractIdFromUrl($obfuscatedId);
          } catch (\Exception $e) {
              throw new \CodeIgniter\Exceptions\PageNotFoundException('URL inválida');
          }
          $alumneModel = new AlumneModel();
          $estructurasModel = new EstructurasModel();

          // Filtros que llegan desde sessionStorage (pasados vía JavaScript)
          // Intentar obtener desde sesión primero, luego desde URL como fallback
          $sessionFilters = session()->get('validadosFilters');
          $filters = [
              'q'      => $sessionFilters['q'] ?? $this->request->getGet('q'),
              'curso'  => $sessionFilters['curso'] ?? $this->request->getGet('curso'),
              'estado' => $sessionFilters['estado'] ?? $this->request->getGet('estado'),
          ];

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

          $estado = $alumne['estado'] ?? 'Para validar';
          $estadoLower = strtolower($estado);
          if ($estadoLower === 'anulado') {
              $estatClase = 'bg-danger text-white';
          } elseif ($estadoLower === 'validado') {
              $estatClase = 'bg-success text-white';
          } elseif ($estadoLower === 'para validar') {
              $estatClase = 'bg-info text-dark';
          } else {
              $estatClase = 'bg-warning text-dark';
          }

          // Check if student is locked by another user
          if ($this->validationLockModel->isStudentLocked($id)) {
              $lock = $this->validationLockModel->getActiveLock($id);
              $data['lock_warning'] = sprintf(
                  'Este alumno está siendo validado actualmente por %s',
                  $lock['usuario_nombre'] ?? 'otro usuario'
              );
          } else {
              // Lock the student for current user
              $this->validationLockModel->lockStudent($id, session()->get('user_id'), session()->get('user_name') ?? 'Usuario');
          }
          $data['matricula'] = [
              'any_escolar' => '2025 / 2026',
              'curs' => $curso['nombre'] ?? 'Desconocido',
              'cicle' => $cicleNombre,
              'estat' => $estado,
              'estat_clase' => $estatClase,
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
              'filters' => $filters,
          ];

          $mensajeModel = new MensajeModel();
          $data['missatges_rapids'] = array_column($mensajeModel->getQuickMessages(), 'mensaje');
          $data['mensajes'] = $mensajeModel->getActiveMessages();
          $data['obfuscated_id'] = $obfuscatedId;

          return view('privat/validar', $data);
      }
      
      public function aprobarAlumno($obfuscatedId = null)
      {
          // Manejar both POST (sin ID en URL) y GET (con ID en URL)
          if ($obfuscatedId === null) {
              // Viene de POST - obtener ID desde POST
              $obfuscatedId = $this->request->getPost('student_id');
              if (!$obfuscatedId) {
                  throw new \CodeIgniter\Exceptions\PageNotFoundException('ID de alumno requerido');
              }
          }
          
          try {
              $id = IdObfuscator::extractIdFromUrl($obfuscatedId);
          } catch (\Exception $e) {
              throw new \CodeIgniter\Exceptions\PageNotFoundException('URL inválida');
          }
          
          $alumneModel = new AlumneModel();
          // Los filtros ahora vienen de la sesión, no del POST
          $filters = session()->get('validadosFilters') ?? [];

          $alumne = $alumneModel->find($id);
          if ($alumne) {
              $alumneModel->update($id, ['estado' => 'Validado']);
              
              // Generate expediente and PDF
              $this->generateExpediente($id);
              
              // Create student folder in writable directory
              $this->createStudentFolder($alumne);
          }
          
          // Unlock the student
          $this->validationLockModel->unlockStudent($id);

          return $this->redirectToNextParaValidar($id, $filters, $obfuscatedId);
      }

      public function anularAlumno($obfuscatedId = null)
      {
          // Manejar both POST (sin ID en URL) y GET (con ID en URL)
          if ($obfuscatedId === null) {
              // Viene de POST - obtener ID desde POST
              $obfuscatedId = $this->request->getPost('student_id');
              if (!$obfuscatedId) {
                  throw new \CodeIgniter\Exceptions\PageNotFoundException('ID de alumno requerido');
              }
          }
          
          try {
              $id = IdObfuscator::extractIdFromUrl($obfuscatedId);
          } catch (\Exception $e) {
              throw new \CodeIgniter\Exceptions\PageNotFoundException('URL inválida');
          }
          
          $alumneModel = new AlumneModel();
          // Los filtros ahora vienen de la sesión, no del POST
          $filters = session()->get('validadosFilters') ?? [];

          $mensaje = $this->request->getPost('mensaje'); // no se usa aún para enviar email

          $alumne = $alumneModel->find($id);
          if ($alumne) {
              $alumneModel->update($id, ['estado' => 'Anulado']);
          }
          
          // Unlock the student
          $this->validationLockModel->unlockStudent($id);

          return $this->redirectToNextParaValidar($id, $filters, $obfuscatedId);
      }

      private function redirectToNextParaValidar(int $currentId, array $filters, string $currentObfuscatedId = null)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('alumne a')
            ->select('a.id_alumne as id, a.nombre, a.apellidos, a.dni, e.nombre as curso')
            ->join('estructuras e', 'e.id = a.estructura_curso_id', 'left')
            ->where('a.id_alumne >', $currentId)
            ->orderBy('a.id_alumne', 'ASC');

        // Obtener filtros de la sesión (los más actualizados)
        $sessionFilters = session()->get('validadosFilters');
        $effectiveFilters = array_merge($filters, $sessionFilters ?? []);

        // Aplicar filtro de estado solo si viene de la vista (PV, V, E, AN)
        if (!empty($effectiveFilters['estado']) && $effectiveFilters['estado'] !== 'ALL') {
            $estadoMap = [
                'PV' => 'Para validar',
                'V'  => 'Validado',
                'E'  => 'En revisión',
                'AN' => 'Anulado',
            ];
            if (isset($estadoMap[$effectiveFilters['estado']])) {
                $builder->where('a.estado', $estadoMap[$effectiveFilters['estado']]);
            }
        }

        if (!empty($effectiveFilters['curso'])) {
            $builder->where('e.nombre', $effectiveFilters['curso']);
        }

        if (!empty($effectiveFilters['q'])) {
            $q = $effectiveFilters['q'];
            $builder->groupStart()
                ->like('a.nombre', $q)
                ->orLike('a.apellidos', $q)
                ->orLike('a.dni', $q)
                ->groupEnd();
        }

        $next = $builder->get()->getRow();

        if ($next) {
            $nextObfuscatedId = IdObfuscator::generateUrlSegment($next->id);
            // Ya no necesitamos pasar parámetros por URL, usamos sesión
            return redirect()->to(base_url('privat/validar/' . $nextObfuscatedId));
        }

        // Si no hay siguiente, volver a la lista (los filtros ya están en sesión)
        return redirect()->to(base_url('privat/validados'));
    }

    /**
     * Create a folder for the student in the writable directory and generate PDF
     */
    private function createStudentFolder($alumne)
    {
        // Clean and format the student name and DNI for folder name
        $nombre = trim($alumne['nombre'] ?? '');
        $apellidos = trim($alumne['apellidos'] ?? '');
        $dni = trim($alumne['dni'] ?? '');
        
        // Remove special characters and replace spaces with underscores
        $nombreLimpio = preg_replace('/[^a-zA-Z0-9]/', '_', $nombre);
        $apellidosLimpio = preg_replace('/[^a-zA-Z0-9]/', '_', $apellidos);
        $dniLimpio = preg_replace('/[^a-zA-Z0-9]/', '_', $dni);
        
        // Create folder name: Nombre_Apellidos_DNI
        $folderName = $nombreLimpio . '_' . $apellidosLimpio . '_' . $dniLimpio;
        
        // Path to writable directory
        $writablePath = WRITEPATH . 'expedientes' . DIRECTORY_SEPARATOR . $folderName;
        
        // Create directory if it doesn't exist
        if (!is_dir($writablePath)) {
            if (mkdir($writablePath, 0755, true)) {
                // Create an index file to prevent directory listing
                $indexPath = $writablePath . DIRECTORY_SEPARATOR . 'index.html';
                file_put_contents($indexPath, '<html><head><title>403 Forbidden</title></head><body><h1>Forbidden</h1></body></html>');
                
                // Log the folder creation
                log_message('info', "Student folder created: {$folderName} for student ID: {$alumne['id_alumne']}");
            } else {
                log_message('error', "Failed to create student folder: {$folderName} for student ID: {$alumne['id_alumne']}");
                return false;
            }
        } else {
            // Log that folder already exists
            log_message('info', "Student folder already exists: {$folderName} for student ID: {$alumne['id_alumne']}");
        }
        
        // Generate PDF with student information and save to folder
        $this->generateStudentInfoPDF($alumne, $writablePath);
        
        return true;
    }

    /**
     * Generate PDF with complete student information
     */
    private function generateStudentInfoPDF($alumne, $folderPath)
    {
        try {
            // Get additional student information
            $estructurasModel = new EstructurasModel();
            $curso = $estructurasModel->find($alumne['estructura_curso_id']);
            
            // Create filename with timestamp
            $timestamp = date('Y-m-d_H-i-s');
            $pdfFilename = "expediente_{$timestamp}.pdf";
            $pdfPath = $folderPath . DIRECTORY_SEPARATOR . $pdfFilename;
            
            // Start HTML content for PDF
            $html = $this->generateStudentInfoHTML($alumne, $curso);
            
            // Use DOMPDF to generate PDF
            $options = new \Dompdf\Options();
            $options->set('defaultFont', 'Arial');
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            // Save PDF to file
            file_put_contents($pdfPath, $dompdf->output());
            
            log_message('info', "Student info PDF generated: {$pdfFilename} for student ID: {$alumne['id_alumne']}");
            
        } catch (\Exception $e) {
            log_message('error', "Failed to generate PDF for student ID: {$alumne['id_alumne']} - " . $e->getMessage());
        }
    }

    /**
     * Generate HTML content for student information PDF
     */
    private function generateStudentInfoHTML($alumne, $curso)
    {
        $html = '
        <style>
            .header { text-align: center; margin-bottom: 30px; }
            .section { margin-bottom: 20px; }
            .field { margin-bottom: 10px; }
            .label { font-weight: bold; width: 150px; display: inline-block; }
            .value { display: inline-block; }
            table { border-collapse: collapse; width: 100%; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
        </style>
        
        <div class="header">
            <h1>EXPEDIENTE DE ALUMNO</h1>
            <h2>CURSO ACADÉMICO ' . date('Y') . ' - ' . (date('Y') + 1) . '</h2>
            <p>Fecha de generación: ' . date('d/m/Y H:i:s') . '</p>
        </div>

        <div class="section">
            <h3>DATOS PERSONALES</h3>
            <div class="field">
                <span class="label">Nombre completo:</span>
                <span class="value">' . esc($alumne['apellidos'] . ', ' . $alumne['nombre']) . '</span>
            </div>
            <div class="field">
                <span class="label">DNI:</span>
                <span class="value">' . esc($alumne['dni']) . '</span>
            </div>
            <div class="field">
                <span class="label">Fecha de nacimiento:</span>
                <span class="value">' . date('d/m/Y', strtotime($alumne['fecha_nacimiento'])) . '</span>
            </div>
            <div class="field">
                <span class="label">Lugar de nacimiento:</span>
                <span class="value">' . esc($alumne['lugar_nacimiento']) . '</span>
            </div>
        </div>

        <div class="section">
            <h3>DATOS DE CONTACTO</h3>
            <div class="field">
                <span class="label">Dirección:</span>
                <span class="value">' . esc($alumne['direccion']) . '</span>
            </div>
            <div class="field">
                <span class="label">Municipio:</span>
                <span class="value">' . esc($alumne['municipio']) . '</span>
            </div>
            <div class="field">
                <span class="label">Código Postal:</span>
                <span class="value">' . esc($alumne['codigo_postal']) . '</span>
            </div>
            <div class="field">
                <span class="label">Teléfono:</span>
                <span class="value">' . esc($alumne['telefono_alumno']) . '</span>
            </div>
            <div class="field">
                <span class="label">Email:</span>
                <span class="value">' . esc($alumne['email_alumno']) . '</span>
            </div>
        </div>

        <div class="section">
            <h3>DATOS ACADÉMICOS</h3>
            <div class="field">
                <span class="label">Curso:</span>
                <span class="value">' . esc($curso['nombre'] ?? 'No asignado') . '</span>
            </div>
            <div class="field">
                <span class="label">Estado actual:</span>
                <span class="value">' . esc($alumne['estado']) . '</span>
            </div>
            <div class="field">
                <span class="label">Fecha de validación:</span>
                <span class="value">' . date('d/m/Y H:i:s') . '</span>
            </div>
        </div>

        <div class="section">
            <h3>OBSERVACIONES</h3>
            <p>Expediente generado automáticamente en el proceso de validación de matrícula.</p>
            <p>Estado de la matrícula: ' . esc($alumne['estado']) . '</p>
            <p>ID de alumno en sistema: ' . $alumne['id_alumne'] . '</p>
        </div>';

        return $html;
    } 
     public function mensatges_view(){
          // Cargar mensajes desde la base de datos
          $this->mensajeModel->initializeDefaultMessages();
          $mensajes = $this->mensajeModel->findAll();
          
          return view('privat/mensatges', ['mensajes' => $mensajes]); 
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
    * API JSON para devolver todas las asignaturas.
    */
   public function getAllAsignaturas()
   {
       $db = \Config\Database::connect();
       $asignaturas = $db->table('asignaturas')
           ->select('id, nombre, horas_semanales, precio')
           ->orderBy('nombre')
           ->get()
           ->getResult();
       return $this->response->setJSON($asignaturas);
   }

   /**
    * API JSON para devolver todas las optativas.
    */
   public function getAllOptativas()
   {
       $db = \Config\Database::connect();
       $optativas = $db->table('optativas')
           ->select('id, nombre, horas_semanales, precio')
           ->orderBy('nombre')
           ->get()
           ->getResult();
       return $this->response->setJSON($optativas);
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
    * API JSON para devolver optativas de una estructura.
    */
   public function optativas()
   {
       $db = \Config\Database::connect();
       $estructura = $this->request->getGet('estructura');
       if ($estructura === null) {
           return $this->fail('Parámetro estructura requerido', 400);
       }
       $optativas = $db->table('optativas')
           ->where('estructura_id', $estructura)
           ->orderBy('nombre')
           ->get()
           ->getResult();
       return $this->response->setJSON($optativas);
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
       $precio = isset($data['precio']) ? (float)$data['precio'] : 0.00;
       $horas = isset($data['horas_semanales']) ? (int)$data['horas_semanales'] : 0;

       if (trim($nombre) === '' || !$estructuraId) {
           return $this->fail('Nombre y estructura_id son obligatorios', 400);
       }

       if ($precio < 0) {
           return $this->fail('El precio no puede ser negativo', 400);
       }

       $db = \Config\Database::connect();
       $builder = $db->table('asignaturas');
       $id = $data['id'] ?? null;

       if ($id) {
           // Update existing record
           $record = [
               'nombre'          => $nombre,
               'precio'          => $precio,
               'horas_semanales' => $horas,
               'estructura_id'   => $estructuraId,
               'updated_at'      => date('Y-m-d H:i:s'),
           ];
           $builder->where('id', $id)->update($record);
       } else {
           // Insert new record
           $record = [
               'nombre'          => $nombre,
               'precio'          => $precio,
               'horas_semanales' => $horas,
               'estructura_id'   => $estructuraId,
               'created_at'      => date('Y-m-d H:i:s'),
               'updated_at'      => date('Y-m-d H:i:s'),
           ];
           $builder->insert($record);
           $id = $db->insertID();
       }

       $asignatura = $db->table('asignaturas')->where('id', $id)->get()->getRowArray();
       return $this->response->setJSON($asignatura);
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
    * Crea o actualiza una optativa.
    */
   public function saveOptativa()
   {
       $data = $this->request->getJSON(true) ?? $this->request->getPost();
       if (!$data) {
           return $this->fail('Datos no válidos', 400);
       }

       $nombre = $data['nombre'] ?? '';
       $estructuraId = $data['estructura_id'] ?? null;
       $precio = isset($data['precio']) ? (float)$data['precio'] : 0.00;
       $horas = isset($data['horas_semanales']) ? (int)$data['horas_semanales'] : 0;

       if (trim($nombre) === '' || !$estructuraId) {
           return $this->fail('Nombre y estructura_id son obligatorios', 400);
       }

       if ($precio < 0) {
           return $this->fail('El precio no puede ser negativo', 400);
       }

       $db = \Config\Database::connect();
       $builder = $db->table('optativas');
       $id = $data['id'] ?? null;

       $record = [
           'nombre'          => $nombre,
           'precio'          => $precio,
           'horas_semanales' => $horas,
           'estructura_id'   => $estructuraId,
           'updated_at'      => date('Y-m-d H:i:s'),
       ];

       if ($id) {
           $builder->where('id', $id)->update($record);
       } else {
           $record['created_at'] = date('Y-m-d H:i:s');
           $builder->insert($record);
           $id = $db->insertID();
       }

       $optativa = $db->table('optativas')->where('id', $id)->get()->getRowArray();
       return $this->response->setJSON($optativa);
   }

   /**
    * Elimina una optativa.
    */
   public function deleteOptativa($id)
   {
       $db = \Config\Database::connect();
       $db->table('optativas')->where('id', $id)->delete();
       return $this->response->setJSON(['status' => 'ok']);
   }

   /**
    * Crea un nuevo nivel educativo.
    */
   public function saveNivel()
   {
       try {
           $data = $this->request->getJSON(true) ?? $this->request->getPost();
           if (!$data || !isset($data['nombre'])) {
               return $this->fail('Nombre del nivel es requerido', 400);
           }

           $db = \Config\Database::connect();
           $builder = $db->table('niveles');
           
           $nivelData = [
               'nombre' => trim($data['nombre']),
               'created_at' => date('Y-m-d H:i:s'),
               'updated_at' => date('Y-m-d H:i:s')
           ];

           $builder->insert($nivelData);
           
           return $this->respondCreated([
               'message' => 'Nivel creado correctamente',
               'nombre' => $nivelData['nombre']
           ]);
           
       } catch (\Exception $e) {
           log_message('error', 'Error creating nivel: ' . $e->getMessage());
           return $this->fail('Error al crear el nivel: ' . $e->getMessage(), 500);
       }
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
      
      /**
       * Generate expediente and PDF for validated student
       */
      private function generateExpediente(int $alumnoId): void
      {
          try {
              // Create expediente record
              $expediente = $this->expedienteModel->createExpediente($alumnoId);
              
              // Generate PDF
              $pdfGenerator = new PdfGenerator();
              $pdfPath = $pdfGenerator->generateMatriculaPdf($alumnoId, $expediente['numero_expediente']);
              
              // Update expediente with PDF path
              $this->expedienteModel->updatePdfPath($expediente['id'], $pdfPath);
              
              log_message('info', 'Expediente generated: ' . $expediente['numero_expediente'] . ' for student: ' . $alumnoId);
          } catch (\Exception $e) {
              log_message('error', 'Error generating expediente for student ' . $alumnoId . ': ' . $e->getMessage());
          }
      }
      
      /**
       * API endpoint to get validation locks
       */
      public function getValidationLocks()
      {
          $locks = $this->validationLockModel->getActiveLocks();
          return $this->response->setJSON($locks);
      }
      
      /**
       * API endpoint to unlock student
       */
      public function unlockStudent($obfuscatedId)
      {
          try {
              $id = IdObfuscator::extractIdFromUrl($obfuscatedId);
              $this->validationLockModel->unlockStudent($id);
              return $this->response->setJSON(['status' => 'success']);
          } catch (\Exception $e) {
              return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()], 400);
          }
      }
}
