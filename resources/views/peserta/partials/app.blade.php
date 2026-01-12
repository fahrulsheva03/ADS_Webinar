<!DOCTYPE html>
<html lang="zxx">


<!-- Mirrored from html.designingmedia.com/confx/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 06 Jan 2026 05:30:26 GMT -->
@include('peserta.partials.header')

<body>
<!-- Preloader -->
<div class="loader-mask">
    <div class="loader">
        <div></div>
        <div></div>
    </div>
</div>
<!-- Preloader -->
    <!-- Navbar START-->
    @include('peserta.partials.navbar')
    <!-- Navbar END -->

    {{-- Content Start--}}
    @yield('content')
    {{-- Content End--}}


    <!-- FOOTER SECTION START -->
    @include('peserta.partials.footer')
    <!-- FOOTER SECTION END -->
    @include('peserta.partials.script')
</body>


<!-- Mirrored from html.designingmedia.com/confx/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 06 Jan 2026 05:30:53 GMT -->
</html>
