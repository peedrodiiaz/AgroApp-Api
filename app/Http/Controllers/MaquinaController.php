<?php

namespace App\Http\Controllers;

use App\Models\Maquina;
use Illuminate\Http\Request;

class MaquinaController extends Controller
{
    public function index(Request $request)
    {
        $query = Maquina::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nombre', 'like', "%{$search}%")
                ->orWhere('numSerie', 'like', "%{$search}%")
                ->orWhere('modelo', 'like', "%{$search}%");
        }

        return response()->json(['success' => true, 'data' => $query->get()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'numSerie' => 'required|string|unique:maquinas,numSerie|max:100',
            'modelo' => 'required|string|max:255',
            'tipo' => 'required|string|max:50',
            'fechaCompra' => 'required|date',
            'estado' => 'nullable|in:activa,inactiva,mantenimiento',
            'ubicacion' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string'
        ]);

        $maquina = Maquina::create($request->all());
        return response()->json(['success' => true, 'data' => $maquina], 201);
    }

    public function show($id)
    {
        $maquina = Maquina::find($id);
        if (!$maquina) {
            return response()->json(['success' => false, 'message' => 'No encontrada'], 404);
        }
        return response()->json(['success' => true, 'data' => $maquina]);
    }

    public function update(Request $request, $id)
    {
        $maquina = Maquina::find($id);
        if (!$maquina) {
            return response()->json(['success' => false, 'message' => 'No encontrada'], 404);
        }

        $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'numSerie' => 'sometimes|string|max:100|unique:maquinas,numSerie,' . $id,
            'modelo' => 'sometimes|string|max:255',
            'tipo' => 'sometimes|string|max:50',
            'fechaCompra' => 'sometimes|date',
            'estado' => 'nullable|in:activa,inactiva,mantenimiento',
            'ubicacion' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string'
        ]);

        $maquina->update($request->all());
        return response()->json(['success' => true, 'data' => $maquina]);
    }

    public function destroy($id)
    {
        $maquina = Maquina::find($id);
        if (!$maquina) {
            return response()->json(['success' => false, 'message' => 'No encontrada'], 404);
        }
        $maquina->delete();
        return response()->json(['success' => true, 'message' => 'Eliminada']);
    }

    public function stats()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total' => Maquina::count(),
                'por_tipo' => Maquina::selectRaw('tipo, COUNT(*) as total')->groupBy('tipo')->get(),
                'por_estado' => Maquina::selectRaw('estado, COUNT(*) as total')->groupBy('estado')->get()
            ]
        ]);
    }

    public function cambiarEstado(Request $request, $id)
    {
        $maquina = Maquina::find($id);
        if (!$maquina) {
            return response()->json(['success' => false, 'message' => 'No encontrada'], 404);
        }

        $request->validate(['estado' => 'required|in:activa,inactiva,mantenimiento']);
        $maquina->update(['estado' => $request->estado]);

        return response()->json(['success' => true, 'data' => $maquina]);
    }
}
