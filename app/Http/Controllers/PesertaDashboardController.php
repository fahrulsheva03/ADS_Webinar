<?php

namespace App\Http\Controllers;

use App\Models\EventSesi;
use App\Models\Pesanan;
use App\Models\VideoSesi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PesertaDashboardController extends Controller
{
    /**
     * DASHBOARD PESERTA
     */
    public function index()
    {
        $userId = (int) Auth::id();

        $deadlineCutoff = now()->subHours(24);
        Pesanan::query()
            ->where('user_id', $userId)
            ->where('status_pembayaran', 'pending')
            ->where('created_at', '<=', $deadlineCutoff)
            ->update(['status_pembayaran' => 'expired']);

        $pesananTerbaru = Pesanan::query()
            ->with(['paket.event', 'paket.sesi', 'ebook'])
            ->where('user_id', $userId)
            ->whereIn('status_pembayaran', ['pending', 'paid'])
            ->orderByDesc('id')
            ->first();

        $riwayatPembayaran = Pesanan::query()
            ->with(['paket.event', 'ebook'])
            ->where('user_id', $userId)
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $pesanan = Pesanan::with(['paket.event.sesi.video'])
            ->where('user_id', $userId)
            ->where('status_pembayaran', 'paid')
            ->whereNotNull('paket_id')
            ->get();

        $ebookOrders = Pesanan::with(['ebook'])
            ->where('user_id', $userId)
            ->where('status_pembayaran', 'paid')
            ->whereNotNull('ebook_id')
            ->get();

        return view('peserta.dashboard.index', [
            'pesanan' => $pesanan,
            'ebookOrders' => $ebookOrders,
            'pesananTerbaru' => $pesananTerbaru,
            'riwayatPembayaran' => $riwayatPembayaran,
        ]);
    }

    /**
     * JOIN LIVE ZOOM
     */
    public function join(EventSesi $sesi)
    {
        // SECURITY UTAMA
        abort_unless(Gate::allows('joinZoom', $sesi), 403);

        return redirect()->away($sesi->zoom_link);
    }

    /**
     * TONTON VIDEO REKAMAN
     */
    public function video(VideoSesi $video)
    {
        // SECURITY UTAMA
        Gate::authorize('view', $video);

        abort_if(empty($video->url_video), 404);

        return redirect()->away($video->url_video);
    }
}
