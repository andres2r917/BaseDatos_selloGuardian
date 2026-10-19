<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Adoption_application;
class AdoptionApplicationController extends Controller
{
  public function consulta1()
  {
      return response()->json(Adoption_application::with(['userCivil', 'pet', 'foundation'])->get());
  }

  public function index()
  {
      return response()->json(Adoption_application::all());
  }

  public function show(Adoption_application $adoption_application)
  {
      return response()->json($adoption_application);
  }

  public function store(Request $request)
  {
      $request->validate([
          'Id_usuarioCivil' => 'required',
          'Id_mascota'      => 'required',
      ]);

      return response()->json(Adoption_application::create($request->all()), 201);
  }

  public function update(Request $request, Adoption_application $adoption_application)
  {
      $adoption_application->update($request->all());

      return response()->json($adoption_application);
  }

  public function destroy(Adoption_application $adoption_application)
  {
      $adoption_application->delete();

      return response()->json(['message' => 'Solicitud de adopción eliminada correctamente']);
  }

  public function consulta10(){
    $adoptionApplications = Adoption_application::where('Estado_solicitud', 'pendiente')->get();
    return $adoptionApplications;
  }

  public function consulta11(){
    $adoptionApplications = Adoption_application::whereDate('Fecha_envio', '2026-05-20')->get();
    return $adoptionApplications;
  }
  public function prueba_application(){
    $application = Adoption_application::find(1);
    return [
        'application' => $application,
        'userCivil' => $application->userCivil,
        'pet' => $application->pet,
        'foundation' => $application->foundation,
    ];
}
}