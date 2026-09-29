<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsSiswa
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

        if (Auth::user()->isSiswa()) {
            return $next($request);
        }

        // Jika yang login adalah Guru, arahkan ke dashboard Guru secara spesifik
        return redirect()->route('guru.ujian.index')
            ->with('error', 'Akses ditolak. Halaman tersebut khusus untuk Siswa.');
    }
}
