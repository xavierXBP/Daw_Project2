<?php

namespace App\Libraries;

use App\Models\AlumneModel;
use App\Models\EstructurasModel;
use App\Models\ExpedienteModel;

class PdfGenerator
{
    private $alumneModel;
    private $estructurasModel;
    private $expedienteModel;

    public function __construct()
    {
        $this->alumneModel = new AlumneModel();
        $this->estructurasModel = new EstructurasModel();
        $this->expedienteModel = new ExpedienteModel();
    }

    /**
     * Generate matricula PDF for a student
     */
    public function generateMatriculaPdf(int $alumnoId, string $expedienteNumber): string
    {
        $alumno = $this->alumneModel->find($alumnoId);
        if (!$alumno) {
            throw new \RuntimeException('Alumno no encontrado');
        }

        $curso = $this->estructurasModel->find($alumno['estructura_curso_id']);
        $cicleNombre = 'Desconocido';
        
        if ($curso && $curso['parent_id']) {
            $cicle = $this->estructurasModel->find($curso['parent_id']);
            if ($cicle) {
                $cicleNombre = $cicle['nombre'];
            }
        }

        // Get student's subjects with prices
        $db = \Config\Database::connect();
        $asignaturas = $db->table('asignaturas')
            ->where('estructura_id', $alumno['estructura_curso_id'])
            ->get()
            ->getResultArray();

        // Calculate total price
        $totalPrecio = 0;
        foreach ($asignaturas as &$asignatura) {
            $totalPrecio += (float) $asignatura['precio'];
        }

        // Create PDF content
        $html = $this->generatePdfHtml([
            'alumno' => $alumno,
            'curso' => $curso,
            'ciclo' => $cicleNombre,
            'asignaturas' => $asignaturas,
            'total_precio' => $totalPrecio,
            'expediente' => $expedienteNumber,
            'fecha_generacion' => date('d/m/Y H:i:s')
        ]);

        // Generate PDF file
        $filename = "matricula_{$expedienteNumber}.pdf";
        $filepath = WRITEPATH . "expedientes/{$expedienteNumber}/" . date('Y') . "-" . (date('Y') + 1) . "/{$filename}";

        // Ensure directory exists
        $this->ensureDirectoryExists(dirname($filepath));

        // Use DOMPDF or similar library (for now, save as HTML)
        file_put_contents($filepath, $html);

        return $filepath;
    }

    /**
     * Generate HTML content for PDF
     */
    private function generatePdfHtml(array $data): string
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Matrícula - ' . $data['expediente'] . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
        .title { font-size: 24px; font-weight: bold; }
        .subtitle { font-size: 16px; color: #666; }
        .section { margin-bottom: 25px; }
        .section-title { font-size: 18px; font-weight: bold; border-bottom: 1px solid #ccc; padding-bottom: 5px; margin-bottom: 15px; }
        .info-row { margin-bottom: 8px; }
        .info-label { font-weight: bold; display: inline-block; width: 150px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; }
        .total-row { font-weight: bold; background-color: #f9f9f9; }
        .footer { margin-top: 40px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>';

        // Header
        $html .= '<div class="header">
    <div class="title">MATRÍCULA ACADÉMICA</div>
    <div class="subtitle">Expediente N°: ' . $data['expediente'] . '</div>
    <div class="subtitle">Año Académico: ' . $data['any_academico'] ?? '2025-2026' . '</div>
</div>';

        // Student Information
        $html .= '<div class="section">
    <div class="section-title">Datos del Alumno</div>
    <div class="info-row"><span class="info-label">Nombre:</span> ' . htmlspecialchars($data['alumno']['apellidos'] . ', ' . $data['alumno']['nombre']) . '</div>
    <div class="info-row"><span class="info-label">DNI:</span> ' . htmlspecialchars($data['alumno']['dni']) . '</div>
    <div class="info-row"><span class="info-label">Fecha Nacimiento:</span> ' . date('d/m/Y', strtotime($data['alumno']['fecha_nacimiento'])) . '</div>
    <div class="info-row"><span class="info-label">Dirección:</span> ' . htmlspecialchars($data['alumno']['direccion']) . '</div>
    <div class="info-row"><span class="info-label">Municipio:</span> ' . htmlspecialchars($data['alumno']['municipio']) . '</div>
    <div class="info-row"><span class="info-label">C.P.:</span> ' . htmlspecialchars($data['alumno']['codigo_postal']) . '</div>
    <div class="info-row"><span class="info-label">Teléfono:</span> ' . htmlspecialchars($data['alumno']['telefono_alumno']) . '</div>
    <div class="info-row"><span class="info-label">Email:</span> ' . htmlspecialchars($data['alumno']['email_alumno']) . '</div>
</div>';

        // Academic Information
        $html .= '<div class="section">
    <div class="section-title">Información Académica</div>
    <div class="info-row"><span class="info-label">Ciclo:</span> ' . htmlspecialchars($data['ciclo']) . '</div>
    <div class="info-row"><span class="info-label">Curso:</span> ' . htmlspecialchars($data['curso']['nombre'] ?? 'N/A') . '</div>
</div>';

        // Subjects with prices
        if (!empty($data['asignaturas'])) {
            $html .= '<div class="section">
    <div class="section-title">Asignaturas Matriculadas</div>
    <table class="table">
        <thead>
            <tr>
                <th>Asignatura</th>
                <th>Horas Semanales</th>
                <th>Precio (€)</th>
            </tr>
        </thead>
        <tbody>';
            
            foreach ($data['asignaturas'] as $asignatura) {
                $html .= '<tr>
                    <td>' . htmlspecialchars($asignatura['nombre']) . '</td>
                    <td>' . $asignatura['horas_semanales'] . '</td>
                    <td>' . number_format($asignatura['precio'], 2) . '</td>
                </tr>';
            }
            
            $html .= '<tr class="total-row">
                <td colspan="2"><strong>TOTAL</strong></td>
                <td><strong>' . number_format($data['total_precio'], 2) . ' €</strong></td>
            </tr>
        </tbody>
    </table>
</div>';
        }

        // Footer
        $html .= '<div class="footer">
    <p>Documento generado el ' . $data['fecha_generacion'] . '</p>
    <p>Sistema de Gestión Académica - CAPARRELLA</p>
</div>';

        $html .= '</body></html>';

        return $html;
    }

    /**
     * Ensure directory exists
     */
    private function ensureDirectoryExists(string $directory): void
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }
}
