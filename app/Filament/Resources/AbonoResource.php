<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbonoResource\Pages;
use App\Filament\Resources\AbonoResource\RelationManagers;
use App\Models\Abono;
use App\Models\Cliente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Credito;
use Filament\Forms\Set;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Rules\MontoAbonoValido;
use Carbon\Carbon;
use Filament\Forms\Components\RichEditor;

use App\Filament\Exports\AbonoExporter;
use Filament\Tables\Actions\ExportAction;







class AbonoResource extends Resource
{
    protected static ?string $model = Abono::class;
    protected static ?int $navigationSort = 4;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

   

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Hidden::make('user_id')
                ->default(auth()->id()),
    
            Forms\Components\Section::make('Información del Abono')
                ->schema([
                    Forms\Components\Select::make('cliente_id')
                        ->label('Cliente')
                        ->options(
                            Cliente::whereHas('creditos', fn ($query) => 
                                $query->where('estado', 'activo')
                            )
                            ->where('estado', 'activo')
                            ->get()
                            ->mapWithKeys(fn ($cliente) => [$cliente->id => $cliente->full_name])
                        )
                        ->required()
                        ->searchable()
                        ->preload()
                        ->afterStateUpdated(fn (Set $set) => $set('credito_id', null)),
    
                    Forms\Components\Select::make('credito_id')
                        ->label('Crédito')
                        ->options(fn (callable $get) => 
                            $get('cliente_id') ? 
                                Credito::where('cliente_id', $get('cliente_id'))
                                    ->where('estado', 'activo')
                                    ->get()
                                    ->mapWithKeys(function ($credito) {
                                        return [
                                            $credito->id => $credito->cliente->full_name . 
                                                            ' - Monto: NIO ' . number_format($credito->monto_total, 2) .
                                                            ' - Saldo: NIO ' . number_format($credito->saldo_pendiente, 2) .
                                                            ' - Tasa I: ' . number_format($credito->tasa_interes, 2) . '%' .
                                                            ' - Plazo: ' . $credito->plazo . ' ' . $credito->unidad_plazo
                                        ];
                                    })
                            : []
                        )
                        ->required()
                        ->searchable()
                        ->preload()
                        ->placeholder('Selecciona un crédito')
                        ->afterStateUpdated(function (Set $set, callable $get) {
                            $credito = Credito::find($get('credito_id'));
                            if ($credito) {
                                $set('monto_total', number_format($credito->monto_total, 2));
                                $set('saldo_pendiente', number_format($credito->saldo_pendiente, 2));
                                $set('tasa_interes', number_format($credito->tasa_interes, 2) . '%');
                                $set('plazo', $credito->plazo . ' ' . $credito->unidad_plazo);
                                $set('fecha_inicio', \Carbon\Carbon::parse($credito->fecha_inicio)->format('d-m-Y'));
                            } else {
                                $set('monto_total', '');
                                $set('saldo_pendiente', '');
                                $set('tasa_interes', '');
                                $set('plazo', '');
                                $set('fecha_inicio', '');
                            }
                        }),
                ])
                ->columns(2) // Se divide en 2 columnas en pantallas grandes
                , // Se apila en una sola columna en pantallas pequeñas
    
            Forms\Components\Section::make('Detalles del Abono')
                ->schema([
                    Forms\Components\TextInput::make('monto_abono')
                        ->label('Monto del Abono')
                        ->required()
                        ->numeric()
                        ->rule(fn ($get) => new MontoAbonoValido($get('credito_id'))), 
    
                    Forms\Components\DatePicker::make('fecha_abono')
                        ->label('Fecha del Abono')
                        ->required(),
                ])
                ->columns(2)
               ,
    
            Forms\Components\Section::make('Comentarios')
                ->schema([
                    RichEditor::make('comentarios')
                        ->label('Comentarios sobre el Abono')
                        ->maxLength(2000)
                        ->hint('Escribe cualquier detalle sobre el abono aquí.')
                        ->columnSpan(2),
                ])
                ->columns(1), // Siempre en una columna
        ]);
    
    }
    

    public static function table(Table $table): Table
    {
        
        return $table

        ->headerActions([ // Aquí se agregan las acciones de encabezado (botones en la parte superior)
            Tables\Actions\Action::make('export')
                ->label('Abonos de Hoy')
                ->url(route('abonos.export')) // Ruta de exportación que definiste en tu controlador
                ->icon('heroicon-o-calendar') // Icono de descarga (outline)
                ->color('success'),
        
            Tables\Actions\Action::make('export')
                ->label('Abonos de la semana')
                ->url(route('abonos.exportsemana')) // Ruta de exportación que definiste en tu controlador
                ->icon('heroicon-o-calendar') // Icono de calendario (outline)
                ->color('success'),
        ])
        
      
    
            ->columns([
                // Mostrar el nombre del usuario en lugar del ID
                TextColumn::make('user.name')
                    ->label('Registrado por')
                    ->sortable()
                    ->searchable(),
    
                // Mostrar el cliente y el monto del crédito en lugar del crédito_id
                Tables\Columns\TextColumn::make('cliente.full_name')
                    ->label('Cliente')
                    ->sortable(),
                    
    
                Tables\Columns\TextColumn::make('credito.monto_total')
                    ->label('Monto del Crédito')
                    ->sortable()
                    ->money('NIO'), // Córdobas
    
                // Monto del abono
                Tables\Columns\TextColumn::make('monto_abono')
                    ->label('Monto del Abono')
                    ->sortable()
                    ->money('NIO'), // Córdobas

                    TextColumn::make('estado_abono')
                    ->label('Estado del Abono')
                    ->getStateUsing(fn ($record) => 
                        Abono::whereDate('fecha_abono', Carbon::today())
                            ->where('credito_id', $record->credito_id)  // Aquí te aseguras de filtrar por el mismo 'credito_id' del registro actual
                            ->exists() 
                            ? 'Pagó Hoy' 
                            : 'Pendiente'
                    )
                    ->color(fn ($state) => $state === 'Pagó Hoy' ? 'success' : 'danger'),
                
                

    
                // Fecha del abono
                Tables\Columns\TextColumn::make('fecha_abono')
                    ->label('Fecha del Abono')
                    ->date()
                    ->sortable(),

                    TextColumn::make('comentarios')
                    ->label('Comentarios')
                    ->getStateUsing(function ($record) {
                        return $record->comentarios;  // Muestra el HTML directamente
                    })
                    ->html(),  // Permite mostrar HTML sin escapar
                
                
                 // Limitar el número de caracteres mostrados
                   // ->sortable()
                   // ->searchable(),
    
                // Fecha de creación (oculta por defecto)
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Registro')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
    
                // Fecha de actualización (oculta por defecto)
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última Modificación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
    
                // Fecha de eliminación (oculta por defecto)
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Eliminado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
               // Filtrar abonos del día
               Filter::make('abonos_del_dia')
               ->label('Abonos del Día')
               ->query(fn ($query) => $query->whereDate('fecha_abono', Carbon::today())),

           // Filtrar por cartera
         
               //SelectFilter::make()
           
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                
               
            ])
            ->bulkActions([
               
            ]);
    }
    
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAbonos::route('/'),
        ];
    }

  
}
