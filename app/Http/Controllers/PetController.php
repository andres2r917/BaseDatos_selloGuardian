<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Pet;

class PetController extends Controller
{
    public function consulta1()
    {
        $respuesta = Pet::with(['foundation', 'adoptionApplications'])->get();
        return response()->json($respuesta);
    }

    public function index()
    {
        return response()->json(Pet::all());
    }

    public function show(Pet $pet)
    {
        return response()->json($pet);
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nombre' => 'required',
            'Id_fundacion' => 'required',
        ]);

        $pet = Pet::create($request->all());

        return response()->json($pet, 201);
    }

    public function update(Request $request, Pet $pet)
    {
        $request->validate([
            'Nombre' => 'required',
        ]);

        $pet->update($request->all());

        return response()->json($pet);
    }

    public function destroy(Pet $pet)
    {
        $pet->delete();

        return response()->json(['message' => 'Mascota eliminada correctamente']);
    }

    // VER MASCOTAS DISPONIBLES
    public function consulta5()
    {
        $pets = Pet::where('Estado_adopcion', 'disponible')->get();
        return response()->json($pets);
    }

    // CONSULTA PARA VER LAS MASCOTAS CON SU FUNDACIÓN
    public function prueba_pet()
    {
        $pets = Pet::with('foundation')->get();
        return response()->json($pets);
    }
}
