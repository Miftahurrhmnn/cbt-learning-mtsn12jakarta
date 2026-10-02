<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
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

        if (Auth::user()->isAdmin()) {
            return $next($request);
        }

        // Jika bukan admin (guru atau siswa), tolak akses
        if (Auth::user()->isGuru()) {
            return redirect()->route('guru.ujian.index')
                ->with('error', 'Akses ditolak. Halaman tersebut khusus untuk Administrator.');
        }

        return redirect()->route('siswa.dashboard')
            ->with('error', 'Akses ditolak. Halaman tersebut khusus untuk Administrator.');
    }
}
