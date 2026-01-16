<?php

namespace App\Http\Controllers;

use App\Models\EventSesi;
use App\Models\KehadiranSesi;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScanQrController extends Controller
{
    /**
     * CHECK-IN OFFLINE VIA QR
     */
    public function checkin(Request $request)
    {
        // 0. Validasi input dasar
        $data = $request->validate([
            'qr_token' => 'required|string',
            'event_sesi_id' => 'required|exists:event_sesi,id',
        ]);

        // Gunakan transaksi biar aman dari race condition
        return DB::transaction(function () use ($data) {
            // 1. Cari pesanan berdasarkan QR
            $pesanan = Pesanan::with(['paket', 'paket.sesi'])
                ->where('kode_pesanan', $data['qr_token'])
                ->where('status_pembayaran', 'paid')
                ->first();

            if (! $pesanan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'QR tidak valid atau pesanan belum dibayar',
                ], 422);
            }

            // 2. Ambil sesi yang discan
            $sesi = EventSesi::find($data['event_sesi_id']);

            // 3. Pastikan sesi milik event yang sama
            if ($pesanan->paket->event_id !== $sesi->event_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'QR tidak berlaku untuk event ini',
                ], 422);
            }

            // 4. Validasi paket BOLEH sesi ini (pivot paket_sesi)
            $bolehSesi = $pesanan->paket->sesi()
                ->where('event_sesi_id', $sesi->id)
                ->exists();

            if (! $bolehSesi) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Peserta tidak memiliki akses untuk hari/sesi ini',
                ], 403);
            }

            // 5. Cegah double check-in (per sesi)
            $sudahCheckin = KehadiranSesi::where('user_id', $pesanan->user_id)
                ->where('event_sesi_id', $sesi->id)
                ->exists();

            if ($sudahCheckin) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Peserta sudah check-in untuk sesi ini',
                ], 409);
            }

            // 6. Simpan kehadiran (OFFLINE QR)
            KehadiranSesi::create([
                'user_id' => $pesanan->user_id,
                'event_sesi_id' => $sesi->id,
                'waktu_join' => now(),
            ]);

            // 7. Response sukses (dipakai UI panitia)
            return response()->json([
                'status' => 'success',
                'message' => 'Check-in berhasil',
                'data' => [
                    'nama_peserta' => $pesanan->user->nama,
                    'event' => $sesi->event->judul,
                    'sesi' => $sesi->judul_sesi,
                    'waktu' => now()->toDateTimeString(),
                ],
            ]);
        });
    }
}
