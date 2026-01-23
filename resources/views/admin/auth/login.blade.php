    <!DOCTYPE html>
    <html lang="en">


    <!-- Mirrored from ventic-html.vercel.app/page-login.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 06 Jan 2026 05:46:20 GMT -->
    <!-- Added by HTTrack -->
    <meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->

    <head>
        <!-- Title -->
        <title>Login</title>
        <!-- Meta -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="author" content="DexignZone">
        <meta name="robots" content="">

        <meta name="keywords"
            content="admin, admin panel, admin dashboard, admin template, administration, analytics, bootstrap, bootstrap admin, coupon, deal, modern, responsive admin dashboard, ticket, ticket dashboard, ticket system">
        <meta name="description"
            content="Enhance your event management with Ventic - the ultimate Event Ticketing Bootstrap 5 Admin Template. Streamline ticket sales, track attendance, and manage your events effortlessly with this powerful and user-friendly template. Elevate your event experience today!">
        <meta property="og:title" content="Ventic - Event Ticketing Bootstrap 5 Admin Template">
        <meta property="og:description"
            content="Enhance your event management with Ventic - the ultimate Event Ticketing Bootstrap 5 Admin Template. Streamline ticket sales, track attendance, and manage your events effortlessly with this powerful and user-friendly template. Elevate your event experience today! ">
        <meta property="og:image" content="../ventic.dexignzone.com/xhtml/social-image.png">
        <meta name="format-detection" content="telephone=no">

        <!-- Mobile Specific -->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <base href="{{ url('/') }}/">

        <!-- Favicon icon -->
        <link rel="shortcut icon" type="image/x-icon" href="/assetsAdmin/images/favicon.png">
        <link href="/assetsAdmin/vendor/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet">

        <!-- Localization Tool -->
        <link href="/assetsAdmin/css/jquery.localizationTool.css" rel="stylesheet">

        <!-- Style Css -->
        <link class="main-css" href="/assetsAdmin/css/style.css" rel="stylesheet">
        <style>
            .authincation-content {
                background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 50%, #22c55e 100%);
                border-radius: 20px;
            }

            .authincation-content .auth-form {
                background: #d6e094;
                border-radius: 20px;
            }
        </style>

    </head>

    <body>
        <div class="authincation ">
            <div class="container ">
                <div class="row justify-content-center align-items-center">
                    <div class="col-lg-6 col-md-8">
                        <div class="authincation-content">
                            <div class="row no-gutters">
                                <div class="col-xl-12">
                                    <div class="auth-form">
                                        <div class="text-center mb-3">
                                            <a href="index.html"><img src="assets/images/logo-full.png"
                                                    alt=""></a>
                                        </div>
                                        <h4 class="text-center mb-4">Sign in your account</h4>
                                        @if (session('success'))
                                            <div class="alert alert-success">
                                                {{ session('success') }}
                                            </div>
                                        @endif

                                        @if (session('error'))
                                            <div class="alert alert-danger">
                                                {{ session('error') }}
                                            </div>
                                        @endif

                                        @if ($errors->any())
                                            <div class="alert alert-danger">
                                                <ul class="mb-0">
                                                    @foreach ($errors->all() as $err)
                                                        <li>{{ $err }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        <form action="{{ route('admin.login.store') }}" method="POST">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="mb-1 form-label">Email</label>
                                                <input type="email" name="email" class="form-control"
                                                    value="{{ old('email') }}" autocomplete="email" required>
                                            </div>
                                            <div class="mb-3 position-relative">
                                                <label class="mb-1 form-label">Password</label>
                                                <input type="password" id="dz-password" name="password"
                                                    class="form-control" autocomplete="current-password" required>
                                                <span class="show-pass eye">

                                                    <i class="fa fa-eye-slash"></i>
                                                    <i class="fa fa-eye"></i>

                                                </span>
                                            </div>
                                            <div class="form-row d-flex  flex-wrap justify-content-between mt-4 mb-2">
                                                <div class="form-group">
                                                    <div class="form-check custom-checkbox ms-1">
                                                        <input type="checkbox" class="form-check-input"
                                                            id="basic_checkbox_1">
                                                        <label class="form-check-label" for="basic_checkbox_1">Remember
                                                            my preference</label>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <a class="text-hover" href="page-forgot-password.html">Forgot
                                                        Password?</a>
                                                </div>
                                            </div>
                                            <div class="text-center">
                                                <button type="submit" class="btn btn-primary btn-block">Sign Me
                                                    In</button>
                                            </div>
                                        </form>
                                        <div class="new-account mt-3">
                                            <p>Don't have an account? <a class="text-primary"
                                                    href="page-register.html">Sign up</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!--**********************************
        Scripts
    ***********************************-->
        <!-- Required vendors -->
        <script src="/assetsAdmin/vendor/global/global.min.js"></script>
        <script src="/assetsAdmin/vendor/bootstrap-select/js/bootstrap-select.min.js"></script>
        <script src="/assetsAdmin/js/deznav-init.js"></script>

        <!-- localizationTool -->
        <script src="/assetsAdmin/js/jquery.localizationTool.js"></script>
        <script src="/assetsAdmin/js/translator.js"></script>

        <script src="/assetsAdmin/js/custom.min.js"></script>
        <script src="/assetsAdmin/js/demo.js"></script>
        <script src="/assetsAdmin/js/styleSwitcher.js"></script>
        {{-- <script src="/assetsAdmin/js/script.js"></script> --}}
    </body>

    <!-- Mirrored from ventic-html.vercel.app/page-login.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 06 Jan 2026 05:46:21 GMT -->

    </html>
