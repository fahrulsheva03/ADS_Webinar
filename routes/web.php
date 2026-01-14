<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('peserta.index');
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
