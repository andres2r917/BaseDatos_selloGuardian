<?php

namespace App\Http\Controllers;

use App\Models\Denunciation;
use Illuminate\Http\Request;

class DenunciationController extends Controller
{
    // De la fundación asignada solo se envían estos datos
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
            // Solo sale el texto de "reportado por", nunca el usuario
            $denuncia->makeHidden('user');
        }

        return $denuncias;
    }

    // Pestaña "Mías"
    public function mias(Request $request)
    {
        return Denunciation::with('evidences', self::FUNDACION)
            ->where('Id_usuario', $request->user()->id)
            ->latest('Fecha_denuncia')
            ->get();
    }

    // "Hacerme cargo": el UPDATE solo afecta si nadie la tomó antes
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

    // El admin cambia cualquier estado; la fundación solo el de su propio caso
    public function cambiarEstado(Request $request, Denunciation $denuncia)
    {
        $datos = $request->validate([
            'Estado_denuncia' => 'required|in:pendiente,verificada,en_rescate,resuelta,descartada',
        ]);

        $usuario = $request->user();
        $esAdmin = $usuario->role->Nombre_rol === 'admin';
        $esSuCaso = $usuario->foundation && $denuncia->Id_fundacion_asignada === $usuario->foundation->id;

        abort_if(! $esAdmin && ! $esSuCaso, 403, 'No puedes cambiar el estado de esta denuncia.');

        $denuncia->update($datos);

        return $denuncia->fresh(['evidences', self::FUNDACION]);
    }

    // Puede enviarla un invitado (anónima) o un usuario con token
    public function store(Request $request)
    {
        $datos = $request->validate([
            'Tipo_denuncia' => 'required',
            'Descripcion_hecho' => 'required',
            'Ubicacion' => 'required',
            'Latitud' => 'nullable|numeric',
            'Longitud' => 'nullable|numeric',
            'Anonima' => 'boolean',
            'Urgencia' => 'in:Alta,Media,Baja',
            'evidencias' => 'array|max:5',
            // Si solo van a aceptar fotos: quitar mp4 y webm, y bajar max a 5120
            'evidencias.*' => 'file|mimes:jpg,jpeg,png,webp,mp4,webm|max:20480',
        ]);

        // Se guarda quién fue (para "Mías") aunque sea anónima; la API nunca lo devuelve
        $denuncia = Denunciation::create($datos + [
            'Id_usuario' => $request->user('sanctum')?->id,
        ]);

        foreach ($request->file('evidencias', []) as $archivo) {
            $denuncia->evidences()->create([
                'Tipo_archivo' => $archivo->getMimeType(),
                'Tamano_bytes' => $archivo->getSize(),
                'Url' => asset('storage/'.$archivo->store('denuncias', 'public')),
            ]);
        }

        return response()->json($denuncia->fresh(['evidences', self::FUNDACION]), 201);
    }

    private function quienReporto(Denunciation $denuncia)
    {
        if ($denuncia->Anonima || ! $denuncia->user) {
            return ['tipo' => 'anonimo', 'nombre' => 'Denuncia anónima'];
        }

        // Método que ya existe en el modelo Denunciation
        $fundacion = $denuncia->reportadoPorFundacion();

        if ($fundacion) {
            return ['tipo' => 'fundacion', 'nombre' => $fundacion->Nombre_organizacion];
        }

        return ['tipo' => 'civil', 'nombre' => 'Usuario registrado'];
    }
}
