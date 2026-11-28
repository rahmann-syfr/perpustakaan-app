<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Batasi akses route berdasarkan role pengguna.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (in_array($user->role->value, $roles, true)) {
            return $next($request);
        }

        return redirect()->route('dashboard')
            ->with('error', 'Anda tidak memiliki izin untuk mengakses halaman tersebut.');
    }
}
