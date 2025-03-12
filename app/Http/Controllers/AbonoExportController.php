<?php

namespace App\Http\Controllers;

use App\Models\Abono;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AbonoExportController extends Controller
{
    public function export(Request $request)
    {
        $abonos = Abono::query();
    
        // Filtrar siempre por la fecha de hoy
        $abonos->whereDate('fecha_abono', Carbon::today());
    
        // Aplicar el filtro de acuerdo al tab seleccionado
        if ($request->input('activeTab') === 'todos') {
            // El filtro ya está aplicado arriba, no es necesario agregar nada más
        }
    
        if ($request->input('activeTab') === 'no_abonaron') {
            $abonos->whereNotExists(function ($subQuery) {
                $subQuery->selectRaw(1)
                    ->from('abonos')
                    ->whereColumn('creditos.id', 'abonos.credito_id')
                    ->whereDate('abonos.fecha_abono', Carbon::today());
            });
        }
    
        // Otros filtros adicionales si es necesario...
    
        // Obtener los resultados filtrados
        $abonos = $abonos->get();
    
        // Continuar con la exportación
        // Crear una nueva instancia de Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        // Agregar la cabecera con estilos
        $sheet->setCellValue('A1', '#')
              ->setCellValue('B1', 'Registrado por')
              ->setCellValue('C1', 'Cliente')
              ->setCellValue('D1', 'Monto del Crédito')
              ->setCellValue('E1', 'Monto Abonado')
              ->setCellValue('F1', 'Fecha del Abono')
              ->setCellValue('G1', 'Fecha de Creación');
    
        // Estilo para la cabecera (negrita y color de fondo)
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00'); // Amarillo
        $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
        // Llenar los datos
        $row = 2;
        foreach ($abonos as $abono) {
            $sheet->setCellValue('A' . $row, $abono->id)
                  ->setCellValue('B' . $row, $abono->user->name)
                  ->setCellValue('C' . $row, $abono->cliente->full_name)
                  ->setCellValue('D' . $row, $abono->credito->monto_total)
                  ->setCellValue('E' . $row, $abono->monto_abono)
                  ->setCellValue('F' . $row, Carbon::parse($abono->fecha_abono)->format('Y-m-d'))
                  ->setCellValue('G' . $row, $abono->created_at->format('Y-m-d H:i:s'));
            $row++;
        }
    
        // Ajustar el ancho de las celdas automáticamente
        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    
        // Crear el nombre del archivo
        $filename = 'abonos_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx';
    
        // Definir la ruta completa para el archivo
        $exportPath = storage_path('app/exports');
        $filePath = $exportPath . DIRECTORY_SEPARATOR . $filename;
    
        // Escribir el archivo Excel
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
    
        // Redirigir al usuario a la descarga del archivo
        return response()->download($filePath);
    }

    public function exportSemana(Request $request)
    {
        $abonos = Abono::query();
        
        // Filtrar abonos de la semana actual (de lunes a domingo)
        $startOfWeek = Carbon::now()->startOfWeek();  // Inicio de la semana (lunes)
        $endOfWeek = Carbon::now()->endOfWeek();      // Fin de la semana (domingo)
        
        $abonos->whereBetween('fecha_abono', [$startOfWeek, $endOfWeek]);
        
        // Aplicar el filtro de acuerdo al tab seleccionado
        if ($request->input('activeTab') === 'todos') {
            // El filtro ya está aplicado arriba, no es necesario agregar nada más
        }
    
        if ($request->input('activeTab') === 'no_abonaron') {
            $abonos->whereNotExists(function ($subQuery) {
                $subQuery->selectRaw(1)
                    ->from('abonos')
                    ->whereColumn('creditos.id', 'abonos.credito_id')
                    ->whereBetween('abonos.fecha_abono', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            });
        }
    
        // Obtener los resultados filtrados
        $abonos = $abonos->get();
        
        // Continuar con la exportación
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        // Agregar la cabecera con estilos
        $sheet->setCellValue('A1', '#')
              ->setCellValue('B1', 'Registrado por')
              ->setCellValue('C1', 'Cliente')
              ->setCellValue('D1', 'Monto del Crédito')
              ->setCellValue('E1', 'Monto Abonado')
              ->setCellValue('F1', 'Fecha del Abono')
              ->setCellValue('G1', 'Fecha de Creación');
    
        // Estilo para la cabecera (negrita y color de fondo)
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00'); // Amarillo
        $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
        // Llenar los datos
        $row = 2;
        foreach ($abonos as $abono) {
            $sheet->setCellValue('A' . $row, $abono->id)
                  ->setCellValue('B' . $row, $abono->user->name)
                  ->setCellValue('C' . $row, $abono->cliente->full_name)
                  ->setCellValue('D' . $row, $abono->credito->monto_total)
                  ->setCellValue('E' . $row, $abono->monto_abono)
                  ->setCellValue('F' . $row, Carbon::parse($abono->fecha_abono)->format('Y-m-d'))
                  ->setCellValue('G' . $row, $abono->created_at->format('Y-m-d H:i:s'));
            $row++;
        }
    
        // Ajustar el ancho de las celdas automáticamente
        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    
        // Crear el nombre del archivo
        $filename = 'abonos_semana_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx';
    
        // Definir la ruta completa para el archivo
        $exportPath = storage_path('app/exports');
        $filePath = $exportPath . DIRECTORY_SEPARATOR . $filename;
    
        // Escribir el archivo Excel
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
    
        // Redirigir al usuario a la descarga del archivo
        return response()->download($filePath);
    }
    
}
