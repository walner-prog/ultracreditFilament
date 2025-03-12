<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Tables\Actions\ActionGroup;


class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static ?string $modelLabel = 'Roles';
    protected static ?string $navigationGroup = 'Configuraciones';
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(191),

            Forms\Components\Select::make('permissions')
                ->label('Permisos') // Added label
                ->multiple()
                ->relationship('permissions', 'name')
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
                ->formatStateUsing(fn ($state) => $state ? $state->format('Y-m-d H:i:s') : null), // Handle nulls

            Tables\Columns\TextColumn::make('updated_at')
                ->label('Actualizado el') // Added label
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: false)
                ->formatStateUsing(fn ($state) => $state ? $state->format('Y-m-d H:i:s') : null), // Handle nulls

           
        ])
        ->filters([
            //
        ])

        ->headerActions([
            Tables\Actions\Action::make('Ver PDF')
                ->label('Exportar PDF')
                ->icon('heroicon-m-document')
                ->url(fn () => route('admin.pdf-roles')) // Redirige a la ruta del PDF
                ->openUrlInNewTab(), // Abre en una nueva pestaña
        ])

        ->actions([
            ActionGroup::make([
                Tables\Actions\ViewAction::make()->label('Ver'),
                Tables\Actions\EditAction::make()->label('Editar'), // Added label
                //Tables\Actions\DeleteAction::make()->label('Eliminar'), // Added label
    
            ])
            ->link()
            ->label('Acciones')

            
    
        ]);
}
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRoles::route('/'),
        ];
    }
}
