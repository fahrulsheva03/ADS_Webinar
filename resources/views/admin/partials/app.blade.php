<!DOCTYPE html>
<html lang="en">

@include('admin.partials.header')

<body>

		<!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
        <div class="loader">
            <div class="loader--dot"></div>
            <div class="loader--dot"></div>
            <div class="loader--dot"></div>
            <div class="loader--dot"></div>
            <div class="loader--dot"></div>
            <div class="loader--dot"></div>
            <div class="loader--text"></div>
        </div>
    </div>
    <!--*******************
        Preloader end
    ********************-->

    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        @include('admin.partials.navbar')


        @include('admin.partials.sidebar')
		<!--**********************************
            Content body start
        ***********************************-->
        <div class=" content-body default-height">
            <!-- row -->
			<div class="container-fluid">
				@yield('content')
            </div>
        </div>
        <!--**********************************
            Content body end
        ***********************************-->

        @include('admin.partials.footer')

	</div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

    <!--**********************************
        Scripts
    ***********************************-->

    @include('admin.partials.script')
</body>

<!-- Mirrored from ventic-html.vercel.app/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 06 Jan 2026 05:46:00 GMT -->
</html>
