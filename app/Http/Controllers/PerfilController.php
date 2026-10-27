<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PerfilController extends Controller
{
    public function guardar(Request $request)
    {
        $usuario = $request->user();

        $datos = $request->validate([
            // ignore() deja guardar de nuevo sin chocar con su propio documento
            'Documento_identidad' => ['required', Rule::unique('usercivils', 'Documento_identidad')->ignore($usuario->userCivil?->id)],
            'Fecha_nacimiento' => 'required|date',
            'Tipo_vivienda' => 'required',
            'Ocupacion' => 'nullable',
            'Direccion' => 'nullable',
            'Salario' => 'nullable|numeric',
            'Telefono' => 'nullable',
            'Tiene_hijos' => 'required|boolean',
        ]);

        // Id_usuario lo pone la relación (sale del token, no del formulario)
        $usuario->userCivil()->updateOrCreate([], $datos);

        // El teléfono también queda en la cuenta (users), para contactar al usuario sin buscar en su perfil
        $usuario->update(['Telefono' => $datos['Telefono'] ?? null]);

        return $usuario->load('role', 'userCivil', 'foundation');
    }

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
