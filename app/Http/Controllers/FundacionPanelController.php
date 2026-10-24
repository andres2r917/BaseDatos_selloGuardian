<?php

namespace App\Http\Controllers;

use App\Models\Adoption_application;
use App\Models\Pet;
use Illuminate\Http\Request;

class FundacionPanelController extends Controller
{
    // ── Mascotas ────────────────────────────────────────────────

    // Todas las mascotas de la fundación (Expedientes y Kanban)
    public function mascotas(Request $request)
    {
        return $request->user()->foundation->pets()->latest()->get();
    }

    // Formulario "Ingresar mascota". La relación pone Id_fundacion sola.
    public function crearMascota(Request $request)
    {
        $datos = $request->validate($this->reglasMascota('required'));

        $mascota = $request->user()->foundation->pets()->create(
            $this->conFoto($request, $datos) + ['Estado_adopcion' => 'en_cuarentena']
        );

        return response()->json($mascota, 201);
    }

    // Editar datos o mover la tarjeta en el Kanban (Estado_adopcion)
    public function actualizarMascota(Request $request, Pet $pet)
    {
        $this->soloMiaMascota($request, $pet);

        $datos = $request->validate($this->reglasMascota('sometimes|required'));
        $pet->update($this->conFoto($request, $datos));

        return $pet;
    }

    // ── Solicitudes de adopción ─────────────────────────────────

    public function solicitudes(Request $request)
    {
        return $request->user()->foundation
            ->adoptionApplications()
            ->with('userCivil.user', 'pet')
            ->latest('Fecha_envio')
            ->get();
    }

    // Aprobar, rechazar o pasar a evaluación. Al aprobar, la mascota queda "en_proceso".
    public function resolverSolicitud(Request $request, Adoption_application $solicitud)
    {
        abort_if($solicitud->Id_fundacion !== $request->user()->foundation->id, 403, 'Esta solicitud no es de tu fundación.');

        $datos = $request->validate([
            'Estado_solicitud' => 'required|in:pendiente,en_evaluacion,aprobada,rechazada',
            'Comentarios_fundacion' => 'nullable|string',
        ]);

        $solicitud->update([
            'Estado_solicitud' => $datos['Estado_solicitud'],
            'Comentarios_fundacion' => $datos['Comentarios_fundacion'] ?? '',
        ]);

        if ($datos['Estado_solicitud'] === 'aprobada') {
            $solicitud->pet->update(['Estado_adopcion' => 'en_proceso']);
        }

        return $solicitud->load('userCivil.user', 'pet');
    }

    // ── Ajustes de la fundación ─────────────────────────────────

    // FundacionAjustes.jsx: cada tarjeta manda solo sus campos, por eso "sometimes"
    public function actualizarPerfil(Request $request)
    {
        $datos = $request->validate([
            'Nombre_organizacion' => 'sometimes|required|string|max:255',
            'Nombre_representante' => 'sometimes|required|string|max:255',
            'Cedula_representante' => 'sometimes|required|string|max:30',
            'Telefono' => 'sometimes|required|string|max:50',
            'Direccion' => 'sometimes|required|string|max:255',
            'Redes' => 'nullable|string|max:255',
            'Descripcion' => 'nullable|string',
            'Capacidad' => 'sometimes|required|integer|min:1',
        ]);

        $request->user()->foundation->update($datos);

        return $request->user()->load('role', 'userCivil', 'foundation');
    }

    // ── Auxiliares ──────────────────────────────────────────────

    private function reglasMascota(string $presencia): array
    {
        return [
            'Nombre' => "$presencia|string|max:100",
            'Especie' => "$presencia|string|max:30",
            'Raza' => "$presencia|string|max:100",
            'Edad_estimada' => "$presencia|string|max:50",
            'Tamano' => "$presencia|string|max:30",
            'Estado_salud' => "$presencia|string|max:100",
            'Descripcion_personalidad' => "$presencia|string",
            'Sexo' => 'nullable|string|max:20',
            'Temperamento' => 'nullable|string|max:50',
            'Convivencia' => 'nullable|string|max:150',
            'Vacunado' => 'boolean',
            'Esterilizado' => 'boolean',
            'Desparasitado' => 'boolean',
            'Estado_adopcion' => 'in:en_cuarentena,disponible,en_proceso,adoptado',
            // La foto puede venir como archivo (Foto) o como enlace (Foto_url)
            'Foto_url' => 'nullable|string|max:500',
            'Foto' => 'nullable|image|max:4096',
        ];
    }

    // Si llegó un archivo, lo guarda y reemplaza Foto_url por su URL completa
    private function conFoto(Request $request, array $datos): array
    {
        unset($datos['Foto']);

        if ($request->hasFile('Foto')) {
            $datos['Foto_url'] = asset('storage/'.$request->file('Foto')->store('mascotas', 'public'));
        }

        return $datos;
    }

    // Una fundación solo puede tocar sus propias mascotas
    private function soloMiaMascota(Request $request, Pet $pet): void
    {
        abort_if($pet->Id_fundacion !== $request->user()->foundation->id, 403, 'Esta mascota no es de tu fundación.');
    }
}
