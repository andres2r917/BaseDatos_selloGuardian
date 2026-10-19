<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Denunciation;

class DenunciationController extends Controller
{
    public function consulta1()
    {
        return response()->json(Denunciation::with(['user', 'evidences', 'fundacionAsignada'])->get());
    }

    public function index()
    {
        return response()->json(Denunciation::all());
    }

    public function show(Denunciation $denunciation)
    {
        return response()->json($denunciation->load('evidences'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Tipo_denuncia' => 'required',
            'Ubicacion'     => 'required',
        ]);

        return response()->json(Denunciation::create($request->all()), 201);
    }

    public function update(Request $request, Denunciation $denunciation)
    {
        $request->validate([
            'Tipo_denuncia' => 'required',
        ]);

        $denunciation->update($request->all());

        return response()->json($denunciation);
    }

    public function destroy(Denunciation $denunciation)
    {
        $denunciation->delete();

        return response()->json(['message' => 'Denuncia eliminada correctamente']);
    }

    public function consulta9(){
        $denunciations = Denunciation::where('Estado_denuncia', 'pendiente')->get();
        return $denunciations;
    }
    
    public function prueba_denunciation(){
        $denunciation = Denunciation::find(1);
        return [
            'denunciation' => $denunciation,
            'user' => $denunciation->user,
        ];
    }
}
