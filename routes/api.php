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
use App\Http\Controllers\TestController;

// Rutas públicas de autenticación
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', [AuthenticatedSessionController::class, 'store']);

// Rutas de prueba para verificar usuarios y login
Route::get('/test-users', [TestController::class, 'testUsers']);
Route::post('/test-login', [TestController::class, 'testLogin']);

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

    Route::get('/trabajadores/stats', [TrabajadorController::class, 'stats']);
    Route::get('trabajadores', [TrabajadorController::class, 'index']);
    Route::get('trabajadores/{id}', [TrabajadorController::class, 'show']);
    
    Route::middleware('role:admin')->group(function () {
        Route::post('trabajadores', [TrabajadorController::class, 'store']);
        Route::put('trabajadores/{id}', [TrabajadorController::class, 'update']);
        Route::patch('trabajadores/{id}', [TrabajadorController::class, 'update']);
        Route::delete('trabajadores/{id}', [TrabajadorController::class, 'destroy']);
    });
    
    Route::get('/maquinas/stats', [MaquinaController::class, 'stats']);
    Route::get('maquinas', [MaquinaController::class, 'index']);
    Route::get('maquinas/{id}', [MaquinaController::class, 'show']);
    
    Route::middleware('role:admin')->group(function () {
        Route::post('maquinas', [MaquinaController::class, 'store']);
        Route::put('maquinas/{id}', [MaquinaController::class, 'update']);
        Route::patch('maquinas/{id}', [MaquinaController::class, 'update']);
        Route::patch('/maquinas/{id}/estado', [MaquinaController::class, 'cambiarEstado']);
        Route::delete('maquinas/{id}', [MaquinaController::class, 'destroy']);
    });
    
    Route::get('cronogramas', [CronogramaController::class, 'index']);
    Route::get('cronogramas/{id}', [CronogramaController::class, 'show']);
    
    Route::middleware('role:admin')->group(function () {
        Route::post('cronogramas', [CronogramaController::class, 'store']);
        Route::put('cronogramas/{id}', [CronogramaController::class, 'update']);
        Route::patch('cronogramas/{id}', [CronogramaController::class, 'update']);
        Route::delete('cronogramas/{id}', [CronogramaController::class, 'destroy']);
    });
    
    Route::get('/incidencias/stats', [IncidenciaController::class, 'stats']);
    Route::get('incidencias', [IncidenciaController::class, 'index']);
    Route::get('incidencias/{id}', [IncidenciaController::class, 'show']);
    
    Route::middleware('role:admin')->group(function () {
        Route::post('incidencias', [IncidenciaController::class, 'store']);
        Route::put('incidencias/{id}', [IncidenciaController::class, 'update']);
        Route::patch('incidencias/{id}', [IncidenciaController::class, 'update']);
        Route::delete('incidencias/{id}', [IncidenciaController::class, 'destroy']);
    });
    
    Route::get('/asignaciones/stats', [AsignacionController::class, 'stats']);
    Route::get('asignaciones', [AsignacionController::class, 'index']);
    Route::get('asignaciones/{id}', [AsignacionController::class, 'show']);
    
    Route::middleware('role:admin')->group(function () {
        Route::post('asignaciones', [AsignacionController::class, 'store']);
        Route::put('asignaciones/{id}', [AsignacionController::class, 'update']);
        Route::patch('asignaciones/{id}', [AsignacionController::class, 'update']);
        Route::delete('asignaciones/{id}', [AsignacionController::class, 'destroy']);
    });
});
