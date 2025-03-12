<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear roles para la financiera
        $adminRole = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Gerente', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Cobrador', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Analista', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Cajero', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Asesor de crédito', 'guard_name' => 'web']);

        // Crear usuario administrador si no existe
        $user = User::firstOrCreate(
            ['email' => 'ca140611@gmail.com'],
            [
                'name'     => 'Carlos Alvarez',
                'password' => Hash::make('12345678')
            ]
        );

        // Asignar rol "Administrador" si no lo tiene
        if (!$user->hasRole('Administrador')) {
            $user->assignRole($adminRole);
        }
    }
}
