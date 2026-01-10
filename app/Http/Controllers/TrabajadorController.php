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
    public function index(Request $request)
    {
        try {
            $query = Trabajador::query();

            // Filtros opcionales
            if ($request->has('rol')) {
                $query->where('rol', $request->rol);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                        ->orWhere('apellido', 'like', "%{$search}%")
                        ->orWhere('dni', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Incluir relaciones si se solicita
            if ($request->has('with')) {
                $with = explode(',', $request->with);
                $query->with($with);
            }

            $trabajadores = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 15);

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
                'dni' => 'required|string|unique:trabajadors,dni|max:20',
                'telefono' => 'nullable|string|max:20',
                'email' => 'required|email|unique:trabajadors,email|max:255',
                'rol' => 'nullable|string|max:50',
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
            $trabajador = Trabajador::with(['cronogramas', 'incidencias', 'asignaciones'])->find($id);

            if (!$trabajador) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trabajador no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $trabajador
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener trabajador',
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
            $trabajador = Trabajador::find($id);

            if (!$trabajador) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trabajador no encontrado'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'nombre' => 'sometimes|required|string|max:255',
                'apellido' => 'sometimes|required|string|max:255',
                'dni' => 'sometimes|required|string|max:20|unique:trabajadors,dni,' . $id,
                'telefono' => 'nullable|string|max:20',
                'email' => 'sometimes|required|email|max:255|unique:trabajadors,email,' . $id,
                'rol' => 'nullable|string|max:50',
                'fechaAlta' => 'sometimes|required|date'
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
            $trabajador = Trabajador::find($id);

            if (!$trabajador) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trabajador no encontrado'
                ], 404);
            }

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
            $porRol = Trabajador::selectRaw('rol, COUNT(*) as total')
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
