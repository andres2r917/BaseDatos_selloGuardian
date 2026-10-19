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
        Schema::create('denunciations', function (Blueprint $table) {
            $table->id();
            $table->string('Tipo_denuncia');
            $table->text('Descripcion_hecho');
            $table->string('Ubicacion');
            $table->decimal('Latitud', 10, 7)->nullable();
            $table->decimal('Longitud', 10, 7)->nullable();
            // Evidencia_url se elimina: ahora puede haber varios archivos, viven en denunciation_evidences
            $table->boolean('Anonima')->default(false);
            $table->string('Estado_denuncia')->default('pendiente');

            $table->unsignedBigInteger('Id_usuario')->nullable();
            $table->foreign('Id_usuario')
            ->references('id')
            ->on('users')
            ->onDelete('set null')
            ->onUpdate('set null');

            $table->dateTime('Fecha_denuncia')->useCurrent();
            // Nivel de urgencia para que el feed comunitario pueda ordenar/priorizar casos
            $table->enum('Urgencia', ['Alta', 'Media', 'Baja'])->default('Media');

            // Fundación que se hizo cargo del caso (nullable: puede no haber ninguna asignada todavía)
            $table->unsignedBigInteger('Id_fundacion_asignada')->nullable();
            $table->foreign('Id_fundacion_asignada')
                ->references('id')
                ->on('foundations')
                ->onDelete('set null')
                ->onUpdate('set null');
            $table->timestamp('Fecha_asignacion')->nullable();

            // Necesario para que Eloquent pueda escribir created_at/updated_at sin error
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('denunciations');
    }
};
