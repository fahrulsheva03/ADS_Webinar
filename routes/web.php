<?php

use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\PesertaDashboardController;
use Illuminate\Support\Facades\Route;

// URL PESERTA
Route::get('/', function () {
    return view('peserta.index');
})->name('peserta.index');
Route::get('/2', function () {
    return view('peserta.index2');
})->name('peserta.index2');
Route::get('/3', function () {
    return view('peserta.index3');
})->name('peserta.index3');
Route::get('/about', function () {
    return view('peserta.about');
})->name('peserta.about');
Route::get('/contact', function () {
    return view('peserta.contact');
})->name('peserta.contact');
Route::get('/event', function () {
    return view('peserta.event');
})->name('peserta.event');
Route::get('/shop', function () {
    return view('peserta.shop');
})->name('peserta.shop');
Route::get('/blog', function () {
    return view('peserta.blog');
})->name('peserta.blog');

// URL DASHBOARD PESERTA
Route::prefix('dashboard')->group(function () {
    Route::get('/', [PesertaDashboardController::class, 'index'])->name('peserta.dashboard');
    Route::post('/join/{sesi}', [PesertaDashboardController::class, 'join'])->name('peserta.join');
    Route::get('/video/{video}', [PesertaDashboardController::class, 'video'])->name('peserta.video');
    Route::get('/profile', function () {
        return view('peserta.dashboard.profile');
    })->name('peserta.profile');
});

// URL AUTH LOGIN & REGISTRASI PESERTA
Route::get('/login', function () {
    return view('peserta.auth.login');
})->name('peserta.login');
Route::get('/registrasi', function () {
    return view('peserta.auth.registrasi');
})->name('peserta.registrasi');

// URL UNTUK ADMIN
Route::prefix('admin')->group(function () {
    Route::get('/', function () {
        return view('admin.index');
    })->name('admin.index');

    Route::get('/events', [AdminEventController::class, 'index'])->name('admin.events.index');
    Route::post('/events', [AdminEventController::class, 'store'])->name('admin.events.store');
    Route::put('/events/{event}', [AdminEventController::class, 'update'])->name('admin.events.update');
    Route::delete('/events/{event}', [AdminEventController::class, 'destroy'])->name('admin.events.destroy');
    Route::post('/events/bulk', [AdminEventController::class, 'bulk'])->name('admin.events.bulk');
    Route::get('/events/export/{format}', [AdminEventController::class, 'export'])->name('admin.events.export');

    // Login
    Route::get('/login', function () {
        return view('admin.auth.login');
    })->name('admin.login');
});
