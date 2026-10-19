<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    use HasFactory;

    protected $table = 'platform_settings';

    protected $fillable = [
        'Nombre_plataforma',
        'Email_soporte',
        'Notificaciones_activas',
        'Modo_mantenimiento',
        'Telefono_emergencia',
    ];
}
