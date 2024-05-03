<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создаем роль "client"
        Role::create(['name' => 'client']);

        // Создаем роль "admin"
        Role::create(['name' => 'admin']);

        // Создаем роль "courier"
        Role::create(['name' => 'courier']);
    }
}
