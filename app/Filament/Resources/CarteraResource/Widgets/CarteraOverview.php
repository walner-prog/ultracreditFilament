<?php

namespace App\Filament\Resources\CarteraResource\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Cartera;

class CarteraOverview extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Carteras Activas', Cartera::where('estado', 'activa')->count())
                ->description('Total de carteras en uso')
                ->color('success'),

            Card::make('Carteras Inactivas', Cartera::where('estado', 'inactiva')->count())
                ->description('Carteras deshabilitadas')
                ->color('danger'),

            Card::make('Total de Carteras', Cartera::count()) // Nuevo Card
                ->description('Cantidad total de carteras registradas')
                ->color('primary'),
        ];
    }
}
