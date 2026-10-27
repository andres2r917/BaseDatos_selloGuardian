<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoundationEvidence extends Model
{
    use HasFactory;

    protected $table = 'foundation_evidences';

    protected $fillable = [
        'Url',
        'Tipo_archivo',
        'Tamano_bytes',
        'Id_fundacion',
    ];

    public function foundation() {
        return $this->belongsTo('App\Models\Foundation', 'Id_fundacion');
    }
}
