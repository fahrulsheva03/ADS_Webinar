@extends('peserta.partials.app')

@section('title', 'Profil')

@push('styles')
    <style>
        .peserta-profile {
            background: radial-gradient(900px circle at 10% 10%, rgba(53, 0, 252, 0.10), transparent 60%),
                radial-gradient(700px circle at 90% 30%, rgba(239, 98, 46, 0.10), transparent 55%),
                linear-gradient(180deg, #f9f9fb 0%, #ffffff 100%);
            color: #111827;
            min-height: 100vh;
        }

        .header-main-con,
        .footer-main-section,
        .copyright,
        #button {
            display: none !important;
        }

        .profile-topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.9);
            border-bottom: 1px solid rgba(17, 24, 39, 0.08);
            padding-top: env(safe-area-inset-top);
        }

        .profile-topbar__bar {
            min-height: 64px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
        }

        .profile-topbar__logo {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }

        .profile-topbar__logo img {
            width: auto;
            max-height: 44px;
            max-width: 130px;
        }

        @media (min-width: 768px) {
            .profile-topbar__logo img {
                max-height: 50px;
                max-width: 180px;
            }
        }

        .profile-icon-btn {
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

        .profile-icon-btn:hover {
            transform: translateY(-1px);
            border-color: rgba(53, 0, 252, 0.35);
            box-shadow: 0 10px 22px rgba(17, 24, 39, 0.08);
            color: #3500fc;
            text-decoration: none;
        }

        .profile-icon-btn:focus {
            outline: 0;
            box-shadow: 0 0 0 3px rgba(53, 0, 252, 0.25);
        }

        .profile-card {
            border: 1px solid rgba(17, 24, 39, 0.08);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 18px 60px rgba(35, 23, 105, 0.08);
            background: #ffffff;
        }

        .profile-card__header {
            padding: 18px;
            background: linear-gradient(135deg, rgba(53, 0, 252, 0.08) 0%, rgba(236, 57, 139, 0.06) 55%, rgba(239, 98, 46, 0.08) 100%);
        }

        .profile-avatar {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            border: 1px solid rgba(17, 24, 39, 0.08);
            background: #ffffff;
            object-fit: cover;
        }

        @media (max-width: 575.98px) {
            .profile-avatar {
                width: 52px;
                height: 52px;
                border-radius: 14px;
            }
        }

        .profile-field {
            border: 1px solid rgba(17, 24, 39, 0.08);
            border-radius: 14px;
            padding: 14px;
            background: #ffffff;
        }

        .profile-label {
            color: #6b7280;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .profile-value {
            color: #111827;
            font-weight: 800;
            margin-bottom: 0;
            word-break: break-word;
        }

        .profile-loading {
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

        .profile-loading.is-visible {
            display: flex;
            opacity: 1;
        }

        .profile-loading__card {
            width: min(420px, calc(100% - 32px));
            border-radius: 18px;
            background: #ffffff;
            padding: 18px;
            border: 1px solid rgba(17, 24, 39, 0.10);
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.22);
        }

        .profile-loading__spinner {
            width: 44px;
            height: 44px;
            border-radius: 999px;
            border: 4px solid rgba(53, 0, 252, 0.15);
            border-top-color: #3500fc;
            animation: profileSpin 900ms linear infinite;
        }

        @keyframes profileSpin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .profile-icon-btn,
            .profile-loading {
                transition: none;
            }
            .profile-loading__spinner {
                animation: none;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $user = auth()->user();
    @endphp

    <div class="peserta-profile">
        <div class="profile-loading" id="profileLoading" aria-live="polite" aria-busy="true" aria-label="Memproses">
            <div class="profile-loading__card" role="status">
                <div class="d-flex align-items-center" style="gap: 14px;">
                    <div class="profile-loading__spinner" aria-hidden="true"></div>
                    <div>
                        <div style="font-weight: 800; color: #111827;">Sedang memproses…</div>
                        <div style="color: #4b5563; font-size: 14px;">Mohon tunggu sebentar.</div>
                    </div>
                </div>
            </div>
        </div>

        <header class="profile-topbar" role="banner">
            <div class="container">
                <div class="profile-topbar__bar">
                    <div class="d-flex align-items-center" style="gap: 10px;">
                        <a
                            href="{{ route('peserta.dashboard') }}"
                            class="profile-icon-btn"
                            aria-label="Kembali ke Dashboard"
                            title="Kembali"
                            data-toggle="tooltip"
                            data-loading-trigger="link"
                        >
                            <i class="fas fa-arrow-left" aria-hidden="true"></i>
                        </a>
                        <span class="d-none d-sm-inline-block" style="font-weight: 700;">Kembali</span>
                    </div>

                    <div class="profile-topbar__logo" aria-hidden="true">
                        <img
                            src="{{ asset('assets/images/logo.png') }}"
                            alt=""
                            width="180"
                            height="50"
                            loading="lazy"
                            decoding="async"
                        >
                    </div>

                    <div class="d-flex align-items-center" style="gap: 10px;">
                        <a
                            href="{{ route('peserta.about') }}#faq"
                            class="profile-icon-btn"
                            aria-label="Bantuan (FAQ)"
                            title="Bantuan (FAQ)"
                            data-toggle="tooltip"
                        >
                            <i class="far fa-question-circle" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <main class="w-100 float-left padding-top padding-bottom" role="main">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-8">
                        <section class="profile-card" aria-label="Profil">
                            <div class="profile-card__header">
                                <div class="d-flex align-items-center" style="gap: 12px;">
                                    <img
                                        src="{{ asset('assets/images/reviewer-img.png') }}"
                                        alt="Foto profil"
                                        width="64"
                                        height="64"
                                        class="profile-avatar"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                    <div>
                                        <div style="font-weight: 950; letter-spacing: -0.03em; font-size: 20px; line-height: 1.25;">
                                            {{ $user?->nama ?? 'Profil Peserta' }}
                                        </div>
                                        <div style="color: #4b5563; font-weight: 600;">
                                            {{ $user?->email ?? 'Silakan login untuk melihat detail akun.' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-3 p-md-4">
                                <div class="row">
                                    <div class="col-12 col-md-6 mb-3">
                                        <div class="profile-field" role="group" aria-label="Nama">
                                            <div class="profile-label">Nama</div>
                                            <p class="profile-value">{{ $user?->nama ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <div class="profile-field" role="group" aria-label="Email">
                                            <div class="profile-label">Email</div>
                                            <p class="profile-value">{{ $user?->email ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <div class="profile-field" role="group" aria-label="Role">
                                            <div class="profile-label">Role</div>
                                            <p class="profile-value">{{ $user?->role ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <div class="profile-field" role="group" aria-label="Status akun">
                                            <div class="profile-label">Status Akun</div>
                                            <p class="profile-value">{{ $user?->status_akun ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex flex-column flex-sm-row" style="gap: 10px;">
                                    <a
                                        href="{{ route('peserta.dashboard') }}"
                                        class="btn btn-primary"
                                        style="border-radius: 12px; font-weight: 800;"
                                        data-loading-trigger="link"
                                    >
                                        Kembali ke Dashboard
                                    </a>
                                    <a
                                        href="{{ route('peserta.contact') }}"
                                        class="btn btn-outline-secondary"
                                        style="border-radius: 12px; font-weight: 800;"
                                    >
                                        Hubungi Admin
                                    </a>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            function showLoading() {
                var el = document.getElementById('profileLoading');
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
            }

            document.addEventListener('DOMContentLoaded', function () {
                initTooltips();
                initInteractions();
            });
        })();
    </script>
@endpush

