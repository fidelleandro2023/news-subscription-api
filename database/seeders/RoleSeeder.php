<?php
namespace Database\Seeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos
        $permissions = [
            'manage-users',
            'manage-subscriptions',
            'view-subscriptions',
            'create-subscriptions',
            'edit-subscriptions',
            'delete-subscriptions',
            'send-emails',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles
        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
        $editorRole = Role::firstOrCreate(['name' => 'Editor']);
        $clientRole = Role::firstOrCreate(['name' => 'Cliente']);

        // Asignar permisos a roles
        $adminRole->givePermissionTo($permissions);
        
        $editorRole->givePermissionTo([
            'manage-subscriptions',
            'view-subscriptions',
            'create-subscriptions',
            'edit-subscriptions',
            'send-emails',
        ]);

        $clientRole->givePermissionTo([
            'view-subscriptions',
            'create-subscriptions',
        ]);
    }
}
