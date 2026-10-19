<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adoption_application extends Model
{
    use HasFactory;

    protected $fillable = [
        'Mensaje_presentacion',
        'Experiencia_mascotas',
        'Tiempo_disponible',
        'Compromiso_veterinario',
        'Estado_solicitud',
        'Comentarios_fundacion',
        'Fecha_envio',
        'Id_usuarioCivil',
        'Id_mascota',
        'Id_fundacion',
    ];

    public function userCivil() {
        return $this->belongsTo('App\Models\Usercivil', 'Id_usuarioCivil');
    }
    public function pet() {
        return $this->belongsTo('App\Models\Pet', 'Id_mascota');
    }
    public function foundation() {
        return $this->belongsTo('App\Models\Foundation', 'Id_fundacion');
    } 
}
