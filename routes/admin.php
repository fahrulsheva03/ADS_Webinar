<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EbookController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\EventSesiController as AdminEventSesiController;
use App\Http\Controllers\Admin\KontenHalamanController;
use App\Http\Controllers\Admin\LaporanKehadiranController as AdminLaporanKehadiranController;
use App\Http\Controllers\Admin\LiveSessionController as AdminLiveSessionController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\PaketController as AdminPaketController;
use App\Http\Controllers\Admin\PesertaController as AdminPesertaController;
use App\Http\Controllers\Admin\ScanController as AdminScanController;
use App\Http\Controllers\Admin\SpeakerController;
use App\Http\Controllers\Admin\TransaksiController as AdminTransaksiController;
use App\Http\Controllers\Admin\VideoController as AdminVideoController;
use App\Http\Controllers\ScanQrController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('admin.login.store');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    Route::middleware('admin')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.index');

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

        Route::prefix('konten-halaman')->group(function () {
            Route::get('/', [KontenHalamanController::class, 'home'])->name('admin.konten-halaman.home');
            Route::post('/', [KontenHalamanController::class, 'updateHome'])->name('admin.konten-halaman.home.update');
            Route::get('/about', [KontenHalamanController::class, 'about'])->name('admin.konten-halaman.about');
            Route::post('/about', [KontenHalamanController::class, 'updateAbout'])->name('admin.konten-halaman.about.update');
            Route::post('/pricing-cards', [KontenHalamanController::class, 'pricingCardsStore'])->name('admin.konten-halaman.pricing-cards.store');
            Route::put('/pricing-cards/{cardId}', [KontenHalamanController::class, 'pricingCardsUpdate'])->name('admin.konten-halaman.pricing-cards.update');
            Route::delete('/pricing-cards/{cardId}', [KontenHalamanController::class, 'pricingCardsDestroy'])->name('admin.konten-halaman.pricing-cards.destroy');
            Route::post('/upload-image', [KontenHalamanController::class, 'uploadImage'])->name('admin.konten-halaman.upload-image');
        });
        Route::prefix('news')->group(function () {
            Route::get('/', [NewsController::class, 'index'])->name('admin.news.index');
            Route::get('/create', [NewsController::class, 'create'])->name('admin.news.create');
            Route::get('/{news}/edit', [NewsController::class, 'edit'])->name('admin.news.edit');
            Route::post('/', [NewsController::class, 'store'])->name('admin.news.store');
            Route::put('/{news}', [NewsController::class, 'update'])->name('admin.news.update');
            Route::delete('/{news}', [NewsController::class, 'destroy'])->name('admin.news.destroy');
        });
        Route::resource('speakers', SpeakerController::class)->names('speakers');

        Route::name('admin.')->group(function () {
            Route::resource('ebooks', EbookController::class);
            Route::get('/ebooks/{ebook}/cover', [EbookController::class, 'cover'])->name('ebooks.cover');
            Route::get('/ebooks/{ebook}/pdf', [EbookController::class, 'pdf'])->name('ebooks.pdf');
        });
    });
});
