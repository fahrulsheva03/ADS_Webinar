<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.index');
        }

        if (Auth::guard('web')->check()) {
            return redirect()->route('peserta.index');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $ok = Auth::guard('admin')->attempt([
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'admin',
            'status_akun' => 'aktif',
        ], false);

        if (! $ok) {
            return back()
                ->withInput($request->except('password'))
                ->with('error', 'Email atau password salah.');
        }

        $request->session()->regenerate();

        return redirect()
            ->route('admin.index')
            ->with('success', 'Login Berhasil');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
