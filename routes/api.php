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


Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', [AuthenticatedSessionController::class, 'store']);


Route::middleware('auth:sanctum')->group(function () {
    
    // Logout
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
    
    // Usuario autenticado
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    });

    Route::apiResource('trabajadores', TrabajadorController::class);
    Route::get('trabajadores-stats', [TrabajadorController::class, 'stats']);
    
    Route::apiResource('maquinas', MaquinaController::class);
    Route::get('maquinas-stats', [MaquinaController::class, 'stats']);
    Route::patch('maquinas/{id}/estado', [MaquinaController::class, 'cambiarEstado']);
    
    Route::apiResource('cronogramas', CronogramaController::class);
    Route::apiResource('incidencias', IncidenciaController::class);
    Route::apiResource('asignaciones', AsignacionController::class);
});
