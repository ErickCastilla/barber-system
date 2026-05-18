<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecutamos el seeder de roles que acabamos de crear
        $this->call(RoleSeeder::class);

        // User::factory(10)->create();

        // Creamos un usuario de prueba para ser el administrador principal
        $adminUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Le asignamos el rol de 'admin' usando el trait HasRoles
        $adminUser->assignRole('admin');
    }
}
