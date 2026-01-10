<?php

namespace App\Http\Controllers;

use App\Models\Maquina;
use Illuminate\Http\Request;

class MaquinaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->role === 'admin') {
            return Maquina::all();
        }
        
        return Maquina::where('user_id', auth()->id())->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'numSerie' => ['required', 'string', 'unique:maquinas,numSerie', 'max:100'],
            'modelo' => ['required', 'string', 'max:255'],
            'tipo' => ['required', 'string', 'max:50'],
            'fechaCompra' => ['required', 'date'],
            'estado' => ['nullable', 'string', 'max:50'],
            'ubicacion' => ['nullable', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
        ]);

        $maquina = Maquina::create([
            'nombre' => $request->nombre,
            'numSerie' => $request->numSerie,
            'modelo' => $request->modelo,
            'tipo' => $request->tipo,
            'fechaCompra' => $request->fechaCompra,
            'estado' => $request->estado ?? 'activa',
            'ubicacion' => $request->ubicacion,
            'descripcion' => $request->descripcion,
            'user_id' => auth()->id(),
        ]);

        return response()->json($maquina, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Maquina $maquina)
    {
        return $maquina;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Maquina $maquina)
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'numSerie' => ['required', 'string', 'max:100'],
            'modelo' => ['required', 'string', 'max:255'],
            'tipo' => ['required', 'string', 'max:50'],
            'fechaCompra' => ['required', 'date'],
            'estado' => ['nullable', 'string', 'max:50'],
            'ubicacion' => ['nullable', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
        ]);

        $maquina->update($request->all());
        return response()->json($maquina, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $maquina)
    {
        return Maquina::destroy($maquina);
    }
}
