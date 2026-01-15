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
        $pesanan = Pesanan::with([
            'paket.event.sesi.video',
        ])
            ->where('user_id', Auth::id())
            ->where('status_pembayaran', 'paid')
            ->get();

        return view('peserta.dashboard.index', compact('pesanan'));
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
