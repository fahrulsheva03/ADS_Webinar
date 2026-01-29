<?php

use App\Models\Ebook;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

Route::post('/payments/midtrans-notification', function (Request $request) {
    $serverKey = (string) config('services.midtrans.server_key', '');
    if ($serverKey === '') {
        return response()->json(['status' => 'CONFIG_MISSING'], 500);
    }

    $orderId = (string) $request->input('order_id', '');
    $statusCode = (string) $request->input('status_code', '');
    $grossAmount = (string) $request->input('gross_amount', '');
    $signatureKey = (string) $request->input('signature_key', '');
    $transactionStatus = strtolower((string) $request->input('transaction_status', ''));
    $fraudStatus = strtolower((string) $request->input('fraud_status', ''));

    if ($orderId === '' || $statusCode === '' || $grossAmount === '' || $signatureKey === '' || $transactionStatus === '') {
        return response()->json(['status' => 'INVALID_PAYLOAD'], 400);
    }

    $expectedSignature = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);
    if (! hash_equals($expectedSignature, $signatureKey)) {
        return response()->json(['status' => 'INVALID_SIGNATURE'], 403);
    }

    $pesanan = Pesanan::query()->where('kode_pesanan', $orderId)->first();
    if (! $pesanan && ctype_digit($orderId)) {
        $pesanan = Pesanan::query()->find((int) $orderId);
    }

    if (! $pesanan) {
        return response()->json(['status' => 'OK'], 200);
    }

    $targetStatus = 'pending';
    if ($transactionStatus === 'settlement') {
        $targetStatus = 'paid';
    } elseif ($transactionStatus === 'capture') {
        $targetStatus = $fraudStatus === 'challenge' ? 'pending' : 'paid';
    } elseif ($transactionStatus === 'pending') {
        $targetStatus = 'pending';
    } elseif ($transactionStatus === 'deny' || $transactionStatus === 'cancel') {
        $targetStatus = 'failed';
    } elseif ($transactionStatus === 'expire') {
        $targetStatus = 'expired';
    } elseif ($transactionStatus === 'refund' || $transactionStatus === 'chargeback' || $transactionStatus === 'failure') {
        $targetStatus = 'failed';
    }

    DB::transaction(function () use ($pesanan, $targetStatus) {
        $locked = Pesanan::query()->whereKey($pesanan->id)->lockForUpdate()->first();
        if (! $locked) {
            return;
        }

        $current = (string) ($locked->status_pembayaran ?? '');
        if ($current === 'paid') {
            return;
        }

        if ($targetStatus !== 'paid' && ($current === 'failed' || $current === 'expired')) {
            return;
        }

        if ($current === $targetStatus) {
            return;
        }

        $update = [
            'status_pembayaran' => $targetStatus,
        ];

        if ((string) ($locked->metode_pembayaran ?? '') === '') {
            $update['metode_pembayaran'] = 'midtrans';
        }

        if ($targetStatus === 'paid') {
            $update['waktu_bayar'] = now();
            $update['metode_pembayaran'] = 'midtrans';
        }

        $locked->update($update);
    });

    return response()->json(['status' => 'OK'], 200);
});

Route::get('/ebooks', function (Request $request) {
    if (! Schema::hasTable('ebooks')) {
        return response()->json([
            'data' => [],
            'meta' => [
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 12,
                'total' => 0,
            ],
        ]);
    }

    $q = trim((string) $request->query('q', ''));

    $query = Ebook::query()->where('is_active', true);

    if ($q !== '') {
        $query->where(function ($sub) use ($q) {
            $sub->where('title', 'like', "%{$q}%")
                ->orWhere('author', 'like', "%{$q}%");
        });
    }

    $ebooks = $query
        ->orderByDesc('created_at')
        ->paginate(12)
        ->withQueryString();

    return response()->json([
        'data' => $ebooks->getCollection()->map(function (Ebook $ebook) {
            return [
                'id' => $ebook->id,
                'title' => $ebook->title,
                'author' => $ebook->author,
                'description' => $ebook->description,
                'price' => $ebook->price,
                'stock' => $ebook->stock,
                'cover_url' => $ebook->cover_image ? Storage::disk('public')->url($ebook->cover_image) : null,
            ];
        })->values(),
        'meta' => [
            'current_page' => $ebooks->currentPage(),
            'last_page' => $ebooks->lastPage(),
            'per_page' => $ebooks->perPage(),
            'total' => $ebooks->total(),
        ],
        'links' => [
            'first' => $ebooks->url(1),
            'last' => $ebooks->url($ebooks->lastPage()),
            'prev' => $ebooks->previousPageUrl(),
            'next' => $ebooks->nextPageUrl(),
        ],
    ]);
});
