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
    public function index(Request $request)
    {
        try {
            $query = Incidencia::with(['maquina', 'trabajador']);

            // Filtros opcionales
            if ($request->has('estado')) {
                $query->where('estado', $request->estado);
            }

            if ($request->has('prioridad')) {
                $query->where('prioridad', $request->prioridad);
            }

            if ($request->has('maquina_id')) {
                $query->where('maquina_id', $request->maquina_id);
            }

            if ($request->has('trabajador_id')) {
                $query->where('trabajador_id', $request->trabajador_id);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('titulo', 'like', "%{$search}%")
                        ->orWhere('descripcion', 'like', "%{$search}%");
                });
            }

            $incidencias = $query->orderBy('fechaApertura', 'desc')->paginate($request->per_page ?? 15);

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
                'estado' => 'nullable|in:abierta,en_progreso,resuelta',
                'prioridad' => 'nullable|in:baja,media,alta',
                'fechaApertura' => 'required|date',
                'fechaCierre' => 'nullable|date|after_or_equal:fechaApertura',
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
            $incidencia = Incidencia::with(['maquina', 'trabajador'])->find($id);

            if (!$incidencia) {
                return response()->json([
                    'success' => false,
                    'message' => 'Incidencia no encontrada'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $incidencia
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener incidencia',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $incidencia = Incidencia::find($id);

            if (!$incidencia) {
                return response()->json([
                    'success' => false,
                    'message' => 'Incidencia no encontrada'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'titulo' => 'sometimes|required|string|max:255',
                'descripcion' => 'sometimes|required|string',
                'estado' => 'nullable|in:abierta,en_progreso,resuelta',
                'prioridad' => 'nullable|in:baja,media,alta',
                'fechaApertura' => 'sometimes|required|date',
                'fechaCierre' => 'nullable|date|after_or_equal:fechaApertura',
                'maquina_id' => 'sometimes|required|exists:maquinas,id',
                'trabajador_id' => 'sometimes|required|exists:trabajadors,id'
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
            $incidencia = Incidencia::find($id);

            if (!$incidencia) {
                return response()->json([
                    'success' => false,
                    'message' => 'Incidencia no encontrada'
                ], 404);
            }

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

    /**
     * Get statistics
     */
    public function stats()
    {
        try {
            $total = Incidencia::count();
            $porEstado = Incidencia::selectRaw('estado, COUNT(*) as total')
                ->groupBy('estado')
                ->get();
            $porPrioridad = Incidencia::selectRaw('prioridad, COUNT(*) as total')
                ->groupBy('prioridad')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $total,
                    'por_estado' => $porEstado,
                    'por_prioridad' => $porPrioridad
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
