<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('denunciation_evidences', function (Blueprint $table) {
            $table->id();
            $table->string('Url');
            // Mime type completo: image/jpeg, image/png, video/mp4, video/webm, etc.
            // Con esto el frontend decide cómo mostrarlo (<img> o <video>) sin necesitar otra columna
            $table->string('Tipo_archivo')->nullable();
            // Tamaño en bytes, útil para mostrar el peso en el panel admin y para validar límites de video
            $table->unsignedBigInteger('Tamano_bytes')->nullable();

            $table->unsignedBigInteger('Id_denuncia');
            $table->foreign('Id_denuncia')
                ->references('id')
                ->on('denunciations')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('denunciation_evidences');
    }
};
