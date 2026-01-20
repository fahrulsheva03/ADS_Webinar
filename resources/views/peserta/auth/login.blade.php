<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Login Peserta</title>

        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicon/favicon-32x32.png') }}">

        <link rel="stylesheet" href="{{ asset('assets/fontawesome/all.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/bootstrap/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/mobile.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">

        <style>
            .peserta-auth {
                background: radial-gradient(900px circle at 10% 10%, rgba(53, 0, 252, 0.10), transparent 60%),
                    radial-gradient(700px circle at 90% 30%, rgba(239, 98, 46, 0.10), transparent 55%),
                    linear-gradient(180deg, #f9f9fb 0%, #ffffff 100%);
            }

            .auth-shell {
                border-radius: 24px;
                overflow: hidden;
                box-shadow: 0 18px 60px rgba(35, 23, 105, 0.10);
                border: 1px solid rgba(35, 23, 105, 0.06);
                background: #ffffff;
            }

            .auth-visual {
                position: relative;
                min-height: 520px;
                background: linear-gradient(135deg, rgba(53, 0, 252, 1) 0%, rgba(98, 46, 239, 1) 60%, rgba(236, 57, 139, 1) 120%);
                color: #fff;
            }

            .auth-visual::after {
                content: "";
                position: absolute;
                inset: 0;
                background:
                    radial-gradient(500px circle at 20% 25%, rgba(255, 255, 255, 0.22), transparent 55%),
                    radial-gradient(420px circle at 80% 70%, rgba(255, 255, 255, 0.18), transparent 60%);
                pointer-events: none;
            }

            .auth-visual-inner {
                position: relative;
                z-index: 1;
            }

            .auth-visual .brand {
                font-family: 'Raleway', sans-serif;
                font-weight: 800;
                font-size: 40px;
                line-height: 48px;
                margin: 0;
            }

            .auth-visual .tagline {
                opacity: 0.92;
                font-size: 18px;
                line-height: 28px;
                margin: 14px 0 0;
            }

            .auth-visual .hero-img {
                width: min(360px, 90%);
                height: auto;
                filter: drop-shadow(0 18px 28px rgba(0, 0, 0, 0.20));
            }

            .auth-panel {
                padding: 34px 30px;
            }

            @media (min-width: 768px) {
                .auth-panel {
                    padding: 44px 44px;
                }
            }

            .auth-logo {
                height: 44px;
                width: auto;
            }

            .auth-title {
                margin: 0;
                font-size: 30px;
                line-height: 38px;
                font-weight: 800;
                color: var(--secondary-color);
            }

            .auth-subtitle {
                margin: 10px 0 0;
                font-size: 16px;
                line-height: 24px;
                color: var(--text-color);
            }

            .auth-label {
                font-size: 14px;
                line-height: 20px;
                color: var(--secondary-color);
                font-weight: 600;
            }

            .auth-input .input-group-text {
                border-radius: 14px 0 0 14px;
                border-color: rgba(35, 23, 105, 0.12);
            }

            .auth-input .form-control {
                border-radius: 0 14px 14px 0;
                border-color: rgba(35, 23, 105, 0.12);
                padding-top: 14px;
                padding-bottom: 14px;
                font-size: 16px;
            }

            .auth-input .form-control:focus {
                border-color: rgba(53, 0, 252, 0.45);
                box-shadow: 0 0 0 0.25rem rgba(53, 0, 252, 0.12);
            }

            .auth-cta {
                border-radius: 14px;
                padding: 14px 16px;
                font-size: 16px;
                font-weight: 700;
                background: var(--button-color);
                border-color: var(--button-color);
                box-shadow: 0 12px 26px rgba(53, 0, 252, 0.22);
            }

            .auth-cta:hover {
                background: var(--light-blue);
                border-color: var(--light-blue);
                transform: translateY(-1px);
            }

            .auth-footnote {
                margin-top: 16px;
                font-size: 13px;
                line-height: 20px;
                color: rgba(109, 108, 108, 0.9);
            }

            .auth-link {
                margin-top: 14px;
                font-size: 14px;
                line-height: 20px;
                color: rgba(109, 108, 108, 0.95);
            }

            .auth-link a {
                color: var(--button-color);
                font-weight: 700;
                text-decoration: none;
            }

            .auth-link a:hover {
                color: var(--light-blue);
                text-decoration: underline;
            }
        </style>
    </head>

    <body class="peserta-auth">
        <main class="min-vh-100 d-flex align-items-center py-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-10 col-xl-9">
                        <div class="auth-shell">
                            <div class="row g-0">
                                <div class="col-lg-6 d-none d-lg-flex align-items-stretch">
                                    <div class="auth-visual w-100 d-flex align-items-center">
                                        <div class="auth-visual-inner px-5 py-5 w-100">
                                            <div class="d-flex align-items-center gap-3 mb-4">
                                                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="height: 36px; width: auto;">
                                                <div class="brand">Webinar</div>
                                            </div>
                                            <p class="tagline">Masuk untuk mengakses tiket, sesi, dan materi event Anda dengan cepat.</p>
                                            <div class="mt-4 pt-2 text-center">
                                                <img class="hero-img" src="{{ asset('assets/images/banner-right-img.png') }}" alt="Ilustrasi">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-lg-6">
                                    <div class="auth-panel">
                                        <div class="d-flex align-items-center justify-content-center justify-content-lg-start gap-3 mb-4">
                                            <img class="auth-logo" src="{{ asset('assets/images/logo.png') }}" alt="Logo">
                                        </div>

                                        <div class="mb-4 text-center text-lg-start">
                                            <h1 class="auth-title">Login Peserta</h1>
                                            <p class="auth-subtitle">Silakan masukkan email dan password Anda.</p>
                                        </div>

                                        @if (session('success'))
                                            <div class="alert alert-success" role="alert">
                                                {{ session('success') }}
                                            </div>
                                        @endif

                                        @if (session('error'))
                                            <div class="alert alert-danger" role="alert">
                                                {{ session('error') }}
                                            </div>
                                        @endif

                                        <form action="#" method="post" class="mt-3">
                                            <div class="mb-3">
                                                <label for="email" class="auth-label mb-2 d-block">Email</label>
                                                <div class="input-group auth-input">
                                                    <span class="input-group-text bg-white">
                                                        <i class="fas fa-envelope text-muted"></i>
                                                    </span>
                                                    <input id="email" name="email" type="email" class="form-control"
                                                        placeholder="Masukkan alamat email" autocomplete="email">
                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                <label for="password" class="auth-label mb-2 d-block">Password</label>
                                                <div class="input-group auth-input">
                                                    <span class="input-group-text bg-white">
                                                        <i class="fas fa-lock text-muted"></i>
                                                    </span>
                                                    <input id="password" name="password" type="password" class="form-control"
                                                        placeholder="Masukkan password" autocomplete="current-password">
                                                </div>
                                            </div>

                                            <button type="button" class="btn w-100 text-white auth-cta">
                                                Login
                                            </button>
                                        </form>

                                        <div class="auth-link text-center text-lg-start">
                                            Belum punya akun? <a href="{{ url('/registrasi') }}">Daftar</a>
                                        </div>

                                        <div class="auth-footnote text-center text-lg-start">
                                            Pastikan email benar dan password sesuai akun Anda.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>
