<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrabajadorController;
use App\Http\Controllers\MaquinaController;
use App\Http\Controllers\CronogramaController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\AsignacionController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('trabajadores', TrabajadorController::class);
Route::apiResource('maquinas', MaquinaController::class);
Route::apiResource('cronogramas', CronogramaController::class);
Route::apiResource('incidencias', IncidenciaController::class);
Route::apiResource('asignaciones', AsignacionController::class);

require __DIR__ . '/auth.php';
