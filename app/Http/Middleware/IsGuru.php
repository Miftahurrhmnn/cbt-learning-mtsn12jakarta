<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsGuru
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->isGuru()) {
            return $next($request);
        }

        // Jika yang login adalah Siswa, arahkan ke dashboard Siswa secara spesifik
        return redirect()->route('siswa.dashboard')
            ->with('error', 'Akses ditolak. Halaman tersebut khusus untuk Guru.');
    }
}
