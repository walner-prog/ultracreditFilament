<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Filament\Resources\PermissionResource\RelationManagers;
use App\Models\Permission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $modelLabel = 'Permisos';
    protected static ?string $navigationGroup = 'Configuraciones';
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre') // Added label
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(191),
    
                Forms\Components\Select::make('roles')
                    ->label('Roles') // Added label
                    ->multiple()
                    ->relationship('roles', 'name') // No relationshipName needed here
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre') // Added label
                    ->searchable(),
    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado el') // Added label
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->formatStateUsing(fn ($state) => $state? $state->format('Y-m-d H:i:s'): null), // Handle potential nulls
    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado el') // Added label
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->formatStateUsing(fn ($state) => $state? $state->format('Y-m-d H:i:s'): null), // Handle potential nulls
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Ver'),
               // Tables\Actions\EditAction::make()->label('Editar'), // Added label
               // Tables\Actions\DeleteAction::make()->label('Eliminar'), // Added label
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePermissions::route('/'),
        ];
    }
}
