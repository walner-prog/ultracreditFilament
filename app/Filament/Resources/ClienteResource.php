<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClienteResource\Pages;
use App\Filament\Resources\ClienteResource\RelationManagers;
use App\Models\Cliente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;
    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function getNavigationBadge(): ?string
    {
     return static::getModel()::count();
    }

    protected static ?string $navigationBadgeTooltip = 'Total de Clientes ';
    public static function getNavigationBadgeTooltip(): ?string
    {
       return 'Total de Clientes';
    }

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Placeholder::make('info')
                ->content('**IMPORTANTE:** Todos los datos ingresados deben ser auténticos y verificables. Proporcionar información falsa puede invalidar su registro.')
                ->columnSpanFull()
                ->extraAttributes([
                    'class' => 'primary text-black p-4 rounded-md border border-yellow-400'
                ]),
    
            Forms\Components\Section::make('Información Personal')
                ->schema([
                    Forms\Components\TextInput::make('nombres')
                        ->required()
                        ->maxLength(255),
    
                    Forms\Components\TextInput::make('apellidos')
                        ->required()
                        ->maxLength(255),
    
                    Forms\Components\TextInput::make('identificacion')
                        ->required()
                        ->maxLength(20)
                        ->rules(function ($get, $context) {
                            return $context === 'edit'
                                ? 'unique:clientes,identificacion,' . $get('id')
                                : 'unique:clientes,identificacion';
                        })
                        ->label('Identificación'),
    
                    Forms\Components\TextInput::make('telefono')
                        ->tel()
                        ->required()
                        ->maxLength(20),
                ])
                ->columns(2),
                 // En móviles se apilan los campos en una sola columna
    
            Forms\Components\Section::make('Dirección')
                ->schema([
                    Forms\Components\Textarea::make('direccion')
                        ->required()
                        ->columnSpanFull(),
    
                    Forms\Components\TextInput::make('km_referencia')
                        ->maxLength(50)
                        ->default(null),
                ])
                ->columns(1), // Mantiene una estructura de una sola columna
    
            Forms\Components\Section::make('Estado')
                ->schema([
                    Forms\Components\Select::make('estado')
                        ->options([
                            'activo' => 'Activo',
                            'inactivo' => 'Inactivo',
                        ])
                        ->default('activo')
                        ->required()
                        ->label('Estado'),
                ])
                ->columns(1), // Siempre en una sola columna
        ]);
    
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombres')
                    ->searchable(),
                Tables\Columns\TextColumn::make('apellidos')
                    ->searchable(),
                Tables\Columns\TextColumn::make('identificacion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telefono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('km_referencia')
                    ->searchable(),
                Tables\Columns\TextColumn::make('estado'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
    
                    Tables\Columns\TextColumn::make('historial_creditos_con_activos')
                    ->label('Historial de Créditos (Activos)')
                    ->searchable(false),
            ])
            ->defaultPaginationPageOption(10)
            ->paginationPageOptions([10, 25, 50, 100])

            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
               // Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
              
                Tables\Actions\RestoreAction::make(),

                Tables\Actions\Action::make('ver_creditos_activos')
                ->label('Ver Créditos Activos')
                ->icon('heroicon-o-eye') // Icono para la acción
                ->modalHeading('Créditos Activos') // Título del modal
                ->modalWidth('lg') // Tamaño del modal
                ->modalButton('Cerrar') // Botón para cerrar el modal
                ->modalContent(function ($record) {
                    // Obtener los créditos activos del cliente
                    $activeCredits = $record->creditos()->where('estado', 'activo')->get();
            
                    // Si hay créditos activos, pasamos los datos al modal
                    if ($activeCredits->isNotEmpty()) {
                        return view('admin.modals.creditos_activos', ['activeCredits' => $activeCredits]);
                    }
            
                    // Si no hay créditos activos, mostramos un mensaje
                    return view('admin.modals.no_creditos_activos');
                })
                ->action(function () {
                    // Lógica si es necesario (no se necesita más lógica en este caso)
                }),

               // Tables\Actions\ForceDeleteAction::make(),
            
                // Esto oculta el botón de eliminar si el cliente tiene al menos un crédito activo.Si el cliente no tiene créditos activos (count() === 0), la acción de eliminar será visible.
                Tables\Actions\DeleteAction::make()
                ->visible(fn ($record) => $record->creditos()
                    ->whereIn('estado', ['activo', 'cancelado', 'moroso'])
                    ->count() === 0
                ) 
                ->requiresConfirmation(),
            
            
            ])
            ->bulkActions([]);
    }
    
    
    


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientes::route('/'),
            'create' => Pages\CreateCliente::route('/create'),
            'view' => Pages\ViewCliente::route('/{record}'),
            'edit' => Pages\EditCliente::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
