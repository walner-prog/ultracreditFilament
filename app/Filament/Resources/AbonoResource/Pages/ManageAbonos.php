<?php

namespace App\Filament\Resources\AbonoResource\Pages;

use App\Filament\Resources\AbonoResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use App\Models\Abono;
use App\Models\Credito;
use App\Models\Cartera;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class ManageAbonos extends ManageRecords
{
    protected static string $resource = AbonoResource::class;

    public function getTabs(): array
    {
        $hoy = Carbon::today();
        $unaSemanaAtras = Carbon::today()->subWeek(); // Calcula la fecha de hace una semana
    
        // Obtener abonos de hoy
        $abonosHoy = Abono::whereDate('fecha_abono', $hoy);
        $totalAbonos = $abonosHoy->sum('monto_abono');

         // Clientes que pagaron hoy
         $clientesQuePagaronHoy = $abonosHoy->pluck('credito_id');

         // Créditos sin abono hoy
         $creditosNoPagadosHoy = Credito::whereNotIn('id', $clientesQuePagaronHoy)->count();
    
        // Obtener abonos de la última semana
        $abonosSemana = Abono::whereBetween('fecha_abono', [$unaSemanaAtras, $hoy]);
        $totalAbonosSemana = $abonosSemana->sum('monto_abono');
    
        // Clientes que pagaron esta semana
        $clientesQuePagaronSemana = $abonosSemana->pluck('credito_id');
    
        // Créditos sin abono esta semana
        $creditosNoPagadosSemana = Credito::whereNotIn('id', $clientesQuePagaronSemana)->count();
    
        // Obtener las carteras disponibles
        $carteras = Cartera::all();
    
        $tabs = [
            'todos' => Tab::make('Todos los Abonos de hoy')
                ->icon('heroicon-m-currency-dollar')
                ->badge($abonosHoy->count())
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereDate('fecha_abono', Carbon::today())
                ),
        
            'total_hoy' => Tab::make('(Info) Total en Córdobas')
                ->icon('heroicon-m-banknotes')
                ->badge('C$ ' . number_format($totalAbonos, 2))
                ->badgeColor('gray') // Color neutro para destacar que es informativo
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereDate('fecha_abono', Carbon::today())
                ),
    
            'no_abonaron' => Tab::make('(Info) No abonaron hoy')
                ->icon('heroicon-m-exclamation-circle')
                ->badge($creditosNoPagadosHoy)
                ->badgeColor('gray') // Color neutro para diferenciarlo de los tabs activos
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereDate('fecha_abono', Carbon::today())
                ),
    
            // Nuevo Tab: Abonos de la última semana
            'abonos_semana' => Tab::make('Abonos de la Última Semana')
                ->icon('heroicon-m-calendar')
                ->badge($abonosSemana->count())
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereBetween('fecha_abono', [$unaSemanaAtras, $hoy])
                ),
    
            'total_semana' => Tab::make('(Info) Total en Córdobas (Semana)')
                ->icon('heroicon-m-banknotes')
                ->badge('C$ ' . number_format($totalAbonosSemana, 2))
                ->badgeColor('gray') // Color neutro para destacar que es informativo
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereBetween('fecha_abono', [$unaSemanaAtras, $hoy])
                ),
    
            'no_abonaron_semana' => Tab::make('(Info) No abonaron esta semana')
                ->icon('heroicon-m-exclamation-circle')
                ->badge($creditosNoPagadosSemana)
                ->badgeColor('gray') // Color neutro para diferenciarlo de los tabs activos
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereBetween('fecha_abono', [$unaSemanaAtras, $hoy])
                ),
        ];
    
        foreach ($carteras as $cartera) {
            // Filtrar los abonos de hoy para la cartera seleccionada
            $abonosCartera = Abono::whereDate('fecha_abono', Carbon::today())
                ->whereHas('credito', fn ($q) => $q->where('cartera_id', $cartera->id))
                ->count();
    
            // Filtrar los abonos de la semana para la cartera seleccionada
            $abonosCarteraSemana = Abono::whereBetween('fecha_abono', [$unaSemanaAtras, $hoy])
                ->whereHas('credito', fn ($q) => $q->where('cartera_id', $cartera->id))
                ->count();
    
            // Solo agregar el tab si hay abonos en esta semana
            if ($abonosCarteraSemana > 0) {
                $tabs['cartera_semana_' . $cartera->id] = Tab::make($cartera->nombre . ' (Semana)')
                    ->icon('heroicon-m-folder')
                    ->badge($abonosCarteraSemana)
                    ->modifyQueryUsing(fn (Builder $query) =>
                        $query->whereHas('credito', fn ($q) =>
                            $q->where('cartera_id', $cartera->id)
                        )->whereBetween('fecha_abono', [$unaSemanaAtras, $hoy]) // FILTRA POR LA SEMANA
                    );
            }
    
            // Solo agregar el tab si hay abonos hoy
            if ($abonosCartera > 0) {
                $tabs['cartera_' . $cartera->id] = Tab::make($cartera->nombre)
                    ->icon('heroicon-m-folder')
                    ->badge($abonosCartera)
                    ->modifyQueryUsing(fn (Builder $query) =>
                        $query->whereHas('credito', fn ($q) =>
                            $q->where('cartera_id', $cartera->id)
                        )->whereDate('fecha_abono', Carbon::today()) // FILTRA POR HOY
                    );
            }
        }
    
        return $tabs;
    }
    



    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
