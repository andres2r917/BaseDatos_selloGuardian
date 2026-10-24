<?php

use App\Http\Controllers\AdoptionApplicationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DenunciationController;
use App\Http\Controllers\FundacionPanelController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

// Públicas
Route::post('register', [AuthController::class, 'registrarCivil']);
Route::post('register/foundation', [AuthController::class, 'registrarFundacion']);
Route::post('login', [AuthController::class, 'login']);

Route::get('pets', [PetController::class, 'index']);
Route::get('denunciations', [DenunciationController::class, 'index']);
Route::post('denunciations', [DenunciationController::class, 'store']);

// Público: teléfono de emergencia para la pantalla de denuncias
Route::get('settings/emergency-phone', [SettingsController::class, 'telefonoEmergencia']);

// Requieren token (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::put('perfil', [PerfilController::class, 'guardar']);
    Route::get('denunciations/mine', [DenunciationController::class, 'mias']);
    Route::post('adoption-applications', [AdoptionApplicationController::class, 'store']);
});

// Cualquier usuario con sesión
Route::middleware('auth:sanctum')->post('perfil/foto', [PerfilController::class, 'subirFoto']);

// Denuncias: fundación se hace cargo; admin o fundación cambian el estado
Route::middleware(['auth:sanctum', 'rol:fundacion'])
    ->patch('denunciations/{denuncia}/claim', [DenunciationController::class, 'reclamar']);
Route::middleware(['auth:sanctum', 'rol:admin,fundacion'])
    ->patch('denunciations/{denuncia}/status', [DenunciationController::class, 'cambiarEstado']);

// Panel de fundación
Route::middleware(['auth:sanctum', 'rol:fundacion'])->prefix('foundation')->group(function () {
    Route::get('pets', [FundacionPanelController::class, 'mascotas']);
    Route::post('pets', [FundacionPanelController::class, 'crearMascota']);
    Route::put('pets/{pet}', [FundacionPanelController::class, 'actualizarMascota']);

    Route::get('adoption-applications', [FundacionPanelController::class, 'solicitudes']);
    Route::put('adoption-applications/{solicitud}', [FundacionPanelController::class, 'resolverSolicitud']);

    Route::put('profile', [FundacionPanelController::class, 'actualizarPerfil']);
});

// Panel de administrador
Route::middleware(['auth:sanctum', 'rol:admin'])->prefix('admin')->group(function () {
    Route::get('stats', [AdminController::class, 'estadisticas']);

    Route::get('users/civiles', [AdminController::class, 'civiles']);
    Route::get('users/administradores', [AdminController::class, 'administradores']);

    Route::get('foundations', [AdminController::class, 'fundaciones']);
    Route::put('foundations/{foundation}/verification', [AdminController::class, 'verificarFundacion']);

    Route::get('denunciations', [AdminController::class, 'denuncias']);
    Route::put('denunciations/{denuncia}/assign', [AdminController::class, 'asignarDenuncia']);

    Route::get('pets', [AdminController::class, 'mascotas']);

    Route::get('settings', [SettingsController::class, 'mostrar']);
    Route::put('settings', [SettingsController::class, 'actualizar']);
});
