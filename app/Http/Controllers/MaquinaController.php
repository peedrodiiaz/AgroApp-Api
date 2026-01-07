<?php

namespace App\Http\Controllers;

use App\Models\Maquina;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaquinaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $maquinas = Maquina::orderBy('created_at', 'desc')->get();
            
            return response()->json([
                'success' => true,
                'data' => $maquinas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener máquinas',
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
                'nombre' => 'required|string|max:255',
                'numSerie' => 'required|string|unique:maquinas,numSerie',
                'modelo' => 'required|string|max:255',
                'tipo' => 'required|string',
                'fechaCompra' => 'required|date',
                'estado' => 'sometimes|in:activa,inactiva,mantenimiento',
                'ubicacion' => 'nullable|string|max:255',
                'descripcion' => 'nullable|string',
                'imagen' => 'nullable|string',
                'potenciaCv' => 'nullable|integer',
                'tipoCombustible' => 'nullable|string',
                'capacidadRemolque' => 'nullable|integer',
                'tipoCultivo' => 'nullable|string',
                'anchoCorte' => 'nullable|string',
                'capacidadTolva' => 'nullable|integer',
                'tipoBala' => 'nullable|string',
                'capacidadEmpaque' => 'nullable|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $maquina = Maquina::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Máquina creada exitosamente',
                'data' => $maquina
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear máquina',
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
            $maquina = Maquina::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $maquina
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Máquina no encontrada',
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
            $maquina = Maquina::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'nombre' => 'sometimes|string|max:255',
                'numSerie' => 'sometimes|string|unique:maquinas,numSerie,' . $id,
                'modelo' => 'sometimes|string|max:255',
                'tipo' => 'sometimes|string',
                'fechaCompra' => 'sometimes|date',
                'estado' => 'sometimes|in:activa,inactiva,mantenimiento',
                'ubicacion' => 'nullable|string|max:255',
                'descripcion' => 'nullable|string',
                'imagen' => 'nullable|string',
                'potenciaCv' => 'nullable|integer',
                'tipoCombustible' => 'nullable|string',
                'capacidadRemolque' => 'nullable|integer',
                'tipoCultivo' => 'nullable|string',
                'anchoCorte' => 'nullable|string',
                'capacidadTolva' => 'nullable|integer',
                'tipoBala' => 'nullable|string',
                'capacidadEmpaque' => 'nullable|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $maquina->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Máquina actualizada exitosamente',
                'data' => $maquina
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar máquina',
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
            $maquina = Maquina::findOrFail($id);
            $maquina->delete();

            return response()->json([
                'success' => true,
                'message' => 'Máquina eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar máquina',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics about machines.
     */
    public function stats()
    {
        try {
            $total = Maquina::count();
            $porEstado = Maquina::select('estado')
                ->selectRaw('count(*) as total')
                ->groupBy('estado')
                ->get();
            $porTipo = Maquina::select('tipo')
                ->selectRaw('count(*) as total')
                ->groupBy('tipo')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $total,
                    'por_estado' => $porEstado,
                    'por_tipo' => $porTipo
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

    /**
     * Change machine status.
     */
    public function cambiarEstado(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'estado' => 'required|in:activa,inactiva,mantenimiento'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $maquina = Maquina::findOrFail($id);
            $maquina->estado = $request->estado;
            $maquina->save();

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado exitosamente',
                'data' => $maquina
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
