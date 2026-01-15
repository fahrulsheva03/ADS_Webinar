<div class="header-main-con w-100 float-left">
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-light p-0 text-uppercase text-center">
            <a class="navbar-brand" href="index.html">
                <figure class="mb-0">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="logo">
                </figure>
            </a>
            <button class="navbar-toggler collapsed" type="button" data-toggle="collapse"
                data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                <span class="navbar-toggler-icon"></span>
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
                <ul class="navbar-nav text-center">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle p-0 active" href="#" id="navbarDropdown3"
                            role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            HOME
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown3">
                            <a class="dropdown-item active" href="{{ route('peserta.index') }}">HOME 01</a>
                            <a class="dropdown-item" href="{{ route('peserta.index2') }}">HOME 02</a>
                            <a class="dropdown-item" href="{{ route('peserta.index3') }}">HOME 03</a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link p-0" href="{{ route('peserta.about') }}">ABOUT</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link p-0" href="{{ route('peserta.event') }}">EVENT</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link p-0" href="{{ route('peserta.shop') }}">SHOP</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link p-0" href="{{ route('peserta.blog') }}">BLOG</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link p-0" href="{{ route('peserta.contact') }}">CONTACT</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link p-0" href="{{ route('peserta.dashboard') }}">DASHBOARD</a>
                    </li>
                </ul>
                <div class="header-contact d-flex align-items-center">
                    <div class="lets-talk-btn ">
                        <a href="{{ route('peserta.login') }}">Login <i class="fas fa-sign-in-alt"></i></a>
                    </div>


                    {{-- <div class="lets-talk-btn ">
                            <a href="">Logout <i class="fas fa-sign-out-alt"></i></a>
                        </div> --}}

                </div>
            </div>

        </nav>
    </div>
</div>
