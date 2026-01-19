<?php

use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\EventSesiController as AdminEventSesiController;
use App\Http\Controllers\Admin\LaporanKehadiranController as AdminLaporanKehadiranController;
use App\Http\Controllers\Admin\LiveSessionController as AdminLiveSessionController;
use App\Http\Controllers\Admin\PaketController as AdminPaketController;
use App\Http\Controllers\Admin\PesertaController as AdminPesertaController;
use App\Http\Controllers\Admin\ScanController as AdminScanController;
use App\Http\Controllers\Admin\TransaksiController as AdminTransaksiController;
use App\Http\Controllers\Admin\VideoController as AdminVideoController;
use App\Http\Controllers\PesertaDashboardController;
use App\Http\Controllers\ScanQrController;
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
    Route::get('/events/{event}/image', [AdminEventController::class, 'image'])->name('admin.events.image');
    Route::post('/events/bulk', [AdminEventController::class, 'bulk'])->name('admin.events.bulk');
    Route::get('/events/export/{format}', [AdminEventController::class, 'export'])->name('admin.events.export');

    Route::get('/sesi-event', [AdminEventSesiController::class, 'index'])->name('admin.sesi-event.index');
    Route::post('/sesi-event', [AdminEventSesiController::class, 'store'])->name('admin.sesi-event.store');
    Route::put('/sesi-event/{sesi}', [AdminEventSesiController::class, 'update'])->name('admin.sesi-event.update');
    Route::delete('/sesi-event/{sesi}', [AdminEventSesiController::class, 'destroy'])->name('admin.sesi-event.destroy');

    Route::get('/paket', [AdminPaketController::class, 'index'])->name('admin.paket.index');
    Route::post('/paket', [AdminPaketController::class, 'store'])->name('admin.paket.store');
    Route::put('/paket/{paket}', [AdminPaketController::class, 'update'])->name('admin.paket.update');
    Route::delete('/paket/{paket}', [AdminPaketController::class, 'destroy'])->name('admin.paket.destroy');

    Route::get('/paket/akses', [AdminPaketController::class, 'akses'])->name('admin.paket.akses');
    Route::get('/paket/{paket}/sesi', [AdminPaketController::class, 'assignedSesi'])->name('admin.paket.sesi.assigned');
    Route::post('/paket/{paket}/sesi', [AdminPaketController::class, 'syncSesi'])->name('admin.paket.sesi.sync');
    Route::delete('/paket/{paket}/sesi/{sesi}', [AdminPaketController::class, 'detachSesi'])->name('admin.paket.sesi.detach');

    Route::get('/peserta', [AdminPesertaController::class, 'index'])->name('admin.peserta.index');
    Route::get('/peserta/{user}', [AdminPesertaController::class, 'show'])->name('admin.peserta.show');

    Route::get('/transaksi', [AdminTransaksiController::class, 'index'])->name('admin.transaksi.index');
    Route::put('/transaksi/{transaksi}', [AdminTransaksiController::class, 'update'])->name('admin.transaksi.update');
    Route::delete('/transaksi/{transaksi}', [AdminTransaksiController::class, 'destroy'])->name('admin.transaksi.destroy');
    Route::post('/transaksi/bulk', [AdminTransaksiController::class, 'bulk'])->name('admin.transaksi.bulk');
    Route::get('/transaksi/export/{format}', [AdminTransaksiController::class, 'export'])->name('admin.transaksi.export');

    Route::get('/live', [AdminLiveSessionController::class, 'index'])->name('admin.live.index');
    Route::get('/live/poll', [AdminLiveSessionController::class, 'poll'])->name('admin.live.poll');
    Route::post('/live', [AdminLiveSessionController::class, 'store'])->name('admin.live.store');
    Route::put('/live/{sesi}', [AdminLiveSessionController::class, 'update'])->name('admin.live.update');
    Route::post('/live/{sesi}/start', [AdminLiveSessionController::class, 'start'])->name('admin.live.start');
    Route::post('/live/{sesi}/stop', [AdminLiveSessionController::class, 'stop'])->name('admin.live.stop');
    Route::delete('/live/{sesi}', [AdminLiveSessionController::class, 'destroy'])->name('admin.live.destroy');

    Route::get('/scan', [AdminScanController::class, 'index'])->name('admin.scan.index');
    Route::get('/scan/history', [AdminScanController::class, 'history'])->name('admin.scan.history');
    Route::get('/scan/export/{format}', [AdminScanController::class, 'export'])->name('admin.scan.export');
    Route::post('/scan/checkin', [ScanQrController::class, 'checkin'])->name('admin.scan.checkin');

    Route::get('/laporan/kehadiran', [AdminLaporanKehadiranController::class, 'index'])->name('admin.laporan.kehadiran.index');
    Route::get('/laporan/kehadiran/export/{format}', [AdminLaporanKehadiranController::class, 'export'])->name('admin.laporan.kehadiran.export');
    Route::get('/laporan/transaksi', [AdminTransaksiController::class, 'laporan'])->name('admin.laporan.transaksi.index');
    Route::get('/laporan/transaksi/export/{format}', [AdminTransaksiController::class, 'exportLaporan'])->name('admin.laporan.transaksi.export');

    Route::get('/video', [AdminVideoController::class, 'index'])->name('admin.video.index');
    Route::post('/video', [AdminVideoController::class, 'store'])->name('admin.video.store');
    Route::put('/video/{video}', [AdminVideoController::class, 'update'])->name('admin.video.update');
    Route::delete('/video/{video}', [AdminVideoController::class, 'destroy'])->name('admin.video.destroy');
    Route::post('/video/bulk', [AdminVideoController::class, 'bulk'])->name('admin.video.bulk');
    Route::get('/video/export/{format}', [AdminVideoController::class, 'export'])->name('admin.video.export');

    // Login
    Route::get('/login', function () {
        return view('admin.auth.login');
    })->name('admin.login');
});
