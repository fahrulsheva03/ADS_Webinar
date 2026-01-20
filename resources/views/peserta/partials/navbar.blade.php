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

                    <li class="nav-item">
                        <a class="nav-link p-0 {{ request()->routeIs('peserta.index') ? 'active' : '' }}"
                            href="{{ route('peserta.index') }}">HOME</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link p-0 {{ request()->routeIs('peserta.about') ? 'active' : '' }}"
                            href="{{ route('peserta.about') }}">ABOUT</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link p-0 {{ request()->routeIs('peserta.event') ? 'active' : '' }}"
                            href="{{ route('peserta.event') }}">EVENT</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link p-0 {{ request()->routeIs('peserta.shop') ? 'active' : '' }}"
                            href="{{ route('peserta.shop') }}">SHOP</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link p-0 {{ request()->routeIs('peserta.blog') ? 'active' : '' }}"
                            href="{{ route('peserta.blog') }}">BLOG</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link p-0 {{ request()->routeIs('peserta.contact') ? 'active' : '' }}"
                            href="{{ route('peserta.contact') }}">CONTACT</a>
                    </li>

                    @auth
                        <li class="nav-item">
                            <a class="nav-link p-0 {{ request()->routeIs('peserta.dashboard') ? 'active' : '' }}"
                                href="{{ route('peserta.dashboard') }}">DASHBOARD</a>
                        </li>
                    @endauth
                </ul>
                <div class="header-contact d-flex align-items-center">
                    @guest
                        <div class="lets-talk-btn ">
                            <a href="{{ route('peserta.login') }}">Login <i class="fas fa-sign-in-alt"></i></a>
                        </div>
                    @endguest

                    @auth
                        <div class="lets-talk-btn ">
                            <form action="{{ route('peserta.logout') }}" method="post">
                                @csrf
                                <a href="{{ route('peserta.logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                                    Logout <i class="fas fa-sign-out-alt"></i>
                                </a>
                            </form>
                        </div>
                    @endauth


                    {{-- <div class="lets-talk-btn ">
                            <a href="">Logout <i class="fas fa-sign-out-alt"></i></a>
                        </div> --}}

                </div>
            </div>

        </nav>
    </div>
</div>
