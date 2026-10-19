<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();
            $table->string('Nombre_plataforma')->default('Sello Guardián');
            $table->string('Email_soporte')->nullable();
            $table->boolean('Notificaciones_activas')->default(true);
            $table->boolean('Modo_mantenimiento')->default(false);
            // Línea de contacto general (autoridades/emergencia), usada como respaldo cuando
            // una denuncia todavía no tiene Id_fundacion_asignada
            $table->string('Telefono_emergencia')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_settings');
    }
};
