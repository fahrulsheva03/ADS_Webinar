@extends('peserta.partials.app')

@section('content')
    @php
        $pesanan = $pesanan ?? null;
        $metodeOptions = $metodeOptions ?? [];
        $deadlineAt = $deadlineAt ?? null;

        $fmtRp = function ($n) {
            return 'Rp '.number_format((float) ($n ?? 0), 0, ',', '.');
        };

        $statusVal = strtolower((string) ($pesanan?->status_pembayaran ?? ''));
        $isPending = $statusVal === 'pending';
        $isPaid = $statusVal === 'paid';
        $isExpired = $statusVal === 'expired';
        $isFailed = $statusVal === 'failed';
        $hasMetode = (string) ($pesanan?->metode_pembayaran ?? '') !== '';

        $uiStatusText = $isPaid ? 'Lunas' : ($isExpired ? 'Expired' : ($isFailed ? 'Gagal' : ($hasMetode ? 'Sedang Diproses' : 'Pending')));
        $uiStatusCls = $isPaid ? 'success' : ($isExpired || $isFailed ? 'secondary' : ($hasMetode ? 'info' : 'warning'));

        $event = $pesanan?->paket?->event;
        $paket = $pesanan?->paket;
        $jumlahSesi = is_object($paket?->sesi) ? $paket->sesi->count() : 0;
        $metodeKey = (string) ($pesanan?->metode_pembayaran ?? '');
        $metodeLabel = $metodeKey !== '' ? (string) ($metodeOptions[$metodeKey] ?? $metodeKey) : '';
    @endphp

    <section class="sub-banner-main-section event-banner-section w-100 float-left">
        <div class="container">
            <div class="sub-banner-inner-con">
                <h1>KONFIRMASI</h1>
                <p>Konfirmasi pesanan Anda sebelum diproses.</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb d-inline-block mb-0">
                        <li class="breadcrumb-item d-inline-block"><a href="{{ route('peserta.index') }}">HOME</a></li>
                        <li class="breadcrumb-item d-inline-block"><a href="{{ route('peserta.cart') }}">KERANJANG</a></li>
                        <li class="breadcrumb-item d-inline-block"><a href="{{ route('peserta.checkout.payment') }}">PEMBAYARAN</a></li>
                        <li class="breadcrumb-item active d-inline-block" aria-current="page">KONFIRMASI</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>

    <section class="shop_section w-100 float-left padding-top padding-bottom" id="confirm_section">
        <div class="container">
            <style>
                .checkout-steps {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 10px;
                    padding: 14px 14px;
                    border: 1px solid rgba(0, 0, 0, 0.08);
                    background: #fff;
                    border-radius: 16px;
                }
                .checkout-step {
                    flex: 1;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    padding: 10px 12px;
                    border-radius: 14px;
                    background: rgba(0, 0, 0, 0.03);
                    min-width: 0;
                }
                .checkout-step__dot {
                    width: 34px;
                    height: 34px;
                    border-radius: 50%;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: 800;
                    background: rgba(0, 0, 0, 0.08);
                    color: #000;
                    flex: 0 0 auto;
                }
                .checkout-step__label {
                    font-weight: 800;
                    color: #000;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }
                .checkout-step.is-active {
                    background: rgba(248, 0, 0, 0.08);
                    border: 1px solid rgba(248, 0, 0, 0.18);
                }
                .checkout-step.is-active .checkout-step__dot {
                    background: rgba(248, 0, 0, 0.16);
                    color: rgb(248, 0, 0);
                }
                .confirm-card {
                    border-radius: 18px;
                    overflow: hidden;
                    border: 1px solid rgba(0, 0, 0, 0.08);
                    background: #fff;
                }
                .confirm-card__header {
                    padding: 16px 16px;
                    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
                }
                .confirm-item {
                    padding: 12px 16px;
                    border-top: 1px solid rgba(0, 0, 0, 0.06);
                }
            </style>

            <div class="mb-4">
                <div class="checkout-steps" aria-label="Status checkout">
                    <div class="checkout-step">
                        <span class="checkout-step__dot">1</span>
                        <span class="checkout-step__label">Pilih Paket</span>
                    </div>
                    <div class="checkout-step">
                        <span class="checkout-step__dot">2</span>
                        <span class="checkout-step__label">Pembayaran</span>
                    </div>
                    <div class="checkout-step is-active">
                        <span class="checkout-step__dot">3</span>
                        <span class="checkout-step__label">Konfirmasi</span>
                    </div>
                    <div class="checkout-step">
                        <span class="checkout-step__dot">4</span>
                        <span class="checkout-step__label">Selesai</span>
                    </div>
                </div>
            </div>

            <div class="row" style="row-gap: 18px;">
                <div class="col-12 col-lg-7">
                    <div class="confirm-card">
                        <div class="confirm-card__header">
                            <div class="d-flex align-items-start justify-content-between" style="gap: 12px;">
                                <div>
                                    <div class="fw-semibold text-black">Detail pesanan</div>
                                    <div class="text-muted small">Periksa ringkasan dan status pemrosesan.</div>
                                </div>
                                <span class="badge bg-{{ $uiStatusCls }}">{{ $uiStatusText }}</span>
                            </div>
                        </div>
                        <div class="p-3 p-md-4">
                            @if (! $pesanan)
                                <div class="border rounded p-4 bg-light">
                                    <div class="fw-semibold text-black">Belum ada pesanan</div>
                                    <div class="text-muted mt-1">Kembali ke keranjang untuk memilih paket.</div>
                                    <div class="mt-3">
                                        <a href="{{ route('peserta.cart') }}" class="btn btn-primary" style="border-radius: 12px; font-weight: 900;">
                                            Kembali ke Keranjang
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="border rounded p-3 bg-light">
                                    <div class="fw-semibold text-black">{{ $event?->judul ?? 'Event' }}</div>
                                    <div class="text-muted small mt-1">Paket: {{ $paket?->nama_paket ?? '-' }}</div>
                                    <div class="text-muted small mt-1">Kode Pesanan: {{ $pesanan->kode_pesanan }}</div>
                                </div>

                                <div class="mt-3">
                                    <div class="fw-semibold text-black">Ringkasan paket</div>
                                    <div class="text-muted small mt-1">Jumlah sesi: {{ $jumlahSesi }}</div>
                                    <div class="text-muted small mt-1">Akses Live: {{ (bool) ($paket?->akses_live ?? false) ? 'Ya' : 'Tidak' }}</div>
                                    <div class="text-muted small mt-1">Akses Rekaman: {{ (bool) ($paket?->akses_rekaman ?? false) ? 'Ya' : 'Tidak' }}</div>
                                    @if (! is_null($paket?->kuota))
                                        <div class="text-muted small mt-1">Kuota: {{ (int) $paket->kuota }}</div>
                                    @endif
                                </div>

                                <div class="mt-3">
                                    <div class="fw-semibold text-black">Metode pembayaran</div>
                                    @if ($metodeLabel !== '')
                                        <div class="text-muted mt-1">{{ $metodeLabel }}</div>
                                    @else
                                        <div class="text-muted mt-1">Belum dipilih. Silakan pilih metode di halaman pembayaran.</div>
                                    @endif
                                </div>

                                @if ($isPending && $deadlineAt)
                                    <div class="mt-3">
                                        <div class="text-muted small">Batas waktu pembayaran</div>
                                        <div class="fw-semibold text-black" id="deadlineText" data-deadline="{{ optional($deadlineAt)->toIso8601String() }}">
                                            {{ optional($deadlineAt)->format('d-m-Y H:i') }}
                                        </div>
                                        <div class="text-muted small mt-1" id="deadlineCountdown"></div>
                                    </div>
                                @endif

                                @if ($isPending && ! $hasMetode && $deadlineAt)
                                    <div class="alert alert-warning mt-3 mb-0" role="alert">
                                        Silakan selesaikan pembayaran untuk mengaktifkan paket Anda.
                                    </div>
                                @endif

                                @if ($isPending && $hasMetode)
                                    <div class="alert alert-info mt-3 mb-0" role="alert">
                                        Pembayaran sedang diproses. Kami akan memperbarui status setelah verifikasi.
                                    </div>
                                @endif

                                @if ($isPaid)
                                    <div class="alert alert-success mt-3 mb-0" role="alert">
                                        Pembayaran berhasil. Paket Anda sudah aktif.
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-5">
                    <div class="confirm-card">
                        <div class="confirm-card__header">
                            <div class="fw-semibold text-black">Total</div>
                            <div class="text-muted small">Ringkasan pembayaran.</div>
                        </div>
                        <div class="p-3 p-md-4">
                            @if ($pesanan)
                                <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                    <div class="text-muted fw-semibold">Harga</div>
                                    <div class="text-black fw-bold">{{ $fmtRp($paket?->harga ?? 0) }}</div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                    <div class="text-muted fw-semibold">Diskon</div>
                                    <div class="text-black fw-bold">{{ $fmtRp(0) }}</div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between py-2">
                                    <div class="text-muted fw-semibold">Total</div>
                                    <div class="text-black fw-bold">{{ $fmtRp($pesanan->total_bayar ?? 0) }}</div>
                                </div>
                            @else
                                <div class="border rounded p-3 bg-light">
                                    <div class="fw-semibold text-black">Belum ada ringkasan</div>
                                    <div class="text-muted mt-1">Buat pesanan dari keranjang dulu.</div>
                                </div>
                            @endif

                            <div class="mt-3 d-grid gap-2">
                                @if ($pesanan && $isPending && ! $hasMetode && ! $isExpired && ! $isFailed)
                                    <a href="{{ route('peserta.checkout.payment', ['pesanan' => $pesanan->id]) }}" class="btn btn-primary" style="border-radius: 12px; font-weight: 900;">
                                        Pilih Metode Pembayaran
                                    </a>
                                @endif

                                <a href="{{ route('peserta.dashboard') }}#dashboardTop" class="btn btn-outline-secondary" style="border-radius: 12px; font-weight: 900;">
                                    Kembali ke Dashboard
                                </a>

                                <a href="{{ route('peserta.contact') }}" class="btn btn-outline-secondary" style="border-radius: 12px; font-weight: 900;">
                                    Hubungi Admin
                                </a>
                            </div>

                            <div class="text-muted small mt-3">
                                Untuk status pending, verifikasi dilakukan oleh admin. Status akan otomatis terlihat di dashboard setelah diperbarui.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            (function () {
                const countdownEl = document.getElementById('deadlineCountdown');
                const deadlineTextEl = document.getElementById('deadlineText');
                if (!countdownEl || !deadlineTextEl) return;

                const raw = deadlineTextEl.getAttribute('data-deadline') || '';
                const deadlineMs = Date.parse(raw);
                if (!isFinite(deadlineMs)) return;

                function pad(n) {
                    return String(n).padStart(2, '0');
                }

                function tick() {
                    const diff = deadlineMs - Date.now();
                    if (!isFinite(diff)) return;
                    if (diff <= 0) {
                        countdownEl.textContent = 'Batas waktu pembayaran sudah lewat.';
                        return;
                    }

                    const totalSeconds = Math.floor(diff / 1000);
                    const hours = Math.floor(totalSeconds / 3600);
                    const minutes = Math.floor((totalSeconds % 3600) / 60);
                    const seconds = totalSeconds % 60;
                    countdownEl.textContent = 'Sisa waktu: ' + pad(hours) + ':' + pad(minutes) + ':' + pad(seconds);
                }

                tick();
                setInterval(tick, 1000);
            })();
        </script>
    @endpush
@endsection
