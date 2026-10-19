<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Foundation;

class FoundationController extends Controller
{
    //
    public function consulta1()
    {
        return response()->json(Foundation::with(['user', 'pets'])->get());
    }

    public function index()
    {
        return response()->json(Foundation::all());
    }

    public function show(Foundation $foundation)
    {
        return response()->json($foundation);
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nombre_organizacion' => 'required',
            'Id_usuario'          => 'required',
        ]);

        return response()->json(Foundation::create($request->all()), 201);
    }

    public function update(Request $request, Foundation $foundation)
    {
        $request->validate([
            'Nombre_organizacion' => 'required',
        ]);

        $foundation->update($request->all());

        return response()->json($foundation);
    }

    public function destroy(Foundation $foundation)
    {
        $foundation->delete();

        return response()->json(['message' => 'Fundación eliminada correctamente']);
    }

    public function consulta7(){
        $foundations = Foundation::find(1);
        return $foundations;
    }
    public function consulta8(){
        $foundations = Foundation::where('Nombre_organizacion', 'like', 'Patitas%')->get();
        return $foundations;
    }
    public function prueba_foundation(){
    $foundation = Foundation::find(1);
    return [
        'foundation' => $foundation,
        'pets' => $foundation->pets,
        'adoptionApplications' => $foundation->adoptionApplications,
    ];
}
}
