<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Denunciation extends Model
{
   use HasFactory;

    protected $fillable = [
        'Tipo_denuncia',
        'Descripcion_hecho',
        'Ubicacion',
        'Latitud',
        'Longitud',
        'Anonima',
        'Estado_denuncia',
        'Fecha_denuncia',
        'Urgencia',
        'Id_usuario',
        'Id_fundacion_asignada',
        'Fecha_asignacion',
    ];

    // Nunca se envía quién hizo la denuncia (hay denuncias anónimas)
    protected $hidden = ['Id_usuario'];

    public function user() {
        return $this->belongsTo('App\Models\User', 'Id_usuario');
    }
    public function evidences() {
        return $this->hasMany('App\Models\DenunciationEvidence', 'Id_denuncia');
    }
    public function fundacionAsignada() {
        return $this->belongsTo('App\Models\Foundation', 'Id_fundacion_asignada');
    }

    // Util para mostrar "Reportado por: <fundación>" vs "Usuario registrado" vs "Anónimo"
    // sin repetir esta lógica en cada controlador/vista
    public function reportadoPorFundacion() {
        if ($this->Anonima) {
            return null;
        }
        return $this->user ? $this->user->foundation : null;
    }
}
