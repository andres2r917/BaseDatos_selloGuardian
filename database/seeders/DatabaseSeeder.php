<?php

namespace Database\Seeders;

use App\Models\Administrator;
use App\Models\Foundation;
use App\Models\PlatformSetting;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Los nombres de rol son los mismos que usa React: admin, civil, fundacion
        $rolAdmin = Role::firstOrCreate(['Nombre_rol' => 'admin']);
        $rolCivil = Role::firstOrCreate(['Nombre_rol' => 'civil']);
        $rolFundacion = Role::firstOrCreate(['Nombre_rol' => 'fundacion']);

        // Una sola fila de configuración de la plataforma
        PlatformSetting::firstOrCreate([], [
            'Email_soporte' => 'soporte@selloguardian.com',
            'Telefono_emergencia' => '+57 300 000 0000',
        ]);

        // Los mismos usuarios que tenía usuariosDemo.js en React
        $admin = User::firstOrCreate(['email' => 'admin@selloguardian.com'], [
            'Nombre_completo' => 'Administrador Sello Guardián',
            'password' => 'Admin123*',
            'Id_rol' => $rolAdmin->id,
        ]);

        // La pestaña "Administradores" lee la tabla administrators
        Administrator::firstOrCreate(['Id_usuario' => $admin->id], ['Nivel_acceso' => 1]);

        User::firstOrCreate(['email' => 'civil@selloguardian.com'], [
            'Nombre_completo' => 'Carlos Ruiz',
            'password' => 'Civil123*',
            'Id_rol' => $rolCivil->id,
        ]);

        $usuarioFundacion = User::firstOrCreate(['email' => 'fundacion@selloguardian.com'], [
            'Nombre_completo' => 'María Elena Gómez',
            'Telefono' => '+57 310 000 0001',
            'password' => 'Fundacion123*',
            'Id_rol' => $rolFundacion->id,
        ]);

        $fundacion = Foundation::firstOrCreate(['Id_usuario' => $usuarioFundacion->id], [
            'Estado_verificacion' => 'verificada',
            'Nombre_organizacion' => 'Huellitas Felices',
            'Nombre_representante' => 'María Elena Gómez',
            'Cedula_representante' => '1061234567',
            'Telefono' => '+57 310 000 0001',
            'Direccion' => 'Vereda Pomona, Km 2',
            'Email_contacto' => 'contacto@huellitasfelices.org',
        ]);

        $fundacion->pets()->createMany([
            [
                'Nombre' => 'Max', 'Especie' => 'perro', 'Raza' => 'Labrador', 'Sexo' => 'Macho',
                'Edad_estimada' => '3 años', 'Tamano' => 'grande', 'Estado_salud' => 'Vacunado',
                'Temperamento' => 'Tranquilo', 'Vacunado' => true, 'Esterilizado' => true, 'Desparasitado' => true,
                'Descripcion_personalidad' => 'Tranquilo y obediente. Ya tiene entrenamiento básico.',
                'Foto_url' => 'https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=400&h=300&fit=crop',
            ],
            [
                'Nombre' => 'Luna', 'Especie' => 'gato', 'Raza' => 'Criolla', 'Sexo' => 'Hembra',
                'Edad_estimada' => '1 año', 'Tamano' => 'pequeño', 'Estado_salud' => 'Vacunado',
                'Temperamento' => 'Cariñosa', 'Vacunado' => true, 'Esterilizado' => false, 'Desparasitado' => true,
                'Descripcion_personalidad' => 'Muy dulce, fue rescatada de la calle.',
                'Foto_url' => 'https://images.unsplash.com/photo-1537151625747-768eb6cf92b2?w=400&h=300&fit=crop',
            ],
            [
                'Nombre' => 'Rocky', 'Especie' => 'perro', 'Raza' => 'Bulldog', 'Sexo' => 'Macho',
                'Edad_estimada' => '5 años', 'Tamano' => 'grande', 'Estado_salud' => 'Tratamiento activo',
                'Temperamento' => 'Protector', 'Vacunado' => true, 'Esterilizado' => true, 'Desparasitado' => true,
                'Descripcion_personalidad' => 'Leal y protector, en tratamiento dermatológico.',
                'Foto_url' => 'https://images.unsplash.com/photo-1561037404-61cd46aa615b?w=400&h=300&fit=crop',
            ],
        ]);
    }
}
