<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            // Nombre completo en un solo campo, igual a como lo pide el formulario de Registro.jsx
            $table->string('Nombre_completo');
            // Columnas en minúscula: Laravel/Sanctum las busca así por convención interna
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('Telefono')->nullable();
            $table->timestamp('Fecha_registro')->useCurrent();
            $table->rememberToken();

            $table->unsignedBigInteger('Id_rol');
            $table->foreign('Id_rol')
            ->references('id')
            ->on('roles')
            ->onDelete('cascade')
            ->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
