<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Cria os papéis (roles)
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $userRole  = Role::firstOrCreate(['name' => 'user',  'guard_name' => 'web']);

        // Cria o usuário administrador
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin'),
            ]
        );

        // Atribui o papel admin ao usuário
        $adminUser->assignRole($adminRole);
    }
}
