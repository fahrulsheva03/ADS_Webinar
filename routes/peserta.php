<?php

use App\Http\Controllers\PesertaAuthController;
use App\Http\Controllers\PesertaDashboardController;
use App\Models\News;
use App\Models\Paket;
use App\Models\Pesanan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

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

Route::get('/cart', function () {
    return view('peserta.cart');
})->name('peserta.cart');

Route::prefix('checkout')->group(function () {
    Route::post('/start', function (\Illuminate\Http\Request $request) {
        $data = $request->validate([
            'paket_id' => 'required|integer|exists:paket,id',
            'qty' => 'nullable|integer|min:1|max:99',
        ]);

        $qty = (int) ($data['qty'] ?? 1);
        $qty = max(1, $qty);

        $paket = Paket::query()->findOrFail((int) $data['paket_id']);
        $harga = (float) ($paket->harga ?? 0);

        $deadlineCutoff = now()->subHours(24);
        Pesanan::query()
            ->where('user_id', (int) $request->user()->id)
            ->where('status_pembayaran', 'pending')
            ->where('created_at', '<=', $deadlineCutoff)
            ->update(['status_pembayaran' => 'expired']);

        $existing = Pesanan::query()
            ->where('user_id', (int) $request->user()->id)
            ->where('paket_id', (int) $paket->id)
            ->where('status_pembayaran', 'pending')
            ->whereNull('metode_pembayaran')
            ->where('created_at', '>', $deadlineCutoff)
            ->latest('id')
            ->first();

        if ($existing) {
            return redirect()->route('peserta.checkout.payment', ['pesanan' => $existing->id]);
        }

        $kode = '';
        for ($i = 0; $i < 5; $i++) {
            $candidate = 'TRX-'.strtoupper(Str::random(10));
            $exists = Pesanan::query()->where('kode_pesanan', $candidate)->exists();
            if (! $exists) {
                $kode = $candidate;
                break;
            }
        }

        if ($kode === '') {
            $kode = 'TRX-'.strtoupper(Str::random(16));
        }

        $pesanan = Pesanan::query()->create([
            'user_id' => (int) $request->user()->id,
            'paket_id' => (int) $paket->id,
            'kode_pesanan' => $kode,
            'status_pembayaran' => 'pending',
            'total_bayar' => $harga * $qty,
            'metode_pembayaran' => null,
            'waktu_bayar' => null,
        ]);

        return redirect()->route('peserta.checkout.payment', ['pesanan' => $pesanan->id]);
    })->middleware('auth')->name('peserta.checkout.start');

    Route::get('/pembayaran/{pesanan?}', function (\Illuminate\Http\Request $request, ?Pesanan $pesanan = null) {
        $userId = (int) $request->user()->id;

        if ($pesanan && (int) $pesanan->user_id !== $userId) {
            abort(404);
        }

        if (! $pesanan) {
            $pesanan = Pesanan::query()
                ->where('user_id', $userId)
                ->orderByDesc('id')
                ->first();
        }

        if (! $pesanan) {
            return redirect()->route('peserta.cart');
        }

        $deadlineAt = optional($pesanan->created_at)->copy()->addHours(24);
        if ($pesanan->status_pembayaran === 'pending' && $deadlineAt && $deadlineAt->lte(now())) {
            $pesanan->update(['status_pembayaran' => 'expired']);
        }

        $pesanan->loadMissing(['paket.event', 'paket.sesi']);

        $metodeOptions = [
            'midtrans' => 'Midtrans (Snap)',
        ];

        return view('peserta.checkout.payment', [
            'pesanan' => $pesanan,
            'metodeOptions' => $metodeOptions,
            'deadlineAt' => $deadlineAt,
            'midtransClientKey' => (string) config('services.midtrans.client_key', ''),
            'midtransSnapJsUrl' => (string) config('services.midtrans.snap_js_url', 'https://app.sandbox.midtrans.com/snap/snap.js'),
        ]);
    })->middleware('auth')->name('peserta.checkout.payment');

    Route::post('/pembayaran/{pesanan}/midtrans-token', function (\Illuminate\Http\Request $request, Pesanan $pesanan) {
        $userId = (int) $request->user()->id;
        abort_if((int) $pesanan->user_id !== $userId, 404);

        $deadlineAt = optional($pesanan->created_at)->copy()->addHours(24);
        if ($pesanan->status_pembayaran === 'pending' && $deadlineAt && $deadlineAt->lte(now())) {
            $pesanan->update(['status_pembayaran' => 'expired']);
        }

        if ($pesanan->status_pembayaran !== 'pending') {
            return response()->json([
                'message' => 'Pesanan sudah diproses.',
            ], 409);
        }

        if ($pesanan->status_pembayaran === 'expired') {
            return response()->json([
                'message' => 'Pesanan sudah expired.',
            ], 422);
        }

        $serverKey = (string) config('services.midtrans.server_key', '');
        $snapUrl = (string) config('services.midtrans.snap_url', 'https://app.sandbox.midtrans.com/snap/v1/transactions');

        if ($serverKey === '') {
            return response()->json([
                'message' => 'Konfigurasi Midtrans belum tersedia.',
            ], 500);
        }

        $pesanan->loadMissing(['paket.event', 'user']);

        $grossAmount = (int) round((float) ($pesanan->total_bayar ?? 0));
        $grossAmount = max(1, $grossAmount);

        $finishUrl = route('peserta.checkout.confirm', ['pesanan' => $pesanan->id], true);

        $payload = [
            'transaction_details' => [
                'order_id' => (string) $pesanan->kode_pesanan,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => (string) ($pesanan->user?->nama ?? 'Peserta'),
                'email' => (string) ($pesanan->user?->email ?? ''),
            ],
            'item_details' => [
                [
                    'id' => (string) ($pesanan->paket?->id ?? $pesanan->paket_id),
                    'price' => $grossAmount,
                    'quantity' => 1,
                    'name' => (string) ($pesanan->paket?->nama_paket ?? 'Paket'),
                ],
            ],
            'callbacks' => [
                'finish' => $finishUrl,
            ],
            'expiry' => [
                'start_time' => now()->format('Y-m-d H:i:s O'),
                'unit' => 'hour',
                'duration' => 24,
            ],
        ];

        $resp = Http::withBasicAuth($serverKey, '')
            ->acceptJson()
            ->asJson()
            ->post($snapUrl, $payload);

        if (! $resp->successful()) {
            return response()->json([
                'message' => 'Gagal membuat transaksi Midtrans.',
            ], 502);
        }

        $data = $resp->json();
        $token = (string) ($data['token'] ?? '');
        $redirectUrl = (string) ($data['redirect_url'] ?? '');

        if ($token === '') {
            return response()->json([
                'message' => 'Token Midtrans tidak tersedia.',
            ], 502);
        }

        if ((string) ($pesanan->metode_pembayaran ?? '') !== 'midtrans') {
            $pesanan->update([
                'metode_pembayaran' => 'midtrans',
            ]);
        }

        return response()->json([
            'token' => $token,
            'redirect_url' => $redirectUrl,
            'finish_url' => $finishUrl,
        ]);
    })->middleware('auth')->name('peserta.checkout.midtrans.token');

    Route::post('/pembayaran/{pesanan}/metode', function (\Illuminate\Http\Request $request, Pesanan $pesanan) {
        $userId = (int) $request->user()->id;
        abort_if((int) $pesanan->user_id !== $userId, 404);

        $data = $request->validate([
            'metode_pembayaran' => 'required|in:bank_bca,bank_bri,bank_mandiri,ewallet_gopay,ewallet_ovo,ewallet_dana',
        ]);

        $deadlineAt = optional($pesanan->created_at)->copy()->addHours(24);
        if ($pesanan->status_pembayaran === 'pending' && $deadlineAt && $deadlineAt->lte(now())) {
            $pesanan->update(['status_pembayaran' => 'expired']);

            return redirect()->route('peserta.checkout.payment', ['pesanan' => $pesanan->id]);
        }

        if ($pesanan->status_pembayaran !== 'pending') {
            return redirect()->route('peserta.checkout.confirm', ['pesanan' => $pesanan->id]);
        }

        $pesanan->update([
            'metode_pembayaran' => $data['metode_pembayaran'],
        ]);

        return redirect()->route('peserta.checkout.confirm', ['pesanan' => $pesanan->id]);
    })->middleware('auth')->name('peserta.checkout.payment.method');

    Route::get('/konfirmasi/{pesanan?}', function (\Illuminate\Http\Request $request, ?Pesanan $pesanan = null) {
        $userId = (int) $request->user()->id;

        if ($pesanan && (int) $pesanan->user_id !== $userId) {
            abort(404);
        }

        if (! $pesanan) {
            $pesanan = Pesanan::query()
                ->where('user_id', $userId)
                ->orderByDesc('id')
                ->first();
        }

        if (! $pesanan) {
            return redirect()->route('peserta.cart');
        }

        $deadlineAt = optional($pesanan->created_at)->copy()->addHours(24);
        if ($pesanan->status_pembayaran === 'pending' && $deadlineAt && $deadlineAt->lte(now())) {
            $pesanan->update(['status_pembayaran' => 'expired']);
        }

        $pesanan->loadMissing(['paket.event', 'paket.sesi']);

        $metodeOptions = [
            'midtrans' => 'Midtrans (Snap)',
        ];

        return view('peserta.checkout.confirm', [
            'pesanan' => $pesanan,
            'metodeOptions' => $metodeOptions,
            'deadlineAt' => $deadlineAt,
        ]);
    })->middleware('auth')->name('peserta.checkout.confirm');
});

Route::get('/blog', function () {
    if (! Schema::hasTable('news')) {
        return view('peserta.blog', [
            'news' => collect(),
        ]);
    }

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
    abort_if(! Schema::hasTable('news'), 404);

    $news = News::query()
        ->with(['category', 'creator'])
        ->where('status', 'published')
        ->where('slug', $slug)
        ->firstOrFail();

    return view('peserta.blog-show', [
        'news' => $news,
    ]);
})->name('peserta.blog.show');

Route::prefix('dashboard')->middleware('auth')->group(function () {
    Route::get('/', [PesertaDashboardController::class, 'index'])->name('peserta.dashboard');
    Route::post('/join/{sesi}', [PesertaDashboardController::class, 'join'])->name('peserta.join');
    Route::get('/video/{video}', [PesertaDashboardController::class, 'video'])->name('peserta.video');
    Route::get('/profile', function () {
        return view('peserta.dashboard.profile');
    })->name('peserta.profile');
});

Route::get('/login', [PesertaAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [PesertaAuthController::class, 'login'])->name('peserta.login.store');
Route::post('/logout', [PesertaAuthController::class, 'logout'])->name('peserta.logout');
Route::get('/registrasi', [PesertaAuthController::class, 'showRegister'])->name('peserta.registrasi');
Route::post('/registrasi', [PesertaAuthController::class, 'register'])->name('peserta.registrasi.store');
