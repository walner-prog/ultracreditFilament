<?php

namespace App\Filament\Widgets;

use App\Models\Cliente;
use App\Models\Credito;
use App\Models\Abono;
use App\Models\Cartera;
use App\Models\Role;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Enums\IconPosition;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            // Estadísticas de Cliente
            Stat::make('Clientes', Cliente::count())
                ->description('Total de clientes')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
            
            // Estadísticas de Credito
            Stat::make('Créditos', Credito::count())
                ->description('Total de créditos')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('warning'),
            
            // Estadísticas de Abono
            Stat::make('Abonos', Abono::count())
                ->description('Total de abonos')
                ->descriptionIcon('heroicon-o-rectangle-stack')
                ->color('success'),
            
            // Estadísticas de Cartera
            Stat::make('Cartera', Cartera::count())
                ->description('Total de carteras')
                ->descriptionIcon('heroicon-m-wallet')
                ->color('danger'),
            
            // Estadísticas de User (Usuarios)
            Stat::make('Usuarios', User::count())
                ->description('Total de usuarios')
                ->descriptionIcon('heroicon-m-users')
                ->color('secondary'),

                Stat::make('Roles', Role::count())
                ->description('Total de roles')
                ->descriptionIcon('heroicon-o-identification')
                ->color('indigo'),
        ];
    }
}
