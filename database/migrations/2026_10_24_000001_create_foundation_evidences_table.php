<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fotos y videos opcionales que la fundación adjunta al registrarse (el admin los ve al verificarla)
        Schema::create('foundation_evidences', function (Blueprint $table) {
            $table->id();
            $table->string('Url');
            // Mime type completo (image/jpeg, video/mp4...): el frontend decide si usa <img> o <video>
            $table->string('Tipo_archivo')->nullable();
            $table->unsignedBigInteger('Tamano_bytes')->nullable();

            $table->unsignedBigInteger('Id_fundacion');
            $table->foreign('Id_fundacion')
                ->references('id')
                ->on('foundations')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foundation_evidences');
    }
};
