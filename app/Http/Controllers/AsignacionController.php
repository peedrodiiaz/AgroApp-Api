<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AsignacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Asignacion::with(['trabajador', 'maquina']);

        // Filtros opcionales
        if ($request->has('trabajador_id')) {
            $query->where('trabajador_id', $request->trabajador_id);
        }

        if ($request->has('maquina_id')) {
            $query->where('maquina_id', $request->maquina_id);
        }

        if ($request->has('tipoAsignacion')) {
            $query->where('tipoAsignacion', $request->tipoAsignacion);
        }

        if ($request->has('activas')) {
            $query->where(function($q) {
                $q->whereNull('fechaFin')
                  ->orWhere('fechaFin', '>=', now());
            });
        }

        $asignaciones = $query->orderBy('fechaInicio', 'desc')->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $asignaciones
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fechaInicio' => 'required|date',
            'fechaFin' => 'nullable|date|after_or_equal:fechaInicio',
            'descripcion' => 'nullable|string',
            'tipoAsignacion' => 'nullable|in:temporal,permanente',
            'trabajador_id' => 'required|exists:trabajadores,id',
            'maquina_id' => 'required|exists:maquinas,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $asignacion = Asignacion::create($request->all());
        $asignacion->load(['trabajador', 'maquina']);

        return response()->json([
            'success' => true,
            'message' => 'Asignación creada exitosamente',
            'data' => $asignacion
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $asignacion = Asignacion::with(['trabajador', 'maquina'])->find($id);

        if (!$asignacion) {
            return response()->json([
                'success' => false,
                'message' => 'Asignación no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $asignacion
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $asignacion = Asignacion::find($id);

        if (!$asignacion) {
            return response()->json([
                'success' => false,
                'message' => 'Asignación no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'fechaInicio' => 'sometimes|required|date',
            'fechaFin' => 'nullable|date|after_or_equal:fechaInicio',
            'descripcion' => 'nullable|string',
            'tipoAsignacion' => 'nullable|in:temporal,permanente',
            'trabajador_id' => 'sometimes|required|exists:trabajadores,id',
            'maquina_id' => 'sometimes|required|exists:maquinas,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $asignacion->update($request->all());
        $asignacion->load(['trabajador', 'maquina']);

        return response()->json([
            'success' => true,
            'message' => 'Asignación actualizada exitosamente',
            'data' => $asignacion
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $asignacion = Asignacion::find($id);

        if (!$asignacion) {
            return response()->json([
                'success' => false,
                'message' => 'Asignación no encontrada'
            ], 404);
        }

        $asignacion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Asignación eliminada exitosamente'
        ]);
    }

    /**
     * Get statistics
     */
    public function stats()
    {
        $total = Asignacion::count();
        $activas = Asignacion::where(function($q) {
            $q->whereNull('fechaFin')
              ->orWhere('fechaFin', '>=', now());
        })->count();
        $porTipo = Asignacion::selectRaw('tipoAsignacion, COUNT(*) as total')
            ->groupBy('tipoAsignacion')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'activas' => $activas,
                'por_tipo' => $porTipo
            ]
        ]);
    }
}
