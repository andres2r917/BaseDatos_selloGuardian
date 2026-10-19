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
        Schema::create('foundations', function (Blueprint $table) {
            $table->id();
            $table->string('Estado_verificacion')->default('pendiente');
            $table->text('Motivo_rechazo')->nullable();
            $table->string('Logo')->nullable();
            $table->string('Nombre_organizacion');
            // NIT de la organización (identificador tributario)
            $table->string('Nit')->nullable()->unique()->default(null);
            $table->string('Nombre_representante');
            // Cédula del representante legal, distinta del NIT de la organización
            $table->string('Cedula_representante');
            $table->string('Telefono');
            $table->string('Direccion');
            $table->string('Redes')->nullable();
            // Correo público/de contacto de la fundación, no el correo de login (ese es users.email)
            $table->string('Email_contacto')->nullable();
            $table->text('Descripcion')->nullable();

            // Ya no hay columna de contraseña aquí: el login de fundación usa users.password
            $table->unsignedBigInteger('Id_usuario')->unique();
            $table->foreign('Id_usuario')
            ->references('id')
            ->on('users')
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
        Schema::dropIfExists('foundations');
    }
};
