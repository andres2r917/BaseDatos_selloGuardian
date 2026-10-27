<?php

namespace App\Http\Controllers;

use App\Models\Adoption_application;
use App\Models\Pet;
use Illuminate\Http\Request;

class FundacionPanelController extends Controller
{
    // ── Mascotas ──

    public function mascotas(Request $request)
    {
        return $request->user()->foundation->pets()->latest()->get();
    }

    public function crearMascota(Request $request)
    {
        $datos = $request->validate($this->reglasMascota('required'));

        // La relación pone Id_fundacion sola
        $mascota = $request->user()->foundation->pets()->create(
            $this->conFoto($request, $datos) + ['Estado_adopcion' => 'en_cuarentena']
        );

        return response()->json($mascota, 201);
    }

    // Sirve para editar datos y para mover la tarjeta en el Kanban (Estado_adopcion)
    public function actualizarMascota(Request $request, Pet $pet)
    {
        abort_if($pet->Id_fundacion !== $request->user()->foundation->id, 403, 'Esta mascota no es de tu fundación.');

        $datos = $request->validate($this->reglasMascota('sometimes|required'));
        $pet->update($this->conFoto($request, $datos));

        // Al cerrar la adopción, las solicitudes que seguían abiertas para esta mascota se rechazan
        if ($pet->Estado_adopcion === 'adoptado') {
            $pet->adoptionApplications()
                ->whereIn('Estado_solicitud', ['pendiente', 'en_evaluacion'])
                ->update([
                    'Estado_solicitud' => 'rechazada',
                    'Comentarios_fundacion' => 'La mascota ya fue adoptada por otra familia.',
                ]);
        }

        return $pet;
    }

    // ── Solicitudes de adopción ──

    public function solicitudes(Request $request)
    {
        return $request->user()->foundation
            ->adoptionApplications()
            ->with('userCivil.user', 'pet')
            ->latest('Fecha_envio')
            ->get();
    }

    // Al aprobar, la mascota queda "en_proceso"
    public function resolverSolicitud(Request $request, Adoption_application $solicitud)
    {
        abort_if($solicitud->Id_fundacion !== $request->user()->foundation->id, 403, 'Esta solicitud no es de tu fundación.');

        $datos = $request->validate([
            'Estado_solicitud' => 'required|in:pendiente,en_evaluacion,aprobada,rechazada',
            'Comentarios_fundacion' => 'nullable',
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

    // ── Ajustes de la fundación ──

    // Cada tarjeta de FundacionAjustes.jsx manda solo sus campos, por eso "sometimes"
    public function actualizarPerfil(Request $request)
    {
        $datos = $request->validate([
            'Nombre_organizacion' => 'sometimes|required',
            'Nombre_representante' => 'sometimes|required',
            'Cedula_representante' => 'sometimes|required',
            'Telefono' => 'sometimes|required',
            'Direccion' => 'sometimes|required',
            'Redes' => 'nullable',
            'Descripcion' => 'nullable',
            'Capacidad' => 'sometimes|required|integer|min:1',
        ]);

        $request->user()->foundation->update($datos);

        return $request->user()->load('role', 'userCivil', 'foundation');
    }

    // ── Auxiliares ──

    // $presencia es 'required' al crear y 'sometimes|required' al editar
    private function reglasMascota($presencia)
    {
        return [
            'Nombre' => $presencia,
            'Especie' => $presencia,
            'Raza' => $presencia,
            'Edad_estimada' => $presencia,
            'Tamano' => $presencia,
            'Estado_salud' => $presencia,
            'Descripcion_personalidad' => $presencia,
            'Sexo' => 'nullable',
            'Temperamento' => 'nullable',
            'Convivencia' => 'nullable',
            'Vacunado' => 'boolean',
            'Esterilizado' => 'boolean',
            'Desparasitado' => 'boolean',
            'Estado_adopcion' => 'in:en_cuarentena,disponible,en_proceso,adoptado',
            // La foto puede llegar como archivo (Foto) o como enlace (Foto_url)
            'Foto_url' => 'nullable',
            'Foto' => 'nullable|image|max:4096',
        ];
    }

    // Si llegó un archivo, se guarda y su URL reemplaza a Foto_url
    private function conFoto(Request $request, array $datos)
    {
        unset($datos['Foto']);

        if ($request->hasFile('Foto')) {
            $datos['Foto_url'] = asset('storage/'.$request->file('Foto')->store('mascotas', 'public'));
        }

        return $datos;
    }
}
