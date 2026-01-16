<div class="deznav">
    <div class="deznav-scroll">
        <ul class="metismenu" id="menu" aria-label="Navigasi admin">
            <li class="nav-label first">Dashboard</li>
            <li>
                <a href="{{ route('admin.index') }}" class="ai-icon" aria-expanded="false">
                    <i class="flaticon-025-dashboard" aria-hidden="true"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li>
                <a class="has-arrow ai-icon" href="javascript:void(0)" aria-expanded="false">
                    <i class="la la-bolt" aria-hidden="true"></i>
                    <span class="nav-text">Quick Actions</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.index', ['tab' => 'events', 'modal' => 'eventModal']) }}">Buat Event</a></li>
                    <li><a href="{{ route('admin.index', ['tab' => 'scan-qr', 'modal' => 'scanQrModal']) }}">Buka Scan QR</a></li>
                </ul>
            </li>

            <li class="nav-label">Manajemen</li>
            <li>
                <a class="has-arrow ai-icon" href="javascript:void(0)" aria-expanded="false">
                    <i class="la la-calendar-alt" aria-hidden="true"></i>
                    <span class="nav-text">Event</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.index', ['tab' => 'events']) }}">Daftar Event</a></li>
                    <li><a href="{{ route('admin.index', ['tab' => 'events', 'modal' => 'eventModal']) }}">Buat Event</a></li>
                </ul>
            </li>
            <li>
                <a href="{{ route('admin.events.index') }}" class="ai-icon" aria-expanded="false">
                    <i class="la la-stream" aria-hidden="true"></i>
                    <span class="nav-text">Event </span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.index', ['tab' => 'sessions']) }}" class="ai-icon" aria-expanded="false">
                    <i class="la la-stream" aria-hidden="true"></i>
                    <span class="nav-text">Sesi Event</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.index', ['tab' => 'packages']) }}" class="ai-icon" aria-expanded="false">
                    <i class="la la-box" aria-hidden="true"></i>
                    <span class="nav-text">Paket & Akses</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.index', ['tab' => 'participants']) }}#participants" class="ai-icon" aria-expanded="false">
                    <i class="la la-users" aria-hidden="true"></i>
                    <span class="nav-text">Peserta</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.index', ['tab' => 'participants']) }}#transactions" class="ai-icon" aria-expanded="false">
                    <i class="la la-credit-card" aria-hidden="true"></i>
                    <span class="nav-text">Transaksi</span>
                </a>
            </li>

            <li class="nav-label">Operasional</li>
            <li>
                <a href="{{ route('admin.index', ['tab' => 'live-session', 'modal' => 'liveSessionModal']) }}" class="ai-icon" aria-expanded="false">
                    <i class="la la-broadcast-tower" aria-hidden="true"></i>
                    <span class="nav-text">Live Session</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.index', ['tab' => 'scan-qr', 'modal' => 'scanQrModal']) }}" class="ai-icon" aria-expanded="false">
                    <i class="la la-qrcode" aria-hidden="true"></i>
                    <span class="nav-text">Scan QR</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.index', ['tab' => 'recordings', 'modal' => 'recordingModal']) }}" class="ai-icon" aria-expanded="false">
                    <i class="la la-video" aria-hidden="true"></i>
                    <span class="nav-text">Rekaman & Video</span>
                </a>
            </li>

            <li class="nav-label">Laporan</li>
            <li>
                <a href="{{ route('admin.index', ['tab' => 'reporting']) }}" class="ai-icon" aria-expanded="false">
                    <i class="la la-chart-bar" aria-hidden="true"></i>
                    <span class="nav-text">Reporting</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.index', ['tab' => 'attendance-report', 'modal' => 'attendanceModal']) }}" class="ai-icon" aria-expanded="false">
                    <i class="la la-clipboard-check" aria-hidden="true"></i>
                    <span class="nav-text">Laporan Kehadiran</span>
                </a>
            </li>

            <li class="nav-label">Akun</li>
            <li>
                <a class="has-arrow ai-icon" href="javascript:void(0)" aria-expanded="false">
                    <i class="la la-cog" aria-hidden="true"></i>
                    <span class="nav-text">Pengaturan</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.index', ['modal' => 'adminProfileModal']) }}">Profil Admin</a></li>
                    <li><a href="{{ route('admin.login') }}">Logout</a></li>
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
