<?php

namespace App\Filament\Resources\CarteraResource\Pages;

use App\Filament\Resources\CarteraResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\CarteraResource\Widgets\CarteraOverview;

class ManageCarteras extends ManageRecords
{
    protected static string $resource = CarteraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array // 🔹 Cambiado a `protected`
    {
        return [
            CarteraOverview::class,
        ];
    }
}
