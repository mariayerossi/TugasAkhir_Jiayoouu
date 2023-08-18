<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Guest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user pernah login
        if(session()->has('role')){
            // Jika iya kembalikan sesuai posisi semula
            if (session()->get('role') == "admin") return redirect('/beranda');
        }

        // Jika tidak lanjutkan request
        return $next($request);
    }
}
