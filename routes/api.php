<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas de consulta con relaciones
Route::get('pets/consulta1', [App\Http\Controllers\PetController::class, 'consulta1'])->name('api.v1.pets.consulta1');
Route::get('users/consulta1', [App\Http\Controllers\UserController::class, 'consulta1'])->name('api.v1.users.consulta1');
Route::get('foundations/consulta1', [App\Http\Controllers\FoundationController::class, 'consulta1'])->name('api.v1.foundations.consulta1');
Route::get('denunciations/consulta1', [App\Http\Controllers\DenunciationController::class, 'consulta1'])->name('api.v1.denunciations.consulta1');
Route::get('adoption-applications/consulta1', [App\Http\Controllers\AdoptionApplicationController::class, 'consulta1'])->name('api.v1.adoption-applications.consulta1');
Route::get('usercivils/consulta1', [App\Http\Controllers\UsercivilController::class, 'consulta1'])->name('api.v1.usercivils.consulta1');

// Pet
Route::get('pets', [App\Http\Controllers\PetController::class, 'index'])->name('api.v1.pets.index');
Route::post('pets', [App\Http\Controllers\PetController::class, 'store'])->name('api.v1.pets.store');
Route::get('pets/{pet}', [App\Http\Controllers\PetController::class, 'show'])->name('api.v1.pets.show');
Route::put('pets/{pet}', [App\Http\Controllers\PetController::class, 'update'])->name('api.v1.pets.update');
Route::delete('pets/{pet}', [App\Http\Controllers\PetController::class, 'destroy'])->name('api.v1.pets.destroy');

// User
Route::get('users', [App\Http\Controllers\UserController::class, 'index'])->name('api.v1.users.index');
Route::post('users', [App\Http\Controllers\UserController::class, 'store'])->name('api.v1.users.store');
Route::get('users/{user}', [App\Http\Controllers\UserController::class, 'show'])->name('api.v1.users.show');
Route::put('users/{user}', [App\Http\Controllers\UserController::class, 'update'])->name('api.v1.users.update');
Route::delete('users/{user}', [App\Http\Controllers\UserController::class, 'destroy'])->name('api.v1.users.destroy');

// Foundation
Route::get('foundations', [App\Http\Controllers\FoundationController::class, 'index'])->name('api.v1.foundations.index');
Route::post('foundations', [App\Http\Controllers\FoundationController::class, 'store'])->name('api.v1.foundations.store');
Route::get('foundations/{foundation}', [App\Http\Controllers\FoundationController::class, 'show'])->name('api.v1.foundations.show');
Route::put('foundations/{foundation}', [App\Http\Controllers\FoundationController::class, 'update'])->name('api.v1.foundations.update');
Route::delete('foundations/{foundation}', [App\Http\Controllers\FoundationController::class, 'destroy'])->name('api.v1.foundations.destroy');

// Denunciation
Route::get('denunciations', [App\Http\Controllers\DenunciationController::class, 'index'])->name('api.v1.denunciations.index');
Route::post('denunciations', [App\Http\Controllers\DenunciationController::class, 'store'])->name('api.v1.denunciations.store');
Route::get('denunciations/{denunciation}', [App\Http\Controllers\DenunciationController::class, 'show'])->name('api.v1.denunciations.show');
Route::put('denunciations/{denunciation}', [App\Http\Controllers\DenunciationController::class, 'update'])->name('api.v1.denunciations.update');
Route::delete('denunciations/{denunciation}', [App\Http\Controllers\DenunciationController::class, 'destroy'])->name('api.v1.denunciations.destroy');

// Adoption Application
Route::get('adoption-applications', [App\Http\Controllers\AdoptionApplicationController::class, 'index'])->name('api.v1.adoption-applications.index');
Route::post('adoption-applications', [App\Http\Controllers\AdoptionApplicationController::class, 'store'])->name('api.v1.adoption-applications.store');
Route::get('adoption-applications/{adoption_application}', [App\Http\Controllers\AdoptionApplicationController::class, 'show'])->name('api.v1.adoption-applications.show');
Route::put('adoption-applications/{adoption_application}', [App\Http\Controllers\AdoptionApplicationController::class, 'update'])->name('api.v1.adoption-applications.update');
Route::delete('adoption-applications/{adoption_application}', [App\Http\Controllers\AdoptionApplicationController::class, 'destroy'])->name('api.v1.adoption-applications.destroy');

// Usercivil
Route::get('usercivils', [App\Http\Controllers\UsercivilController::class, 'index'])->name('api.v1.usercivils.index');
Route::post('usercivils', [App\Http\Controllers\UsercivilController::class, 'store'])->name('api.v1.usercivils.store');
Route::get('usercivils/{usercivil}', [App\Http\Controllers\UsercivilController::class, 'show'])->name('api.v1.usercivils.show');
Route::put('usercivils/{usercivil}', [App\Http\Controllers\UsercivilController::class, 'update'])->name('api.v1.usercivils.update');
Route::delete('usercivils/{usercivil}', [App\Http\Controllers\UsercivilController::class, 'destroy'])->name('api.v1.usercivils.destroy');

