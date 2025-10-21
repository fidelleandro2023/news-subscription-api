<?php
namespace Database\Seeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si ya existen usuarios para evitar duplicados
        if (User::where('email', 'admin@cafifapps.com')->exists()) {
            return; // Si ya existe el admin, no crear usuarios duplicados
        }
        
        // Crear usuario administrador
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@cafifapps.com',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now()
        ]);

        // Crear usuario editor
        $editor = User::create([
            'name' => 'Editor Principal',
            'email' => 'editor@cafifapps.com',
            'password' => Hash::make('editor123'),
            'email_verified_at' => now()
        ]);

        // Crear usuarios clientes de ejemplo
        $client1 = User::create([
            'name' => 'Juan Pérez',
            'email' => 'juan.perez@example.com',
            'password' => Hash::make('cliente123'),
            'email_verified_at' => now()
        ]);

        $client2 = User::create([
            'name' => 'María García',
            'email' => 'maria.garcia@example.com',
            'password' => Hash::make('cliente123'),
            'email_verified_at' => now()
        ]);

        $client3 = User::create([
            'name' => 'Carlos López',
            'email' => 'carlos.lopez@example.com',
            'password' => Hash::make('cliente123'),
            'email_verified_at' => now()
        ]);

        // Asignar roles
        $adminRole = Role::where('name', 'Administrador')->first();
        $editorRole = Role::where('name', 'Editor')->first();
        $clientRole = Role::where('name', 'Cliente')->first();

        if ($adminRole) {
            $admin->assignRole($adminRole);
        }

        if ($editorRole) {
            $editor->assignRole($editorRole);
        }

        if ($clientRole) {
            $client1->assignRole($clientRole);
            $client2->assignRole($clientRole);
            $client3->assignRole($clientRole);
        }
    }
}
