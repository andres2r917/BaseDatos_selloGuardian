<?php

namespace App\Http\Controllers;

use App\Models\PlatformSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    // Público: lo usa la pantalla de denuncias cuando un caso aún no tiene fundación
    public function telefonoEmergencia()
    {
        return response()->json([
            'Telefono_emergencia' => PlatformSetting::value('Telefono_emergencia'),
        ]);
    }

    // AdminConfiguracion.jsx: la plataforma tiene una sola fila de configuración
    public function mostrar()
    {
        return PlatformSetting::firstOrFail();
    }

    public function actualizar(Request $request)
    {
        $datos = $request->validate([
            'Nombre_plataforma' => 'sometimes|required',
            'Email_soporte' => 'nullable|email',
            'Notificaciones_activas' => 'boolean',
            'Modo_mantenimiento' => 'boolean',
            'Telefono_emergencia' => 'nullable',
        ]);

        $configuracion = PlatformSetting::firstOrFail();
        $configuracion->update($datos);

        return $configuracion;
    }
}
