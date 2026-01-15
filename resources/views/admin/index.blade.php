@extends('admin.partials.app')

@section('content')
    @php
        $now = now();
        $badgeClass = function ($tone) {
            $tone = strtolower((string) $tone);
            $className = "badge bg-{$tone}";
            if (in_array($tone, ['warning', 'light'], true)) {
                $className .= ' text-dark';
            }
            return $className;
        };

        $ringkasan = [
            [
                'label' => 'Total Event Aktif',
                'value' => 6,
                'delta' => 'stabil',
                'tone' => 'success',
                'chartId' => 'widgetChart1',
            ],
            [
                'label' => 'Event Hari Ini',
                'value' => 2,
                'delta' => '1 online, 1 offline',
                'tone' => 'primary',
                'chartId' => 'widgetChart2',
            ],
            [
                'label' => 'Peserta Hari Ini',
                'value' => 57,
                'delta' => '+12 vs kemarin',
                'tone' => 'success',
                'chartId' => null,
            ],
            [
                'label' => 'Tiket Dukungan',
                'value' => 4,
                'delta' => '1 prioritas tinggi',
                'tone' => 'warning',
                'chartId' => null,
            ],
        ];

        $contohEvents = [
            [
                'nama' => 'Webinar Keamanan Aplikasi',
                'mulai' => '2026-02-04 09:00',
                'selesai' => '2026-02-04 12:00',
                'status' => 'Terjadwal',
                'statusTone' => 'primary',
            ],
            [
                'nama' => 'Workshop Laravel untuk Tim',
                'mulai' => '2026-02-10 13:00',
                'selesai' => '2026-02-10 16:00',
                'status' => 'Draft',
                'statusTone' => 'secondary',
            ],
            [
                'nama' => 'Kelas Offline: QA & UAT',
                'mulai' => '2026-01-22 10:00',
                'selesai' => '2026-01-22 15:30',
                'status' => 'Berjalan',
                'statusTone' => 'success',
            ],
        ];

        $contohSesi = [
            [
                'judul' => 'Pembukaan & Orientasi',
                'mulai' => '09:00',
                'selesai' => '09:15',
                'status' => 'Siap',
                'statusTone' => 'success',
            ],
            [
                'judul' => 'Materi: Hardening & OWASP Top 10',
                'mulai' => '09:15',
                'selesai' => '10:45',
                'status' => 'Terjadwal',
                'statusTone' => 'primary',
            ],
            [
                'judul' => 'Q&A',
                'mulai' => '10:45',
                'selesai' => '11:15',
                'status' => 'Menunggu',
                'statusTone' => 'warning',
            ],
        ];

        $contohPaket = [
            [
                'nama' => 'Basic',
                'akses' => 'Live 1x + Rekaman 7 hari',
                'harga' => 'Rp 99.000',
                'aktif' => true,
            ],
            [
                'nama' => 'Pro',
                'akses' => 'Live + Rekaman 30 hari + Sertifikat',
                'harga' => 'Rp 199.000',
                'aktif' => true,
            ],
            [
                'nama' => 'Enterprise',
                'akses' => 'Kustom (SLA, Whitelist, SSO)',
                'harga' => 'Hubungi',
                'aktif' => false,
            ],
        ];

        $contohPeserta = [
            [
                'nama' => 'Siti Aulia',
                'email' => 'siti@example.test',
                'paket' => 'Pro',
                'status' => 'Aktif',
                'statusTone' => 'success',
            ],
            [
                'nama' => 'Budi Santoso',
                'email' => 'budi@example.test',
                'paket' => 'Basic',
                'status' => 'Menunggu verifikasi',
                'statusTone' => 'warning',
            ],
        ];

        $contohTransaksi = [
            [
                'kode' => 'INV-2026-00012',
                'peserta' => 'Siti Aulia',
                'nominal' => 'Rp 199.000',
                'status' => 'Paid',
                'statusTone' => 'success',
                'waktu' => '2026-01-14 16:12',
            ],
            [
                'kode' => 'INV-2026-00013',
                'peserta' => 'Budi Santoso',
                'nominal' => 'Rp 99.000',
                'status' => 'Pending',
                'statusTone' => 'warning',
                'waktu' => '2026-01-15 09:31',
            ],
        ];

        $contohTiket = [
            [
                'kode' => 'TCK-1024',
                'judul' => 'Peserta tidak bisa join Zoom',
                'prioritas' => 'Tinggi',
                'prioritasTone' => 'danger',
                'status' => 'Open',
                'statusTone' => 'warning',
                'updated' => '5 menit lalu',
            ],
            [
                'kode' => 'TCK-1021',
                'judul' => 'Konfirmasi pembayaran tertunda',
                'prioritas' => 'Sedang',
                'prioritasTone' => 'warning',
                'status' => 'Investigasi',
                'statusTone' => 'primary',
                'updated' => '1 jam lalu',
            ],
        ];

        $requestedTab = (string) request()->query('tab', 'events');
        $tabToModule = [
            'events' => 'events',
            'sessions' => 'sessions',
            'packages' => 'packages',
            'participants' => 'participants',
            'transactions' => 'participants',
            'operations' => 'operations',
            'live-session' => 'operations',
            'scan-qr' => 'operations',
            'recordings' => 'operations',
            'reporting' => 'reporting',
            'attendance-report' => 'reporting',
        ];
        $activeModule = $tabToModule[$requestedTab] ?? 'events';
    @endphp

    <a class="visually-hidden-focusable" href="#main-content">Lewati ke konten utama</a>

    <main id="main-content" tabindex="-1" class="pb-4" role="main" aria-label="Dashboard admin">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div class="me-auto">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <h1 class="h3 mb-0">Dashboard Admin</h1>
                    <span class="badge bg-light text-dark">Terakhir diperbarui: {{ $now->format('d M Y H:i') }}</span>
                </div>
                <p class="mb-0 text-muted">Kelola event, sesi, paket, peserta, operasi, dan laporan dari satu tempat.</p>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <div class="input-group search-area2" style="min-width: 260px;">
                    <label class="input-group-text" for="dashboard-search" aria-label="Cari">
                        <i class="flaticon-381-search-2 text-primary" aria-hidden="true"></i>
                    </label>
                    <input
                        id="dashboard-search"
                        type="search"
                        class="form-control"
                        placeholder="Cari event, sesi, peserta, transaksi…"
                        autocomplete="off"
                    >
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#dashboardHelpModal">
                    Bantuan
                </button>
            </div>
        </div>

        <section class="mb-4" aria-label="Aksi cepat">
            <div class="card">
                <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <div class="fw-semibold">Aksi cepat</div>
                        <div class="text-muted">Buat perubahan tanpa pindah halaman.</div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#eventModal">
                            Buat event
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#scanQrModal">
                            Buka Scan QR
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#sessionModal">
                            Jadwalkan sesi
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#packageModal">
                            Tambah paket
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#participantModal">
                            Daftarkan peserta
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#supportTicketModal">
                            Buat tiket dukungan
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#reportModal">
                            Buat laporan
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section class="mb-4" aria-label="Ringkasan utama">
            <div class="row">
                @foreach ($ringkasan as $item)
                    <div class="col-12 col-sm-6 col-xxl-3 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div>
                                        <div class="text-muted">{{ $item['label'] }}</div>
                                        <div class="d-flex align-items-baseline gap-2">
                                            <div class="fs-30 fw-semibold text-black">{{ $item['value'] }}</div>
                                            <span class="{{ $badgeClass($item['tone']) }}">{{ $item['delta'] }}</span>
                                        </div>
                                    </div>
                                    @if ($item['chartId'])
                                        <div class="ms-auto" style="min-width: 130px;">
                                            <div id="{{ $item['chartId'] }}" style="height: 70px;"></div>
                                        </div>
                                    @else
                                        <div class="d-inline-block position-relative donut-chart-sale" aria-hidden="true">
                                            <span class="donut1" data-peity='{ "fill": ["var(--primary)", "rgba(240, 240, 240)"], "innerRadius": 35, "radius": 10 }'>5/8</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="mb-4" aria-label="Pusat kendali modul">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <h2 class="h5 mb-0">Pusat Kendali</h2>
                        <div class="text-muted">Navigasi cepat per modul.</div>
                    </div>
                </div>
                <div class="card-body pt-3">
                    <ul class="nav nav-pills flex-wrap gap-2" id="admin-module-tabs" role="tablist" aria-label="Tab modul admin">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if ($activeModule === 'events') active @endif" id="tab-events" data-bs-toggle="tab" data-bs-target="#pane-events" type="button" role="tab" aria-controls="pane-events" aria-selected="{{ $activeModule === 'events' ? 'true' : 'false' }}">
                                Event
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if ($activeModule === 'sessions') active @endif" id="tab-sessions" data-bs-toggle="tab" data-bs-target="#pane-sessions" type="button" role="tab" aria-controls="pane-sessions" aria-selected="{{ $activeModule === 'sessions' ? 'true' : 'false' }}">
                                Sesi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if ($activeModule === 'packages') active @endif" id="tab-packages" data-bs-toggle="tab" data-bs-target="#pane-packages" type="button" role="tab" aria-controls="pane-packages" aria-selected="{{ $activeModule === 'packages' ? 'true' : 'false' }}">
                                Paket & Akses
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if ($activeModule === 'participants') active @endif" id="tab-participants" data-bs-toggle="tab" data-bs-target="#pane-participants" type="button" role="tab" aria-controls="pane-participants" aria-selected="{{ $activeModule === 'participants' ? 'true' : 'false' }}">
                                Peserta & Transaksi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if ($activeModule === 'operations') active @endif" id="tab-operations" data-bs-toggle="tab" data-bs-target="#pane-operations" type="button" role="tab" aria-controls="pane-operations" aria-selected="{{ $activeModule === 'operations' ? 'true' : 'false' }}">
                                Live & Offline
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if ($activeModule === 'reporting') active @endif" id="tab-reporting" data-bs-toggle="tab" data-bs-target="#pane-reporting" type="button" role="tab" aria-controls="pane-reporting" aria-selected="{{ $activeModule === 'reporting' ? 'true' : 'false' }}">
                                Laporan
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content pt-4" id="admin-module-tab-content">
                        <div class="tab-pane fade @if ($activeModule === 'events') show active @endif" id="pane-events" role="tabpanel" aria-labelledby="tab-events" tabindex="0">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                <div>
                                    <div class="fw-semibold text-black">Event Control</div>
                                    <div class="text-muted">Kelola event, jadwal, dan status.</div>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#eventModal">Tambah event</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#eventBulkModal">Aksi massal</button>
                                </div>
                            </div>

                            <div class="table-responsive" data-dashboard-search-scope>
                                <table class="table table-striped align-middle mb-0" aria-label="Daftar event">
                                    <thead>
                                        <tr>
                                            <th scope="col">Nama</th>
                                            <th scope="col">Jadwal</th>
                                            <th scope="col">Status</th>
                                            <th scope="col" class="text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($contohEvents as $event)
                                            <tr>
                                                <th scope="row" class="text-black">{{ $event['nama'] }}</th>
                                                <td class="text-muted">
                                                    {{ \Carbon\Carbon::parse($event['mulai'])->format('d M Y H:i') }}
                                                    <span class="text-muted">–</span>
                                                    {{ \Carbon\Carbon::parse($event['selesai'])->format('H:i') }}
                                                </td>
                                                <td>
                                                    <span class="{{ $badgeClass($event['statusTone']) }}">{{ $event['status'] }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <div class="btn-group" role="group" aria-label="Aksi event">
                                                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#eventModal">Edit</button>
                                                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">Hapus</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12 col-xl-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-header border-0 pb-0">
                                            <h3 class="h6 mb-0">Status & Penjadwalan</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex flex-column gap-3">
                                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                                    <div>
                                                        <div class="text-black fw-semibold">Event berjalan</div>
                                                        <div class="text-muted">Pantau progress operasional event yang sedang live.</div>
                                                    </div>
                                                    <span class="badge bg-success">Online</span>
                                                </div>
                                                <div>
                                                    <div class="d-flex justify-content-between">
                                                        <span class="text-muted">Kesiapan sesi</span>
                                                        <span class="text-black fw-semibold">8/10</span>
                                                    </div>
                                                    <div class="progress" style="height: 6px;">
                                                        <div class="progress-bar bg-success" role="progressbar" style="width: 80%;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="d-flex justify-content-between">
                                                        <span class="text-muted">Penjualan paket</span>
                                                        <span class="text-black fw-semibold">62%</span>
                                                    </div>
                                                    <div class="progress" style="height: 6px;">
                                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 62%;" aria-valuenow="62" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                                <div class="text-muted">
                                                    Gunakan tombol <span class="text-black fw-semibold">Tambah event</span> untuk membuat draft, lalu set status ke <span class="text-black fw-semibold">Terjadwal</span> saat siap rilis.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-xl-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-header border-0 pb-0">
                                            <h3 class="h6 mb-0">Kontrol cepat</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-12 col-md-6">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <input class="form-check-input mt-0" type="checkbox" id="toggle-public" checked>
                                                        <label class="form-check-label text-black" for="toggle-public">Event publik</label>
                                                    </div>
                                                    <div class="text-muted">Tampilkan event pada halaman peserta.</div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <input class="form-check-input mt-0" type="checkbox" id="toggle-registration" checked>
                                                        <label class="form-check-label text-black" for="toggle-registration">Registrasi dibuka</label>
                                                    </div>
                                                    <div class="text-muted">Izinkan peserta mendaftar.</div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="alert alert-light mb-0" role="note">
                                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                                            <div>
                                                                <div class="text-black fw-semibold">Saran</div>
                                                                <div class="text-muted">Jadwalkan sesi minimal H-1 untuk menghindari bentrok slot.</div>
                                                            </div>
                                                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#sessionModal">Atur sesi</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade @if ($activeModule === 'sessions') show active @endif" id="pane-sessions" role="tabpanel" aria-labelledby="tab-sessions" tabindex="0">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                <div>
                                    <div class="fw-semibold text-black">Session Control</div>
                                    <div class="text-muted">Penjadwalan sesi, alokasi slot, dan monitoring status.</div>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#sessionModal">Tambah sesi</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#slotModal">Alokasi slot</button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 col-xl-7 mb-3" data-dashboard-search-scope>
                                    <div class="card h-100">
                                        <div class="card-header border-0 pb-0">
                                            <h3 class="h6 mb-0">Jadwal hari ini</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="list-group">
                                                @foreach ($contohSesi as $sesi)
                                                    <div class="list-group-item d-flex flex-wrap align-items-center justify-content-between gap-2">
                                                        <div class="me-auto">
                                                            <div class="text-black fw-semibold">{{ $sesi['judul'] }}</div>
                                                            <div class="text-muted">{{ $sesi['mulai'] }} – {{ $sesi['selesai'] }}</div>
                                                        </div>
                                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                                            <span class="{{ $badgeClass($sesi['statusTone']) }}">{{ $sesi['status'] }}</span>
                                                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#sessionModal">Edit</button>
                                                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#operationNoteModal">Catatan</button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-xl-5 mb-3">
                                    <div class="card h-100">
                                        <div class="card-header border-0 pb-0">
                                            <h3 class="h6 mb-0">Monitoring status sesi</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-muted">Terjadwal</span>
                                                    <span class="text-black fw-semibold">12</span>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 60%;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-muted">Siap</span>
                                                    <span class="text-black fw-semibold">5</span>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                            <div class="mb-0">
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-muted">Butuh perhatian</span>
                                                    <span class="text-black fw-semibold">1</span>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 5%;" aria-valuenow="5" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>

                                            <div class="alert alert-light mt-4 mb-0" role="note">
                                                <div class="text-black fw-semibold">Tip alokasi slot</div>
                                                <div class="text-muted">Gunakan buffer 5–10 menit antarsesi untuk mitigasi keterlambatan.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade @if ($activeModule === 'packages') show active @endif" id="pane-packages" role="tabpanel" aria-labelledby="tab-packages" tabindex="0">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                <div>
                                    <div class="fw-semibold text-black">Package & Access Control</div>
                                    <div class="text-muted">Konfigurasi paket, level akses, dan izin.</div>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#packageModal">Tambah paket</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#permissionModal">Kelola izin</button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 col-xl-8 mb-3" data-dashboard-search-scope>
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0" aria-label="Daftar paket">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Paket</th>
                                                    <th scope="col">Akses</th>
                                                    <th scope="col">Harga</th>
                                                    <th scope="col">Aktif</th>
                                                    <th scope="col" class="text-end">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($contohPaket as $paket)
                                                    <tr>
                                                        <th scope="row" class="text-black">{{ $paket['nama'] }}</th>
                                                        <td class="text-muted">{{ $paket['akses'] }}</td>
                                                        <td class="text-black">{{ $paket['harga'] }}</td>
                                                        <td>
                                                            <div class="form-check form-switch m-0">
                                                                <input class="form-check-input" type="checkbox" role="switch" @checked($paket['aktif']) aria-label="Aktifkan paket {{ $paket['nama'] }}">
                                                            </div>
                                                        </td>
                                                        <td class="text-end">
                                                            <div class="btn-group" role="group" aria-label="Aksi paket">
                                                                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#packageModal">Edit</button>
                                                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">Hapus</button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-12 col-xl-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-header border-0 pb-0">
                                            <h3 class="h6 mb-0">Kebijakan langganan</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label class="form-label text-black" for="subscription-window">Masa akses rekaman (hari)</label>
                                                <input id="subscription-window" type="number" class="form-control" value="30" min="1" max="365">
                                                <div class="form-text">Berlaku untuk paket yang mengizinkan rekaman.</div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-black" for="access-level">Level akses default</label>
                                                <select id="access-level" class="form-select">
                                                    <option value="basic">Basic</option>
                                                    <option value="pro" selected>Pro</option>
                                                    <option value="enterprise">Enterprise</option>
                                                </select>
                                            </div>
                                            <div class="d-flex flex-wrap gap-2">
                                                <button type="button" class="btn btn-primary btn-sm">Simpan</button>
                                                <button type="button" class="btn btn-outline-secondary btn-sm">Reset</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade @if ($activeModule === 'participants') show active @endif" id="pane-participants" role="tabpanel" aria-labelledby="tab-participants" tabindex="0">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                <div>
                                    <div class="fw-semibold text-black">Participants & Transactions</div>
                                    <div class="text-muted">Kelola peserta, histori transaksi, dan status pembayaran.</div>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#participantModal">Tambah peserta</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#transactionModal">Catat transaksi</button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 col-xl-6 mb-3" data-dashboard-search-scope id="participants">
                                    <div class="card h-100">
                                        <div class="card-header border-0 pb-0">
                                            <h3 class="h6 mb-0">Peserta</h3>
                                        </div>
                                        <div class="card-body pt-3">
                                            <div class="table-responsive">
                                                <table class="table table-hover align-middle mb-0" aria-label="Daftar peserta">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">Nama</th>
                                                            <th scope="col">Email</th>
                                                            <th scope="col">Paket</th>
                                                            <th scope="col">Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($contohPeserta as $peserta)
                                                            <tr>
                                                                <th scope="row" class="text-black">{{ $peserta['nama'] }}</th>
                                                                <td class="text-muted">{{ $peserta['email'] }}</td>
                                                                <td class="text-black">{{ $peserta['paket'] }}</td>
                                                                <td>
                                                                    <span class="{{ $badgeClass($peserta['statusTone']) }}">{{ $peserta['status'] }}</span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-xl-6 mb-3" data-dashboard-search-scope id="transactions">
                                    <div class="card h-100">
                                        <div class="card-header border-0 pb-0">
                                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                                <h3 class="h6 mb-0">Transaksi</h3>
                                                <button type="button" class="btn btn-outline-secondary btn-sm" id="export-transaksi-csv">Ekspor CSV</button>
                                            </div>
                                        </div>
                                        <div class="card-body pt-3">
                                            <div class="table-responsive">
                                                <table class="table table-hover align-middle mb-0" id="table-transaksi" aria-label="Histori transaksi">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">Kode</th>
                                                            <th scope="col">Peserta</th>
                                                            <th scope="col">Nominal</th>
                                                            <th scope="col">Status</th>
                                                            <th scope="col">Waktu</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($contohTransaksi as $trx)
                                                            <tr>
                                                                <th scope="row" class="text-black">{{ $trx['kode'] }}</th>
                                                                <td class="text-muted">{{ $trx['peserta'] }}</td>
                                                                <td class="text-black">{{ $trx['nominal'] }}</td>
                                                                <td><span class="{{ $badgeClass($trx['statusTone']) }}">{{ $trx['status'] }}</span></td>
                                                                <td class="text-muted">{{ \Carbon\Carbon::parse($trx['waktu'])->format('d M Y H:i') }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="d-flex flex-wrap gap-2 mt-3">
                                                <button type="button" class="btn btn-outline-secondary btn-sm" id="export-transaksi-pdf">Ekspor PDF</button>
                                                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#reconcileModal">Rekonsiliasi</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade @if ($activeModule === 'operations') show active @endif" id="pane-operations" role="tabpanel" aria-labelledby="tab-operations" tabindex="0">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                <div>
                                    <div class="fw-semibold text-black">Live & Offline Operations</div>
                                    <div class="text-muted">Monitoring real-time, indikator status, dan tiket dukungan.</div>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#supportTicketModal">Buat tiket</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="refresh-ops">Refresh status</button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 col-xxl-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-header border-0 pb-0">
                                            <h3 class="h6 mb-0">Indikator operasional</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex flex-column gap-3" aria-live="polite" aria-atomic="true" id="ops-status">
                                                <div class="d-flex align-items-center justify-content-between gap-2">
                                                    <div>
                                                        <div class="text-black fw-semibold">Gateway pembayaran</div>
                                                        <div class="text-muted">Webhook & settlement</div>
                                                    </div>
                                                    <span class="badge bg-success">Sehat</span>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between gap-2">
                                                    <div>
                                                        <div class="text-black fw-semibold">Integrasi Zoom</div>
                                                        <div class="text-muted">Join link & host key</div>
                                                    </div>
                                                    <span class="badge bg-warning text-dark">Perlu verifikasi</span>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between gap-2">
                                                    <div>
                                                        <div class="text-black fw-semibold">Pengiriman email</div>
                                                        <div class="text-muted">Konfirmasi & reminder</div>
                                                    </div>
                                                    <span class="badge bg-success">Sehat</span>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between gap-2">
                                                    <div>
                                                        <div class="text-black fw-semibold">Mode event</div>
                                                        <div class="text-muted">Live / offline</div>
                                                    </div>
                                                    <div class="form-check form-switch m-0">
                                                        <input class="form-check-input" type="checkbox" role="switch" id="toggle-live-mode" checked aria-label="Aktifkan mode live">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-xxl-8 mb-3" data-dashboard-search-scope>
                                    <div class="card h-100">
                                        <div class="card-header border-0 pb-0">
                                            <h3 class="h6 mb-0">Tiket dukungan</h3>
                                        </div>
                                        <div class="card-body pt-3">
                                            <div class="table-responsive">
                                                <table class="table table-hover align-middle mb-0" aria-label="Daftar tiket dukungan">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">Kode</th>
                                                            <th scope="col">Judul</th>
                                                            <th scope="col">Prioritas</th>
                                                            <th scope="col">Status</th>
                                                            <th scope="col">Update</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($contohTiket as $tiket)
                                                            <tr>
                                                                <th scope="row" class="text-black">{{ $tiket['kode'] }}</th>
                                                                <td class="text-muted">{{ $tiket['judul'] }}</td>
                                                                <td><span class="{{ $badgeClass($tiket['prioritasTone']) }}">{{ $tiket['prioritas'] }}</span></td>
                                                                <td><span class="{{ $badgeClass($tiket['statusTone']) }}">{{ $tiket['status'] }}</span></td>
                                                                <td class="text-muted">{{ $tiket['updated'] }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade @if ($activeModule === 'reporting') show active @endif" id="pane-reporting" role="tabpanel" aria-labelledby="tab-reporting" tabindex="0">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                <div>
                                    <div class="fw-semibold text-black">Reporting</div>
                                    <div class="text-muted">Visualisasi data, laporan kustom, dan ekspor.</div>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#reportModal">Generate report</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="export-report-csv">Ekspor CSV</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="export-report-pdf">Ekspor PDF</button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 col-xxl-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-header border-0 pb-0">
                                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                                <h3 class="h6 mb-0">Ringkasan penjualan</h3>
                                                <div class="btn-group" role="group" aria-label="Rentang ringkasan">
                                                    <button class="btn btn-outline-secondary btn-sm active" type="button" data-bs-toggle="tab" data-bs-target="#donut-month">Bulanan</button>
                                                    <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="tab" data-bs-target="#donut-week">Mingguan</button>
                                                    <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="tab" data-bs-target="#donut-day">Harian</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="tab-content">
                                                <div class="tab-pane fade show active" id="donut-month" role="tabpanel">
                                                    <div id="donutChart2" style="min-height: 240px;"></div>
                                                </div>
                                                <div class="tab-pane fade" id="donut-week" role="tabpanel">
                                                    <div id="donutChart3" style="min-height: 240px;"></div>
                                                </div>
                                                <div class="tab-pane fade" id="donut-day" role="tabpanel">
                                                    <div id="donutChart4" style="min-height: 240px;"></div>
                                                </div>
                                            </div>
                                            <div class="text-muted mt-2">Gunakan filter laporan untuk menyesuaikan segmentasi.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-xxl-8 mb-3">
                                    <div class="card h-100">
                                        <div class="card-header border-0 pb-0">
                                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                                <h3 class="h6 mb-0">Analitik</h3>
                                                <div class="d-flex flex-wrap gap-2">
                                                    <div class="input-group input-group-sm" style="max-width: 260px;">
                                                        <label class="input-group-text" for="report-range">Rentang</label>
                                                        <input id="report-range" type="text" class="form-control" value="{{ $now->copy()->subDays(7)->format('d M Y') }} - {{ $now->format('d M Y') }}" aria-label="Rentang laporan">
                                                    </div>
                                                    <button type="button" class="btn btn-outline-primary btn-sm">Terapkan</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-12 col-lg-4">
                                                    <div class="card bg-light h-100">
                                                        <div class="card-body">
                                                            <div class="text-muted">Konversi</div>
                                                            <div class="fs-24 fw-semibold text-black">3,8%</div>
                                                            <div class="text-muted">Pendaftar → Pembayaran</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-4">
                                                    <div class="card bg-light h-100">
                                                        <div class="card-body">
                                                            <div class="text-muted">Refund</div>
                                                            <div class="fs-24 fw-semibold text-black">0,4%</div>
                                                            <div class="text-muted">7 hari terakhir</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-4">
                                                    <div class="card bg-light h-100">
                                                        <div class="card-body">
                                                            <div class="text-muted">Rata-rata tiket</div>
                                                            <div class="fs-24 fw-semibold text-black">Rp 146k</div>
                                                            <div class="text-muted">Per transaksi</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="row g-3">
                                                        <div class="col-12 col-xl-4">
                                                            <div class="card h-100">
                                                                <div class="card-body">
                                                                    <div class="text-muted mb-2">Tren pendaftar</div>
                                                                    <div id="salesChart" style="height: 220px;"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-xl-4">
                                                            <div class="card h-100">
                                                                <div class="card-body">
                                                                    <div class="text-muted mb-2">Tren pembayaran</div>
                                                                    <div id="salesChart1" style="height: 220px;"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-xl-4">
                                                            <div class="card h-100">
                                                                <div class="card-body">
                                                                    <div class="text-muted mb-2">Tren attendance</div>
                                                                    <div id="salesChart2" style="height: 220px;"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 col-xl-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-header border-0 pb-0">
                                            <h3 class="h6 mb-0">Widget tambahan</h3>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="widgetChart3" height="80" aria-label="Grafik ringkas" role="img"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-xl-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-header border-0 pb-0">
                                            <h3 class="h6 mb-0">Checklist ekspor</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex flex-column gap-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="1" id="export-include-events" checked>
                                                    <label class="form-check-label text-black" for="export-include-events">Sertakan event</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="1" id="export-include-sessions" checked>
                                                    <label class="form-check-label text-black" for="export-include-sessions">Sertakan sesi</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="1" id="export-include-transactions" checked>
                                                    <label class="form-check-label text-black" for="export-include-transactions">Sertakan transaksi</label>
                                                </div>
                                                <div class="text-muted mt-2">Untuk PDF, gunakan dialog print browser agar konsisten.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade" id="dashboardHelpModal" tabindex="-1" aria-labelledby="dashboardHelpTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="dashboardHelpTitle">Bantuan cepat</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="accordion" id="help-accordion">
                            <div class="accordion-item">
                                <h3 class="accordion-header" id="help-events-heading">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#help-events" aria-expanded="true" aria-controls="help-events">
                                        Event Control
                                    </button>
                                </h3>
                                <div id="help-events" class="accordion-collapse collapse show" aria-labelledby="help-events-heading" data-bs-parent="#help-accordion">
                                    <div class="accordion-body">
                                        <div class="text-black fw-semibold mb-2">Alur yang disarankan</div>
                                        <ol class="mb-0">
                                            <li>Buat event sebagai Draft.</li>
                                            <li>Isi jadwal mulai/selesai dan publish.</li>
                                            <li>Aktifkan registrasi, lalu pantau kesiapan sesi.</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h3 class="accordion-header" id="help-sessions-heading">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#help-sessions" aria-expanded="false" aria-controls="help-sessions">
                                        Session Control
                                    </button>
                                </h3>
                                <div id="help-sessions" class="accordion-collapse collapse" aria-labelledby="help-sessions-heading" data-bs-parent="#help-accordion">
                                    <div class="accordion-body">
                                        <div class="text-muted">Atur slot dengan buffer dan gunakan status sesi untuk memudahkan monitoring saat live.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h3 class="accordion-header" id="help-packages-heading">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#help-packages" aria-expanded="false" aria-controls="help-packages">
                                        Paket & Akses
                                    </button>
                                </h3>
                                <div id="help-packages" class="accordion-collapse collapse" aria-labelledby="help-packages-heading" data-bs-parent="#help-accordion">
                                    <div class="accordion-body">
                                        <div class="text-muted">Pastikan izin paket selaras dengan akses Live/Offline, durasi rekaman, dan peran pengguna.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h3 class="accordion-header" id="help-reporting-heading">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#help-reporting" aria-expanded="false" aria-controls="help-reporting">
                                        Laporan & Ekspor
                                    </button>
                                </h3>
                                <div id="help-reporting" class="accordion-collapse collapse" aria-labelledby="help-reporting-heading" data-bs-parent="#help-accordion">
                                    <div class="accordion-body">
                                        <div class="text-muted">CSV tersedia untuk pengolahan data, sedangkan PDF menggunakan fitur print browser.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mengerti</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="eventModalTitle">Event</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label text-black" for="event-name">Nama event</label>
                                    <input id="event-name" class="form-control" type="text" placeholder="Contoh: Webinar Keamanan Aplikasi">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="event-start">Mulai</label>
                                    <input id="event-start" class="form-control" type="datetime-local">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="event-end">Selesai</label>
                                    <input id="event-end" class="form-control" type="datetime-local">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="event-status">Status</label>
                                    <select id="event-status" class="form-select">
                                        <option>Draft</option>
                                        <option selected>Terjadwal</option>
                                        <option>Berjalan</option>
                                        <option>Selesai</option>
                                        <option>Ditutup</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="event-visibility">Visibilitas</label>
                                    <select id="event-visibility" class="form-select">
                                        <option value="public" selected>Publik</option>
                                        <option value="private">Privat</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-black" for="event-notes">Catatan</label>
                                    <textarea id="event-notes" class="form-control" rows="3" placeholder="Catatan internal untuk tim admin"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Simpan</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="eventBulkModal" tabindex="-1" aria-labelledby="eventBulkModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="eventBulkModalTitle">Aksi massal event</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-muted mb-3">Gunakan untuk update status atau publish beberapa event sekaligus.</div>
                        <div class="mb-3">
                            <label class="form-label text-black" for="bulk-action">Aksi</label>
                            <select id="bulk-action" class="form-select">
                                <option selected>Ubah status ke Terjadwal</option>
                                <option>Ubah status ke Ditutup</option>
                                <option>Publish</option>
                                <option>Unpublish</option>
                            </select>
                        </div>
                        <div class="mb-0">
                            <label class="form-label text-black" for="bulk-reason">Alasan</label>
                            <textarea id="bulk-reason" class="form-control" rows="3" placeholder="Opsional"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Terapkan</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="sessionModal" tabindex="-1" aria-labelledby="sessionModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="sessionModalTitle">Sesi</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label text-black" for="session-title">Judul sesi</label>
                                    <input id="session-title" class="form-control" type="text" placeholder="Contoh: Materi Utama">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="session-start">Mulai</label>
                                    <input id="session-start" class="form-control" type="time">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="session-end">Selesai</label>
                                    <input id="session-end" class="form-control" type="time">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="session-status">Status</label>
                                    <select id="session-status" class="form-select">
                                        <option selected>Terjadwal</option>
                                        <option>Siap</option>
                                        <option>Berjalan</option>
                                        <option>Selesai</option>
                                        <option>Butuh perhatian</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="session-slot">Slot kapasitas</label>
                                    <input id="session-slot" class="form-control" type="number" min="1" value="300">
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-black" for="session-link">Link live</label>
                                    <input id="session-link" class="form-control" type="url" placeholder="https://">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Simpan</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="slotModal" tabindex="-1" aria-labelledby="slotModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="slotModalTitle">Alokasi slot</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-black" for="slot-rule">Aturan</label>
                            <select id="slot-rule" class="form-select">
                                <option selected>Auto-allocate berdasarkan kapasitas</option>
                                <option>Batasi per paket</option>
                                <option>Whitelist peserta</option>
                            </select>
                        </div>
                        <div class="mb-0">
                            <label class="form-label text-black" for="slot-note">Catatan</label>
                            <textarea id="slot-note" class="form-control" rows="3" placeholder="Contoh: Paket Pro memiliki prioritas"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Terapkan</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="packageModal" tabindex="-1" aria-labelledby="packageModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="packageModalTitle">Paket</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="package-name">Nama paket</label>
                                    <input id="package-name" class="form-control" type="text" placeholder="Contoh: Pro">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="package-price">Harga</label>
                                    <input id="package-price" class="form-control" type="text" placeholder="Rp 199.000">
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-black" for="package-access">Akses</label>
                                    <textarea id="package-access" class="form-control" rows="3" placeholder="Contoh: Live + rekaman 30 hari + sertifikat"></textarea>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex align-items-center gap-2">
                                        <input class="form-check-input mt-0" type="checkbox" id="package-active" checked>
                                        <label class="form-check-label text-black" for="package-active">Aktif</label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Simpan</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="permissionModal" tabindex="-1" aria-labelledby="permissionModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="permissionModalTitle">Pengaturan izin</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="text-black fw-semibold">Admin</div>
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" id="perm-admin-all" checked>
                                            <label class="form-check-label" for="perm-admin-all">Akses penuh</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="text-black fw-semibold">Operator</div>
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" id="perm-operator-events" checked>
                                            <label class="form-check-label" for="perm-operator-events">Kelola event & sesi</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="perm-operator-payments">
                                            <label class="form-check-label" for="perm-operator-payments">Kelola pembayaran</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-muted mt-3">Sesuaikan izin agar aman dan sesuai kebutuhan operasional.</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Simpan</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="participantModal" tabindex="-1" aria-labelledby="participantModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="participantModalTitle">Pendaftaran peserta</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="participant-name">Nama</label>
                                    <input id="participant-name" class="form-control" type="text" placeholder="Nama lengkap">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="participant-email">Email</label>
                                    <input id="participant-email" class="form-control" type="email" placeholder="email@domain.tld" autocomplete="email">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="participant-package">Paket</label>
                                    <select id="participant-package" class="form-select">
                                        <option>Basic</option>
                                        <option selected>Pro</option>
                                        <option>Enterprise</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="participant-status">Status</label>
                                    <select id="participant-status" class="form-select">
                                        <option selected>Aktif</option>
                                        <option>Menunggu verifikasi</option>
                                        <option>Dinonaktifkan</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Simpan</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="transactionModalTitle">Catat transaksi</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="trx-code">Kode</label>
                                    <input id="trx-code" class="form-control" type="text" placeholder="INV-2026-XXXXX">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="trx-amount">Nominal</label>
                                    <input id="trx-amount" class="form-control" type="text" placeholder="Rp 0">
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-black" for="trx-participant">Peserta</label>
                                    <input id="trx-participant" class="form-control" type="text" placeholder="Nama peserta">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="trx-status">Status pembayaran</label>
                                    <select id="trx-status" class="form-select">
                                        <option selected>Pending</option>
                                        <option>Paid</option>
                                        <option>Failed</option>
                                        <option>Refund</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="trx-time">Waktu</label>
                                    <input id="trx-time" class="form-control" type="datetime-local">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Simpan</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="supportTicketModal" tabindex="-1" aria-labelledby="supportTicketModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="supportTicketModalTitle">Tiket dukungan</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label text-black" for="ticket-title">Judul</label>
                                    <input id="ticket-title" class="form-control" type="text" placeholder="Contoh: Gagal join live">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="ticket-priority">Prioritas</label>
                                    <select id="ticket-priority" class="form-select">
                                        <option selected>Sedang</option>
                                        <option>Tinggi</option>
                                        <option>Rendah</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="ticket-status">Status</label>
                                    <select id="ticket-status" class="form-select">
                                        <option selected>Open</option>
                                        <option>Investigasi</option>
                                        <option>Selesai</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-black" for="ticket-desc">Deskripsi</label>
                                    <textarea id="ticket-desc" class="form-control" rows="4" placeholder="Detail masalah, langkah reproduksi, dan konteks"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Simpan</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="reportModalTitle">Generate report</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="report-type">Jenis laporan</label>
                                    <select id="report-type" class="form-select">
                                        <option selected>Ringkasan event</option>
                                        <option>Penjualan paket</option>
                                        <option>Transaksi & pembayaran</option>
                                        <option>Operasional live</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="report-format">Format</label>
                                    <select id="report-format" class="form-select">
                                        <option selected>CSV</option>
                                        <option>PDF (Print)</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-black" for="report-filter">Filter</label>
                                    <textarea id="report-filter" class="form-control" rows="3" placeholder="Contoh: status=paid, paket=pro, tanggal=2026-01-01..2026-01-31"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Generate</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="operationNoteModal" tabindex="-1" aria-labelledby="operationNoteModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="operationNoteModalTitle">Catatan operasional</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label text-black" for="ops-note">Catatan</label>
                        <textarea id="ops-note" class="form-control" rows="5" placeholder="Catatan internal untuk pengingat saat live"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Simpan</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="reconcileModal" tabindex="-1" aria-labelledby="reconcileModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="reconcileModalTitle">Rekonsiliasi pembayaran</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-muted mb-3">Cocokkan status transaksi dengan data gateway pembayaran.</div>
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label text-black" for="reconcile-from">Dari tanggal</label>
                                <input id="reconcile-from" type="date" class="form-control">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label text-black" for="reconcile-to">Sampai tanggal</label>
                                <input id="reconcile-to" type="date" class="form-control">
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="reconcile-auto" checked>
                                    <label class="form-check-label text-black" for="reconcile-auto">Perbaiki status otomatis jika cocok</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Jalankan</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalTitle" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="confirmDeleteModalTitle">Konfirmasi hapus</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-black fw-semibold">Hapus item terpilih?</div>
                        <div class="text-muted">Tindakan ini tidak dapat dibatalkan.</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Hapus</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="scanQrModal" tabindex="-1" aria-labelledby="scanQrModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="scanQrModalTitle">Scan QR</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12 col-lg-5">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label text-black" for="scan-event">Pilih event</label>
                                                <select id="scan-event" class="form-select">
                                                    <option selected value="1">Webinar Keamanan Aplikasi</option>
                                                    <option value="2">Workshop Laravel untuk Tim</option>
                                                    <option value="3">Kelas Offline: QA & UAT</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label text-black" for="scan-session">Pilih sesi/hari</label>
                                                <select id="scan-session" class="form-select">
                                                    <option selected value="101">Day 1 — Pembukaan & Orientasi</option>
                                                    <option value="102">Day 1 — Materi Utama</option>
                                                    <option value="201">Day 2 — Praktik</option>
                                                </select>
                                                <div class="form-text">Untuk event multi-day, sesi bisa dikelompokkan Day 1, Day 2, dst.</div>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label text-black" for="scan-token">QR token (fallback manual)</label>
                                                <input id="scan-token" type="text" class="form-control" placeholder="Tempel token QR di sini jika kamera tidak tersedia">
                                            </div>
                                            <div class="col-12 d-flex flex-wrap gap-2">
                                                <button type="button" class="btn btn-primary" id="btn-scan-start">Buka kamera</button>
                                                <button type="button" class="btn btn-outline-secondary" id="btn-scan-stop" disabled>Stop</button>
                                                <button type="button" class="btn btn-outline-primary" id="btn-scan-submit">Check-in</button>
                                            </div>
                                            <div class="col-12">
                                                <div class="alert alert-light mb-0" role="status" id="scan-status" aria-live="polite">
                                                    Siap untuk scan.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-lg-7">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                            <div class="text-black fw-semibold">Kamera</div>
                                            <div class="text-muted">Pastikan izin kamera diaktifkan pada browser.</div>
                                        </div>
                                        <div class="ratio ratio-16x9 bg-light rounded">
                                            <video id="scan-video" class="w-100 h-100" autoplay playsinline muted></video>
                                        </div>
                                        <div class="text-muted mt-2">Jika QR tidak terbaca, gunakan input manual token untuk check-in.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="liveSessionModal" tabindex="-1" aria-labelledby="liveSessionModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="liveSessionModalTitle">Live Session</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label text-black" for="live-session-select">Pilih sesi</label>
                                <select id="live-session-select" class="form-select">
                                    <option selected value="101">Pembukaan & Orientasi</option>
                                    <option value="102">Materi: Hardening & OWASP Top 10</option>
                                    <option value="103">Q&A</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-black" for="live-zoom-link">Link Zoom</label>
                                <input id="live-zoom-link" type="url" class="form-control" placeholder="https://">
                            </div>
                            <div class="col-12 d-flex flex-wrap align-items-center gap-2">
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" role="switch" id="live-toggle" checked>
                                    <label class="form-check-label text-black" for="live-toggle">Aktifkan sesi live</label>
                                </div>
                                <span class="text-muted">Gunakan saat sesi mulai.</span>
                            </div>
                            <div class="col-12">
                                <div class="card bg-light mb-0">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <div class="text-black fw-semibold">Kehadiran peserta</div>
                                                <div class="text-muted">Ringkasan real-time (placeholder)</div>
                                            </div>
                                            <span class="badge bg-success">Stabil</span>
                                        </div>
                                        <div class="row g-3 mt-1">
                                            <div class="col-6 col-lg-4">
                                                <div class="text-muted">Join</div>
                                                <div class="fs-20 fw-semibold text-black">128</div>
                                            </div>
                                            <div class="col-6 col-lg-4">
                                                <div class="text-muted">Aktif</div>
                                                <div class="fs-20 fw-semibold text-black">97</div>
                                            </div>
                                            <div class="col-12 col-lg-4">
                                                <div class="text-muted">Drop</div>
                                                <div class="fs-20 fw-semibold text-black">6</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Simpan</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="recordingModal" tabindex="-1" aria-labelledby="recordingModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="recordingModalTitle">Rekaman & Video</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label text-black" for="video-session-select">Sesi</label>
                                <select id="video-session-select" class="form-select">
                                    <option selected value="102">Materi: Hardening & OWASP Top 10</option>
                                    <option value="103">Q&A</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-black" for="video-file">Upload video</label>
                                <input id="video-file" type="file" class="form-control" accept="video/*">
                                <div class="form-text">Alternatif: gunakan URL video jika hosting terpisah.</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-black" for="video-url">URL video</label>
                                <input id="video-url" type="url" class="form-control" placeholder="https://">
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="video-publish" checked>
                                    <label class="form-check-label text-black" for="video-publish">Publish video</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Simpan</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="attendanceModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="attendanceModalTitle">Laporan Kehadiran</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label text-black" for="attendance-event">Event</label>
                                <select id="attendance-event" class="form-select">
                                    <option selected value="1">Webinar Keamanan Aplikasi</option>
                                    <option value="2">Workshop Laravel untuk Tim</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label text-black" for="attendance-mode">Mode laporan</label>
                                <select id="attendance-mode" class="form-select">
                                    <option selected value="per-sesi">Per sesi</option>
                                    <option value="per-peserta">Per peserta</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="text-muted">Ekspor dalam bentuk CSV agar bisa dibuka di Excel.</div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="export-attendance-excel">Ekspor Excel</button>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0" id="table-attendance" aria-label="Data kehadiran">
                                        <thead>
                                            <tr>
                                                <th scope="col">Nama</th>
                                                <th scope="col">Sesi</th>
                                                <th scope="col">Metode</th>
                                                <th scope="col">Waktu</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th scope="row" class="text-black">Siti Aulia</th>
                                                <td class="text-muted">Day 1 — Materi Utama</td>
                                                <td><span class="badge bg-success">Live</span></td>
                                                <td class="text-muted">2026-01-15 09:12</td>
                                            </tr>
                                            <tr>
                                                <th scope="row" class="text-black">Budi Santoso</th>
                                                <td class="text-muted">Day 1 — Pembukaan</td>
                                                <td><span class="badge bg-warning text-dark">Offline QR</span></td>
                                                <td class="text-muted">2026-01-15 08:58</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="adminProfileModal" tabindex="-1" aria-labelledby="adminProfileModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="adminProfileModalTitle">Profil Admin</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label text-black" for="admin-name">Nama</label>
                                <input id="admin-name" type="text" class="form-control" value="Admin">
                            </div>
                            <div class="col-12">
                                <label class="form-label text-black" for="admin-email">Email</label>
                                <input id="admin-email" type="email" class="form-control" value="admin@example.test">
                            </div>
                            <div class="col-12">
                                <label class="form-label text-black" for="admin-password">Password baru</label>
                                <input id="admin-password" type="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Simpan</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            (function () {
                const searchInput = document.getElementById('dashboard-search');
                const scopes = Array.from(document.querySelectorAll('[data-dashboard-search-scope]'));

                function normalize(text) {
                    return (text || '')
                        .toString()
                        .toLowerCase()
                        .replace(/\s+/g, ' ')
                        .trim();
                }

                function filterScope(scope, query) {
                    const rows = Array.from(scope.querySelectorAll('tbody tr, .list-group-item'));
                    if (!rows.length) return;

                    rows.forEach((row) => {
                        const hay = normalize(row.innerText);
                        const show = !query || hay.includes(query);
                        row.style.display = show ? '' : 'none';
                    });
                }

                if (searchInput) {
                    searchInput.addEventListener('input', () => {
                        const query = normalize(searchInput.value);
                        scopes.forEach((scope) => filterScope(scope, query));
                    });
                }

                function tableToCsv(table) {
                    const rows = Array.from(table.querySelectorAll('tr'));
                    return rows
                        .map((tr) => Array.from(tr.querySelectorAll('th,td')).map((cell) => {
                            const text = (cell.innerText || '').replace(/\s+/g, ' ').trim();
                            const escaped = '"' + text.replace(/"/g, '""') + '"';
                            return escaped;
                        }).join(','))
                        .join('\n');
                }

                function downloadText(filename, text) {
                    const blob = new Blob([text], { type: 'text/csv;charset=utf-8' });
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    URL.revokeObjectURL(url);
                }

                const exportTransaksiCsvBtn = document.getElementById('export-transaksi-csv');
                const transaksiTable = document.getElementById('table-transaksi');
                if (exportTransaksiCsvBtn && transaksiTable) {
                    exportTransaksiCsvBtn.addEventListener('click', () => {
                        const csv = tableToCsv(transaksiTable);
                        downloadText('transaksi.csv', csv);
                    });
                }

                const exportTransaksiPdfBtn = document.getElementById('export-transaksi-pdf');
                if (exportTransaksiPdfBtn) {
                    exportTransaksiPdfBtn.addEventListener('click', () => window.print());
                }

                const exportReportCsvBtn = document.getElementById('export-report-csv');
                if (exportReportCsvBtn && transaksiTable) {
                    exportReportCsvBtn.addEventListener('click', () => {
                        const csv = tableToCsv(transaksiTable);
                        downloadText('report.csv', csv);
                    });
                }

                const exportReportPdfBtn = document.getElementById('export-report-pdf');
                if (exportReportPdfBtn) {
                    exportReportPdfBtn.addEventListener('click', () => window.print());
                }

                const refreshOpsBtn = document.getElementById('refresh-ops');
                const opsStatus = document.getElementById('ops-status');
                if (refreshOpsBtn && opsStatus) {
                    refreshOpsBtn.addEventListener('click', () => {
                        opsStatus.setAttribute('data-last-refresh', new Date().toISOString());
                    });
                }

                const params = new URLSearchParams(window.location.search);
                const requestedTab = params.get('tab') || '';
                const tabToModule = {
                    events: 'events',
                    sessions: 'sessions',
                    packages: 'packages',
                    participants: 'participants',
                    transactions: 'participants',
                    operations: 'operations',
                    'live-session': 'operations',
                    'scan-qr': 'operations',
                    recordings: 'operations',
                    reporting: 'reporting',
                    'attendance-report': 'reporting',
                };

                const moduleKey = tabToModule[requestedTab] || '';
                if (moduleKey && window.bootstrap && bootstrap.Tab) {
                    const tabButton = document.getElementById(`tab-${moduleKey}`);
                    if (tabButton) {
                        bootstrap.Tab.getOrCreateInstance(tabButton).show();
                    }
                }

                const requestedModal = params.get('modal') || '';
                if (requestedModal && window.bootstrap && bootstrap.Modal) {
                    const modalEl = document.getElementById(requestedModal);
                    if (modalEl) {
                        bootstrap.Modal.getOrCreateInstance(modalEl).show();
                    }
                }

                if (window.location.hash) {
                    const hashTarget = document.querySelector(window.location.hash);
                    if (hashTarget) {
                        requestAnimationFrame(() => hashTarget.scrollIntoView({ behavior: 'smooth', block: 'start' }));
                    }
                }
            })();
        </script>
    </main>
@endsection
