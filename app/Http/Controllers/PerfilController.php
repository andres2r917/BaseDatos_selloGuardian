<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PerfilController extends Controller
{
    // Perfil.jsx: crea o actualiza los datos del usuario civil que tiene la sesión
    public function guardar(Request $request)
    {
        $usuario = $request->user();

        abort_if($usuario->role->Nombre_rol !== 'civil', 403, 'Solo los usuarios civiles tienen este perfil.');

        $datos = $request->validate([
            // ignore(): permite guardar de nuevo sin chocar con su propio documento
            'Documento_identidad' => ['required', 'string', 'max:30', Rule::unique('usercivils', 'Documento_identidad')->ignore($usuario->userCivil?->id)],
            'Fecha_nacimiento' => 'required|date',
            'Tipo_vivienda' => 'required|string|max:50',
            'Ocupacion' => 'nullable|string|max:255',
            'Direccion' => 'nullable|string|max:255',
            'Salario' => 'nullable|numeric|min:0',
            'Telefono' => 'nullable|string|max:50',
            'Tiene_hijos' => 'required|boolean',
        ]);

        // Id_usuario lo pone la relación (sale del token, no del cliente)
        $usuario->userCivil()->updateOrCreate([], $datos);

        return $usuario->load('role', 'userCivil', 'foundation');
    }

    // Foto de perfil de cualquier usuario (multipart, campo "Foto")
    public function subirFoto(Request $request)
    {
        $request->validate(['Foto' => 'required|image|max:2048']);

        $usuario = $request->user();
        $usuario->update([
            'Foto_url' => asset('storage/'.$request->file('Foto')->store('perfiles', 'public')),
        ]);

        return $usuario->load('role', 'userCivil', 'foundation');
    }
}
