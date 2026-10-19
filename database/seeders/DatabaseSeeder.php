<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\Role::firstOrCreate(['id' => 1], ['Nombre_rol' => 'Admin']);
        \App\Models\Role::firstOrCreate(['id' => 2], ['Nombre_rol' => 'usercivil']);
        \App\Models\Role::firstOrCreate(['id' => 3], ['Nombre_rol' => 'fundacion']);
    }
}
