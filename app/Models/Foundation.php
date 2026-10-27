<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Foundation extends Model
{
    use HasFactory;

    protected $fillable = [
        'Estado_verificacion',
        'Motivo_rechazo',
        'Logo',
        'Nombre_organizacion',
        'Nit',
        'Nombre_representante',
        'Cedula_representante',
        'Telefono',
        'Direccion',
        'Redes',
        'Email_contacto',
        'Descripcion',
        'Capacidad',
        'Id_usuario',
    ];

    public function user() {
        return $this->belongsTo('App\Models\User', 'Id_usuario');
    }
    // Fotos y videos que adjuntó al registrarse (opcionales)
    public function evidences() {
        return $this->hasMany('App\Models\FoundationEvidence', 'Id_fundacion');
    }
    public function pets() {
        return $this->hasMany('App\Models\Pet', 'Id_fundacion');
    }
    public function adoptionApplications() {
        return $this->hasMany('App\Models\Adoption_application', 'Id_fundacion');
    }

    // Denuncias que esta fundación se asignó para rescatar
    public function denunciasAsignadas() {
        return $this->hasMany('App\Models\Denunciation', 'Id_fundacion_asignada');
    }
}