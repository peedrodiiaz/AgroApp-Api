<?php

namespace App\Http\Controllers;

use App\Models\Incidencia;
use Illuminate\Http\Request;

class IncidenciaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->role === 'admin') {
            return Incidencia::all();
        }
        
        return Incidencia::where('user_id', auth()->id())->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string'],
            'estado' => ['required', 'string', 'max:50'],
            'prioridad' => ['required', 'string', 'max:50'],
            'fechaApertura' => ['required', 'date'],
            'fechaCierre' => ['nullable', 'date'],
            'trabajador_id' => ['required', 'exists:trabajadors,id'],
            'maquina_id' => ['required', 'exists:maquinas,id'],
        ]);

        $incidencia = Incidencia::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'estado' => $request->estado,
            'prioridad' => $request->prioridad,
            'fechaApertura' => $request->fechaApertura,
            'fechaCierre' => $request->fechaCierre,
            'trabajador_id' => $request->trabajador_id,
            'maquina_id' => $request->maquina_id,
            'user_id' => auth()->id(),
        ]);

        return response()->json($incidencia, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Incidencia $incidencia)
    {
        return $incidencia;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Incidencia $incidencia)
    {
        $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string'],
            'estado' => ['required', 'string', 'max:50'],
            'prioridad' => ['required', 'string', 'max:50'],
            'fechaApertura' => ['required', 'date'],
            'fechaCierre' => ['nullable', 'date'],
            'trabajador_id' => ['required', 'exists:trabajadors,id'],
            'maquina_id' => ['required', 'exists:maquinas,id'],
        ]);

        $incidencia->update($request->all());
        return response()->json($incidencia, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $incidencia)
    {
        return Incidencia::destroy($incidencia);
    }
}
