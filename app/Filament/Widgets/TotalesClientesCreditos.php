<?php

namespace App\Filament\Widgets;

use App\Models\Cliente;
use App\Models\Credito;
use Filament\Widgets\ChartWidget;

class TotalesClientesCreditos extends ChartWidget
{
    protected static ?string $heading = 'Totales de Clientes y Créditos';

    protected function getData(): array
    {
        // Obtener los totales de clientes y créditos
        $totalClientes = Cliente::count(); // Contamos todos los clientes
        $totalCreditos = Credito::where('estado', 'activo')->count(); // Contamos los créditos activos
       // $totalSaldoPendiente = Credito::where('estado', 'activo')->sum('saldo_pendiente'); // Sumo el saldo pendiente de los créditos activos

        // Si los valores son 0, puedes agregar una condición de fallback
        $totalClientes = $totalClientes ?: 0;
        $totalCreditos = $totalCreditos ?: 0;
      //  $totalSaldoPendiente = $totalSaldoPendiente ?: 0;

        return [
            'datasets' => [
                [
                    'label' => 'Clientes',
                    'data' => [$totalClientes], // Datos de total de clientes
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Créditos Activos',
                    'data' => [$totalCreditos], // Datos de total de créditos activos
                    'backgroundColor' => 'rgba(153, 102, 255, 0.2)',
                    'borderColor' => 'rgba(153, 102, 255, 1)',
                    'borderWidth' => 1,
                ],
                
            ],
            'labels' => ['Totales'], // Etiqueta para el eje X
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Tipo de gráfico (puedes cambiarlo a 'line' o 'pie')
    }
}
