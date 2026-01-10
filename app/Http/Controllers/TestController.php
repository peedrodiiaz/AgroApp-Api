<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    public function testUsers()
    {
        $users = User::all();
        return response()->json([
            'total_users' => $users->count(),
            'users' => $users->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'usuario' => $user->usuario,
                    'email' => $user->email,
                ];
            })
        ]);
    }

    public function testLogin(Request $request)
    {
        $usuario = $request->input('usuario');
        $password = $request->input('password');

        // Buscar el usuario
        $user = User::where('usuario', $usuario)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado',
                'usuario_buscado' => $usuario
            ], 404);
        }

        // Verificar la contraseña
        $passwordMatch = Hash::check($password, $user->password);

        return response()->json([
            'success' => true,
            'user_found' => true,
            'password_match' => $passwordMatch,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'usuario' => $user->usuario,
            ]
        ]);
    }
}
