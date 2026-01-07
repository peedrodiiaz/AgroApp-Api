<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): Response
    {
        $credentials = $request->validate([
            'usuario' => ['required', 'string'], // Cambiado de 'email' a 'usuario'
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials)) {
            return response([
                'success' => false,
                'message' => 'Credenciales inválidas'
            ], 422);
        }

        $user = $request->user();
        $token = $user->createToken('api')->plainTextToken;
        
        return response([
            'success' => true,
            'token' => $token,
            'user'  => $user,
        ], 200);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $request->user()->currentAccessToken()?->delete();
        return response()->noContent();
    }
}
