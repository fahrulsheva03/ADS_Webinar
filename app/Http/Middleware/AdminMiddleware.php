<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();

            if (($user->role ?? null) === 'admin' && ($user->status_akun ?? null) === 'aktif') {
                Auth::shouldUse('admin');

                return $next($request);
            }

            Auth::guard('admin')->logout();
        }

        if (Auth::guard('web')->check()) {
            return redirect()->route('peserta.index');
        }

        return redirect()->route('admin.login');
    }
}
