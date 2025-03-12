<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';
    
    protected function getStats(): array
    {

        
        return [
            Stat::make('Total de Usuarios', User::count())
                ->icon('heroicon-o-users')
                ->description('Usuarios registrados en la plataforma')
                ->color('primary'),
        ];
    }
}
