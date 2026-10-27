<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdoptionApplicationController;
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
Route::get('settings/emergency-phone', [SettingsController::class, 'telefonoEmergencia']);

// Con sesión (token de Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('perfil/foto', [PerfilController::class, 'subirFoto']);
    Route::get('denunciations/mine', [DenunciationController::class, 'mias']);
    Route::patch('denunciations/{denuncia}/status', [DenunciationController::class, 'cambiarEstado']);

    // Usuario civil
    Route::put('perfil', [PerfilController::class, 'guardar']);
    Route::post('adoption-applications', [AdoptionApplicationController::class, 'store']);
    Route::get('adoption-applications/mine', [AdoptionApplicationController::class, 'mias']);

    // Fundación
    Route::patch('denunciations/{denuncia}/claim', [DenunciationController::class, 'reclamar']);

    Route::prefix('foundation')->group(function () {
        Route::get('pets', [FundacionPanelController::class, 'mascotas']);
        Route::post('pets', [FundacionPanelController::class, 'crearMascota']);
        Route::put('pets/{pet}', [FundacionPanelController::class, 'actualizarMascota']);

        Route::get('adoption-applications', [FundacionPanelController::class, 'solicitudes']);
        Route::put('adoption-applications/{solicitud}', [FundacionPanelController::class, 'resolverSolicitud']);

        Route::put('profile', [FundacionPanelController::class, 'actualizarPerfil']);
    });

    // Administrador
    Route::prefix('admin')->group(function () {
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
});
