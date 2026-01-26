<?php

use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

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
