<?php

namespace App\Filament\Exports;

use App\Models\Abono;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\ExportColumn;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Filament\Actions\Action;

class AbonoExporter extends Exporter
{
    protected static ?string $model = Abono::class;

    // Asegúrate de que la consulta se filtre correctamente
    public static function getQuery(Builder $query): Builder
    {
        return $query
            ->when(request('activeTab') === 'todos', fn ($q) =>
                $q->whereDate('fecha_abono', Carbon::today())
            )
            ->when(request('activeTab') === 'no_abonaron', fn ($q) =>
                $q->whereNotExists(function ($subQuery) {
                    $subQuery->selectRaw(1)
                        ->from('abonos')
                        ->whereColumn('creditos.id', 'abonos.credito_id')
                        ->whereDate('abonos.fecha_abono', Carbon::today());
                })
            );
    }

    // Define las columnas que deseas exportar
    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('user.name')->label('Registrado por'),
            ExportColumn::make('cliente.nombre')->label('Cliente'),
            ExportColumn::make('credito.monto_total')->label('Monto del Crédito'),
            ExportColumn::make('monto_abono')->label('Monto Abonado'),
            ExportColumn::make('fecha_abono')->label('Fecha del Abono'),
            ExportColumn::make('created_at')->label('Fecha de Creación'),
        ];
    }

    public static function getCompletedNotificationBody($export): string
    {
        $body = 'La exportación se completó con éxito.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . $failedRowsCount . ' filas no pudieron exportarse.';
        }

        return $body;
    }

    public static function getCompletedNotificationActions($export): array
{
    return [
        Action::make('download')
            ->label('Descargar Archivo')
            ->url($export->getDownloadUrl(), shouldOpenInNewTab: true),
    ];
}
}
