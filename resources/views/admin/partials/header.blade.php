
<!-- Mirrored from ventic-html.vercel.app/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 06 Jan 2026 05:44:52 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->
<head>

    <!-- Title -->
	<title>Admin Webinar</title>
	<!-- Meta -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="author" content="DexignZone">
	<meta name="robots" content="">

	<meta name="keywords" content="admin, admin panel, admin dashboard, admin template, administration, analytics, bootstrap, bootstrap admin, coupon, deal, modern, responsive admin dashboard, ticket, ticket dashboard, ticket system">
	<meta name="description" content="Enhance your event management with Ventic - the ultimate Event Ticketing Bootstrap 5 Admin Template. Streamline ticket sales, track attendance, and manage your events effortlessly with this powerful and user-friendly template. Elevate your event experience today!" >
	<meta property="og:title" content="Ventic - Event Ticketing Bootstrap 5 Admin Template">
	<meta property="og:description" content="Enhance your event management with Ventic - the ultimate Event Ticketing Bootstrap 5 Admin Template. Streamline ticket sales, track attendance, and manage your events effortlessly with this powerful and user-friendly template. Elevate your event experience today! ">
	<meta property="og:image" content="../ventic.dexignzone.com/xhtml/social-image.png">
	<meta name="format-detection" content="telephone=no">

	<!-- Mobile Specific -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<base href="{{ url('/') }}/">

	<!-- Favicon icon -->
	<link rel="shortcut icon" type="image/x-icon" href="{{ asset('assetsAdmin/images/favicon.png') }}">

	<link href="{{ asset('assetsAdmin/vendor/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet">

	<link rel="stylesheet" href="{{ asset('assetsAdmin/vendor/chartist/css/chartist.min.css') }}">
	<link href="{{ asset('assetsAdmin/vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">

	<!-- Localization Tool -->
	<link href="{{ asset('assetsAdmin/css/jquery.localizationTool.css') }}" rel="stylesheet">

	<!-- Style Css -->
	<link class="main-css" href="{{ asset('assetsAdmin/css/style.css') }}" rel="stylesheet">

	<script>
		(function () {
			try {
				var d = new Date();
				d.setTime(d.getTime() + 30 * 60 * 1000);
				var expires = 'expires=' + d.toUTCString();
				document.cookie = 'sidebarStyle=full;' + expires + ';path=/';
				document.cookie = 'sidebarPosition=fixed;' + expires + ';path=/';
			} catch (e) {}
		})();
	</script>

	<style>
		:root {
			--primary: #F80000;
			--secondary: #2A2A2A;
			--primary-hover: #C50000;
			--primary-light: #FF4D4D;
			--primary-dark: #9A0000;
			--rgba-primary-03: rgba(248, 0, 0, 0.03);
			--rgba-primary-1: rgba(248, 0, 0, 0.10);
			--rgba-primary-2: rgba(248, 0, 0, 0.20);
			--rgba-primary-3: rgba(248, 0, 0, 0.30);
			--rgba-primary-4: rgba(248, 0, 0, 0.40);
			--rgba-primary-5: rgba(248, 0, 0, 0.50);
			--rgba-primary-6: rgba(248, 0, 0, 0.60);
			--rgba-primary-7: rgba(248, 0, 0, 0.70);
			--rgba-primary-8: rgba(248, 0, 0, 0.80);
			--rgba-primary-9: rgba(248, 0, 0, 0.90);
			--admin-bg: #F4F5F7;
			--admin-surface: #FFFFFF;
			--admin-text: #1B1B1B;
			--admin-text-muted: #5C5C5C;
			--admin-border: #E5E7EB;
		}

		html, body {
			background-color: var(--admin-bg);
			color: var(--admin-text);
		}

		.content-body {
			background-color: transparent;
		}

		.card,
		.modal-content,
		.dropdown-menu {
			background: linear-gradient(180deg, var(--admin-surface) 0%, var(--admin-bg) 100%);
			border-color: var(--admin-border);
		}

		.card-header,
		.card-footer,
		.modal-header,
		.modal-footer {
			background: linear-gradient(90deg, rgba(248, 0, 0, 0.08) 0%, rgba(42, 42, 42, 0.04) 100%);
			border-color: var(--admin-border);
		}

		.nav-header,
		.header {
			position: relative;
			isolation: isolate;
			background: #292929;
		}

		.nav-header::before,
		.header::before {
			content: "";
			position: absolute;
			inset: 0;
			z-index: 0;
			background:
				radial-gradient(900px 180px at 18% 35%, rgba(255, 255, 255, 0.10) 0%, rgba(255, 255, 255, 0.00) 60%),
				linear-gradient(180deg, rgba(255, 255, 255, 0.06) 0%, rgba(0, 0, 0, 0.12) 70%, rgba(0, 0, 0, 0.20) 100%);
			opacity: 0.95;
			pointer-events: none;
		}

		.nav-header > *,
		.header > * {
			position: relative;
			z-index: 1;
		}

		.nav-header .brand-logo .svg-logo-rect {
			fill: rgba(248, 0, 0, 0.95);
		}

		.nav-header .brand-logo .svg-logo-text {
			fill: #FFFFFF;
		}

		.header,
		.header a,
		.header .nav-link,
		.nav-header a,
		.nav-header .nav-control .hamburger .line {
			color: #FFFFFF;
		}

		.header .nav-link svg path,
		.header .nav-link svg circle,
		.header .nav-link svg rect {
			fill: rgba(255, 255, 255, 0.85);
		}

		.deznav {
			background: linear-gradient(180deg, #2A2A2A 0%, #1F1F1F 100%);
		}

		.deznav .metismenu a,
		.deznav .metismenu a i,
		.deznav .metismenu a .nav-text {
			color: rgba(244, 245, 247, 0.92);
		}

		.deznav .metismenu li > a:hover {
			background-color: rgba(248, 0, 0, 0.12);
		}

		.deznav .metismenu li.mm-active > a,
		.deznav .metismenu li.mm-active > a:focus,
		.deznav .metismenu li.mm-active > a:hover {
			background-color: rgba(248, 0, 0, 0.18);
			color: #FFFFFF;
		}

		.deznav .metismenu li.mm-active > a i {
			color: rgba(255, 255, 255, 0.95);
		}

		.btn-primary {
			background: linear-gradient(135deg, #F80000 0%, #C50000 55%, #2A2A2A 140%);
			border-color: rgba(248, 0, 0, 0.65);
		}

		.btn-primary:hover,
		.btn-primary:focus {
			background: linear-gradient(135deg, #FF1A1A 0%, #F80000 55%, #2A2A2A 140%);
			border-color: rgba(248, 0, 0, 0.75);
		}

		.btn-outline-primary {
			color: var(--primary);
			border-color: rgba(248, 0, 0, 0.55);
			background-color: transparent;
		}

		.btn-outline-primary:hover,
		.btn-outline-primary:focus {
			color: #FFFFFF;
			background: linear-gradient(135deg, #F80000 0%, #C50000 70%);
			border-color: rgba(248, 0, 0, 0.75);
		}

		.badge.bg-primary,
		.bg-primary {
			background-color: var(--primary) !important;
		}

		.text-primary {
			color: var(--primary) !important;
		}

		.form-control:focus,
		.form-select:focus,
		.btn:focus {
			border-color: rgba(248, 0, 0, 0.55);
			box-shadow: 0 0 0 0.2rem rgba(248, 0, 0, 0.15);
		}

		::selection {
			background: rgba(248, 0, 0, 0.25);
		}

		.DZ-theme-btn { display: none !important; }
		.sidebar-right,
		.sidebar-right-trigger,
		.dz-demo-panel,
		.dz-demo-trigger { display: none !important; }
	</style>

</head>
