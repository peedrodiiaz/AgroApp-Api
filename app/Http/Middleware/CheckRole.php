<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\Cast\String_;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle (Request $request, Closure $next, String ...$roles):
    Response {
        $user = $request->user();
        if (!$user){
            return response()-> json([
                'message' => 'Usuario no autenticado'
            ]);
        }
        $allowedRoles = array_map(fn($role) => strtolower($role), $roles);

        if (!in_array($user->email, $allowedRoles, true)) {
            return response()->json([
                'message' => 'Acceso no autorizado para el rol de usuario actual.'
            ], 403);
        }
    }
}
