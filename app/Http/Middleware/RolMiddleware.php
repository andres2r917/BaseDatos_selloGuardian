<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RolMiddleware
{
    // Se usa en las rutas así: ->middleware('rol:admin') o ->middleware('rol:admin,fundacion')
    // Deja pasar solo si el rol del usuario con sesión está en la lista.
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! in_array($request->user()->role->Nombre_rol, $roles)) {
            return response()->json(['message' => 'No tienes permiso para esta acción.'], 403);
        }

        return $next($request);
    }
}
