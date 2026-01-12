<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Peserta</title>
</head>
<body>

<h1>Dashboard Peserta</h1>

<hr>

@if ($pesanan->isEmpty())
    <p>Kamu belum mengikuti event apapun.</p>
@else

    @foreach ($pesanan as $order)
        @php
            $event = $order->paket->event;
        @endphp

        <div style="border:1px solid #ccc; padding:15px; margin-bottom:20px;">
            <h2>{{ $event->judul }}</h2>
            <p>Status Event: <strong>{{ strtoupper($event->status) }}</strong></p>
            <p>Paket: {{ $order->paket->nama_paket }}</p>

            <hr>

            <h3>Daftar Sesi</h3>

            @foreach ($event->sesi as $sesi)
                <div style="margin-bottom:10px; padding-left:15px;">
                    <p>
                        <strong>{{ $sesi->judul_sesi }}</strong><br>
                        {{ $sesi->waktu_mulai }} - {{ $sesi->waktu_selesai }}<br>
                        Status Sesi: {{ $sesi->status_sesi }}
                    </p>

                    {{-- JOIN LIVE --}}
                    @if (
                        $sesi->status_sesi === 'live'
                        && $order->paket->akses_live
                    )
                        <form action="{{ route('peserta.join', $sesi->id) }}" method="POST">
                            @csrf
                            <button type="submit">Join Live Session</button>
                        </form>
                    @endif

                    {{-- VIDEO REKAMAN --}}
                    @if (
                        $event->status === 'finished'
                        && $order->paket->akses_rekaman
                        && $sesi->video->count() > 0
                    )
                        <p><strong>Rekaman:</strong></p>
                        <ul>
                            @foreach ($sesi->video as $video)
                                <li>
                                    <a href="{{ route('peserta.video', $video->id) }}">
                                        {{ $video->judul_video }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endforeach
        </div>

    @endforeach

@endif

</body>
</html>
