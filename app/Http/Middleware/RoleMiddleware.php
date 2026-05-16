<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if ($role === 'teacher' && !session('teacher_id')) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Dosen terlebih dahulu.');
        }

        if ($role === 'admin' && !session('admin_id')) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Admin terlebih dahulu.');
        }

        return $next($request);
    }
}
