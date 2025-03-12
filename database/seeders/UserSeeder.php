<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Roles ya creados
        $roles = Role::whereIn('name', ['Cobrador', 'Analista', 'Cajero', 'Asesor de crédito'])->get();

        // Crear una instancia de Faker para generar datos en español
        $faker = Faker::create('es_ES');

        // Generar 15 usuarios con nombres reales
        for ($i = 0; $i < 15; $i++) {
            $user = User::create([
                'name' => $faker->name,  // Nombre real del usuario
                'email' => $faker->unique()->safeEmail,  // Correo electrónico único
                'password' => Hash::make('12345678'),  // Contraseña
            ]);

            // Asignar un rol aleatorio a cada usuario
            $user->roles()->attach($roles->random());
        }
    }
}
