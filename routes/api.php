<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\TrabajadorController;
use App\Http\Controllers\MaquinaController;
use App\Http\Controllers\CronogramaController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\AsignacionController;

// Rutas públicas de autenticación
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', [AuthenticatedSessionController::class, 'store']);

// Rutas protegidas con autenticación
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    });

    // Trabajadores
    Route::get('/trabajadores/stats', [TrabajadorController::class, 'stats']);
    Route::apiResource('trabajadores', TrabajadorController::class);
    
    // Máquinas
    Route::get('/maquinas/stats', [MaquinaController::class, 'stats']);
    Route::patch('/maquinas/{id}/estado', [MaquinaController::class, 'cambiarEstado']);
    Route::apiResource('maquinas', MaquinaController::class);
    
    // Cronogramas
    Route::apiResource('cronogramas', CronogramaController::class);
    
    // Incidencias
    Route::get('/incidencias/stats', [IncidenciaController::class, 'stats']);
    Route::apiResource('incidencias', IncidenciaController::class);
    
    // Asignaciones
    Route::get('/asignaciones/stats', [AsignacionController::class, 'stats']);
    Route::apiResource('asignaciones', AsignacionController::class);
});
