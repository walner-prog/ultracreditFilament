<?php

namespace App\Filament\Resources\ClienteResource\Pages;

use App\Filament\Resources\ClienteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditCliente extends EditRecord
{
    protected static string $resource = ClienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            
          
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');

    }

  
 
protected function getSavedNotification(): ?Notification
{
    return Notification::make()
        ->success()
        ->title('Cliente Actualizado')
        ->body('El cliente se Actualizo con exito.');
}
}
