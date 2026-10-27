<?php

namespace App\Http\Controllers;

use App\Models\Adoption_application;
use App\Models\Pet;
use Illuminate\Http\Request;

class AdoptionApplicationController extends Controller
{
    // Las solicitudes del usuario con sesión, con la mascota y la fundación (para avisarle qué decidieron)
    public function mias(Request $request)
    {
        $perfil = $request->user()->userCivil;

        // Sin perfil todavía no pudo enviar ninguna
        if (! $perfil) {
            return [];
        }

        return Adoption_application::where('Id_usuarioCivil', $perfil->id)
            ->with('pet:id,Nombre,Foto_url,Estado_adopcion', 'foundation:id,Nombre_organizacion,Telefono')
            ->latest('Fecha_envio')
            ->get();
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'Id_mascota' => 'required|exists:pets,id',
            'Mensaje_presentacion' => 'required',
            'Experiencia_mascotas' => 'required',
            'Tiempo_disponible' => 'required',
            'Compromiso_veterinario' => 'boolean',
        ]);

        // Sin perfil completo no se puede adoptar (Perfil.jsx crea la fila en usercivils)
        $perfil = $request->user()->userCivil;

        if (! $perfil) {
            return response()->json(['message' => 'Completa tu perfil antes de solicitar una adopción.'], 422);
        }

        $mascota = Pet::findOrFail($datos['Id_mascota']);

        // La fundación sale de la mascota y el adoptante del token, no del formulario
        $solicitud = Adoption_application::create($datos + [
            'Id_usuarioCivil' => $perfil->id,
            'Id_fundacion' => $mascota->Id_fundacion,
        ]);

        return response()->json($solicitud, 201);
    }
}
