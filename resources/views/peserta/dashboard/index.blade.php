@extends('peserta.partials.app')

@section('title', 'Dashboard Peserta')

@push('styles')
    <style>
        .peserta-dashboard {
            background: radial-gradient(900px circle at 10% 10%, rgba(53, 0, 252, 0.10), transparent 60%),
                radial-gradient(700px circle at 90% 30%, rgba(239, 98, 46, 0.10), transparent 55%),
                linear-gradient(180deg, #f9f9fb 0%, #ffffff 100%);
            color: #111827;
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
            background: rgba(255, 255, 255, 0.9);
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
            border-color: rgba(53, 0, 252, 0.35);
            box-shadow: 0 10px 22px rgba(17, 24, 39, 0.08);
            color: #3500fc;
            text-decoration: none;
        }

        .dashboard-icon-btn:focus {
            outline: 0;
            box-shadow: 0 0 0 3px rgba(53, 0, 252, 0.25);
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
            color: #3500fc;
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
            box-shadow: 0 18px 60px rgba(35, 23, 105, 0.08);
            background: #ffffff;
            transition: transform 180ms ease, box-shadow 180ms ease;
        }

        .event-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 24px 80px rgba(35, 23, 105, 0.12);
        }

        .event-card__header {
            padding: 18px 18px 12px 18px;
            background: linear-gradient(135deg, rgba(53, 0, 252, 0.08) 0%, rgba(236, 57, 139, 0.06) 55%, rgba(239, 98, 46, 0.08) 100%);
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
            color: #3500fc;
        }

        .sesi-item {
            border: 1px solid rgba(17, 24, 39, 0.08);
            border-radius: 14px;
            padding: 14px;
            background: #ffffff;
            transition: border-color 150ms ease, transform 150ms ease;
        }

        .sesi-item:hover {
            border-color: rgba(53, 0, 252, 0.35);
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
            background: #3500fc;
            border-color: #3500fc;
            color: #ffffff;
            border-radius: 12px;
            padding: 10px 14px;
            font-weight: 700;
            transition: transform 150ms ease, box-shadow 150ms ease, background 150ms ease, border-color 150ms ease;
        }

        .btn-primary-soft:hover {
            background: #2d00d8;
            border-color: #2d00d8;
            transform: translateY(-1px);
            box-shadow: 0 14px 30px rgba(53, 0, 252, 0.25);
        }

        .btn-primary-soft:focus {
            box-shadow: 0 0 0 3px rgba(53, 0, 252, 0.25);
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
            border-color: rgba(53, 0, 252, 0.35);
            box-shadow: 0 10px 22px rgba(17, 24, 39, 0.08);
            color: #3500fc;
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
            border: 4px solid rgba(53, 0, 252, 0.15);
            border-top-color: #3500fc;
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
                            src="{{ asset('assets/images/logo.png') }}"
                            alt=""
                            width="180"
                            height="50"
                            loading="lazy"
                            decoding="async"
                        >
                    </div>

                    <div class="dashboard-topbar__right">
                        <button
                            type="button"
                            class="dashboard-icon-btn position-relative"
                            aria-label="Notifikasi"
                            title="Notifikasi (segera hadir)"
                            data-toggle="tooltip"
                            data-notification
                        >
                            <i class="far fa-bell" aria-hidden="true"></i>
                            <span class="position-absolute" style="top: 8px; right: 10px; width: 8px; height: 8px; border-radius: 999px; background: #ef3a2f;" aria-hidden="true"></span>
                        </button>

                        <a
                            href="{{ route('peserta.about') }}#faq"
                            class="dashboard-icon-btn"
                            aria-label="Bantuan (FAQ)"
                            title="Bantuan (FAQ)"
                            data-toggle="tooltip"
                        >
                            <i class="far fa-question-circle" aria-hidden="true"></i>
                        </a>

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
                        <div>
                            <div style="font-weight: 900; letter-spacing: -0.02em; font-size: 18px;">Dashboard Peserta</div>
                            <div style="color: #4b5563; font-size: 14px;">Kelola akses live dan rekaman event.</div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="w-100 float-left padding-top padding-bottom" role="main">
            <div class="container">
                @if ($pesanan->isEmpty())
                    <div class="event-card" data-aos="fade-up" data-aos-duration="700">
                        <div class="event-card__header">
                            <div class="d-flex align-items-center" style="gap: 12px;">
                                <span class="dashboard-icon-btn" aria-hidden="true" style="border-color: rgba(53, 0, 252, 0.18);">
                                    <i class="fas fa-ticket-alt" aria-hidden="true"></i>
                                </span>
                                <div>
                                    <div style="font-weight: 900; font-size: 18px;">Belum ada event</div>
                                    <div style="color: #4b5563; font-size: 14px;">Kamu belum mengikuti event apapun.</div>
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
                    <div class="row">
                        @foreach ($pesanan as $order)
                            @php
                                $event = $order->paket->event;
                            @endphp

                            <div class="col-12 col-lg-6 mb-4" data-aos="fade-up" data-aos-duration="700">
                                <section class="event-card" aria-label="Event {{ $event->judul }}">
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
                                                                    style="border-color: rgba(53, 0, 252, 0.18);"
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

            document.addEventListener('DOMContentLoaded', function () {
                initTooltips();
                initInteractions();
            });
        })();
    </script>
@endpush
