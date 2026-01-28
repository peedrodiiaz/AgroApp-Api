<?php

namespace App\Http\Controllers;

use App\Models\Trabajador;
use Illuminate\Http\Request;

class TrabajadorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Trabajador::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nombre', 'like', "%{$search}%")
                ->orWhere('apellido', 'like', "%{$search}%")
                ->orWhere('dni', 'like', "%{$search}%");
        }

        return response()->json([
            'success' => true,
            'data' => $query->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'dni' => 'required|string|unique:trabajadors,dni|max:20',
            'email' => 'required|email|unique:trabajadors,email|max:255',
            'telefono' => 'nullable|string|max:20',
            'rol' => 'nullable|string|max:50',
            'fechaAlta' => 'required|date'
        ]);

        $trabajador = Trabajador::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $trabajador
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $trabajador = Trabajador::find($id);

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

        $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'apellido' => 'sometimes|required|string|max:255',
            'dni' => 'sometimes|required|string|max:20|unique:trabajadors,dni,' . $id,
            'email' => 'sometimes|required|email|max:255|unique:trabajadors,email,' . $id,
            'telefono' => 'nullable|string|max:20',
            'rol' => 'nullable|string|max:50',
            'fechaAlta' => 'sometimes|required|date'
        ]);

        $trabajador->update($request->all());

        return response()->json([
            'success' => true,
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
            'message' => 'Trabajador eliminado'
        ]);
    }

    /**
     * Get statistics.
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
