@extends('peserta.partials.app')

@section('content')
    @php
        $pesanan = $pesanan ?? null;
        $metodeOptions = $metodeOptions ?? [];
        $deadlineAt = $deadlineAt ?? null;
        $midtransClientKey = (string) ($midtransClientKey ?? '');
        $midtransSnapJsUrl = (string) ($midtransSnapJsUrl ?? 'https://app.sandbox.midtrans.com/snap/snap.js');

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
        $fiturRows = [
            ['label' => 'Akses Live', 'value' => (bool) ($paket?->akses_live ?? false)],
            ['label' => 'Akses Rekaman', 'value' => (bool) ($paket?->akses_rekaman ?? false)],
        ];
    @endphp

    <section class="sub-banner-main-section event-banner-section w-100 float-left">
        <div class="container">
            <div class="sub-banner-inner-con">
                <h1>PEMBAYARAN</h1>
                <p>Selesaikan pembayaran untuk mengaktifkan akses event Anda.</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb d-inline-block mb-0">
                        <li class="breadcrumb-item d-inline-block"><a href="{{ route('peserta.index') }}">HOME</a></li>
                        <li class="breadcrumb-item d-inline-block"><a href="{{ route('peserta.cart') }}">KERANJANG</a></li>
                        <li class="breadcrumb-item active d-inline-block" aria-current="page">PEMBAYARAN</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>

    <section class="shop_section w-100 float-left padding-top padding-bottom" id="payment_section">
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
                .pay-card {
                    border-radius: 18px;
                    overflow: hidden;
                    border: 1px solid rgba(0, 0, 0, 0.08);
                    background: #fff;
                }
                .pay-card__header {
                    padding: 16px 16px;
                    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
                }
            </style>

            <div class="mb-4">
                <div class="checkout-steps" aria-label="Status checkout">
                    <div class="checkout-step">
                        <span class="checkout-step__dot">1</span>
                        <span class="checkout-step__label">Pilih Paket</span>
                    </div>
                    <div class="checkout-step is-active">
                        <span class="checkout-step__dot">2</span>
                        <span class="checkout-step__label">Pembayaran</span>
                    </div>
                    <div class="checkout-step">
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
                    <div class="pay-card">
                        <div class="pay-card__header">
                            <div class="d-flex align-items-start justify-content-between" style="gap: 12px;">
                                <div>
                                    <div class="fw-semibold text-black">Pembayaran via Midtrans</div>
                                    <div class="text-muted small">Klik bayar untuk membuka halaman pembayaran Midtrans.</div>
                                </div>
                                <span class="badge bg-{{ $uiStatusCls }}">{{ $uiStatusText }}</span>
                            </div>
                        </div>
                        <div class="p-3 p-md-4">
                            @if (! $pesanan)
                                <div class="border rounded p-3 bg-light">
                                    <div class="fw-semibold text-black">Belum ada pesanan</div>
                                    <div class="text-muted mt-1">Kembali ke keranjang untuk memilih paket.</div>
                                    <div class="mt-3">
                                        <a href="{{ route('peserta.cart') }}" class="btn btn-primary" style="border-radius: 12px; font-weight: 900;">
                                            Kembali ke Keranjang
                                        </a>
                                    </div>
                                </div>
                            @elseif ($isPaid)
                                <div class="border rounded p-3 bg-light">
                                    <div class="fw-semibold text-black">Pembayaran sudah lunas</div>
                                    <div class="text-muted mt-1">Akses paket sudah aktif. Kamu bisa kembali ke dashboard.</div>
                                    <div class="mt-3 d-flex flex-wrap" style="gap: 10px;">
                                        <a href="{{ route('peserta.dashboard') }}#dashboardTop" class="btn btn-primary" style="border-radius: 12px; font-weight: 900;">
                                            Kembali ke Dashboard
                                        </a>
                                        <a href="{{ route('peserta.shop') }}" class="btn btn-outline-secondary" style="border-radius: 12px; font-weight: 900;">
                                            Pilih Paket Lain
                                        </a>
                                    </div>
                                </div>
                            @elseif ($isExpired || $isFailed)
                                <div class="border rounded p-3 bg-light">
                                    <div class="fw-semibold text-black">Pesanan tidak dapat diproses</div>
                                    <div class="text-muted mt-1">Silakan buat pesanan baru dari keranjang.</div>
                                    <div class="mt-3 d-flex flex-wrap" style="gap: 10px;">
                                        <a href="{{ route('peserta.cart') }}" class="btn btn-primary" style="border-radius: 12px; font-weight: 900;">
                                            Kembali ke Keranjang
                                        </a>
                                        <a href="{{ route('peserta.shop') }}" class="btn btn-outline-secondary" style="border-radius: 12px; font-weight: 900;">
                                            Ke Shop
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="border rounded p-3 bg-light">
                                    <div class="fw-semibold text-black">Kode Pesanan: {{ $pesanan->kode_pesanan }}</div>
                                    <div class="text-muted small mt-1">Total: {{ $fmtRp($pesanan->total_bayar ?? 0) }}</div>
                                </div>

                                @if ($midtransClientKey === '')
                                    <div class="alert alert-warning mt-3 mb-0" role="alert">
                                        Konfigurasi Midtrans belum tersedia. Hubungi admin untuk bantuan.
                                    </div>
                                @else
                                    <div class="mt-3 d-flex flex-wrap" style="gap: 10px;">
                                        <button type="button" id="btnPayMidtrans" class="btn btn-primary" style="border-radius: 12px; font-weight: 900;">
                                            Bayar Sekarang
                                        </button>
                                        <a href="{{ route('peserta.checkout.confirm', ['pesanan' => $pesanan->id]) }}" class="btn btn-outline-secondary" style="border-radius: 12px; font-weight: 900;">
                                            Lihat Status
                                        </a>
                                    </div>

                                    <div class="text-muted small mt-3 mb-0">
                                        Setelah pembayaran, status akan diperbarui otomatis dari Midtrans.
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-5">
                    <div class="pay-card">
                        <div class="pay-card__header">
                            <div class="fw-semibold text-black">Ringkasan</div>
                            <div class="text-muted small">Ringkasan paket yang dipilih.</div>
                        </div>
                        <div class="p-3 p-md-4">
                            @if ($pesanan)
                                <div class="border rounded p-3 bg-light">
                                    <div class="fw-semibold text-black">{{ $event?->judul ?? 'Event' }}</div>
                                    <div class="text-muted small mt-1">Paket: {{ $paket?->nama_paket ?? '-' }}</div>
                                    <div class="text-muted small mt-1">Kode Pesanan: {{ $pesanan->kode_pesanan }}</div>
                                </div>

                                <div class="mt-3">
                                    <div class="fw-semibold text-black">Fitur paket</div>
                                    <ul class="text-muted small mb-0 mt-2" style="padding-left: 18px;">
                                        @foreach ($fiturRows as $f)
                                            <li>{{ $f['label'] }}: {{ $f['value'] ? 'Ya' : 'Tidak' }}</li>
                                        @endforeach
                                        <li>Jumlah sesi: {{ $jumlahSesi }}</li>
                                        @if (! is_null($paket?->kuota))
                                            <li>Kuota: {{ (int) $paket->kuota }}</li>
                                        @endif
                                    </ul>
                                </div>

                                <div class="mt-3">
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
                            @else
                                <div class="border rounded p-3 bg-light">
                                    <div class="fw-semibold text-black">Belum ada ringkasan</div>
                                    <div class="text-muted mt-1">Silakan buat pesanan dari keranjang.</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        @if ($midtransClientKey !== '')
            <script src="{{ $midtransSnapJsUrl }}" data-client-key="{{ $midtransClientKey }}"></script>
        @endif
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

        @if ($pesanan && $isPending && ! $isExpired && ! $isFailed && $midtransClientKey !== '')
            <script>
                (function () {
                    const btn = document.getElementById('btnPayMidtrans');
                    if (!btn) return;

                    const csrf = @json(csrf_token());
                    const tokenUrl = @json(route('peserta.checkout.midtrans.token', ['pesanan' => $pesanan->id]));
                    const confirmUrl = @json(route('peserta.checkout.confirm', ['pesanan' => $pesanan->id]));

                    function setLoading(isLoading) {
                        btn.disabled = !!isLoading;
                        btn.setAttribute('aria-busy', isLoading ? 'true' : 'false');
                    }

                    async function createToken() {
                        const resp = await fetch(tokenUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                            },
                            body: JSON.stringify({}),
                        });

                        const data = await resp.json().catch(() => ({}));
                        if (!resp.ok) {
                            const msg = (data && data.message) ? String(data.message) : 'Gagal membuat transaksi Midtrans.';
                            throw new Error(msg);
                        }

                        if (!data || !data.token) {
                            throw new Error('Token Midtrans tidak tersedia.');
                        }

                        return String(data.token);
                    }

                    async function pay() {
                        if (!window.snap || typeof window.snap.pay !== 'function') {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Midtrans belum siap',
                                    text: 'Coba refresh halaman atau hubungi admin.',
                                });
                            }
                            return;
                        }

                        setLoading(true);
                        try {
                            const token = await createToken();
                            window.snap.pay(token, {
                                onSuccess: function () {
                                    window.location.href = confirmUrl + '?midtrans=success';
                                },
                                onPending: function () {
                                    window.location.href = confirmUrl + '?midtrans=pending';
                                },
                                onError: function () {
                                    window.location.href = confirmUrl + '?midtrans=error';
                                },
                                onClose: function () {
                                    setLoading(false);
                                }
                            });
                        } catch (e) {
                            setLoading(false);
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal memulai pembayaran',
                                    text: String(e && e.message ? e.message : e),
                                });
                            }
                        }
                    }

                    btn.addEventListener('click', function () {
                        pay();
                    });
                })();
            </script>
        @endif
    @endpush
@endsection
