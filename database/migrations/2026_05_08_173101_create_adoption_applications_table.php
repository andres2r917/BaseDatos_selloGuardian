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
        Schema::create('adoption_applications', function (Blueprint $table) {
            $table->id();
            $table->text('Mensaje_presentacion');
            $table->text('Experiencia_mascotas'); 
            $table->string('Tiempo_disponible'); 
            $table->boolean('Compromiso_veterinario')->default(false);
            $table->string('Estado_solicitud')->default('pendiente');
            $table->text('Comentarios_fundacion')->nullable();

            $table->unsignedBigInteger('Id_usuarioCivil');
            $table->foreign('Id_usuarioCivil')
            ->references('id')
            ->on('usercivils')
            ->onDelete('cascade')
            ->onUpdate('cascade');

            $table->unsignedBigInteger('Id_mascota');
            $table->foreign('Id_mascota')
            ->references('id')
            ->on('pets')
            ->onDelete('cascade')
            ->onUpdate('cascade');

            $table->unsignedBigInteger('Id_fundacion');
            $table->foreign('Id_fundacion')
            ->references('id')
            ->on('foundations')
            ->onDelete('cascade')
            ->onUpdate('cascade');

            $table->dateTime('Fecha_envio')->useCurrent();
            // Necesario para que Eloquent pueda escribir created_at/updated_at sin error
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adoption_applications');
    }
};
