<?php

namespace App\Http\Controllers;

use App\Models\Adoption_application;
use App\Models\Pet;
use Illuminate\Http\Request;

class AdoptionApplicationController extends Controller
{
    // Formulario de Adopcion.jsx
    public function store(Request $request)
    {
        $datos = $request->validate([
            'Id_mascota' => 'required|exists:pets,id',
            'Mensaje_presentacion' => 'required|string',
            'Experiencia_mascotas' => 'required|string',
            'Tiempo_disponible' => 'required|string|max:50',
            'Compromiso_veterinario' => 'boolean',
        ]);

        // Sin perfil no se puede adoptar: Perfil.jsx crea la fila en usercivils
        $perfil = $request->user()->userCivil;

        if (! $perfil) {
            return response()->json(['message' => 'Completa tu perfil antes de solicitar una adopción.'], 422);
        }

        $mascota = Pet::findOrFail($datos['Id_mascota']);

        $solicitud = Adoption_application::create($datos + [
            // Estos dos salen del servidor, no del cliente
            'Id_usuarioCivil' => $perfil->id,
            'Id_fundacion' => $mascota->Id_fundacion,
        ]);

        return response()->json($solicitud, 201);
    }
}
