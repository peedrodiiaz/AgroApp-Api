<?php

namespace App\Http\Controllers;

use App\Models\Trabajador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrabajadorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $trabajadores = Trabajador::orderBy('created_at', 'desc')->get();
            
            return response()->json([
                'success' => true,
                'data' => $trabajadores
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener trabajadores',
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
            \Log::info('Datos recibidos para crear trabajador:', $request->all());

            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:255',
                'apellido' => 'required|string|max:255',
                'dni' => 'required|string|unique:trabajadors,dni',
                'telefono' => 'nullable|string|max:20',
                'email' => 'required|email|unique:trabajadors,email',
                'rol' => 'required|string',
                'fechaAlta' => 'required|date'
            ]);

            if ($validator->fails()) {
                \Log::error('Errores de validación:', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $trabajador = Trabajador::create($request->all());
            \Log::info('Trabajador creado exitosamente:', $trabajador->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Trabajador creado exitosamente',
                'data' => $trabajador
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Error al crear trabajador:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al crear trabajador',
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
            $trabajador = Trabajador::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $trabajador
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Trabajador no encontrado',
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
            $trabajador = Trabajador::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'nombre' => 'sometimes|string|max:255',
                'apellido' => 'sometimes|string|max:255',
                'dni' => 'sometimes|string|unique:trabajadors,dni,' . $id,
                'telefono' => 'nullable|string|max:20',
                'email' => 'sometimes|email|unique:trabajadors,email,' . $id,
                'rol' => 'sometimes|string',
                'fechaAlta' => 'sometimes|date'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $trabajador->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Trabajador actualizado exitosamente',
                'data' => $trabajador
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar trabajador',
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
            $trabajador = Trabajador::findOrFail($id);
            $trabajador->delete();

            return response()->json([
                'success' => true,
                'message' => 'Trabajador eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar trabajador',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics about workers.
     */
    public function stats()
    {
        try {
            $total = Trabajador::count();
            $porRol = Trabajador::select('rol')
                ->selectRaw('count(*) as total')
                ->groupBy('rol')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $total,
                    'por_rol' => $porRol
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
