<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Cek apakah role user ada di dalam daftar role yang diizinkan
        if (in_array(Auth::user()->role, $roles)) {
            return $next($request);
        }

        // Jika tidak punya akses (misal: mahasiswa mencoba masuk halaman admin), tendang ke katalog
        return redirect('/katalog')->withErrors('Anda tidak memiliki izin untuk mengakses halaman tersebut.');
    }
}