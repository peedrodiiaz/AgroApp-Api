<?php

namespace App\Http\Controllers;

use App\Models\Incidencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IncidenciaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $incidencias = Incidencia::with(['maquina', 'trabajador'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $incidencias
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener incidencias',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'titulo' => 'required|string|max:255',
                'descripcion' => 'required|string',
                'estado' => 'sometimes|in:abierta,en_progreso,resuelta',
                'prioridad' => 'sometimes|in:baja,media,alta',
                'fechaApertura' => 'required|date',
                'fechaCierre' => 'nullable|date',
                'maquina_id' => 'required|exists:maquinas,id',
                'trabajador_id' => 'required|exists:trabajadors,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $incidencia = Incidencia::create($request->all());
            $incidencia->load(['maquina', 'trabajador']);

            return response()->json([
                'success' => true,
                'message' => 'Incidencia creada exitosamente',
                'data' => $incidencia
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear incidencia',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $incidencia = Incidencia::with(['maquina', 'trabajador'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $incidencia
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Incidencia no encontrada',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $incidencia = Incidencia::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'titulo' => 'sometimes|string|max:255',
                'descripcion' => 'sometimes|string',
                'estado' => 'sometimes|in:abierta,en_progreso,resuelta',
                'prioridad' => 'sometimes|in:baja,media,alta',
                'fechaApertura' => 'sometimes|date',
                'fechaCierre' => 'nullable|date',
                'maquina_id' => 'sometimes|exists:maquinas,id',
                'trabajador_id' => 'sometimes|exists:trabajadors,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $incidencia->update($request->all());
            $incidencia->load(['maquina', 'trabajador']);

            return response()->json([
                'success' => true,
                'message' => 'Incidencia actualizada exitosamente',
                'data' => $incidencia
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar incidencia',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $incidencia = Incidencia::findOrFail($id);
            $incidencia->delete();

            return response()->json([
                'success' => true,
                'message' => 'Incidencia eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar incidencia',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
