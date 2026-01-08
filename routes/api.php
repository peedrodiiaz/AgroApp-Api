<?php

require __DIR__ . '/auth.php';

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\TrabajadorController;
use App\Http\Controllers\MaquinaController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\AsignacionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/register', [RegisteredUserController::class, 'store']);

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('trabajadores', TrabajadorController::class);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('maquinas', MaquinaController::class);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('incidencias', IncidenciaController::class);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/asignaciones/stats', [AsignacionController::class, 'stats']);
    Route::apiResource('asignaciones', AsignacionController::class);
});
