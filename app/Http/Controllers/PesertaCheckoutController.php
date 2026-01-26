<?php

namespace App\Http\Controllers;

use App\Models\Paket;
use App\Models\Pesanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PesertaCheckoutController extends Controller
{
    public function start(Request $request): RedirectResponse
    {
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
    }

    public function payment(Request $request, ?Pesanan $pesanan = null): RedirectResponse|View
    {
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
    }

    public function midtransToken(Request $request, Pesanan $pesanan): JsonResponse
    {
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
    }

    public function paymentMethod(Request $request, Pesanan $pesanan): RedirectResponse
    {
        $userId = (int) $request->user()->id;
        abort_if((int) $pesanan->user_id !== $userId, 404);

        $data = $request->validate([
            'metode_pembayaran' => 'required|in:midtrans',
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
    }

    public function confirm(Request $request, ?Pesanan $pesanan = null): RedirectResponse|View
    {
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
    }
}
