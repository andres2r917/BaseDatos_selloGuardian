<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DenunciationEvidence extends Model
{
    use HasFactory;

    protected $table = 'denunciation_evidences';

    protected $fillable = [
        'Url',
        'Tipo_archivo',
        'Tamano_bytes',
        'Id_denuncia',
    ];

    public function denunciation() {
        return $this->belongsTo('App\Models\Denunciation', 'Id_denuncia');
    }

    // Util para el frontend/API: saber si es imagen o video sin repetir el startsWith() en cada vista
    public function esVideo() {
        return str_starts_with($this->Tipo_archivo ?? '', 'video/');
    }
}
