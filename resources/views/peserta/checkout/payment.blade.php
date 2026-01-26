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
                                    <div class="fw-semibold text-black">Metode pembayaran</div>
                                    <div class="text-muted small">Pilih metode, ikuti instruksi, lalu lanjut konfirmasi.</div>
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
                                <div class="text-muted small mb-3">
                                    Silakan selesaikan pembayaran untuk mengaktifkan paket Anda.
                                </div>

                                <form action="{{ route('peserta.checkout.payment.method', ['pesanan' => $pesanan->id]) }}" method="post">
                                    @csrf

                                    <div class="d-flex flex-column" style="gap: 10px;">
                                        @foreach ($metodeOptions as $value => $label)
                                            @php
                                                $checked = (string) ($pesanan->metode_pembayaran ?? '') === (string) $value;
                                            @endphp
                                            <label class="border rounded p-3 d-flex align-items-start justify-content-between" style="cursor: pointer; gap: 12px;">
                                                <span class="d-flex align-items-start" style="gap: 10px;">
                                                    <input type="radio" name="metode_pembayaran" value="{{ $value }}" @checked($checked) style="margin-top: 4px;">
                                                    <span>
                                                        <span class="fw-semibold text-black">{{ $label }}</span>
                                                        <span class="d-block text-muted small mt-1">
                                                            @if (str_starts_with((string) $value, 'bank_'))
                                                                Transfer sesuai nominal total, lalu simpan bukti pembayaran.
                                                            @else
                                                                Bayar lewat aplikasi e-wallet, lalu simpan bukti pembayaran.
                                                            @endif
                                                        </span>
                                                    </span>
                                                </span>
                                                <i class="fas fa-chevron-right text-muted" aria-hidden="true"></i>
                                            </label>
                                        @endforeach
                                    </div>

                                    @error('metode_pembayaran')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror

                                    <div class="mt-3 d-flex flex-wrap" style="gap: 10px;">
                                        <button type="submit" class="btn btn-primary" style="border-radius: 12px; font-weight: 900;">
                                            Lanjut ke Konfirmasi
                                        </button>
                                        <a href="{{ route('peserta.contact') }}" class="btn btn-outline-secondary" style="border-radius: 12px; font-weight: 900;">
                                            Hubungi Admin
                                        </a>
                                    </div>
                                </form>

                                <div class="border rounded p-3 bg-light mt-4">
                                    <div class="fw-semibold text-black">Instruksi pembayaran</div>
                                    <div class="text-muted mt-1">
                                        Setelah memilih metode, lakukan pembayaran sesuai total. Lanjutkan ke halaman konfirmasi untuk melihat ringkasan dan status pemrosesan.
                                    </div>
                                </div>
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
