<?php

namespace App\Http\Controllers;

use App\Models\Incidencia;
use Illuminate\Http\Request;

class IncidenciaController extends Controller
{
    public function index(Request $request)
    {
        $query = Incidencia::query();

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
            $query->where('titulo', 'like', "%{$search}%")
                ->orWhere('descripcion', 'like', "%{$search}%");
        }

        return response()->json(['success' => true, 'data' => $query->orderBy('fechaApertura', 'desc')->get()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'estado' => 'nullable|in:abierta,en_progreso,resuelta',
            'prioridad' => 'nullable|in:baja,media,alta',
            'fechaApertura' => 'required|date',
            'fechaCierre' => 'nullable|date|after_or_equal:fechaApertura',
            'maquina_id' => 'required|exists:maquinas,id',
            'trabajador_id' => 'required|exists:trabajadors,id'
        ]);

        $incidencia = Incidencia::create($request->all());
        return response()->json(['success' => true, 'data' => $incidencia], 201);
    }

    public function show($id)
    {
        $incidencia = Incidencia::find($id);
        if (!$incidencia) {
            return response()->json(['success' => false, 'message' => 'No encontrada'], 404);
        }
        return response()->json(['success' => true, 'data' => $incidencia]);
    }

    public function update(Request $request, $id)
    {
        $incidencia = Incidencia::find($id);
        if (!$incidencia) {
            return response()->json(['success' => false, 'message' => 'No encontrada'], 404);
        }

        $request->validate([
            'titulo' => 'sometimes|string|max:255',
            'descripcion' => 'sometimes|string',
            'estado' => 'nullable|in:abierta,en_progreso,resuelta',
            'prioridad' => 'nullable|in:baja,media,alta',
            'fechaApertura' => 'sometimes|date',
            'fechaCierre' => 'nullable|date|after_or_equal:fechaApertura',
            'maquina_id' => 'sometimes|exists:maquinas,id',
            'trabajador_id' => 'sometimes|exists:trabajadors,id'
        ]);

        $incidencia->update($request->all());
        return response()->json(['success' => true, 'data' => $incidencia]);
    }

    public function destroy($id)
    {
        $incidencia = Incidencia::find($id);
        if (!$incidencia) {
            return response()->json(['success' => false, 'message' => 'No encontrada'], 404);
        }
        $incidencia->delete();
        return response()->json(['success' => true, 'message' => 'Eliminada']);
    }

    public function stats()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total' => Incidencia::count(),
                'por_estado' => Incidencia::selectRaw('estado, COUNT(*) as total')->groupBy('estado')->get(),
                'por_prioridad' => Incidencia::selectRaw('prioridad, COUNT(*) as total')->groupBy('prioridad')->get()
            ]
        ]);
    }
}
