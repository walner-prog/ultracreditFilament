<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CreditoResource\Pages;
use App\Filament\Resources\CreditoResource\RelationManagers;
use App\Models\Cartera;
use App\Models\Credito;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Cliente;

class CreditoResource extends Resource
{
    protected static ?string $model = Credito::class;
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationBadgeTooltip = 'Total de Creditos ';
    public static function getNavigationBadgeTooltip(): ?string
    {
       return 'Total de Creditos';
    }

    public static function getNavigationBadge(): ?string
    {
     return static::getModel()::count();
    }


    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Placeholder::make('info')
                ->content('**IMPORTANTE:** Asegúrese de que todos los datos del crédito sean correctos y verificables. Modificar el monto total o el saldo pendiente puede afectar los términos del crédito y la relación con el cliente. Revise cuidadosamente los detalles antes de proceder. Si tiene dudas o necesita correcciones, consulte los abonos realizados o comuníquese con el administrador antes de realizar cambios.')
                ->columnSpanFull()
                ->extraAttributes([
                    'class' => 'primary text-black p-4 rounded-md border border-yellow-400'
                ]),
    
            Forms\Components\Section::make('Datos del Cliente')
                ->schema([
                    Forms\Components\Select::make('cliente_id')
                        ->label('Cliente')
                        ->options(Cliente::all()->pluck('full_name', 'id'))
                        ->searchable()
                        ->preload()
                        ->required(),
    
                    Forms\Components\Select::make('cartera_id')
                        ->label('Cartera')
                        ->options(fn ($get) => optional(Cliente::find($get('cliente_id')))->cartera
                            ? [optional(Cliente::find($get('cliente_id')))->cartera_id => optional(Cliente::find($get('cliente_id')))->cartera->nombre]
                            : Cartera::pluck('nombre', 'id'))
                        ->searchable()
                        ->preload()
                        ->required(),
                ])
                ->columns(2),
               
    
            Forms\Components\Section::make('Detalles del Crédito')
                ->schema([
                    Forms\Components\TextInput::make('monto_total')
                        ->label('Monto Total (C$)')
                        ->numeric()
                        ->rules(['required', 'numeric', 'min:0.01'])
                        ->helperText('Monto total del crédito en córdobas (C$).')
                        ->placeholder('Ingrese el monto total')
                        ->required(),
    
                    Forms\Components\TextInput::make('saldo_pendiente')
                        ->label('Saldo Pendiente (C$)')
                        ->numeric()
                        ->rules(['required', 'numeric', 'min:0.01'])
                        ->disabled(fn ($get) => $get('id') !== null)
                        ->helperText('Saldo pendiente del crédito en córdobas (C$).')
                        ->placeholder('Ingrese el saldo pendiente')
                        ->required(),
    
                    Forms\Components\TextInput::make('tasa_interes')
                        ->label('Tasa de Interés (%)')
                        ->numeric()
                        ->rules(['required', 'numeric', 'between:0,100'])
                        ->helperText('Ingrese la tasa de interés en porcentaje.')
                        ->default(20.00)
                        ->required(),
    
                    Forms\Components\TextInput::make('plazo')
                        ->label('Plazo del Crédito')
                        ->numeric()
                        ->rules(['required', 'numeric', 'min:1'])
                        ->helperText('Ingrese la cantidad de días o meses según la unidad seleccionada.')
                        ->placeholder('Ingrese el plazo')
                        ->required(),
    
                    Forms\Components\Select::make('unidad_plazo')
                        ->label('Unidad de Plazo')
                        ->options([
                            'dias' => 'Días',
                            'meses' => 'Meses',
                        ])
                        ->preload()
                        ->helperText('Seleccione la unidad de tiempo para el plazo.')
                        ->required(),
                ])
                ->columns(2),
               
    
            Forms\Components\Section::make('Estado y Fechas')
                ->schema([
                    Forms\Components\Select::make('estado')
                        ->label('Estado del Crédito')
                        ->options([
                            'activo' => 'Activo',
                            'cancelado' => 'Cancelado',
                            'moroso' => 'Moroso',
                        ])
                        ->default('activo')
                        ->preload()
                        ->helperText('Seleccione el estado actual del crédito.')
                        ->required(),
    
                    Forms\Components\DatePicker::make('fecha_inicio')
                        ->label('Fecha de Inicio')
                        ->required()
                        ->helperText('Seleccione la fecha en la que inicia el crédito.'),
    
                    Forms\Components\DatePicker::make('fecha_vencimiento')
                        ->label('Fecha de Vencimiento')
                        ->required()
                        ->helperText('Seleccione la fecha en la que vence el crédito.'),
                ])
                ->columns(2),
              
        ]);
    
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            

            Tables\Columns\TextColumn::make('cliente.nombres')
                ->label('Nombres')
                ->sortable()
                ->searchable(),

                Tables\Columns\TextColumn::make('cliente.apellidos')
                ->label('Apellido')
                ->sortable()
                ->searchable(),
        
            Tables\Columns\TextColumn::make('monto_total')
                ->label('Monto Total (C$)')
                ->money('NIO') // Formatea como dinero en córdobas
                ->sortable(),
        
            Tables\Columns\TextColumn::make('saldo_pendiente')
                ->label('Saldo Pendiente (C$)')
                ->money('NIO')
                ->sortable(),
        
            Tables\Columns\TextColumn::make('tasa_interes')
                ->label('Tasa de Interés (%)')
                ->suffix('%') // Agrega el símbolo de porcentaje
                ->sortable(),
        
            Tables\Columns\TextColumn::make('plazo')
                ->label('Plazo')
                ->sortable(),
        
            Tables\Columns\TextColumn::make('unidad_plazo')
                ->label('Unidad de Plazo')
                ->formatStateUsing(fn ($state) => ucfirst($state)), // Capitaliza la unidad de plazo
        
            Tables\Columns\BadgeColumn::make('estado')
                ->label('Estado')
                ->sortable()
                ->colors([
                    'success' => 'activo',
                    'danger'  => 'cancelado',
                    'warning' => 'moroso',
                ]),

                Tables\Columns\TextColumn::make('cartera.nombre')
                ->label('Cartera')
                ->sortable()
                ->searchable(query: function ($query, $search): void {
                    $query->whereHas('cartera', function ($query) use ($search) {
                        $query->where('nombre', 'like', "%{$search}%");
                    });
                }),
        
            Tables\Columns\TextColumn::make('fecha_inicio')
                ->label('Fecha de Inicio')
                ->date('d/m/Y')
                ->sortable(),
        
            Tables\Columns\TextColumn::make('fecha_vencimiento')
                ->label('Fecha de Vencimiento')
                ->date('d/m/Y')
                ->sortable(),
        
            Tables\Columns\TextColumn::make('created_at')
                ->label('Fecha de Creación')
                ->dateTime('d/m/Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        
            Tables\Columns\TextColumn::make('updated_at')
                ->label('Última Actualización')
                ->dateTime('d/m/Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        
            Tables\Columns\TextColumn::make('deleted_at')
                ->label('Fecha de Eliminación')
                ->dateTime('d/m/Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->defaultPaginationPageOption(10)
        ->paginationPageOptions([10, 25, 50, 100])
        
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
               // Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('ver_abonos_credito')
                ->label('Ver Abonos')
                ->icon('heroicon-o-eye') // Icono para la acción
                ->modalHeading('Detalles de Abonos') // Título del modal
                ->modalWidth('lg') // Tamaño del modal
                ->modalButton('Cerrar') // Botón para cerrar el modal
   
                 ->modalContent(function ($record) {
                 // Obtener los abonos del crédito seleccionado
                   $abonos = $record->abonos()->with('user')->get();

                   // Si hay abonos, pasamos los datos al modal
                   if ($abonos->isNotEmpty()) {
                    return view('admin.modals.abonos_credito', ['credito' => $record, 'abonos' => $abonos]);
                   }

                   // Si no hay abonos, mostramos un mensaje
                    return view('admin.modals.no_abonos_credito');
                   })
                   ->action(function () {
                      // No se necesita lógica adicional aquí
                   }),

                Tables\Actions\DeleteAction::make(),
               // Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
               
            ]);
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
            'index' => Pages\ListCreditos::route('/'),
            'create' => Pages\CreateCredito::route('/create'),
            'view' => Pages\ViewCredito::route('/{record}'),
            'edit' => Pages\EditCredito::route('/{record}/edit'),
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
