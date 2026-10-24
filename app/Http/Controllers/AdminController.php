<?php

namespace App\Http\Controllers;

use App\Models\Administrator;
use App\Models\Denunciation;
use App\Models\Foundation;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    // Solo se envían estos datos de la fundación asignada
    private const FUNDACION = 'fundacionAsignada:id,Nombre_organizacion,Telefono';

    // Cifras del dashboard
    public function estadisticas()
    {
        $activas = Denunciation::whereIn('Estado_denuncia', ['pendiente', 'verificada', 'en_rescate'])->count();
        $resueltas = Denunciation::where('Estado_denuncia', 'resuelta')->count();
        $validas = Denunciation::where('Estado_denuncia', '!=', 'descartada')->count();

        return response()->json([
            'denuncias_activas' => $activas,
            'casos_resueltos' => $resueltas,
            'adopciones_concretadas' => Pet::where('Estado_adopcion', 'adoptado')->count(),
            // Porcentaje de denuncias válidas que terminaron resueltas
            'tasa_resolucion' => $validas > 0 ? round($resueltas / $validas * 100) : 0,
            'usuarios_civiles' => User::whereHas('role', fn ($q) => $q->where('Nombre_rol', 'civil'))->count(),
            'fundaciones_verificadas' => Foundation::where('Estado_verificacion', 'verificada')->count(),
            'fundaciones_pendientes' => Foundation::where('Estado_verificacion', 'pendiente')->count(),
            'mascotas_disponibles' => Pet::where('Estado_adopcion', 'disponible')->count(),
        ]);
    }

    // Usuarios civiles, con su perfil y cuántas denuncias hicieron
    public function civiles()
    {
        return User::whereHas('role', fn ($q) => $q->where('Nombre_rol', 'civil'))
            ->with('userCivil')
            ->withCount('denunciations')
            ->latest('Fecha_registro')
            ->get();
    }

    public function administradores()
    {
        return Administrator::with('user')->get();
    }

    // Fundaciones con el correo de su cuenta y cuántas mascotas tienen en el refugio
    public function fundaciones()
    {
        return Foundation::with('user:id,email')
            ->withCount(['pets as ocupacion' => fn ($q) => $q->where('Estado_adopcion', '!=', 'adoptado')])
            ->latest()
            ->get();
    }

    // Verificar o rechazar una fundación (si se rechaza, hay que dar el motivo)
    public function verificarFundacion(Request $request, Foundation $foundation)
    {
        $datos = $request->validate([
            'Estado_verificacion' => 'required|in:verificada,rechazada,pendiente',
            'Motivo_rechazo' => 'required_if:Estado_verificacion,rechazada|nullable|string',
        ]);

        $foundation->update([
            'Estado_verificacion' => $datos['Estado_verificacion'],
            // El motivo solo se guarda cuando se rechaza
            'Motivo_rechazo' => $datos['Estado_verificacion'] === 'rechazada' ? $datos['Motivo_rechazo'] : null,
        ]);

        return $foundation;
    }

    // Todas las denuncias, incluidas las descartadas
    public function denuncias()
    {
        return Denunciation::with('evidences', self::FUNDACION)
            ->latest('Fecha_denuncia')
            ->get();
    }

    // Asignar una denuncia a una fundación ya verificada
    public function asignarDenuncia(Request $request, Denunciation $denuncia)
    {
        $datos = $request->validate([
            'Id_fundacion_asignada' => ['required', Rule::exists('foundations', 'id')->where('Estado_verificacion', 'verificada')],
        ]);

        $denuncia->update($datos + [
            'Fecha_asignacion' => now(),
            'Estado_denuncia' => 'en_rescate',
        ]);

        return $denuncia->fresh(['evidences', self::FUNDACION]);
    }

    // Todas las mascotas, con el nombre de su fundación
    public function mascotas()
    {
        return Pet::with('foundation:id,Nombre_organizacion')->latest()->get();
    }
}
