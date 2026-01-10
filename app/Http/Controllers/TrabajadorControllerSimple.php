<?php

namespace App\Http\Controllers;

use App\Models\Trabajador;
use Illuminate\Http\Request;

class TrabajadorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->role === 'admin') {
            return Trabajador::all();
        }
        
        return Trabajador::where('user_id', auth()->id())->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'dni' => ['required', 'string', 'unique:trabajadors,dni', 'max:20'],
            'email' => ['required', 'email', 'unique:trabajadors,email', 'max:255'],
            'telefono' => ['required', 'string', 'max:20'],
            'rol' => ['required', 'string', 'max:50'],
            'fechaAlta' => ['required', 'date'],
        ]);

        $trabajador = Trabajador::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'dni' => $request->dni,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'rol' => $request->rol,
            'fechaAlta' => $request->fechaAlta,
            'user_id' => auth()->id(),
        ]);

        return response()->json($trabajador, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Trabajador $trabajador)
    {
        return $trabajador;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Trabajador $trabajador)
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'dni' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'telefono' => ['required', 'string', 'max:20'],
            'rol' => ['required', 'string', 'max:50'],
            'fechaAlta' => ['required', 'date'],
        ]);

        $trabajador->update($request->all());
        return response()->json($trabajador, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $trabajador)
    {
        return Trabajador::destroy($trabajador);
    }
}
