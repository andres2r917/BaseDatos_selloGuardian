<?php

namespace App\Http\Controllers;

use App\Models\Foundation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Registro.jsx (usuario civil)
    public function registrarCivil(Request $request)
    {
        $datos = $request->validate([
            'Nombre_completo' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        // El rol lo decide el servidor, nunca el cliente
        $usuario = User::create($datos + [
            'Id_rol' => Role::where('Nombre_rol', 'civil')->value('id'),
        ]);

        return $this->respuestaConToken($usuario, 201);
    }

    // RegistroFundacion.jsx: crea el usuario y la fundación juntos
    public function registrarFundacion(Request $request)
    {
        $datos = $request->validate([
            'Nombre_organizacion' => 'required|string|max:255',
            'Nombre_representante' => 'required|string|max:255',
            'Cedula_representante' => 'required|string|max:30',
            'Telefono' => 'required|string|max:50',
            'Direccion' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        // Transacción: se crean los dos registros o ninguno
        DB::transaction(function () use ($datos) {
            $usuario = User::create([
                'Nombre_completo' => $datos['Nombre_representante'],
                'email' => $datos['email'],
                'password' => $datos['password'],
                'Telefono' => $datos['Telefono'],
                'Id_rol' => Role::where('Nombre_rol', 'fundacion')->value('id'),
            ]);

            Foundation::create([
                'Nombre_organizacion' => $datos['Nombre_organizacion'],
                'Nombre_representante' => $datos['Nombre_representante'],
                'Cedula_representante' => $datos['Cedula_representante'],
                'Telefono' => $datos['Telefono'],
                'Direccion' => $datos['Direccion'],
                'Email_contacto' => $datos['email'],
                'Id_usuario' => $usuario->id,
            ]);
        });

        // Sin token: queda pendiente de verificación
        return response()->json([
            'message' => 'Solicitud enviada. Un administrador verificará tu fundación.',
        ], 201);
    }

    public function login(Request $request)
    {
        $datos = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
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

    // Devuelve el usuario de la sesión (para cuando se recarga la página)
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
