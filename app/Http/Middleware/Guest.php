<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

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
        if(Session::has("role")){
            // Jika iya kembalikan sesuai posisi semula
            if (Session::get("role") == "admin") return redirect('/admin/beranda');
            if (Session::get("role") == "pemilik") return redirect('/pemilik/beranda');
            if (Session::get("role") == "tempat") return redirect('/tempat/beranda');
        }

        // Jika tidak lanjutkan request
        return $next($request);
    }
}
