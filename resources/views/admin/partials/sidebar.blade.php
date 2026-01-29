<div class="deznav">
    <div class="deznav-scroll">
        <ul class="metismenu" id="menu" aria-label="Navigasi admin">
            <li class="nav-label first">Dashboard</li>
            <li>
                <a href="{{ route('admin.index') }}" class="ai-icon" aria-expanded="false">
                    <i class="flaticon-381-app" aria-hidden="true"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>


            <li class="nav-label">Manajemen</li>

            <li>
                <a href="{{ route('admin.events.index') }}" class="ai-icon" aria-expanded="false">
                    <i class="flaticon-381-calendar" aria-hidden="true"></i>
                    <span class="nav-text">Event </span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.sesi-event.index') }}" class="ai-icon" aria-expanded="false">
                    <i class="flaticon-381-list" aria-hidden="true"></i>
                    <span class="nav-text">Sesi Event</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.paket.index') }}" class="ai-icon" aria-expanded="false">
                    <i class="flaticon-381-box" aria-hidden="true"></i>
                    <span class="nav-text">Paket & Akses</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('admin.konten-halaman.home') }}" class="ai-icon" aria-expanded="false">
                    <i class="flaticon-381-notepad" aria-hidden="true"></i>
                    <span class="nav-text">Konten Halaman</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('admin.news.index') }}" class="ai-icon" aria-expanded="false">
                    <i class="flaticon-381-newspaper" aria-hidden="true"></i>
                    <span class="nav-text">News</span>
                </a>
            </li>
            <li>
                <a href="{{ route('speakers.index') }}" class="ai-icon" aria-expanded="false">
                    <i class="flaticon-381-microphone-1" aria-hidden="true"></i>
                    <span class="nav-text">Speakers</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.ebooks.index') }}" class="ai-icon" aria-expanded="false">
                    <i class="flaticon-381-book" aria-hidden="true"></i>
                    <span class="nav-text">E-book</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.peserta.index') }}" class="ai-icon" aria-expanded="false">
                    <i class="flaticon-381-user" aria-hidden="true"></i>
                    <span class="nav-text">Peserta</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.transaksi.index') }}" class="ai-icon" aria-expanded="false">
                    <i class="flaticon-381-calculator" aria-hidden="true"></i>
                    <span class="nav-text">Transaksi</span>
                </a>
            </li>

            <li class="nav-label">Operasional</li>
            <li>
                <a href="{{ route('admin.live.index') }}" class="ai-icon" aria-expanded="false">
                    <i class="flaticon-381-microphone" aria-hidden="true"></i>
                    <span class="nav-text">Live Session</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.scan.index') }}" class="ai-icon" aria-expanded="false">
                    <i class="flaticon-381-search" aria-hidden="true"></i>
                    <span class="nav-text">Scan QR</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.video.index') }}" class="ai-icon" aria-expanded="false">
                    <i class="flaticon-381-video-camera" aria-hidden="true"></i>
                    <span class="nav-text">Rekaman & Video</span>
                </a>
            </li>

            <li class="nav-label">Laporan</li>
            <li>
                <a href="{{ route('admin.laporan.transaksi.index') }}" class="ai-icon" aria-expanded="false">
                    <i class="flaticon-381-notebook" aria-hidden="true"></i>
                    <span class="nav-text">Reporting</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.laporan.kehadiran.index') }}" class="ai-icon" aria-expanded="false">
                    <i class="flaticon-381-file" aria-hidden="true"></i>
                    <span class="nav-text">Laporan Kehadiran</span>
                </a>
            </li>

            <li class="nav-label">Akun</li>
            <li>
                <a class="has-arrow ai-icon" href="javascript:void(0)" aria-expanded="false">
                    <i class="flaticon-381-settings" aria-hidden="true"></i>
                    <span class="nav-text">Pengaturan</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.index', ['modal' => 'adminProfileModal']) }}">Profil Admin</a></li>
                    <li>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <a href="{{ route('admin.logout') }}"
                                onclick="event.preventDefault(); this.closest('form').submit();">Logout</a>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>

        <div class="plus-box">
            <p class="fs-16 font-w500 mb-3">Operasional hari ini</p>
            <a class="text-white fs-14" href="{{ route('admin.index', ['tab' => 'operations']) }}">Buka panel monitoring</a>
        </div>

        <div class="copyright">
            <p><strong>Admin Webinar</strong> © <span class="current-year">2026</span></p>
        </div>
    </div>
</div>
