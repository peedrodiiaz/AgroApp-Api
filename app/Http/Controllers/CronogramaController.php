<?php

namespace App\Http\Controllers;

use App\Models\Cronograma;
use Illuminate\Http\Request;

class CronogramaController extends Controller
{
    public function index(Request $request)
    {
        $query = Cronograma::query();

        if ($request->has('trabajador_id')) {
            $query->where('trabajador_id', $request->trabajador_id);
        }

        if ($request->has('maquina_id')) {
            $query->where('maquina_id', $request->maquina_id);
        }

        if ($request->has('fecha_inicio')) {
            $query->whereDate('fechaInicio', '>=', $request->fecha_inicio);
        }

        if ($request->has('fecha_fin')) {
            $query->whereDate('fechaFin', '<=', $request->fecha_fin);
        }

        return response()->json(['success' => true, 'data' => $query->orderBy('fechaInicio', 'desc')->get()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'fechaInicio' => 'required|date',
            'fechaFin' => 'required|date|after_or_equal:fechaInicio',
            'horaInicio' => 'nullable|date_format:H:i',
            'horaFin' => 'nullable|date_format:H:i|after:horaInicio',
            'color' => 'nullable|string|max:20',
            'descripcion' => 'nullable|string',
            'estado' => 'nullable|in:pendiente,confirmada,en_uso,completada,cancelada',
            'trabajador_id' => 'required|exists:trabajadors,id',
            'maquina_id' => 'required|exists:maquinas,id'
        ]);

        $cronograma = Cronograma::create($request->all());
        return response()->json(['success' => true, 'data' => $cronograma], 201);
    }

    public function show($id)
    {
        $cronograma = Cronograma::find($id);
        if (!$cronograma) {
            return response()->json(['success' => false, 'message' => 'No encontrado'], 404);
        }
        return response()->json(['success' => true, 'data' => $cronograma]);
    }

    public function update(Request $request, $id)
    {
        $cronograma = Cronograma::find($id);
        if (!$cronograma) {
            return response()->json(['success' => false, 'message' => 'No encontrado'], 404);
        }

        $request->validate([
            'fechaInicio' => 'sometimes|date',
            'fechaFin' => 'sometimes|date|after_or_equal:fechaInicio',
            'color' => 'nullable|string|max:20',
            'descripcion' => 'nullable|string',
            'estado' => 'nullable|in:pendiente,confirmada,en_uso,completada,cancelada',
            'trabajador_id' => 'sometimes|exists:trabajadors,id',
            'maquina_id' => 'sometimes|exists:maquinas,id'
        ]);

        $cronograma->update($request->all());
        return response()->json(['success' => true, 'data' => $cronograma]);
    }

    public function destroy($id)
    {
        $cronograma = Cronograma::find($id);
        if (!$cronograma) {
            return response()->json(['success' => false, 'message' => 'No encontrado'], 404);
        }
        $cronograma->delete();
        return response()->json(['success' => true, 'message' => 'Eliminado']);
    }
}
