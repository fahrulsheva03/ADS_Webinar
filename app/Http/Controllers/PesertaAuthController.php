<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PesertaAuthController extends Controller
{
    public function showRegister(): View
    {
        return view('peserta.auth.registrasi');
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
}

