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
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'dni' => 'required|string|unique:trabajadores,dni|max:20',
            'telefono' => 'nullable|string|max:20',
            'email' => 'required|email|unique:trabajadores,email|max:255',
            'rol' => 'nullable|string|max:50',
            'fechaAlta' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $trabajador = Trabajador::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Trabajador creado exitosamente',
            'data' => $trabajador
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
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
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
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
            'dni' => 'sometimes|required|string|max:20|unique:trabajadores,dni,' . $id,
            'telefono' => 'nullable|string|max:20',
            'email' => 'sometimes|required|email|max:255|unique:trabajadores,email,' . $id,
            'rol' => 'nullable|string|max:50',
            'fechaAlta' => 'sometimes|required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $trabajador->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Trabajador actualizado exitosamente',
            'data' => $trabajador
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
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
    }

    /**
     * Get statistics
     */
    public function stats()
    {
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
    }
}
