<?php

namespace App\Filament\Widgets;

use App\Models\Cliente;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TrecimientoClientesWidget extends ChartWidget
{
    protected static ?string $heading = 'Crecimiento de Clientes en los Últimos Meses';

    protected function getData(): array
    {
        // Obtener los totales de clientes por mes
        $clientesPorMes = Cliente::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(12) // Obtener solo los últimos 12 meses
            ->get();

        // Procesar los datos para el gráfico
        $labels = [];
        $clientes = [];
        foreach ($clientesPorMes as $registro) {
            // Formatear la fecha en un formato más legible (Ej: Enero 2025)
            $labels[] = $this->getMesNombre($registro->month) . ' ' . $registro->year;
            $clientes[] = $registro->total;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Clientes Nuevos',
                    'data' => $clientes, // Número de clientes por mes
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels, // Etiquetas de los meses
        ];
    }

    protected function getType(): string
    {
        return 'line'; // Tipo de gráfico (puedes cambiar a 'bar' o 'pie')
    }

    /**
     * Función para obtener el nombre del mes en español.
     */
    private function getMesNombre(int $mes): string
    {
        $meses = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];

        return $meses[$mes];
    }
}
