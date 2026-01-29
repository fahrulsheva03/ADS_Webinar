@extends('peserta.partials.app')

@section('title', 'Dashboard Peserta')

@push('styles')
    <style>
        .peserta-dashboard {
            --dashboard-primary: #F80000;
            --dashboard-secondary: #000000;
            background: radial-gradient(900px circle at 12% 10%, rgba(248, 0, 0, 0.18), transparent 60%),
                radial-gradient(700px circle at 88% 24%, rgba(0, 0, 0, 0.12), transparent 55%),
                linear-gradient(180deg, #f2f2f2 0%, #ffffff 100%);
            color: #111827;
            min-height: 100vh;
        }

        .dashboard-panel {
            border: 1px solid rgba(17, 24, 39, 0.08);
            border-radius: 18px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.92);
            box-shadow: 0 18px 60px rgba(0, 0, 0, 0.08);
        }

        .dashboard-panel__header {
            padding: 16px 18px;
            background: linear-gradient(135deg, rgba(248, 0, 0, 0.16) 0%, rgba(0, 0, 0, 0.10) 100%);
        }

        .dashboard-panel__title {
            font-weight: 950;
            letter-spacing: -0.03em;
            font-size: 18px;
            line-height: 1.25;
            margin-bottom: 2px;
        }

        .dashboard-panel__subtitle {
            color: #4b5563;
            font-weight: 600;
            font-size: 14px;
        }

        .dashboard-kpi {
            border-radius: 16px;
            border: 1px solid rgba(17, 24, 39, 0.08);
            background: #ffffff;
            padding: 12px 12px;
            height: 100%;
        }

        .dashboard-kpi__label {
            color: #4b5563;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .dashboard-kpi__value {
            font-size: 22px;
            font-weight: 950;
            letter-spacing: -0.03em;
            line-height: 1.15;
            margin-top: 6px;
        }

        .dashboard-step {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            border-radius: 16px;
            border: 1px solid rgba(17, 24, 39, 0.08);
            background: #ffffff;
            padding: 12px 12px;
        }

        .dashboard-step__icon {
            width: 40px;
            height: 40px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(248, 0, 0, 0.22);
            background: rgba(248, 0, 0, 0.08);
            color: var(--dashboard-primary);
            flex: 0 0 auto;
        }

        .dashboard-step__title {
            font-weight: 900;
            margin-bottom: 2px;
        }

        .dashboard-step__desc {
            color: #4b5563;
            font-size: 14px;
            margin-bottom: 0;
        }

        .dashboard-history-item {
            border-radius: 16px !important;
            border: 1px solid rgba(17, 24, 39, 0.08);
            background: #ffffff;
            padding: 14px 14px;
        }

        .dashboard-history-item + .dashboard-history-item {
            margin-top: 10px;
        }

        .dashboard-history-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .header-main-con,
        .footer-main-section,
        .copyright,
        #button {
            display: none !important;
        }

        .dashboard-topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            backdrop-filter: blur(10px);
            background: linear-gradient(90deg, rgba(248, 0, 0, 0.08) 0%, rgba(0, 0, 0, 0.05) 100%),
                rgba(255, 255, 255, 0.88);
            border-bottom: 1px solid rgba(17, 24, 39, 0.08);
            padding-top: env(safe-area-inset-top);
        }

        .dashboard-topbar__bar {
            min-height: 64px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
        }

        .dashboard-topbar__logo {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }

        .dashboard-topbar__logo img {
            width: auto;
            max-height: 44px;
            max-width: 130px;
        }

        .dashboard-topbar__right {
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        @media (min-width: 768px) {
            .dashboard-topbar__logo img {
                max-height: 50px;
                max-width: 180px;
            }
        }

        .dashboard-icon-btn {
            width: 44px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            border: 1px solid rgba(17, 24, 39, 0.12);
            background: #ffffff;
            color: #111827;
            transition: transform 150ms ease, box-shadow 150ms ease, border-color 150ms ease;
        }

        .dashboard-icon-btn:hover {
            transform: translateY(-1px);
            border-color: rgba(248, 0, 0, 0.45);
            box-shadow: 0 10px 22px rgba(17, 24, 39, 0.08);
            color: var(--dashboard-primary);
            text-decoration: none;
        }

        .dashboard-icon-btn:focus {
            outline: 0;
            box-shadow: 0 0 0 3px rgba(248, 0, 0, 0.22);
        }

        .dashboard-breadcrumb .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
        }

        .dashboard-breadcrumb {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .dashboard-breadcrumb::-webkit-scrollbar {
            display: none;
        }

        .dashboard-breadcrumb .breadcrumb-item a {
            color: var(--dashboard-primary);
            font-weight: 600;
        }

        .dashboard-subbar {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding-bottom: 14px;
        }

        @media (min-width: 768px) {
            .dashboard-subbar {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                padding-bottom: 16px;
            }
        }

        .dashboard-pagehead__img {
            width: 56px;
            height: 56px;
        }

        @media (max-width: 575.98px) {
            .dashboard-pagehead__img {
                width: 44px;
                height: 44px;
            }
        }

        .event-card {
            border: 1px solid rgba(17, 24, 39, 0.08);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 18px 60px rgba(0, 0, 0, 0.08);
            background: #ffffff;
            transition: transform 180ms ease, box-shadow 180ms ease;
        }

        .event-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.12);
        }

        .event-card__header {
            padding: 18px 18px 12px 18px;
            background: linear-gradient(135deg, rgba(248, 0, 0, 0.14) 0%, rgba(0, 0, 0, 0.10) 100%);
        }

        .event-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .event-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid rgba(17, 24, 39, 0.10);
            background: rgba(255, 255, 255, 0.75);
            font-weight: 600;
            color: #111827;
        }

        .event-badge i {
            color: var(--dashboard-primary);
        }

        .payment-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            font-weight: 800;
            border: 1px solid rgba(17, 24, 39, 0.10);
            background: rgba(255, 255, 255, 0.85);
            color: #111827;
        }

        .payment-badge.is-warning {
            border-color: rgba(234, 179, 8, 0.30);
            background: rgba(234, 179, 8, 0.16);
            color: #92400e;
        }

        .payment-badge.is-info {
            border-color: rgba(59, 130, 246, 0.30);
            background: rgba(59, 130, 246, 0.14);
            color: #1d4ed8;
        }

        .payment-badge.is-success {
            border-color: rgba(34, 197, 94, 0.30);
            background: rgba(34, 197, 94, 0.14);
            color: #166534;
        }

        .payment-badge.is-muted {
            border-color: rgba(0, 0, 0, 0.10);
            background: rgba(0, 0, 0, 0.04);
            color: #374151;
        }

        .checkout-progress {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 12px 12px;
            border: 1px solid rgba(17, 24, 39, 0.08);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.92);
        }

        .checkout-progress__step {
            flex: 1;
            min-width: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 14px;
            background: rgba(0, 0, 0, 0.03);
        }

        .checkout-progress__dot {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            background: rgba(0, 0, 0, 0.08);
            color: #000000;
            flex: 0 0 auto;
        }

        .checkout-progress__label {
            font-weight: 900;
            color: #111827;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .checkout-progress__step.is-active {
            background: rgba(248, 0, 0, 0.08);
            border: 1px solid rgba(248, 0, 0, 0.18);
        }

        .checkout-progress__step.is-active .checkout-progress__dot {
            background: rgba(248, 0, 0, 0.16);
            color: rgb(248, 0, 0);
        }

        .sesi-item {
            border: 1px solid rgba(17, 24, 39, 0.08);
            border-radius: 14px;
            padding: 14px;
            background: #ffffff;
            transition: border-color 150ms ease, transform 150ms ease;
        }

        .sesi-item:hover {
            border-color: rgba(248, 0, 0, 0.45);
            transform: translateY(-1px);
        }

        .sesi-title {
            font-weight: 800;
            color: #111827;
            margin-bottom: 4px;
        }

        .sesi-subtitle {
            color: #4b5563;
            font-size: 14px;
            margin-bottom: 0;
        }

        .btn-primary-soft {
            background: var(--dashboard-primary);
            border-color: var(--dashboard-primary);
            color: #ffffff;
            border-radius: 12px;
            padding: 10px 14px;
            font-weight: 700;
            transition: transform 150ms ease, box-shadow 150ms ease, background 150ms ease, border-color 150ms ease;
        }

        .btn-primary-soft:hover {
            background: #d40000;
            border-color: #d40000;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 14px 30px rgba(248, 0, 0, 0.22);
            text-decoration: none;
        }

        .btn-primary-soft:focus {
            color: #ffffff;
            box-shadow: 0 0 0 3px rgba(248, 0, 0, 0.22);
            text-decoration: none;
        }

        .peserta-dashboard .btn-outline-secondary {
            border-radius: 12px;
            border-color: rgba(0, 0, 0, 0.35);
            color: #000000;
            font-weight: 800;
        }

        .peserta-dashboard .btn-outline-secondary:hover {
            background: #000000;
            border-color: #000000;
            color: #ffffff;
        }

        .peserta-dashboard .btn-outline-secondary:focus {
            box-shadow: 0 0 0 3px rgba(248, 0, 0, 0.22);
        }

        .video-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px solid rgba(17, 24, 39, 0.08);
            background: #ffffff;
            color: #111827;
            font-weight: 600;
            transition: transform 150ms ease, box-shadow 150ms ease, border-color 150ms ease;
        }

        .video-link:hover {
            transform: translateY(-1px);
            border-color: rgba(248, 0, 0, 0.45);
            box-shadow: 0 10px 22px rgba(17, 24, 39, 0.08);
            color: var(--dashboard-primary);
            text-decoration: none;
        }

        .dashboard-loading {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(17, 24, 39, 0.35);
            backdrop-filter: blur(6px);
            z-index: 2000;
            opacity: 0;
            transition: opacity 180ms ease;
        }

        .dashboard-loading.is-visible {
            display: flex;
            opacity: 1;
        }

        .dashboard-loading__card {
            width: min(420px, calc(100% - 32px));
            border-radius: 18px;
            background: #ffffff;
            padding: 18px;
            border: 1px solid rgba(17, 24, 39, 0.10);
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.22);
        }

        .dashboard-loading__spinner {
            width: 44px;
            height: 44px;
            border-radius: 999px;
            border: 4px solid rgba(248, 0, 0, 0.16);
            border-top-color: var(--dashboard-primary);
            animation: dashboardSpin 900ms linear infinite;
        }

        @keyframes dashboardSpin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .event-card,
            .sesi-item,
            .dashboard-icon-btn,
            .btn-primary-soft,
            .video-link,
            .dashboard-loading {
                transition: none;
            }
            .dashboard-loading__spinner {
                animation: none;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $pesanan = $pesanan ?? collect();
        $ebookOrders = $ebookOrders ?? collect();
        $pesananTerbaru = $pesananTerbaru ?? null;
        $riwayatPembayaran = $riwayatPembayaran ?? collect();
    @endphp

    <div class="peserta-dashboard">
        <div class="dashboard-loading" id="dashboardLoading" aria-live="polite" aria-busy="true" aria-label="Memproses">
            <div class="dashboard-loading__card" role="status">
                <div class="d-flex align-items-center" style="gap: 14px;">
                    <div class="dashboard-loading__spinner" aria-hidden="true"></div>
                    <div>
                        <div style="font-weight: 800; color: #111827;">Sedang memproses…</div>
                        <div style="color: #4b5563; font-size: 14px;">Mohon tunggu sebentar.</div>
                    </div>
                </div>
            </div>
        </div>

        <header class="dashboard-topbar" role="banner">
            <div class="container">
                <div class="dashboard-topbar__bar">
                    <div class="d-flex align-items-center" style="gap: 10px;">
                        <a
                            href="{{ url()->previous() }}"
                            class="dashboard-icon-btn"
                            aria-label="Kembali"
                            title="Kembali"
                            data-toggle="tooltip"
                            data-loading-trigger="link"
                        >
                            <i class="fas fa-arrow-left" aria-hidden="true"></i>
                        </a>
                        <span class="d-none d-sm-inline-block" style="font-weight: 700;">Kembali</span>
                    </div>

                    <div class="dashboard-topbar__logo" aria-hidden="true">
                        <img
                            src="{{ asset('assets/images/ads/ads-full.png') }}"
                            alt=""
                            width="300"
                            height="300"
                            loading="lazy"
                            decoding="async"
                        >
                    </div>

                    <div class="dashboard-topbar__right">
                        <form action="{{ route('peserta.logout') }}" method="POST" class="mb-0" style="display: inline-flex;" data-loading-trigger="submit">
                            @csrf
                            <button
                                type="submit"
                                class="dashboard-icon-btn"
                                aria-label="Logout"
                                title="Logout"
                                data-toggle="tooltip"
                            >
                                <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                            </button>
                        </form>

                        <a
                            href="{{ route('peserta.profile') }}"
                            class="dashboard-icon-btn"
                            aria-label="Profil"
                            title="Profil"
                            data-toggle="tooltip"
                        >
                            <i class="far fa-user" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>

                <div class="dashboard-subbar">
                    <div class="dashboard-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb d-flex flex-nowrap mb-0" style="gap: 0;">
                                {{-- <li class="breadcrumb-item">
                                    <a href="{{ route('peserta.index') }}">Home</a>
                                </li> --}}
                                {{-- <li c  lass="breadcrumb-item active" aria-current="page">Dashboard</li> --}}
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex align-items-center" style="gap: 10px;">
                        <img
                            src="{{ asset('assets/images/dash-bg-img.png') }}"
                            alt=""
                            width="56"
                            height="56"
                            loading="lazy"
                            decoding="async"
                            class="dashboard-pagehead__img"
                            style="border-radius: 16px; border: 1px solid rgba(17, 24, 39, 0.08); background: #ffffff;"
                        >
                    </div>
                </div>
            </div>
        </header>

        <main class="w-100 float-left padding-top padding-bottom" role="main">
            <div class="container">
                <div id="dashboardTop" aria-hidden="true"></div>

                @php
                    $orderTerbaru = $pesananTerbaru;
                    $fmtRp = function ($n) {
                        return 'Rp '.number_format((float) ($n ?? 0), 0, ',', '.');
                    };

                    $orderStatusVal = strtolower((string) ($orderTerbaru?->status_pembayaran ?? ''));
                    $orderHasMetode = (string) ($orderTerbaru?->metode_pembayaran ?? '') !== '';
                    $orderDeadlineAt = $orderTerbaru?->created_at ? optional($orderTerbaru->created_at)->copy()->addHours(24) : null;

                    $orderIsPending = $orderStatusVal === 'pending';
                    $orderIsPaid = $orderStatusVal === 'paid';
                    $orderIsExpired = $orderStatusVal === 'expired';
                    $orderIsFailed = $orderStatusVal === 'failed';

                    $orderUiStatusText = $orderIsPaid ? 'Lunas' : ($orderIsExpired ? 'Expired' : ($orderIsFailed ? 'Gagal' : ($orderHasMetode ? 'Sedang Diproses' : 'Pending')));
                    $orderUiStatusClass = $orderIsPaid ? 'is-success' : ($orderIsExpired || $orderIsFailed ? 'is-muted' : ($orderHasMetode ? 'is-info' : 'is-warning'));

                    $orderStep = 1;
                    if ($orderTerbaru) {
                        $orderStep = $orderIsPaid ? 4 : ($orderIsPending ? ($orderHasMetode ? 3 : 2) : 2);
                    }

                    $orderIsEbook = (bool) ($orderTerbaru?->ebook_id ?? false);
                    $orderEbook = $orderTerbaru?->ebook;
                    $orderEvent = $orderTerbaru?->paket?->event;
                    $orderPaket = $orderTerbaru?->paket;
                    $orderJumlahSesi = is_object($orderPaket?->sesi) ? $orderPaket->sesi->count() : 0;
                    $orderNeedReminder = false;
                    if ($orderIsPending && $orderDeadlineAt && now()->lt($orderDeadlineAt) && ! $orderHasMetode) {
                        $orderNeedReminder = now()->diffInMinutes($orderDeadlineAt) <= 120;
                    }
                @endphp

                <section class="dashboard-panel mb-4" aria-label="Informasi paket dan pembayaran">
                    <div class="dashboard-panel__header">
                        <div class="d-flex align-items-center justify-content-between" style="gap: 12px;">
                            <div class="d-flex align-items-center" style="gap: 12px;">
                                <span class="dashboard-step__icon" aria-hidden="true">
                                    <i class="fas fa-receipt"></i>
                                </span>
                                <div>
                                    <div class="dashboard-panel__title">Paket &amp; Pembayaran</div>
                                    <div class="dashboard-panel__subtitle">Pilih paket/e-book, bayar, konfirmasi, lalu akses konten.</div>
                                </div>
                            </div>
                            @if ($orderTerbaru)
                                <span class="payment-badge {{ $orderUiStatusClass }}" aria-label="Status pembayaran">
                                    <i class="fas fa-info-circle" aria-hidden="true"></i>
                                    <span>{{ $orderUiStatusText }}</span>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="p-3 p-md-4">
                        <div class="checkout-progress mb-3" aria-label="Progress checkout">
                            <div class="checkout-progress__step {{ $orderStep === 1 ? 'is-active' : '' }}">
                                <span class="checkout-progress__dot">1</span>
                                <span class="checkout-progress__label">Pilih Paket</span>
                            </div>
                            <div class="checkout-progress__step {{ $orderStep === 2 ? 'is-active' : '' }}">
                                <span class="checkout-progress__dot">2</span>
                                <span class="checkout-progress__label">Bayar</span>
                            </div>
                            <div class="checkout-progress__step {{ $orderStep === 3 ? 'is-active' : '' }}">
                                <span class="checkout-progress__dot">3</span>
                                <span class="checkout-progress__label">Konfirmasi</span>
                            </div>
                            <div class="checkout-progress__step {{ $orderStep === 4 ? 'is-active' : '' }}">
                                <span class="checkout-progress__dot">4</span>
                                <span class="checkout-progress__label">Selesai</span>
                            </div>
                        </div>

                        @if (! $orderTerbaru)
                            <div class="border rounded p-4 bg-light">
                                <div class="fw-semibold text-black">Belum memilih paket</div>
                                <div class="text-muted mt-1">Silakan pilih paket terlebih dahulu untuk melanjutkan pembayaran.</div>
                                <div class="mt-3 d-flex flex-wrap" style="gap: 10px;">
                                    <a href="{{ route('peserta.shop') }}" class="btn btn-primary-soft" aria-label="Pilih paket di shop">
                                        Pilih Paket <i class="fas fa-arrow-right ml-1" aria-hidden="true"></i>
                                    </a>
                                    <a href="{{ route('peserta.cart') }}" class="btn btn-outline-secondary" aria-label="Buka keranjang">
                                        Buka Keranjang
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="row" style="row-gap: 14px;">
                                <div class="col-12 col-lg-7">
                                    <div class="border rounded p-3 h-100" style="background: #ffffff;">
                                        <div class="fw-semibold text-black">{{ $orderIsEbook ? 'Detail e-book yang dipilih' : 'Detail paket yang dipilih' }}</div>
                                        <div class="text-muted small mt-1">Pastikan detail dan total sudah sesuai.</div>

                                        <div class="mt-3">
                                            <div style="font-weight: 950; letter-spacing: -0.02em; font-size: 16px; line-height: 1.25;">
                                                {{ $orderIsEbook ? ($orderEbook?->title ?? '-') : ($orderPaket?->nama_paket ?? '-') }}
                                            </div>
                                            <div class="text-muted mt-1">
                                                {{ $orderIsEbook ? ('Penulis: '.($orderEbook?->author ?? '-')) : ($orderEvent?->judul ?? 'Event') }}
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <div class="d-flex align-items-center justify-content-between py-2 border-top">
                                                <div class="text-muted fw-semibold">Harga</div>
                                                <div class="fw-bold text-black">{{ $fmtRp($orderIsEbook ? ($orderEbook?->price ?? 0) : ($orderPaket?->harga ?? 0)) }}</div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between py-2 border-top">
                                                <div class="text-muted fw-semibold">Total</div>
                                                <div class="fw-bold text-black">{{ $fmtRp($orderTerbaru?->total_bayar ?? 0) }}</div>
                                            </div>
                                        </div>

                                        @if (! $orderIsEbook)
                                            <div class="mt-3">
                                                <div class="fw-semibold text-black">Fitur paket</div>
                                                <ul class="text-muted small mb-0 mt-2" style="padding-left: 18px;">
                                                    <li>Akses Live: {{ (bool) ($orderPaket?->akses_live ?? false) ? 'Ya' : 'Tidak' }}</li>
                                                    <li>Akses Rekaman: {{ (bool) ($orderPaket?->akses_rekaman ?? false) ? 'Ya' : 'Tidak' }}</li>
                                                    <li>Jumlah sesi: {{ $orderJumlahSesi }}</li>
                                                    @if (! is_null($orderPaket?->kuota))
                                                        <li>Kuota: {{ (int) $orderPaket->kuota }}</li>
                                                    @endif
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-12 col-lg-5">
                                    <div class="border rounded p-3 h-100" style="background: #ffffff;">
                                        <div class="fw-semibold text-black">Panduan singkat</div>
                                        <div class="text-muted small mt-1">Ikuti langkah sesuai status pembayaran.</div>

                                        @if ($orderIsPending && ! $orderHasMetode && ! $orderIsExpired && ! $orderIsFailed)
                                            <div class="alert alert-warning mt-3 mb-0" role="alert">
                                                Silakan selesaikan pembayaran untuk mengaktifkan {{ $orderIsEbook ? 'e-book' : 'paket' }} Anda.
                                            </div>
                                        @elseif ($orderIsPending && $orderHasMetode)
                                            <div class="alert alert-info mt-3 mb-0" role="alert">
                                                Pembayaran sedang diproses. Kami akan memperbarui status setelah verifikasi.
                                            </div>
                                        @elseif ($orderIsPaid)
                                            <div class="alert alert-success mt-3 mb-0" role="alert">
                                                Pembayaran berhasil. {{ $orderIsEbook ? 'E-book' : 'Paket' }} Anda sudah aktif.
                                            </div>
                                        @elseif ($orderIsExpired || $orderIsFailed)
                                            <div class="alert alert-secondary mt-3 mb-0" role="alert">
                                                Pesanan tidak dapat diproses. Silakan buat pesanan baru dari keranjang.
                                            </div>
                                        @endif

                                        @if ($orderNeedReminder)
                                            <div class="alert alert-warning mt-3 mb-0" role="alert">
                                                Pembayaran mendekati batas waktu. Segera selesaikan pembayaran.
                                            </div>
                                        @endif

                                        @if ($orderIsPending && $orderDeadlineAt)
                                            <div class="mt-3">
                                                <div class="text-muted small">Batas waktu pembayaran</div>
                                                <div class="fw-semibold text-black" id="orderDeadlineText" data-deadline="{{ optional($orderDeadlineAt)->toIso8601String() }}">
                                                    {{ optional($orderDeadlineAt)->format('d-m-Y H:i') }}
                                                </div>
                                                <div class="text-muted small mt-1" id="orderDeadlineCountdown"></div>
                                            </div>
                                        @endif

                                        <div class="mt-3 d-grid gap-2">
                                            @if ($orderIsPending && ! $orderHasMetode && ! $orderIsExpired && ! $orderIsFailed)
                                                <a href="{{ route('peserta.checkout.payment', ['pesanan' => $orderTerbaru->id]) }}" class="btn btn-primary-soft" aria-label="Bayar sekarang">
                                                    Bayar Sekarang <i class="fas fa-arrow-right ml-1" aria-hidden="true"></i>
                                                </a>
                                            @elseif ($orderIsPending && $orderHasMetode)
                                                <a href="{{ route('peserta.checkout.confirm', ['pesanan' => $orderTerbaru->id]) }}" class="btn btn-primary-soft" aria-label="Lihat status pembayaran">
                                                    Lihat Status <i class="fas fa-arrow-right ml-1" aria-hidden="true"></i>
                                                </a>
                                            @elseif ($orderIsPaid)
                                                <a href="#riwayatEvent" class="btn btn-primary-soft" aria-label="Lihat {{ $orderIsEbook ? 'e-book' : 'event' }}">
                                                    Lihat {{ $orderIsEbook ? 'E-book' : 'Event' }} <i class="fas fa-arrow-down ml-1" aria-hidden="true"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('peserta.cart') }}" class="btn btn-primary-soft" aria-label="Buat pesanan baru">
                                                    Buat Pesanan Baru <i class="fas fa-arrow-right ml-1" aria-hidden="true"></i>
                                                </a>
                                            @endif

                                            <a href="{{ route('peserta.shop') }}" class="btn btn-outline-secondary" aria-label="Kembali ke shop">
                                                Pilih Paket Lain
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </section>

                @if (is_object($riwayatPembayaran) && $riwayatPembayaran->isNotEmpty())
                    <section class="dashboard-panel mb-4" aria-label="Riwayat pembayaran">
                        <div class="dashboard-panel__header">
                            <div class="d-flex align-items-center justify-content-between" style="gap: 12px;">
                                <div class="d-flex align-items-center" style="gap: 12px;">
                                    <span class="dashboard-step__icon" aria-hidden="true" style="background: rgba(0, 0, 0, 0.06); border-color: rgba(0, 0, 0, 0.18); color: #000000;">
                                        <i class="fas fa-history"></i>
                                    </span>
                                    <div>
                                        <div class="dashboard-panel__title">Riwayat Pembayaran</div>
                                        <div class="dashboard-panel__subtitle">Daftar transaksi terbaru Anda.</div>
                                    </div>
                                </div>
                                <span class="event-badge" aria-label="Total transaksi">
                                    <i class="fas fa-receipt" aria-hidden="true"></i>
                                    <span>{{ $riwayatPembayaran->count() }} transaksi</span>
                                </span>
                            </div>
                        </div>
                        <div class="p-3 p-md-4">
                            <div class="list-group">
                                @foreach ($riwayatPembayaran as $trx)
                                    @php
                                        $s = strtolower((string) ($trx->status_pembayaran ?? ''));
                                        $hasM = (string) ($trx->metode_pembayaran ?? '') !== '';
                                        $txt = $s === 'paid' ? 'Lunas' : ($s === 'expired' ? 'Expired' : ($s === 'failed' ? 'Gagal' : ($hasM ? 'Sedang Diproses' : 'Pending')));
                                        $cls = $s === 'paid' ? 'is-success' : ($s === 'expired' || $s === 'failed' ? 'is-muted' : ($hasM ? 'is-info' : 'is-warning'));
                                        $evt = $trx->paket?->event;
                                        $trxIsEbook = (bool) ($trx->ebook_id ?? false);
                                        $trxEbook = $trx->ebook;
                                    @endphp
                                    <div class="list-group-item dashboard-history-item">
                                        <div class="d-flex align-items-start justify-content-between" style="gap: 12px;">
                                            <div style="min-width: 0;">
                                                <div style="font-weight: 950; letter-spacing: -0.02em; font-size: 16px; line-height: 1.25;">
                                                    {{ $trxIsEbook ? ($trxEbook?->title ?? 'E-book') : ($evt?->judul ?? 'Event') }}
                                                </div>
                                                <div class="text-muted" style="font-weight: 600; font-size: 14px;">
                                                    @if ($trxIsEbook)
                                                        E-book
                                                    @else
                                                        Paket: {{ $trx->paket?->nama_paket ?? '-' }}
                                                    @endif
                                                </div>
                                                <div class="text-muted small mt-1">
                                                    Kode: {{ $trx->kode_pesanan }} • {{ optional($trx->created_at)->format('d-m-Y H:i') }}
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <div class="payment-badge {{ $cls }}">
                                                    <i class="fas fa-info-circle" aria-hidden="true"></i>
                                                    <span>{{ $txt }}</span>
                                                </div>
                                                <div class="fw-bold text-black mt-2">{{ $fmtRp($trx->total_bayar ?? 0) }}</div>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-wrap mt-3" style="gap: 10px;">
                                            @if ($s === 'pending' && ! $hasM)
                                                <a href="{{ route('peserta.checkout.payment', ['pesanan' => $trx->id]) }}" class="btn btn-primary-soft" aria-label="Lanjutkan pembayaran">
                                                    Bayar Sekarang <i class="fas fa-arrow-right ml-1" aria-hidden="true"></i>
                                                </a>
                                            @elseif ($s === 'pending' && $hasM)
                                                <a href="{{ route('peserta.checkout.confirm', ['pesanan' => $trx->id]) }}" class="btn btn-primary-soft" aria-label="Lihat status transaksi">
                                                    Lihat Status <i class="fas fa-arrow-right ml-1" aria-hidden="true"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('peserta.checkout.confirm', ['pesanan' => $trx->id]) }}" class="btn btn-outline-secondary" aria-label="Lihat detail transaksi">
                                                    Detail
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif

                @if ($pesanan->isEmpty() && (! is_object($ebookOrders) || $ebookOrders->isEmpty()))
                    <div class="row">
                        <div class="col-12 col-lg-7 mb-4 mb-lg-0" data-aos="fade-up" data-aos-duration="700">
                            <section class="dashboard-panel" aria-label="Ringkasan dashboard peserta">
                                <div class="dashboard-panel__header">
                                    <div class="d-flex align-items-center" style="gap: 12px;">
                                        <span class="dashboard-step__icon" aria-hidden="true">
                                            <i class="far fa-user"></i>
                                        </span>
                                        <div>
                                            <div class="dashboard-panel__title">
                                                Selamat datang
                                                @auth
                                                    , {{ auth()->user()?->nama ?? 'Peserta' }}
                                                @endauth
                                            </div>
                                            <div class="dashboard-panel__subtitle">Mulai dari sini untuk membeli event dan mengatur akses.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3 p-md-4">
                                    <div class="row">
                                        <div class="col-6 col-md-3 mb-3">
                                            <div class="dashboard-kpi">
                                                <div class="dashboard-kpi__label">Event</div>
                                                <div class="dashboard-kpi__value">0</div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3 mb-3">
                                            <div class="dashboard-kpi">
                                                <div class="dashboard-kpi__label">Akses Live</div>
                                                <div class="dashboard-kpi__value">0</div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3 mb-3">
                                            <div class="dashboard-kpi">
                                                <div class="dashboard-kpi__label">Akses Rekaman</div>
                                                <div class="dashboard-kpi__value">0</div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3 mb-3">
                                            <div class="dashboard-kpi">
                                                <div class="dashboard-kpi__label">Sesi</div>
                                                <div class="dashboard-kpi__value">0</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex flex-wrap align-items-center" style="gap: 10px;">
                                        <a href="{{ route('peserta.shop') }}" class="btn btn-primary-soft" aria-label="Cari event di shop">
                                            Cari Event <i class="fas fa-arrow-right ml-1" aria-hidden="true"></i>
                                        </a>
                                        <a href="{{ route('peserta.profile') }}" class="btn btn-outline-secondary" style="border-radius: 12px; font-weight: 800;" aria-label="Buka profil peserta">
                                            Profil
                                        </a>
                                        <a href="{{ route('peserta.about') }}#faq" class="btn btn-outline-secondary" style="border-radius: 12px; font-weight: 800;" aria-label="Buka FAQ peserta">
                                            Panduan (FAQ)
                                        </a>
                                        <a href="{{ route('peserta.contact') }}" class="btn btn-outline-secondary" style="border-radius: 12px; font-weight: 800;" aria-label="Hubungi admin">
                                            Hubungi Admin
                                        </a>
                                    </div>
                                </div>
                            </section>
                        </div>

                        <div class="col-12 col-lg-5" data-aos="fade-up" data-aos-duration="700">
                            <section class="dashboard-panel" aria-label="Panduan cepat dashboard">
                                <div class="dashboard-panel__header">
                                    <div class="d-flex align-items-center" style="gap: 12px;">
                                    <span class="dashboard-step__icon" aria-hidden="true" style="background: rgba(0, 0, 0, 0.06); border-color: rgba(0, 0, 0, 0.18); color: #000000;">
                                            <i class="far fa-compass"></i>
                                        </span>
                                        <div>
                                            <div class="dashboard-panel__title">Panduan Cepat</div>
                                            <div class="dashboard-panel__subtitle">Agar tidak bingung saat pertama masuk.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3 p-md-4">
                                    <div class="d-flex flex-column" style="gap: 12px;">
                                        <div class="dashboard-step">
                                            <span class="dashboard-step__icon" aria-hidden="true">
                                                <i class="fas fa-store"></i>
                                            </span>
                                            <div>
                                                <div class="dashboard-step__title">1) Beli event</div>
                                                <p class="dashboard-step__desc">Masuk ke Shop, pilih event dan paket sesuai kebutuhan.</p>
                                            </div>
                                        </div>
                                        <div class="dashboard-step">
                                            <span class="dashboard-step__icon" aria-hidden="true">
                                                <i class="fas fa-video"></i>
                                            </span>
                                            <div>
                                                <div class="dashboard-step__title">2) Join saat Live</div>
                                                <p class="dashboard-step__desc">Tombol Join Live muncul saat sesi berstatus live dan paket mendukung live.</p>
                                            </div>
                                        </div>
                                        <div class="dashboard-step">
                                            <span class="dashboard-step__icon" aria-hidden="true">
                                                <i class="fas fa-play-circle"></i>
                                            </span>
                                            <div>
                                                <div class="dashboard-step__title">3) Tonton rekaman</div>
                                                <p class="dashboard-step__desc">Rekaman muncul jika event selesai dan paket punya akses rekaman.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>

                    <div class="event-card mt-4" data-aos="fade-up" data-aos-duration="700">
                        <div class="event-card__header">
                            <div class="d-flex align-items-center" style="gap: 12px;">
                                <span class="dashboard-icon-btn" aria-hidden="true" style="border-color: rgba(248, 0, 0, 0.25);">
                                    <i class="fas fa-ticket-alt" aria-hidden="true"></i>
                                </span>
                                <div>
                                    <div style="font-weight: 900; font-size: 18px;">Belum ada event</div>
                                    <div style="color: #4b5563; font-size: 14px;">Setelah checkout berhasil, event akan muncul di sini beserta daftar sesi.</div>
                                </div>
                            </div>
                        </div>
                        <div class="p-3 p-md-4">
                            <a href="{{ route('peserta.shop') }}" class="btn btn-primary-soft" aria-label="Lihat daftar event di shop">
                                Lihat Event <i class="fas fa-arrow-right ml-1" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                @else
                    @php
                        $totalEvent = $pesanan->count();
                        $totalLive = $pesanan->filter(function ($o) {
                            return (bool) ($o->paket->akses_live ?? false);
                        })->count();
                        $totalRekaman = $pesanan->filter(function ($o) {
                            return (bool) ($o->paket->akses_rekaman ?? false);
                        })->count();
                        $totalSesi = 0;
                        foreach ($pesanan as $o) {
                            $evt = $o->paket->event;
                            $totalSesi += $evt?->sesi?->count() ?? 0;
                        }
                        $pesananAktif = $pesanan->filter(function ($o) {
                            $status = strtolower((string) ($o->paket->event->status ?? ''));
                            return $status !== 'finished';
                        });
                        $pesananSelesai = $pesanan->filter(function ($o) {
                            $status = strtolower((string) ($o->paket->event->status ?? ''));
                            return $status === 'finished';
                        });
                    @endphp

                    <div class="row mb-4">
                        <div class="col-12 col-lg-7 mb-4 mb-lg-0" data-aos="fade-up" data-aos-duration="700">
                            <section class="dashboard-panel" aria-label="Ringkasan dashboard peserta">
                                <div class="dashboard-panel__header">
                                    <div class="d-flex align-items-center" style="gap: 12px;">
                                        <span class="dashboard-step__icon" aria-hidden="true">
                                            <i class="far fa-user"></i>
                                        </span>
                                        <div>
                                            <div class="dashboard-panel__title">
                                                Selamat datang
                                                @auth
                                                    , {{ auth()->user()?->nama ?? 'Peserta' }}
                                                @endauth
                                            </div>
                                            <div class="dashboard-panel__subtitle">Ringkasan akses dan riwayat event yang kamu ikuti.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3 p-md-4">
                                    <div class="row">
                                        <div class="col-6 col-md-3 mb-3">
                                            <div class="dashboard-kpi">
                                                <div class="dashboard-kpi__label">Event</div>
                                                <div class="dashboard-kpi__value">{{ $totalEvent }}</div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3 mb-3">
                                            <div class="dashboard-kpi">
                                                <div class="dashboard-kpi__label">Akses Live</div>
                                                <div class="dashboard-kpi__value">{{ $totalLive }}</div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3 mb-3">
                                            <div class="dashboard-kpi">
                                                <div class="dashboard-kpi__label">Akses Rekaman</div>
                                                <div class="dashboard-kpi__value">{{ $totalRekaman }}</div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3 mb-3">
                                            <div class="dashboard-kpi">
                                                <div class="dashboard-kpi__label">Total Sesi</div>
                                                <div class="dashboard-kpi__value">{{ $totalSesi }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex flex-wrap align-items-center" style="gap: 10px;">
                                        <a href="{{ route('peserta.shop') }}" class="btn btn-primary-soft" aria-label="Cari event di shop">
                                            Cari Event <i class="fas fa-arrow-right ml-1" aria-hidden="true"></i>
                                        </a>
                                        <a href="{{ route('peserta.profile') }}" class="btn btn-outline-secondary" style="border-radius: 12px; font-weight: 800;" aria-label="Buka profil peserta">
                                            Profil
                                        </a>
                                        <a href="{{ route('peserta.about') }}#faq" class="btn btn-outline-secondary" style="border-radius: 12px; font-weight: 800;" aria-label="Buka FAQ peserta">
                                            Panduan (FAQ)
                                        </a>
                                        <a href="{{ route('peserta.contact') }}" class="btn btn-outline-secondary" style="border-radius: 12px; font-weight: 800;" aria-label="Hubungi admin">
                                            Hubungi Admin
                                        </a>
                                    </div>
                                </div>
                            </section>
                        </div>

                        <div class="col-12 col-lg-5" data-aos="fade-up" data-aos-duration="700">
                            <section class="dashboard-panel" aria-label="Cara menggunakan dashboard">
                                <div class="dashboard-panel__header">
                                    <div class="d-flex align-items-center" style="gap: 12px;">
                                    <span class="dashboard-step__icon" aria-hidden="true" style="background: rgba(0, 0, 0, 0.06); border-color: rgba(0, 0, 0, 0.18); color: #000000;">
                                            <i class="far fa-compass"></i>
                                        </span>
                                        <div>
                                            <div class="dashboard-panel__title">Cara Pakai Dashboard</div>
                                            <div class="dashboard-panel__subtitle">Pilih event, lalu akses sesi yang tersedia.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3 p-md-4">
                                    <div class="d-flex flex-column" style="gap: 12px;">
                                        <div class="dashboard-step">
                                            <span class="dashboard-step__icon" aria-hidden="true">
                                                <i class="fas fa-history"></i>
                                            </span>
                                            <div>
                                                <div class="dashboard-step__title">Riwayat event</div>
                                                <p class="dashboard-step__desc">Gunakan daftar riwayat untuk cepat lompat ke detail event.</p>
                                            </div>
                                        </div>
                                        <div class="dashboard-step">
                                            <span class="dashboard-step__icon" aria-hidden="true">
                                                <i class="fas fa-video"></i>
                                            </span>
                                            <div>
                                                <div class="dashboard-step__title">Join Live</div>
                                                <p class="dashboard-step__desc">Tombol Join Live hanya muncul pada sesi yang live dan paket mendukung.</p>
                                            </div>
                                        </div>
                                        <div class="dashboard-step">
                                            <span class="dashboard-step__icon" aria-hidden="true">
                                                <i class="fas fa-play-circle"></i>
                                            </span>
                                            <div>
                                                <div class="dashboard-step__title">Rekaman</div>
                                                <p class="dashboard-step__desc">Rekaman tersedia setelah event selesai dan ada video pada sesi.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>

                    <section class="dashboard-panel mb-4" id="riwayatEvent" aria-label="Riwayat event">
                        <div class="dashboard-panel__header">
                            <div class="d-flex align-items-center justify-content-between" style="gap: 12px;">
                                <div class="d-flex align-items-center" style="gap: 12px;">
                                    <span class="dashboard-step__icon" aria-hidden="true">
                                        <i class="fas fa-list"></i>
                                    </span>
                                    <div>
                                        <div class="dashboard-panel__title">Riwayat Event</div>
                                        <div class="dashboard-panel__subtitle">Klik item untuk membuka detail dan daftar sesi.</div>
                                    </div>
                                </div>
                                <span class="event-badge" aria-label="Total event">
                                    <i class="fas fa-ticket-alt" aria-hidden="true"></i>
                                    <span>{{ $totalEvent }} event</span>
                                </span>
                            </div>
                        </div>
                        <div class="p-3 p-md-4">
                            @if ($pesananAktif->isNotEmpty())
                                <div style="font-weight: 900; margin-bottom: 10px;">Sedang Berjalan</div>
                                <div class="list-group">
                                    @foreach ($pesananAktif as $order)
                                        @php
                                            $event = $order->paket->event;
                                        @endphp
                                        <a href="#order-{{ $order->id }}" class="list-group-item list-group-item-action dashboard-history-item" aria-label="Buka detail event {{ $event->judul }}">
                                            <div class="d-flex align-items-start justify-content-between" style="gap: 12px;">
                                                <div>
                                                    <div style="font-weight: 950; letter-spacing: -0.02em; font-size: 16px; line-height: 1.25;">
                                                        {{ $event->judul }}
                                                    </div>
                                                    <div style="color: #4b5563; font-weight: 600; font-size: 14px;">
                                                        Paket: {{ $order->paket->nama_paket }}
                                                    </div>
                                                </div>
                                                <span class="event-badge" aria-label="Status event {{ $event->status }}">
                                                    <i class="fas fa-info-circle" aria-hidden="true"></i>
                                                    <span>{{ strtoupper($event->status) }}</span>
                                                </span>
                                            </div>
                                            <div class="dashboard-history-meta" aria-label="Ringkasan akses">
                                                <span class="event-badge">
                                                    <i class="fas fa-layer-group" aria-hidden="true"></i>
                                                    <span>{{ $event->sesi->count() }} sesi</span>
                                                </span>
                                                <span class="event-badge">
                                                    <i class="fas fa-video" aria-hidden="true"></i>
                                                    <span>{{ $order->paket->akses_live ? 'Live' : 'Tanpa live' }}</span>
                                                </span>
                                                <span class="event-badge">
                                                    <i class="fas fa-play-circle" aria-hidden="true"></i>
                                                    <span>{{ $order->paket->akses_rekaman ? 'Rekaman' : 'Tanpa rekaman' }}</span>
                                                </span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif

                            @if ($pesananSelesai->isNotEmpty())
                                <div style="font-weight: 900; margin-top: 18px; margin-bottom: 10px;">Selesai</div>
                                <div class="list-group">
                                    @foreach ($pesananSelesai as $order)
                                        @php
                                            $event = $order->paket->event;
                                        @endphp
                                        <a href="#order-{{ $order->id }}" class="list-group-item list-group-item-action dashboard-history-item" aria-label="Buka detail event {{ $event->judul }}">
                                            <div class="d-flex align-items-start justify-content-between" style="gap: 12px;">
                                                <div>
                                                    <div style="font-weight: 950; letter-spacing: -0.02em; font-size: 16px; line-height: 1.25;">
                                                        {{ $event->judul }}
                                                    </div>
                                                    <div style="color: #4b5563; font-weight: 600; font-size: 14px;">
                                                        Paket: {{ $order->paket->nama_paket }}
                                                    </div>
                                                </div>
                                                <span class="event-badge" aria-label="Status event {{ $event->status }}">
                                                    <i class="fas fa-check-circle" aria-hidden="true"></i>
                                                    <span>{{ strtoupper($event->status) }}</span>
                                                </span>
                                            </div>
                                            <div class="dashboard-history-meta" aria-label="Ringkasan akses">
                                                <span class="event-badge">
                                                    <i class="fas fa-layer-group" aria-hidden="true"></i>
                                                    <span>{{ $event->sesi->count() }} sesi</span>
                                                </span>
                                                <span class="event-badge">
                                                    <i class="fas fa-video" aria-hidden="true"></i>
                                                    <span>{{ $order->paket->akses_live ? 'Live' : 'Tanpa live' }}</span>
                                                </span>
                                                <span class="event-badge">
                                                    <i class="fas fa-play-circle" aria-hidden="true"></i>
                                                    <span>{{ $order->paket->akses_rekaman ? 'Rekaman' : 'Tanpa rekaman' }}</span>
                                                </span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif

                            @if (is_object($ebookOrders) && $ebookOrders->isNotEmpty())
                                <div style="font-weight: 900; margin-top: 18px; margin-bottom: 10px;">E-book</div>
                                <div class="list-group">
                                    @foreach ($ebookOrders as $order)
                                        @php
                                            $ebook = $order->ebook;
                                        @endphp
                                        <div class="list-group-item dashboard-history-item">
                                            <div class="d-flex align-items-start justify-content-between" style="gap: 12px;">
                                                <div style="min-width: 0;">
                                                    <div style="font-weight: 950; letter-spacing: -0.02em; font-size: 16px; line-height: 1.25;">
                                                        {{ $ebook?->title ?? 'E-book' }}
                                                    </div>
                                                    <div style="color: #4b5563; font-weight: 600; font-size: 14px;">
                                                        Penulis: {{ $ebook?->author ?? '-' }}
                                                    </div>
                                                    <div class="text-muted small mt-1">
                                                        Kode: {{ $order->kode_pesanan }} • {{ optional($order->waktu_bayar)->format('d-m-Y H:i') }}
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <div class="fw-bold text-black">{{ $fmtRp($order->total_bayar ?? 0) }}</div>
                                                </div>
                                            </div>
                                            @if ($ebook && ! empty($ebook->pdf_file))
                                                <div class="d-flex flex-wrap mt-3" style="gap: 10px;">
                                                    <a href="{{ route('peserta.ebooks.download', $ebook) }}" class="btn btn-primary-soft" aria-label="Download e-book {{ $ebook->title }}">
                                                        Download <i class="fas fa-file-download ml-1" aria-hidden="true"></i>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </section>

                    <div class="d-flex align-items-center justify-content-between mb-3" style="gap: 12px;">
                        <div style="font-weight: 950; letter-spacing: -0.03em; font-size: 18px;">Detail Event & Sesi</div>
                        <a href="#dashboardTop" class="btn btn-outline-secondary" style="border-radius: 12px; font-weight: 800;">
                            Kembali ke atas
                        </a>
                    </div>

                    <div class="row">
                        @foreach ($pesanan as $order)
                            @php
                                $event = $order->paket->event;
                            @endphp

                            <div class="col-12 col-lg-6 mb-4" data-aos="fade-up" data-aos-duration="700">
                                <section class="event-card" aria-label="Event {{ $event->judul }}" id="order-{{ $order->id }}">
                                    <div class="event-card__header">
                                        <div class="d-flex align-items-start justify-content-between" style="gap: 12px;">
                                            <div>
                                                <h2 class="mb-1" style="font-weight: 950; letter-spacing: -0.03em; font-size: 20px; line-height: 1.25;">
                                                    {{ $event->judul }}
                                                </h2>
                                                <div style="color: #4b5563; font-weight: 600;">
                                                    Paket: {{ $order->paket->nama_paket }}
                                                </div>
                                            </div>

                                            <span class="event-badge" aria-label="Status event {{ $event->status }}">
                                                <i class="fas fa-info-circle" aria-hidden="true"></i>
                                                <span>{{ strtoupper($event->status) }}</span>
                                            </span>
                                        </div>

                                        <div class="event-meta" aria-label="Akses paket">
                                            <span class="event-badge" title="Akses live" data-toggle="tooltip">
                                                <i class="fas fa-video" aria-hidden="true"></i>
                                                <span>{{ $order->paket->akses_live ? 'Live' : 'Tidak ada live' }}</span>
                                            </span>
                                            <span class="event-badge" title="Akses rekaman" data-toggle="tooltip">
                                                <i class="fas fa-play-circle" aria-hidden="true"></i>
                                                <span>{{ $order->paket->akses_rekaman ? 'Rekaman' : 'Tidak ada rekaman' }}</span>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="p-3 p-md-4">
                                        <div class="d-flex align-items-center justify-content-between mb-3" style="gap: 12px;">
                                            <h3 class="mb-0" style="font-weight: 900; font-size: 16px;">Daftar Sesi</h3>
                                            <span style="color: #4b5563; font-size: 14px;">
                                                {{ $event->sesi->count() }} sesi
                                            </span>
                                        </div>

                                        <div class="d-flex flex-column" style="gap: 12px;">
                                            @foreach ($event->sesi as $sesi)
                                                <div class="sesi-item">
                                                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between" style="gap: 12px;">
                                                        <div>
                                                            <div class="sesi-title">{{ $sesi->judul_sesi }}</div>
                                                            <p class="sesi-subtitle">
                                                                <i class="far fa-clock mr-1" aria-hidden="true"></i>
                                                                <span>{{ $sesi->waktu_mulai }} – {{ $sesi->waktu_selesai }}</span>
                                                                <span class="mx-2" aria-hidden="true">•</span>
                                                                <span>Status: {{ $sesi->status_sesi }}</span>
                                                            </p>
                                                        </div>

                                                        <div class="d-flex flex-wrap align-items-center" style="gap: 10px;">
                                                            @if ($sesi->status_sesi === 'live' && $order->paket->akses_live)
                                                                <form
                                                                    action="{{ route('peserta.join', $sesi->id) }}"
                                                                    method="POST"
                                                                    class="mb-0"
                                                                    data-loading-trigger="submit"
                                                                    aria-label="Form join live"
                                                                >
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-primary-soft" title="Join sesi live" data-toggle="tooltip">
                                                                        Join Live <i class="fas fa-external-link-alt ml-1" aria-hidden="true"></i>
                                                                    </button>
                                                                </form>
                                                            @endif

                                                            @if ($event->status === 'finished' && $order->paket->akses_rekaman && $sesi->video->count() > 0)
                                                                <button
                                                                    type="button"
                                                                    class="dashboard-icon-btn"
                                                                    aria-label="Rekaman tersedia"
                                                                    title="Rekaman tersedia"
                                                                    data-toggle="tooltip"
                                                                    style="border-color: rgba(248, 0, 0, 0.25);"
                                                                >
                                                                    <i class="fas fa-film" aria-hidden="true"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    @if ($event->status === 'finished' && $order->paket->akses_rekaman && $sesi->video->count() > 0)
                                                        <div class="mt-3" aria-label="Daftar rekaman video">
                                                            <div style="font-weight: 800; color: #111827; margin-bottom: 10px;">
                                                                Rekaman
                                                            </div>
                                                            <div class="d-flex flex-column" style="gap: 10px;">
                                                                @foreach ($sesi->video as $video)
                                                                    <a
                                                                        href="{{ route('peserta.video', $video->id) }}"
                                                                        class="video-link"
                                                                        data-loading-trigger="link"
                                                                        aria-label="Tonton rekaman {{ $video->judul_video }}"
                                                                        title="Tonton rekaman"
                                                                        data-toggle="tooltip"
                                                                    >
                                                                        <i class="fas fa-play" aria-hidden="true"></i>
                                                                        <span class="flex-grow-1">{{ $video->judul_video }}</span>
                                                                        <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                                                    </a>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </section>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </main>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            function showLoading() {
                var el = document.getElementById('dashboardLoading');
                if (!el) return;
                el.classList.add('is-visible');
            }

            function initTooltips() {
                try {
                    if (window.jQuery && window.jQuery.fn && window.jQuery.fn.tooltip) {
                        window.jQuery('[data-toggle="tooltip"]').tooltip();
                    }
                } catch (e) {}
            }

            function initInteractions() {
                document.addEventListener('click', function (e) {
                    var trigger = e.target.closest('[data-loading-trigger="link"]');
                    if (!trigger) return;

                    var isDisabled = trigger.getAttribute('aria-disabled') === 'true' || trigger.hasAttribute('disabled');
                    if (!isDisabled) showLoading();
                });

                document.addEventListener('submit', function (e) {
                    var form = e.target.closest('[data-loading-trigger="submit"]');
                    if (!form) return;
                    showLoading();
                });

                var notif = document.querySelector('[data-notification]');
                if (notif) {
                    notif.addEventListener('click', function () {
                        notif.blur();
                    });
                }
            }

            function initDeadlineCountdown() {
                var deadlineTextEl = document.getElementById('orderDeadlineText');
                var countdownEl = document.getElementById('orderDeadlineCountdown');
                if (!deadlineTextEl || !countdownEl) return;

                var raw = deadlineTextEl.getAttribute('data-deadline') || '';
                var deadlineMs = Date.parse(raw);
                if (!isFinite(deadlineMs)) return;

                function pad(n) {
                    return String(n).padStart(2, '0');
                }

                function tick() {
                    var diff = deadlineMs - Date.now();
                    if (!isFinite(diff)) return;
                    if (diff <= 0) {
                        countdownEl.textContent = 'Batas waktu pembayaran sudah lewat.';
                        return;
                    }

                    var totalSeconds = Math.floor(diff / 1000);
                    var hours = Math.floor(totalSeconds / 3600);
                    var minutes = Math.floor((totalSeconds % 3600) / 60);
                    var seconds = totalSeconds % 60;
                    countdownEl.textContent = 'Sisa waktu: ' + pad(hours) + ':' + pad(minutes) + ':' + pad(seconds);
                }

                tick();
                setInterval(tick, 1000);
            }

            document.addEventListener('DOMContentLoaded', function () {
                initTooltips();
                initInteractions();
                initDeadlineCountdown();
            });
        })();
    </script>
@endpush
