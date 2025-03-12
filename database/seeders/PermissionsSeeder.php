<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lista de entidades clave en la financiera
        $entities = [
            'user',
            'role',
            'permision',
            'abono',
            'cartera',
            'cliente',
            'credito',
        ];

        // Acciones que se pueden realizar sobre cada entidad
        $actions = ['_ver', '_crear', '_editar', '_eliminar'];

        // Crear el permiso "admin" y asignarlo al Administrador
        $adminPermission = Permission::firstOrCreate([
            'name'       => 'admin',
            'guard_name' => 'web',
        ]);

        $adminRole = Role::where('name', 'Administrador')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($adminPermission);
        }

        // Obtener los roles creados
        $gerente  = Role::where('name', 'Gerente')->first();
        $cobrador = Role::where('name', 'Cobrador')->first();
        $analista = Role::where('name', 'Analista')->first();
        $cajero   = Role::where('name', 'Cajero')->first();
        $asesor   = Role::where('name', 'Asesor de crédito')->first();

        // Asignación de permisos según la entidad
        foreach ($entities as $entity) {
            foreach ($actions as $action) {
                $permissionName = $entity . $action;

                // Crear permiso si no existe
                $permission = Permission::firstOrCreate([
                    'name'       => $permissionName,
                    'guard_name' => 'web',
                ]);

                // El Administrador tiene todos los permisos
                if ($adminRole) {
                    $adminRole->givePermissionTo($permission);
                }

                // Distribución de permisos según rol
                switch ($entity) {
                    case 'user':
                    case 'role':
                    case 'permision':
                        if ($gerente && $action == '_ver') {
                            $gerente->givePermissionTo($permission);
                        }
                        break;

                    case 'abono':
                        if ($cobrador) {
                            $cobrador->givePermissionTo($permission);
                        }
                        if ($cajero && in_array($action, ['_crear', '_editar', '_ver'])) {
                            $cajero->givePermissionTo($permission);
                        }
                        break;

                    case 'cartera':
                        if ($gerente) {
                            $gerente->givePermissionTo($permission);
                        }
                        if ($analista && $action == '_ver') {
                            $analista->givePermissionTo($permission);
                        }
                        break;

                    case 'cliente':
                        if ($asesor) {
                            $asesor->givePermissionTo($permission);
                        }
                        if ($cobrador && $action == '_ver') {
                            $cobrador->givePermissionTo($permission);
                        }
                        break;

                    case 'credito':
                        if ($analista) {
                            $analista->givePermissionTo($permission);
                        }
                        if ($gerente && in_array($action, ['_crear', '_editar', '_eliminar'])) {
                            $gerente->givePermissionTo($permission);
                        }
                        if ($asesor && $action == '_ver') {
                            $asesor->givePermissionTo($permission);
                        }
                        break;
                }
            }
        }
    }
}
