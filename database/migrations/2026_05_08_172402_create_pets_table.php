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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->string('Nombre');
            $table->string('Especie')->default('perro'); // perro, gato, ave, etc
            $table->string('Raza');
            $table->string('Edad_estimada');
            // Sin ñ en el nombre de columna, evita problemas de encoding al mapear JSON en el frontend
            $table->string('Tamano');
            $table->string('Estado_salud');
            $table->text('Descripcion_personalidad');
            $table->string('Foto_url')->nullable();
            $table->string('Estado_adopcion')->default('disponible');

            $table->unsignedBigInteger('Id_fundacion');
            $table->foreign('Id_fundacion')
            ->references('id')
            ->on('foundations')
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
        Schema::dropIfExists('pets');
    }
};
