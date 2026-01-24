<?php

use App\Http\Controllers\PesertaAuthController;
use App\Http\Controllers\PesertaDashboardController;
use App\Models\News;
use Illuminate\Support\Facades\Route;

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
    $news = News::query()
        ->with(['category', 'creator'])
        ->where('status', 'published')
        ->orderByDesc('published_at')
        ->orderByDesc('created_at')
        ->paginate(9)
        ->withQueryString();

    return view('peserta.blog', [
        'news' => $news,
    ]);
})->name('peserta.blog');

Route::get('/blog/{slug}', function (string $slug) {
    $news = News::query()
        ->with(['category', 'creator'])
        ->where('status', 'published')
        ->where('slug', $slug)
        ->firstOrFail();

    return view('peserta.blog-show', [
        'news' => $news,
    ]);
})->name('peserta.blog.show');

Route::prefix('dashboard')->group(function () {
    Route::get('/', [PesertaDashboardController::class, 'index'])->name('peserta.dashboard');
    Route::post('/join/{sesi}', [PesertaDashboardController::class, 'join'])->name('peserta.join');
    Route::get('/video/{video}', [PesertaDashboardController::class, 'video'])->name('peserta.video');
    Route::get('/profile', function () {
        return view('peserta.dashboard.profile');
    })->name('peserta.profile');
});

Route::get('/login', [PesertaAuthController::class, 'showLogin'])->name('peserta.login');
Route::post('/login', [PesertaAuthController::class, 'login'])->name('peserta.login.store');
Route::post('/logout', [PesertaAuthController::class, 'logout'])->name('peserta.logout');
Route::get('/registrasi', [PesertaAuthController::class, 'showRegister'])->name('peserta.registrasi');
Route::post('/registrasi', [PesertaAuthController::class, 'register'])->name('peserta.registrasi.store');
