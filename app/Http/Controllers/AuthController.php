<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function registrarCivil(Request $request)
    {
        $datos = $request->validate([
            'Nombre_completo' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        // El rol lo decide el servidor, no el formulario
        $usuario = User::create($datos + [
            'Id_rol' => Role::where('Nombre_rol', 'civil')->value('id'),
        ]);

        return $this->respuestaConToken($usuario, 201);
    }

    public function registrarFundacion(Request $request)
    {
        $datos = $request->validate([
            'Nombre_organizacion' => 'required',
            'Nombre_representante' => 'required',
            'Cedula_representante' => 'required',
            'Telefono' => 'required',
            'Direccion' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            // Opcionales: fotos o videos de la fundación para que el admin la verifique
            'evidencias' => 'array|max:5',
            'evidencias.*' => 'file|mimes:jpg,jpeg,png,webp,mp4,webm|max:20480',
        ]);

        $usuario = User::create([
            'Nombre_completo' => $datos['Nombre_representante'],
            'email' => $datos['email'],
            'password' => $datos['password'],
            'Telefono' => $datos['Telefono'],
            'Id_rol' => Role::where('Nombre_rol', 'fundacion')->value('id'),
        ]);

        // La relación pone Id_usuario sola; la fundación queda pendiente de verificación
        $fundacion = $usuario->foundation()->create([
            'Nombre_organizacion' => $datos['Nombre_organizacion'],
            'Nombre_representante' => $datos['Nombre_representante'],
            'Cedula_representante' => $datos['Cedula_representante'],
            'Telefono' => $datos['Telefono'],
            'Direccion' => $datos['Direccion'],
            'Email_contacto' => $datos['email'],
        ]);

        foreach ($request->file('evidencias', []) as $archivo) {
            $fundacion->evidences()->create([
                'Tipo_archivo' => $archivo->getMimeType(),
                'Tamano_bytes' => $archivo->getSize(),
                'Url' => asset('storage/'.$archivo->store('fundaciones', 'public')),
            ]);
        }

        return response()->json([
            'message' => 'Solicitud enviada. Un administrador verificará tu fundación.',
        ], 201);
    }

    public function login(Request $request)
    {
        $datos = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $usuario = User::where('email', $datos['email'])->first();

        if (! $usuario || ! Hash::check($datos['password'], $usuario->password)) {
            return response()->json(['message' => 'Correo o contraseña incorrectos.'], 401);
        }

        // Una fundación no entra hasta ser verificada
        if ($usuario->foundation && $usuario->foundation->Estado_verificacion !== 'verificada') {
            return response()->json([
                'message' => 'Tu fundación aún está pendiente de verificación por el administrador.',
            ], 403);
        }

        return $this->respuestaConToken($usuario);
    }

    public function me(Request $request)
    {
        return $request->user()->load('role', 'userCivil', 'foundation');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }

    private function respuestaConToken(User $usuario, int $codigo = 200)
    {
        return response()->json([
            'token' => $usuario->createToken('react')->plainTextToken,
            'usuario' => $usuario->load('role', 'userCivil', 'foundation'),
        ], $codigo);
    }
}
