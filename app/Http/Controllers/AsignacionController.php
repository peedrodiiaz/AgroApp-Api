<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use Illuminate\Http\Request;

class AsignacionController extends Controller
{
    public function index(Request $request)
    {
        $query = Asignacion::query();

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
                $q->whereNull('fechaFin')->orWhere('fechaFin', '>=', now());
            });
        }

        return response()->json(['success' => true, 'data' => $query->orderBy('fechaInicio', 'desc')->get()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'fechaInicio' => 'required|date',
            'fechaFin' => 'nullable|date|after_or_equal:fechaInicio',
            'descripcion' => 'nullable|string',
            'tipoAsignacion' => 'nullable|in:temporal,permanente',
            'trabajador_id' => 'required|exists:trabajadors,id',
            'maquina_id' => 'required|exists:maquinas,id'
        ]);

        $asignacion = Asignacion::create($request->all());
        return response()->json(['success' => true, 'data' => $asignacion], 201);
    }

    public function show($id)
    {
        $asignacion = Asignacion::find($id);
        if (!$asignacion) {
            return response()->json(['success' => false, 'message' => 'No encontrada'], 404);
        }
        return response()->json(['success' => true, 'data' => $asignacion]);
    }

    public function update(Request $request, $id)
    {
        $asignacion = Asignacion::find($id);
        if (!$asignacion) {
            return response()->json(['success' => false, 'message' => 'No encontrada'], 404);
        }

        $request->validate([
            'fechaInicio' => 'sometimes|date',
            'fechaFin' => 'nullable|date|after_or_equal:fechaInicio',
            'descripcion' => 'nullable|string',
            'tipoAsignacion' => 'nullable|in:temporal,permanente',
            'trabajador_id' => 'sometimes|exists:trabajadors,id',
            'maquina_id' => 'sometimes|exists:maquinas,id'
        ]);

        $asignacion->update($request->all());
        return response()->json(['success' => true, 'data' => $asignacion]);
    }

    public function destroy($id)
    {
        $asignacion = Asignacion::find($id);
        if (!$asignacion) {
            return response()->json(['success' => false, 'message' => 'No encontrada'], 404);
        }
        $asignacion->delete();
        return response()->json(['success' => true, 'message' => 'Eliminada']);
    }

    public function stats()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total' => Asignacion::count(),
                'activas' => Asignacion::where(function($q) {
                    $q->whereNull('fechaFin')->orWhere('fechaFin', '>=', now());
                })->count(),
                'por_tipo' => Asignacion::selectRaw('tipoAsignacion, COUNT(*) as total')->groupBy('tipoAsignacion')->get()
            ]
        ]);
    }
}
