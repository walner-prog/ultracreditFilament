<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CreditoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener todos los clientes activos
        $clientes = DB::table('clientes')->where('estado', 'activo')->get();

        // Obtener las carteras (asumimos que ya están creadas)
        $carteras = DB::table('carteras')->pluck('id')->toArray();  // Esto obtiene los IDs de las carteras

        foreach ($clientes as $cliente) {
            // Definir el monto y el plazo
            $monto_total = mt_rand(5001, 10000); // Monto mayor a 5000
            $plazo = mt_rand(1, 3); // Plazo de entre 3 y 6 meses
            $unidad_plazo = 'meses';
            
            // Generar fecha de inicio (fecha actual)
            $fecha_inicio = Carbon::now();
            // Calcular la fecha de vencimiento
            $fecha_vencimiento = $fecha_inicio->copy()->addMonths($plazo);

            // Si el plazo es de 21 días, ajustar la fecha de vencimiento a 21 días después de la fecha de inicio
            if ($plazo == 3) {
                $plazo = 21;
                $unidad_plazo = 'dias';
                $fecha_vencimiento = $fecha_inicio->copy()->addDays($plazo);
            }

            // Asignar un cartera_id aleatorio (1 o 2)
            $cartera_id = $carteras[array_rand($carteras)];

            // Insertar un crédito por cliente
            DB::table('creditos')->insert([
                'cliente_id' => $cliente->id,
                'cartera_id' => $cartera_id, // Asignar cartera_id
                'monto_total' => $monto_total,
                'saldo_pendiente' => $monto_total, // Inicialmente el saldo pendiente es igual al monto total
                'tasa_interes' => 20.00, // Tasa de interés del 20%
                'plazo' => $plazo,
                'unidad_plazo' => $unidad_plazo,
                'estado' => 'activo',
                'fecha_inicio' => $fecha_inicio,
                'fecha_vencimiento' => $fecha_vencimiento,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
