<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PesertaAuthController extends Controller
{
    public function showLogin(): View
    {
        return view('peserta.auth.login');
    }

    public function showRegister(): View
    {
        return view('peserta.auth.registrasi');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::query()
            ->where('email', $data['email'])
            ->where('role', 'user')
            ->first();

        if (! $user) {
            return back()
                ->withInput($request->except('password'))
                ->with('error', 'Email atau password salah.');
        }

        if ($user->status_akun !== 'aktif') {
            return back()
                ->withInput($request->except('password'))
                ->with('error', 'Akun Anda nonaktif.');
        }

        $ok = Auth::attempt([
            'email' => $data['email'],
            'password' => $data['password'],
        ], false);

        if (! $ok) {
            return back()
                ->withInput($request->except('password'))
                ->with('error', 'Email atau password salah.');
        }

        $request->session()->regenerate();

        return redirect()
            ->route('peserta.index')
            ->with('login_success', 'Login Berhasil');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => 'required|string|max:120',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|max:255',
        ]);

        try {
            User::query()->create([
                'nama' => $data['nama'],
                'email' => strtolower($data['email']),
                'password' => Hash::make($data['password']),
                'role' => 'user',
                'status_akun' => 'aktif',
            ]);
        } catch (\Throwable $e) {
            return back()
                ->withInput($request->except('password'))
                ->with('error', 'Registrasi gagal, silakan coba lagi.');
        }

        return redirect()
            ->route('peserta.login')
            ->with('success', 'Registrasi berhasil. Silakan login.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        if (! Auth::guard('admin')->check()) {
            $request->session()->invalidate();
        } else {
            $request->session()->migrate(true);
        }

        $request->session()->regenerateToken();

        return redirect()
            ->route('peserta.index');
    }
}
