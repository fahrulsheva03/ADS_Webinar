<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('peserta.index');
});

// Login
Route::get('/login', function () {
    return view('peserta.auth.login');
});

Route::get('/registrasi', function () {
    return view('peserta.auth.registrasi');
});

Route::prefix('admin')->group(function () {
    Route::get('/', function () {
        return view('admin.index');
    });

    // Login
    Route::get('/login', function () {
        return view('admin.auth.login');
    });
});
