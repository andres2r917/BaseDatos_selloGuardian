<?php

namespace App\Http\Controllers;

use App\Models\Denunciation;
use Illuminate\Http\Request;

class DenunciationController extends Controller
{
    // Solo se envían estos datos de la fundación asignada (no su cédula ni su correo)
    private const FUNDACION = 'fundacionAsignada:id,Nombre_organizacion,Telefono';

    // Feed público: todas menos las descartadas
    public function index()
    {
        $denuncias = Denunciation::with('evidences', self::FUNDACION, 'user.foundation')
            ->where('Estado_denuncia', '!=', 'descartada')
            ->latest('Fecha_denuncia')
            ->get();

        foreach ($denuncias as $denuncia) {
            $denuncia->reportado_por = $this->quienReporto($denuncia);
            // Se oculta el usuario completo: solo sale el texto de "reportado por"
            $denuncia->makeHidden('user');
        }

        return $denuncias;
    }

    // Pestaña "Mías": solo las denuncias del usuario con sesión
    public function mias(Request $request)
    {
        return Denunciation::with('evidences', self::FUNDACION)
            ->where('Id_usuario', $request->user()->id)
            ->latest('Fecha_denuncia')
            ->get();
    }

    // "Hacerme cargo": el UPDATE solo afecta si nadie la tomó antes,
    // así dos fundaciones no se quedan con el mismo caso
    public function reclamar(Request $request, Denunciation $denuncia)
    {
        $asignadas = Denunciation::where('id', $denuncia->id)
            ->whereNull('Id_fundacion_asignada')
            ->whereIn('Estado_denuncia', ['pendiente', 'verificada'])
            ->update([
                'Id_fundacion_asignada' => $request->user()->foundation->id,
                'Fecha_asignacion' => now(),
                'Estado_denuncia' => 'en_rescate',
            ]);

        if ($asignadas === 0) {
            return response()->json(['message' => 'Este caso ya fue asignado a otra fundación.'], 409);
        }

        return $denuncia->fresh(['evidences', self::FUNDACION]);
    }

    // El admin verifica o descarta; la fundación cambia el estado de su propio caso (por ejemplo, a "resuelta")
    public function cambiarEstado(Request $request, Denunciation $denuncia)
    {
        $estado = $request->validate([
            'Estado_denuncia' => 'required|in:pendiente,verificada,en_rescate,resuelta,descartada',
        ])['Estado_denuncia'];

        $usuario = $request->user();
        $esAdmin = $usuario->role->Nombre_rol === 'admin';
        $esSuCaso = $usuario->foundation && $denuncia->Id_fundacion_asignada === $usuario->foundation->id;

        abort_if(! $esAdmin && ! $esSuCaso, 403, 'No puedes cambiar el estado de esta denuncia.');

        $denuncia->update(['Estado_denuncia' => $estado]);

        return $denuncia->fresh(['evidences', self::FUNDACION]);
    }

    // Puede enviarla un invitado (anónima) o un usuario con token
    public function store(Request $request)
    {
        $datos = $request->validate([
            'Tipo_denuncia' => 'required|string|max:50',
            'Descripcion_hecho' => 'required|string',
            'Ubicacion' => 'required|string|max:255',
            'Latitud' => 'nullable|numeric',
            'Longitud' => 'nullable|numeric',
            'Anonima' => 'boolean',
            'Urgencia' => 'nullable|in:Alta,Media,Baja',
            'evidencias' => 'array|max:5',
            // Si solo van a aceptar fotos: quitar mp4 y webm, y bajar max a 5120
            'evidencias.*' => 'file|mimes:jpg,jpeg,png,webp,mp4,webm|max:20480',
        ]);

        $denuncia = Denunciation::create([
            'Tipo_denuncia' => $datos['Tipo_denuncia'],
            'Descripcion_hecho' => $datos['Descripcion_hecho'],
            'Ubicacion' => $datos['Ubicacion'],
            'Latitud' => $datos['Latitud'] ?? null,
            'Longitud' => $datos['Longitud'] ?? null,
            'Anonima' => $datos['Anonima'] ?? false,
            'Urgencia' => $datos['Urgencia'] ?? 'Media',
            // Si trae token se guarda quién fue (para "Mías"), aunque sea anónima
            'Id_usuario' => $request->user('sanctum')?->id,
        ]);

        foreach ($request->file('evidencias', []) as $archivo) {
            $tipo = $archivo->getMimeType();
            $tamano = $archivo->getSize();
            $ruta = $archivo->store('denuncias', 'public');

            $denuncia->evidences()->create([
                // Se guarda la URL completa, igual que las de ejemplo (Unsplash)
                'Url' => asset('storage/'.$ruta),
                'Tipo_archivo' => $tipo,
                'Tamano_bytes' => $tamano,
            ]);
        }

        // fresh() vuelve a leer la fila para incluir los valores por defecto de la base
        return response()->json($denuncia->fresh(['evidences', self::FUNDACION]), 201);
    }

    private function quienReporto(Denunciation $denuncia): array
    {
        if ($denuncia->Anonima || ! $denuncia->user) {
            return ['tipo' => 'anonimo', 'nombre' => 'Denuncia anónima'];
        }

        if ($denuncia->user->foundation) {
            return ['tipo' => 'fundacion', 'nombre' => $denuncia->user->foundation->Nombre_organizacion];
        }

        return ['tipo' => 'civil', 'nombre' => 'Usuario registrado'];
    }
}
