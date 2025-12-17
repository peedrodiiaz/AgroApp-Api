<?php

namespace App\Http\Controllers;

use App\Models\Maquina;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class MaquinaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Maquina::query();

        // Filtros opcionales
        if ($request->has('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('numSerie', 'like', "%{$search}%")
                    ->orWhere('modelo', 'like', "%{$search}%");
            });
        }

        // Incluir relaciones si se solicita
        if ($request->has('with')) {
            $with = explode(',', $request->with);
            $query->with($with);
        }

        $maquinas = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $maquinas
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'imagen' => 'nullable|string|max:255',
            'numSerie' => 'required|string|unique:maquinas,numSerie|max:100',
            'modelo' => 'required|string|max:255',
            'tipo' => 'required|string|max:50',
            'fechaCompra' => 'required|date',
            'estado' => 'nullable|in:activa,inactiva,mantenimiento',
            'ubicacion' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'potenciaCv' => 'nullable|integer',
            'tipoCombustible' => 'nullable|string|max:50',
            'capacidadRemolque' => 'nullable|integer',
            'tipoCultivo' => 'nullable|string|max:100',
            'anchoCorte' => 'nullable|string|max:50',
            'capacidadTolva' => 'nullable|integer',
            'tipoBala' => 'nullable|string|max:100',
            'capacidadEmpaque' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $maquina = Maquina::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Máquina creada exitosamente',
            'data' => $maquina
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $maquina = Maquina::with(['cronogramas', 'incidencias', 'asignaciones'])->find($id);

        if (!$maquina) {
            return response()->json([
                'success' => false,
                'message' => 'Máquina no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $maquina
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $maquina = Maquina::find($id);

        if (!$maquina) {
            return response()->json([
                'success' => false,
                'message' => 'Máquina no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255',
            'imagen' => 'nullable|string|max:255',
            'numSerie' => 'sometimes|required|string|max:100|unique:maquinas,numSerie,' . $id,
            'modelo' => 'sometimes|required|string|max:255',
            'tipo' => 'sometimes|required|string|max:50',
            'fechaCompra' => 'sometimes|required|date',
            'estado' => 'nullable|in:activa,inactiva,mantenimiento',
            'ubicacion' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'potenciaCv' => 'nullable|integer',
            'tipoCombustible' => 'nullable|string|max:50',
            'capacidadRemolque' => 'nullable|integer',
            'tipoCultivo' => 'nullable|string|max:100',
            'anchoCorte' => 'nullable|string|max:50',
            'capacidadTolva' => 'nullable|integer',
            'tipoBala' => 'nullable|string|max:100',
            'capacidadEmpaque' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $maquina->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Máquina actualizada exitosamente',
            'data' => $maquina
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $maquina = Maquina::find($id);

        if (!$maquina) {
            return response()->json([
                'success' => false,
                'message' => 'Máquina no encontrada'
            ], 404);
        }

        $maquina->delete();

        return response()->json([
            'success' => true,
            'message' => 'Máquina eliminada exitosamente'
        ]);
    }

    /**
     * Get statistics
     */
    public function stats()
    {
        $total = Maquina::count();
        $porTipo = Maquina::selectRaw('tipo, COUNT(*) as total')
            ->groupBy('tipo')
            ->get();
        $porEstado = Maquina::selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'por_tipo' => $porTipo,
                'por_estado' => $porEstado
            ]
        ]);
    }

    /**
     * Change machine status
     */
    public function cambiarEstado(Request $request, $id)
    {
        $maquina = Maquina::find($id);

        if (!$maquina) {
            return response()->json([
                'success' => false,
                'message' => 'Máquina no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'estado' => 'required|in:activa,inactiva,mantenimiento'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $maquina->estado = $request->estado;
        $maquina->save();

        return response()->json([
            'success' => true,
            'message' => 'Estado de la máquina actualizado exitosamente',
            'data' => $maquina
        ]);
    }
}
