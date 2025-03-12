<?php

namespace App\Filament\Resources\CreditoResource\Pages;

use App\Filament\Resources\CreditoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;


use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use App\Filament\Resources\ClienteResource;

use Illuminate\Database\Eloquent\Builder;

use Carbon\Carbon;
use Filament\Resources\Components\Tab;

class ListCreditos extends ListRecords
{
    protected static string $resource = CreditoResource::class;

    public function getTabs(): array
    {
        $model = static::getModel();
    
        // Contadores por estado
        $totalCount     = $model::count();
        $activosCount   = $model::where('estado', 'activo')->count();
        $morososCount   = $model::where('estado', 'moroso')->count();
        $canceladosCount = $model::where('estado', 'cancelado')->count();
    
        // Fecha actual
        $hoy = Carbon::today();
    
        // Créditos con abonos hoy
        $abonosHoy = \App\Models\Abono::whereDate('fecha_abono', $hoy)->pluck('credito_id');
        $alDiaCount = $model::whereIn('id', $abonosHoy)->count();
    
        // Créditos pendientes de pago hoy
        $pendientesCount = $model::whereNotIn('id', $abonosHoy)->where('estado', 'activo')->count();
    
        return [
            'todos' => Tab::make('Todos')
                ->icon('heroicon-m-folder') // Icono corregido
                ->badge($totalCount),
    
            'activos' => Tab::make('Activos')
                ->icon('heroicon-m-bolt')
                ->badge($activosCount)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('estado', 'activo')),
    
            'morosos' => Tab::make('Morosos')
                ->icon('heroicon-m-exclamation-circle')
                ->badge($morososCount)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('estado', 'moroso')),
    
            'cancelados' => Tab::make('Cancelados')
                ->icon('heroicon-m-check-circle')
                ->badge($canceladosCount)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('estado', 'cancelado')),
    

    
            'pendientes' => Tab::make('Pendientes de Pago')
                ->icon('heroicon-m-clock')
                ->badge($pendientesCount)
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotIn('id', $abonosHoy)->where('estado', 'activo')),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('cliente.nombres')
                ->label('Cliente')
                ->sortable()
                ->searchable(),

            TextColumn::make('monto_total')
                ->label('Monto Total')
                ->money('USD')
                ->sortable(),

            BadgeColumn::make('estado')
                ->label('Estado')
                ->colors([
                    'success' => 'activo',
                    'danger' => 'moroso',
                    'gray' => 'cancelado',
                ]),

            BadgeColumn::make('saldo_pendiente')
                ->label('Saldo Pendiente')
                ->colors([
                    'success' => fn ($record) => $record->saldo_pendiente == 0,
                    'warning' => fn ($record) => $record->saldo_pendiente > 0,
                ])
                ->formatStateUsing(fn ($state) => "$" . number_format($state, 2)),

            BadgeColumn::make('unidad_plazo')
                ->label('Plazo')
                ->colors([
                    'blue' => 'dias',
                    'indigo' => 'meses',
                ])
                ->formatStateUsing(fn ($state) => ucfirst($state)),
        ];
    }
}
