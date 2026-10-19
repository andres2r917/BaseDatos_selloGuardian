<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usercivil extends Model
{
    use HasFactory;

    protected $table = 'usercivils';

    protected $fillable = [
        'Documento_identidad',
        'Fecha_nacimiento',
        'Direccion',
        'Tipo_vivienda',
        'Ocupacion',
        'Salario',
        'Telefono',
        'Tiene_hijos',
        'Id_usuario',
    ];

    public function user() {
        return $this->belongsTo('App\Models\User', 'Id_usuario');
    }
    public function adoptionApplications() {
        // Corregido: adoption_applications usa Id_usuarioCivil, no Id_usuario
        return $this->hasMany('App\Models\Adoption_application', 'Id_usuarioCivil');
    }
}
