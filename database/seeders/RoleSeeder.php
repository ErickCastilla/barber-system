<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Rol 'admin': Tiene acceso total al sistema. Puede gestionar barberos, servicios, configuraciones y ver todo.
        Role::create(['name' => 'admin']);

        // 2. Rol 'barbero': Accede a su propia agenda, puede ver sus citas asignadas y marcarlas como completadas.
        Role::create(['name' => 'barbero']);

        // 3. Rol 'recepcionista': Encargado de la atención al cliente. Puede crear, editar o cancelar citas de cualquier barbero, pero no accede a configuraciones del sistema.
        Role::create(['name' => 'recepcionista']);

        // 4. Rol 'cliente': El usuario final que entra al sistema para agendar su propia cita.
        Role::create(['name' => 'cliente']);
    }
}
