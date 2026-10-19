<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usercivil;

class UsercivilController extends Controller
{
    public function consulta1()
    {
        return response()->json(Usercivil::with(['user', 'adoptionApplications'])->get());
    }

    public function index()
    {
        return response()->json(Usercivil::all());
    }

    public function show(Usercivil $usercivil)
    {
        return response()->json($usercivil);
    }

    public function store(Request $request)
    {
        $request->validate([
            'Documento_identidad' => 'required',
            'Id_usuario'          => 'required',
        ]);

        return response()->json(Usercivil::create($request->all()), 201);
    }

    public function update(Request $request, Usercivil $usercivil)
    {
        $request->validate([
            'Documento_identidad' => 'required',
        ]);

        $usercivil->update($request->all());

        return response()->json($usercivil);
    }

    public function destroy(Usercivil $usercivil)
    {
        $usercivil->delete();

        return response()->json(['message' => 'Usuario civil eliminado correctamente']);
    }

    public function consulta2(){
        $usercivils = Usercivil::find(1);
        return $usercivils;
    }

    public function prueba_usercivil(){
        $usercivil = Usercivil::find(1);
        return [
            "usercivil" => $usercivil,
            "user" => $usercivil ? $usercivil->user : null,
        ];
    }
}
