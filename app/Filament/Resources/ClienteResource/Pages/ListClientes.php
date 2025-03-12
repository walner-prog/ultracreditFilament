<?php

namespace App\Filament\Resources\ClienteResource\Pages;

use App\Filament\Resources\ClienteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use Filament\Resources\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

use Carbon\Carbon;
use Filament\Resources\Components\Tab;


class ListClientes extends ListRecords
{
    protected static string $resource = ClienteResource::class;

    public function getTabs(): array
{
    $model = static::getModel();

    // Contadores de clientes por estado
    $totalClientes = $model::count();
    $activos = $model::where('estado', 'activo')->count();
    $inactivos = $model::where('estado', 'inactivo')->count();

    // Contadores de clientes con créditos
    $conCreditos = $model::whereHas('creditos')->count();
    $sinCreditos = $model::whereDoesntHave('creditos')->count();
    $conCreditosActivos = $model::whereHas('creditos', function ($query) {
        $query->where('estado', 'activo');
    })->count();
    $conCreditosMorosos = $model::whereHas('creditos', function ($query) {
        $query->where('estado', 'moroso');
    })->count();
    $conCreditosCancelados = $model::whereHas('creditos', function ($query) {
        $query->where('estado', 'cancelado');
    })->count();

    return [
        'todos' => Tab::make('Todos')
            ->icon('heroicon-m-user-group')
            ->badge($totalClientes)
            ->badgeColor('success'),

        'activos' => Tab::make('Activos')
            ->icon('heroicon-m-check-circle')
            ->badge($activos)
            ->modifyQueryUsing(fn (Builder $query) => $query->where('estado', 'activo')),

        'inactivos' => Tab::make('Inactivos')
            ->icon('heroicon-m-x-circle')
            ->badge($inactivos)
            ->modifyQueryUsing(fn (Builder $query) => $query->where('estado', 'inactivo')),

        'con_creditos' => Tab::make('Con Créditos')
            ->icon('heroicon-m-banknotes')
            ->badge($conCreditos)
            ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('creditos')),

        'sin_creditos' => Tab::make('Sin Créditos')
            ->icon('heroicon-m-credit-card')
            ->badge($sinCreditos)
            ->modifyQueryUsing(fn (Builder $query) => $query->whereDoesntHave('creditos')),

        'creditos_activos' => Tab::make('Créditos Activos')
            ->icon('heroicon-m-wallet')
            ->badge($conCreditosActivos)
            ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('creditos', fn ($query) => $query->where('estado', 'activo'))),
   
    /*
        'creditos_morosos' => Tab::make('Créditos Morosos')
            ->icon('heroicon-m-exclamation-circle')
            ->badge($conCreditosMorosos)
            ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('creditos', fn ($query) => $query->where('estado', 'moroso'))),

        'creditos_cancelados' => Tab::make('Créditos Cancelados')
            ->icon('heroicon-m-check-badge')
            ->badge($conCreditosCancelados)
            ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('creditos', fn ($query) => $query->where('estado', 'cancelado'))),
       */     
    ];
}

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
