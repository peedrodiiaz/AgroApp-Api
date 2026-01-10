<?php

namespace App\Http\Controllers;

use App\Models\Cronograma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CronogramaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Cronograma::with(['trabajador', 'maquina']);

        // Filtros opcionales
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

        $cronogramas = $query->orderBy('fechaInicio', 'desc')->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $cronogramas
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar disponibilidad de la máquina
        $conflicto = $this->verificarDisponibilidad(
            $request->maquina_id,
            $request->fechaInicio,
            $request->fechaFin,
            $request->horaInicio,
            $request->horaFin
        );

        if ($conflicto) {
            return response()->json([
                'success' => false,
                'message' => 'La máquina ya está reservada en ese periodo de tiempo'
            ], 409);
        }

        $cronograma = Cronograma::create($request->all());
        $cronograma->load(['trabajador', 'maquina']);

        return response()->json([
            'success' => true,
            'message' => 'Reserva creada exitosamente',
            'data' => $cronograma
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $cronograma = Cronograma::with(['trabajador', 'maquina'])->find($id);

        if (!$cronograma) {
            return response()->json([
                'success' => false,
                'message' => 'Cronograma no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $cronograma
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $cronograma = Cronograma::find($id);

        if (!$cronograma) {
            return response()->json([
                'success' => false,
                'message' => 'Cronograma no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'fechaInicio' => 'sometimes|required|date',
            'fechaFin' => 'sometimes|required|date|after_or_equal:fechaInicio',
            'color' => 'nullable|string|max:20',
            'descripcion' => 'nullable|string',
            'trabajador_id' => 'sometimes|required|exists:trabajadors,id',
            'maquina_id' => 'sometimes|required|exists:maquinas,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $cronograma->update($request->all());
        $cronograma->load(['trabajador', 'maquina']);

        return response()->json([
            'success' => true,
            'message' => 'Cronograma actualizado exitosamente',
            'data' => $cronograma
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $cronograma = Cronograma::find($id);

        if (!$cronograma) {
            return response()->json([
                'success' => false,
                'message' => 'Cronograma no encontrado'
            ], 404);
        }

        $cronograma->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cronograma eliminado exitosamente'
        ]);
    }

    /**
     * Verificar disponibilidad de una máquina en un periodo
     */
    private function verificarDisponibilidad($maquina_id, $fechaInicio, $fechaFin, $horaInicio = null, $horaFin = null, $excluirId = null)
    {
        $query = Cronograma::where('maquina_id', $maquina_id)
            ->whereIn('estado', ['pendiente', 'confirmada', 'en_uso'])
            ->where(function($q) use ($fechaInicio, $fechaFin) {
                $q->whereBetween('fechaInicio', [$fechaInicio, $fechaFin])
                  ->orWhereBetween('fechaFin', [$fechaInicio, $fechaFin])
                  ->orWhere(function($q2) use ($fechaInicio, $fechaFin) {
                      $q2->where('fechaInicio', '<=', $fechaInicio)
                         ->where('fechaFin', '>=', $fechaFin);
                  });
            });

        if ($excluirId) {
            $query->where('id', '!=', $excluirId);
        }

        return $query->exists();
    }

    /**
     * Obtener disponibilidad de máquinas en un rango de fechas
     */
    public function disponibilidad(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fechaInicio' => 'required|date',
            'fechaFin' => 'required|date|after_or_equal:fechaInicio',
            'maquina_id' => 'nullable|exists:maquinas,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $query = Cronograma::with(['maquina', 'trabajador'])
            ->whereIn('estado', ['pendiente', 'confirmada', 'en_uso'])
            ->where(function($q) use ($request) {
                $q->whereBetween('fechaInicio', [$request->fechaInicio, $request->fechaFin])
                  ->orWhereBetween('fechaFin', [$request->fechaInicio, $request->fechaFin])
                  ->orWhere(function($q2) use ($request) {
                      $q2->where('fechaInicio', '<=', $request->fechaInicio)
                         ->where('fechaFin', '>=', $request->fechaFin);
                  });
            });

        if ($request->has('maquina_id')) {
            $query->where('maquina_id', $request->maquina_id);
        }

        $reservas = $query->get();

        return response()->json([
            'success' => true,
            'data' => $reservas
        ]);
    }
}
