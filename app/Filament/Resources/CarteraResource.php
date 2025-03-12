<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarteraResource\Pages;
use App\Filament\Resources\CarteraResource\RelationManagers;
use App\Models\Cartera;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CarteraResource\Widgets\CarteraOverview;



class CarteraResource extends Resource
{
    protected static ?string $model = Cartera::class;
    protected static ?int $navigationSort = 3;

       protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

     

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                ->label('Cobrador')
                ->options(User::all()->pluck('name', 'id'))
                ->required()
                ->searchable()
                ->preload(),
        
                
                Forms\Components\TextInput::make('nombre')
                ->required()
                ->maxLength(255)
                ->unique(table: 'carteras', column: 'nombre', ignoreRecord: true),
            

                Forms\Components\Toggle::make('estado')
                ->label('Estado')  // Etiqueta general del campo
                ->onColor('success')  // Color verde cuando está activo
                ->offColor('danger')  // Color rojo cuando está inactivo
                ->default(true)       // Estado inicial (puedes usar true para 'activa' o false para 'inactiva')
                ->required()          // Requerido
                ->reactive()          // Sincroniza el estado cuando cambie
                ->helperText(function ($get) {
                    // Cambia el texto del helper basado en el valor de 'estado'
                    return $get('estado') ? 'Cartera activa' : 'Cartera inactiva';
                }),
    

            
            
                    
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('nombre')
                ->searchable(),
        
            // Estado con Badge de color
            

            Tables\Columns\BadgeColumn::make('estado')
            ->label('Estado')
            ->sortable()
            ->colors([
                'success' => 'activa',
                'danger'  => 'inactiva',
            ])
            ->formatStateUsing(fn ($state) => ucfirst($state)), // Convierte 'activa' o 'inactiva' en texto visible
        
        
                Tables\Columns\ToggleColumn::make('estado')
                ->onIcon('heroicon-o-check-circle')  // Icono cuando está activa
                ->offIcon('heroicon-o-x-circle')     // Icono cuando está inactiva
                ->onColor('success')  // Verde cuando está activa
                ->offColor('danger')  // Rojo cuando está inactiva
                ->sortable(),
        
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
        ])
        
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                //el botón solo aparecerá si la cartera no tiene clientes asignados.
                
               /* Tables\Actions\DeleteAction::make()
                ->visible(fn ($record) => $record->cartera()->count() === 0) 
                ->requiresConfirmation(), */
            
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
               
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCarteras::route('/'),
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
