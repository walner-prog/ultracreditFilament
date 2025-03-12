<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use  \App\Filament\Resources\UserResource\Widgets\UserOverview;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;


    public function getTabs(): array
    {
        return [
            'todos' => Tab::make('Todos')
                ->icon('heroicon-m-user-group'),
            'administradores' => Tab::make('Administradores')
                ->icon('heroicon-m-shield-check')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->whereHas('roles', fn (Builder $query) => $query->where('name', 'Administrador'))
                ),
            'usuarios' => Tab::make('Usuarios')
                ->icon('heroicon-m-user')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->whereHas('roles', fn (Builder $query) => $query->where('name', 'Usuario'))
                ),
        ];
    }
    
    

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

   /*
      protected function getHeaderWidgets(): array
    {
        return [
            UserOverview::class,
        ];
    }
   */
}
