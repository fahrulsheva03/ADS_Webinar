@extends('peserta.partials.app')

@section('content')
    <!-- BANNER SECTION START -->
    <section class="sub-banner-main-section event-banner-section w-100 float-left">
        <div class="container">
            <div class="sub-banner-inner-con">
                <h1>SHOP</h1>
                <p>Inspiring Talks, Meet the Best Product People Around the World, <br> and Party Together After the Event!
                </p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb d-inline-block mb-0">
                        <li class="breadcrumb-item d-inline-block"><a href="{{ route('peserta.index') }}">HOME</a></li>
                        <li class="breadcrumb-item active d-inline-block" aria-current="page">SHOP</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>
    <!-- BANNER SECTION END -->
    <!-- SHOP SECTION -->
    <section class="shop_section w-100 float-left padding-top padding-bottom" id="shop_section">
        <div class="container">
            @php
                use App\Models\Paket;
                use Illuminate\Support\Facades\Schema;

                $paketRows = collect();
                if (Schema::hasTable('paket')) {
                    $paketQuery = Paket::query()
                        ->with(['event', 'sesi'])
                        ->orderByDesc('id');

                    if (Schema::hasColumn('paket', 'status')) {
                        $paketQuery->where(function ($q) {
                            $q->whereNull('status')
                                ->orWhere('status', '!=', 'nonaktif');
                        });
                    }

                    $paketRows = $paketQuery->get();
                }

                $events = $paketRows
                    ->map(fn ($p) => $p->event)
                    ->filter()
                    ->unique('id')
                    ->sortByDesc(fn ($e) => $e->tanggal_mulai ?? $e->created_at)
                    ->values();

                $selectedPaketId = (int) request()->query('paket', 0);
                $selectedPaket = $selectedPaketId > 0 ? $paketRows->firstWhere('id', $selectedPaketId) : null;
            @endphp

            @if ($selectedPaket)
                @php
                    $selectedEvent = $selectedPaket->event;
                    $selectedHarga = (float) ($selectedPaket->harga ?? 0);
                @endphp

                <div class="mb-4">
                    <div class="border rounded p-3 p-md-4 bg-light">
                        <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap: 12px;">
                            <div>
                                <div class="fw-semibold text-black">Paket dipilih</div>
                                <div class="text-muted">
                                    {{ $selectedEvent?->judul ?? 'Event' }} — {{ $selectedPaket->nama_paket }}
                                    <span class="mx-2">•</span>
                                    Rp {{ number_format($selectedHarga, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="d-flex flex-wrap" style="gap: 10px;">
                                @guest
                                    <a href="{{ route('login') }}" class="btn btn-primary" style="border-radius: 12px; font-weight: 800;">
                                        Login untuk lanjut
                                    </a>
                                @else
                                    <a href="{{ route('peserta.contact') }}" class="btn btn-primary" style="border-radius: 12px; font-weight: 800;">
                                        Lanjutkan
                                    </a>
                                @endguest
                                <a href="{{ route('peserta.shop') }}#shop_section" class="btn btn-outline-secondary" style="border-radius: 12px; font-weight: 800;">
                                    Ganti paket
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="tabs-box tabs-options">
                <ul class="nav nav-tabs flex-wrap">
                    <li><a class="active" data-toggle="tab" href="#all">Semua</a></li>
                    @foreach ($events as $event)
                        <li><a data-toggle="tab" href="#event-{{ $event->id }}">{{ $event->judul }}</a></li>
                    @endforeach
                </ul>
                <div class="tab-content">
                    <div id="all" class="tab-pane fade in active show">
                        @if ($paketRows->isEmpty())
                            <div class="border rounded p-4 bg-light">
                                <div class="fw-semibold text-black">Belum ada paket tersedia</div>
                                <div class="text-muted">Admin belum menambahkan paket event yang aktif.</div>
                            </div>
                        @else
                            <div class="row" data-aos="fade-up">
                                @foreach ($paketRows as $p)
                                    @php
                                        $event = $p->event;
                                        $harga = (float) ($p->harga ?? 0);
                                        $img = $event?->gambar_utama ? asset('storage/'.$event->gambar_utama) : asset('assets/images/business-conference.jpg');
                                        $tanggalMulai = $event?->tanggal_mulai ? optional($event->tanggal_mulai)->format('d M Y') : null;
                                        $tanggalSelesai = $event?->tanggal_selesai ? optional($event->tanggal_selesai)->format('d M Y') : null;
                                        $rentangTanggal = $tanggalMulai && $tanggalSelesai ? "{$tanggalMulai} - {$tanggalSelesai}" : ($tanggalMulai ?? $tanggalSelesai ?? null);
                                        $isSelected = $selectedPaketId > 0 && (string) $selectedPaketId === (string) $p->id;
                                        $fiturLive = (bool) ($p->akses_live ?? false);
                                        $fiturRekaman = (bool) ($p->akses_rekaman ?? false);
                                        $kuota = $p->kuota;
                                        $jumlahSesi = is_object($p->sesi) ? $p->sesi->count() : 0;
                                    @endphp

                                    <div class="col-lg-4 col-md-4 col-sm-6 col-12">
                                        <div class="shop_box">
                                            <div class="shop_image_box">
                                                <figure class="mb-0">
                                                    <img src="{{ $img }}" alt="{{ $event?->judul ?? $p->nama_paket }}" class="img-fluid hover-effect">
                                                </figure>
                                            </div>
                                            <div class="shop_box_content">
                                                <ul class="list-unstyled">
                                                    <li class="text-size-16">
                                                        <i class="fas fa-tag"></i>
                                                        {{ $p->nama_paket }}
                                                    </li>
                                                    @if ($event)
                                                        <li class="text-size-16">
                                                            <i class="fas fa-calendar-alt"></i>
                                                            {{ $rentangTanggal ?? '-' }}
                                                        </li>
                                                        @if (! empty($event->lokasi))
                                                            <li class="text-size-16">
                                                                <i class="fas fa-map-marker-alt"></i>
                                                                {{ $event->lokasi }}
                                                            </li>
                                                        @endif
                                                    @endif
                                                    <li class="text-size-16">
                                                        <i class="fas fa-video"></i>
                                                        Live: {{ $fiturLive ? 'Ya' : 'Tidak' }}
                                                        <span class="mx-2">•</span>
                                                        Rekaman: {{ $fiturRekaman ? 'Ya' : 'Tidak' }}
                                                    </li>
                                                    <li class="text-size-16">
                                                        <i class="fas fa-list"></i>
                                                        Sesi: {{ $jumlahSesi }}
                                                        @if (! is_null($kuota))
                                                            <span class="mx-2">•</span>
                                                            Kuota: {{ (int) $kuota }}
                                                        @endif
                                                    </li>
                                                </ul>
                                                <h5>{{ $event?->judul ?? 'Event' }}</h5>
                                                <p class="text-size-16">{{ $p->deskripsi ?: ($event?->deskripsi ?? '') }}</p>
                                                <div class="price_wrapper">
                                                    <span>Rp</span>
                                                    <span>{{ number_format($harga, 0, ',', '.') }}</span>
                                                </div>
                                                <div class="btn_wrapper">
                                                    @if ($isSelected)
                                                        <a
                                                            class="text-decoration-none js-add-to-cart"
                                                            href="{{ route('peserta.cart') }}?paket={{ $p->id }}"
                                                            data-paket-id="{{ $p->id }}"
                                                            data-paket-nama="{{ $p->nama_paket }}"
                                                            data-paket-harga="{{ $harga }}"
                                                            data-event-id="{{ $event?->id }}"
                                                            data-event-judul="{{ $event?->judul }}"
                                                            data-akses-live="{{ $fiturLive ? 1 : 0 }}"
                                                            data-akses-rekaman="{{ $fiturRekaman ? 1 : 0 }}"
                                                            data-jumlah-sesi="{{ $jumlahSesi }}"
                                                            data-kuota="{{ is_null($kuota) ? '' : (int) $kuota }}"
                                                        >
                                                            Dipilih <i class="fas fa-check"></i>
                                                        </a>
                                                    @else
                                                        <a
                                                            class="text-decoration-none js-add-to-cart"
                                                            href="{{ route('peserta.cart') }}?paket={{ $p->id }}"
                                                            data-paket-id="{{ $p->id }}"
                                                            data-paket-nama="{{ $p->nama_paket }}"
                                                            data-paket-harga="{{ $harga }}"
                                                            data-event-id="{{ $event?->id }}"
                                                            data-event-judul="{{ $event?->judul }}"
                                                            data-akses-live="{{ $fiturLive ? 1 : 0 }}"
                                                            data-akses-rekaman="{{ $fiturRekaman ? 1 : 0 }}"
                                                            data-jumlah-sesi="{{ $jumlahSesi }}"
                                                            data-kuota="{{ is_null($kuota) ? '' : (int) $kuota }}"
                                                        >
                                                            Pilih paket <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    @foreach ($events as $event)
                        @php
                            $eventPaket = $paketRows->filter(fn ($p) => (string) ($p->event_id ?? '') === (string) $event->id)->values();
                        @endphp
                        <div id="event-{{ $event->id }}" class="tab-pane fade">
                            @if ($eventPaket->isEmpty())
                                <div class="border rounded p-4 bg-light">
                                    <div class="fw-semibold text-black">Belum ada paket aktif</div>
                                    <div class="text-muted">Event ini belum memiliki paket yang aktif.</div>
                                </div>
                            @else
                                <div class="row" data-aos="fade-up">
                                    @foreach ($eventPaket as $p)
                                        @php
                                            $harga = (float) ($p->harga ?? 0);
                                            $img = $event->gambar_utama ? asset('storage/'.$event->gambar_utama) : asset('assets/images/business-conference.jpg');
                                            $tanggalMulai = $event->tanggal_mulai ? optional($event->tanggal_mulai)->format('d M Y') : null;
                                            $tanggalSelesai = $event->tanggal_selesai ? optional($event->tanggal_selesai)->format('d M Y') : null;
                                            $rentangTanggal = $tanggalMulai && $tanggalSelesai ? "{$tanggalMulai} - {$tanggalSelesai}" : ($tanggalMulai ?? $tanggalSelesai ?? null);
                                            $isSelected = $selectedPaketId > 0 && (string) $selectedPaketId === (string) $p->id;
                                            $fiturLive = (bool) ($p->akses_live ?? false);
                                            $fiturRekaman = (bool) ($p->akses_rekaman ?? false);
                                            $kuota = $p->kuota;
                                            $jumlahSesi = is_object($p->sesi) ? $p->sesi->count() : 0;
                                        @endphp

                                        <div class="col-lg-4 col-md-4 col-sm-6 col-12">
                                            <div class="shop_box">
                                                <div class="shop_image_box">
                                                    <figure class="mb-0">
                                                        <img src="{{ $img }}" alt="{{ $event->judul }}" class="img-fluid hover-effect">
                                                    </figure>
                                                </div>
                                                <div class="shop_box_content">
                                                    <ul class="list-unstyled">
                                                        <li class="text-size-16">
                                                            <i class="fas fa-tag"></i>
                                                            {{ $p->nama_paket }}
                                                        </li>
                                                        <li class="text-size-16">
                                                            <i class="fas fa-calendar-alt"></i>
                                                            {{ $rentangTanggal ?? '-' }}
                                                        </li>
                                                        @if (! empty($event->lokasi))
                                                            <li class="text-size-16">
                                                                <i class="fas fa-map-marker-alt"></i>
                                                                {{ $event->lokasi }}
                                                            </li>
                                                        @endif
                                                        <li class="text-size-16">
                                                            <i class="fas fa-video"></i>
                                                            Live: {{ $fiturLive ? 'Ya' : 'Tidak' }}
                                                            <span class="mx-2">•</span>
                                                            Rekaman: {{ $fiturRekaman ? 'Ya' : 'Tidak' }}
                                                        </li>
                                                        <li class="text-size-16">
                                                            <i class="fas fa-list"></i>
                                                            Sesi: {{ $jumlahSesi }}
                                                            @if (! is_null($kuota))
                                                                <span class="mx-2">•</span>
                                                                Kuota: {{ (int) $kuota }}
                                                            @endif
                                                        </li>
                                                    </ul>
                                                    <h5>{{ $event->judul }}</h5>
                                                    <p class="text-size-16">{{ $p->deskripsi ?: ($event->deskripsi ?? '') }}</p>
                                                    <div class="price_wrapper">
                                                        <span>Rp</span>
                                                        <span>{{ number_format($harga, 0, ',', '.') }}</span>
                                                    </div>
                                                    <div class="btn_wrapper">
                                                        @if ($isSelected)
                                                            <a
                                                                class="text-decoration-none js-add-to-cart"
                                                                href="{{ route('peserta.cart') }}?paket={{ $p->id }}"
                                                                data-paket-id="{{ $p->id }}"
                                                                data-paket-nama="{{ $p->nama_paket }}"
                                                                data-paket-harga="{{ $harga }}"
                                                                data-event-id="{{ $event?->id }}"
                                                                data-event-judul="{{ $event?->judul }}"
                                                                data-akses-live="{{ $fiturLive ? 1 : 0 }}"
                                                                data-akses-rekaman="{{ $fiturRekaman ? 1 : 0 }}"
                                                                data-jumlah-sesi="{{ $jumlahSesi }}"
                                                                data-kuota="{{ is_null($kuota) ? '' : (int) $kuota }}"
                                                            >
                                                                Dipilih <i class="fas fa-check"></i>
                                                            </a>
                                                        @else
                                                            <a
                                                                class="text-decoration-none js-add-to-cart"
                                                                href="{{ route('peserta.cart') }}?paket={{ $p->id }}"
                                                                data-paket-id="{{ $p->id }}"
                                                                data-paket-nama="{{ $p->nama_paket }}"
                                                                data-paket-harga="{{ $harga }}"
                                                                data-event-id="{{ $event?->id }}"
                                                                data-event-judul="{{ $event?->judul }}"
                                                                data-akses-live="{{ $fiturLive ? 1 : 0 }}"
                                                                data-akses-rekaman="{{ $fiturRekaman ? 1 : 0 }}"
                                                                data-jumlah-sesi="{{ $jumlahSesi }}"
                                                                data-kuota="{{ is_null($kuota) ? '' : (int) $kuota }}"
                                                            >
                                                                Pilih paket <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            (function () {
                const storageKey = 'peserta_cart_v1';
                const isGuest = @json(! auth()->check());
                const loginUrl = @json(route('login', ['redirect' => '/cart']));

                function safeParse(raw) {
                    try {
                        return JSON.parse(raw);
                    } catch (e) {
                        return null;
                    }
                }

                function readCart() {
                    const data = safeParse(localStorage.getItem(storageKey));
                    if (!data || typeof data !== 'object') return { items: [], updated_at: null };
                    if (!Array.isArray(data.items)) return { items: [], updated_at: null };
                    return data;
                }

                function writeCart(cart) {
                    cart.updated_at = new Date().toISOString();
                    localStorage.setItem(storageKey, JSON.stringify(cart));
                }

                function addItemFromEl(el) {
                    const dataset = el.dataset || {};
                    const paketId = parseInt(dataset.paketId || '0', 10);
                    if (!paketId) return;

                    const harga = Number(dataset.paketHarga || 0);
                    const cart = readCart();

                    const existing = cart.items.find((i) => String(i.paket_id) === String(paketId));
                    if (existing) {
                        existing.qty = Math.max(1, (parseInt(existing.qty || 1, 10)));
                    } else {
                        cart.items.unshift({
                            paket_id: paketId,
                            qty: 1,
                            nama_paket: dataset.paketNama || '',
                            harga: isFinite(harga) ? harga : 0,
                            event: {
                                id: dataset.eventId ? parseInt(dataset.eventId, 10) : null,
                                judul: dataset.eventJudul || '',
                            },
                            fitur: {
                                akses_live: String(dataset.aksesLive || '0') === '1',
                                akses_rekaman: String(dataset.aksesRekaman || '0') === '1',
                                jumlah_sesi: dataset.jumlahSesi ? parseInt(dataset.jumlahSesi, 10) : 0,
                                kuota: dataset.kuota === '' ? null : parseInt(dataset.kuota || '0', 10),
                            },
                        });
                    }

                    writeCart(cart);
                }

                document.addEventListener('click', function (e) {
                    const target = e.target.closest && e.target.closest('a.js-add-to-cart');
                    if (!target) return;

                    e.preventDefault();
                    addItemFromEl(target);

                    if (isGuest) {
                        if (typeof Swal === 'undefined') {
                            window.location.href = loginUrl;
                            return;
                        }

                        Swal.fire({
                            icon: 'warning',
                            title: 'Silahkan login terlebih dahulu',
                            confirmButtonText: 'OK',
                            allowOutsideClick: false,
                            allowEscapeKey: true,
                        }).then((res) => {
                            if (res.isConfirmed) {
                                window.location.href = loginUrl;
                            }
                        });

                        return;
                    }

                    window.location.href = target.href;
                });
            })();
        </script>
    @endpush
@endsection
