@extends('admin.partials.app')

@section('content')
    <a class="visually-hidden-focusable" href="#main-content">Lewati ke konten utama</a>

    <style>
        .dash-card-title {
            letter-spacing: 0.2px;
        }
        .dash-kpi {
            font-size: 1.9rem;
            line-height: 1.1;
        }
        .dash-mini-calendar th,
        .dash-mini-calendar td {
            width: 14.285%;
            text-align: center;
            padding: 0.35rem;
            vertical-align: middle;
        }
        .dash-mini-calendar .day {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            border-radius: 0.65rem;
        }
    </style>

    <main id="main-content" tabindex="-1" class="pb-4" role="main" aria-label="Dashboard admin">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div class="me-auto">
                <h1 class="h3 mb-1">Dashboard Admin</h1>
                <div class="text-muted">Prioritas operasional hari ini, status event, dan monitoring kehadiran.</div>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="badge bg-light text-dark">Update: {{ $now->format('d M Y H:i') }}</span>
                @if (Route::has('admin.events.index'))
                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.events.index') }}">Lihat Semua Event</a>
                @endif
            </div>
        </div>

        <section class="mb-4" aria-label="Ringkasan event aktif">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h2 class="h5 mb-0 dash-card-title">Ringkasan Event Aktif</h2>
                            <div class="text-muted">Jumlah event yang sedang berlangsung dan indikator progress.</div>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="{{ $badgeClass['success'] }}">Berlangsung: {{ $ongoingCount }}</span>
                            <span class="{{ $badgeClass['primary'] }}">Aktif: {{ $allActiveCount }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-3">
                    <div class="row g-3 align-items-stretch">
                        <div class="col-12 col-lg-4">
                            <div class="p-3 rounded border h-100">
                                <div class="text-muted">Event sedang berlangsung</div>
                                <div class="dash-kpi fw-semibold text-black">{{ $ongoingCount }}</div>
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between small">
                                        <span class="text-muted">Rata-rata progress event</span>
                                        <span class="fw-semibold text-black">{{ $ongoingAvgProgress }}%</span>
                                    </div>
                                    <div class="progress mt-1" style="height: 8px;">
                                        <div
                                            class="progress-bar bg-success"
                                            role="progressbar"
                                            style="width: {{ $ongoingAvgProgress }}%;"
                                            aria-valuenow="{{ $ongoingAvgProgress }}"
                                            aria-valuemin="0"
                                            aria-valuemax="100"
                                        ></div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    @if (Route::has('admin.events.index'))
                                        <a class="btn btn-primary btn-sm" href="{{ route('admin.events.index', ['status' => 'aktif']) }}">
                                            <i class="la la-external-link-alt me-1" aria-hidden="true"></i> Detail Event Aktif
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-8">
                            <div class="p-3 rounded border h-100">
                                <div class="fw-semibold text-black mb-2">Event yang sedang berlangsung</div>
                                @if ($ongoingEvents->isEmpty())
                                    <div class="text-muted">Belum ada event yang berlangsung hari ini.</div>
                                @else
                                    <div class="d-flex flex-column gap-3">
                                        @foreach ($ongoingEvents->take(5) as $event)
                                            <div>
                                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                                    <div class="me-auto">
                                                        <div class="fw-semibold text-black">{{ $event->judul }}</div>
                                                        <div class="text-muted small">
                                                            {{ $event->tanggal_mulai?->format('d M Y') ?? '-' }}
                                                            <span class="text-muted">–</span>
                                                            {{ $event->tanggal_selesai?->format('d M Y') ?? '-' }}
                                                            @if (! empty($event->lokasi))
                                                                <span class="text-muted">·</span> {{ $event->lokasi }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="{{ $badgeClass['success'] }}">{{ $event->dash_progress_pct }}%</span>
                                                        <a class="btn btn-outline-primary btn-sm" href="{{ $event->dash_url }}">Buka</a>
                                                    </div>
                                                </div>
                                                <div class="progress mt-2" style="height: 7px;">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $event->dash_progress_pct }}%;" aria-valuenow="{{ $event->dash_progress_pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mb-4" aria-label="Event dan sesi hari ini">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h2 class="h5 mb-0 dash-card-title">Event & Sesi Hari Ini</h2>
                            <div class="text-muted">Semua event/sesi terjadwal hari ini lengkap dengan status dan akses cepat.</div>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            @if (Route::has('admin.sesi-event.index'))
                                <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.sesi-event.index') }}">Kelola Sesi</a>
                            @endif
                            @if (Route::has('admin.live.index'))
                                <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.live.index', ['status' => 'live']) }}">Monitoring Live</a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body pt-3">
                    @if ($todaySessions->isEmpty())
                        <div class="text-muted">Tidak ada sesi yang terjadwal untuk hari ini.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" aria-label="Daftar event dan sesi hari ini">
                                <thead>
                                    <tr>
                                        <th scope="col">Event</th>
                                        <th scope="col">Sesi</th>
                                        <th scope="col">Waktu</th>
                                        <th scope="col">Lokasi</th>
                                        <th scope="col">Status</th>
                                        <th scope="col" class="text-end">Akses Cepat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($todaySessions as $sesi)
                                        <tr>
                                            <td class="text-black fw-semibold">{{ $sesi->event?->judul ?? '-' }}</td>
                                            <td>
                                                <div class="text-black fw-semibold">{{ $sesi->judul_sesi }}</div>
                                                <div class="text-muted small">ID Sesi: {{ $sesi->id }}</div>
                                            </td>
                                            <td class="text-muted">
                                                {{ $sesi->dash_time_label }}
                                            </td>
                                            <td class="text-muted">{{ $sesi->dash_lokasi }}</td>
                                            <td><span class="{{ $badgeClass[$sesi->dash_status_tone] }}">{{ $sesi->dash_status_text }}</span></td>
                                            <td class="text-end">
                                                <div class="d-inline-flex flex-wrap gap-2 justify-content-end">
                                                    <a class="btn btn-outline-primary btn-sm" href="{{ $sesi->dash_action_live_url }}">
                                                        <i class="la la-broadcast-tower me-1" aria-hidden="true"></i> Live
                                                    </a>
                                                    <a class="btn btn-outline-secondary btn-sm" href="{{ $sesi->dash_action_scan_url }}">
                                                        <i class="la la-qrcode me-1" aria-hidden="true"></i> QR
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <section class="mb-4" aria-label="Tombol operasional cepat">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h2 class="h5 mb-0 dash-card-title">Tombol Operasional Cepat</h2>
                    <div class="text-muted">Akses cepat ke aksi utama operasional.</div>
                </div>
                <div class="card-body pt-3">
                    <div class="row g-3">
                        <div class="col-12 col-md-6 col-xl-3">
                            <a class="card h-100 mb-0" href="{{ Route::has('admin.events.index') ? route('admin.events.index') : '#' }}" aria-label="Buat Event Baru">
                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="la la-calendar-plus fs-3 text-primary" aria-hidden="true"></i>
                                        <div>
                                            <div class="fw-semibold text-black">Buat Event Baru</div>
                                            <div class="text-muted small">Tambah event dan atur status.</div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-12 col-md-6 col-xl-3">
                            <a class="card h-100 mb-0" href="{{ Route::has('admin.peserta.index') ? route('admin.peserta.index') : '#' }}" aria-label="Manajemen Peserta">
                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="la la-users fs-3 text-primary" aria-hidden="true"></i>
                                        <div>
                                            <div class="fw-semibold text-black">Manajemen Peserta</div>
                                            <div class="text-muted small">Pantau akun, status, dan detail.</div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-12 col-md-6 col-xl-3">
                            <a class="card h-100 mb-0" href="{{ Route::has('admin.laporan.kehadiran.index') ? route('admin.laporan.kehadiran.index', ['from' => $now->toDateString(), 'to' => $now->toDateString()]) : '#' }}" aria-label="Laporan Harian">
                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="la la-clipboard-check fs-3 text-primary" aria-hidden="true"></i>
                                        <div>
                                            <div class="fw-semibold text-black">Laporan Harian</div>
                                            <div class="text-muted small">Rekap kehadiran dan ekspor.</div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-12 col-md-6 col-xl-3">
                            <a class="card h-100 mb-0" href="{{ Route::has('admin.index') ? route('admin.index', ['modal' => 'adminProfileModal']) : '#' }}" aria-label="Pengaturan Cepat">
                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="la la-cog fs-3 text-primary" aria-hidden="true"></i>
                                        <div>
                                            <div class="fw-semibold text-black">Pengaturan Cepat</div>
                                            <div class="text-muted small">Profil admin dan konfigurasi.</div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mb-4" aria-label="Status kehadiran hari ini">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h2 class="h5 mb-0 dash-card-title">Status Kehadiran Hari Ini</h2>
                            <div class="text-muted">Persentase hadir vs tidak hadir serta peserta prioritas yang belum hadir.</div>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="{{ $badgeClass['success'] }}">Hadir: {{ $hadirCount }}</span>
                            <span class="{{ $badgeClass['danger'] }}">Belum: {{ $tidakHadirCount }}</span>
                            <span class="{{ $badgeClass['primary'] }}">Online: {{ $onlineNowCount }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-3">
                    <div class="row g-3">
                        <div class="col-12 col-xl-6">
                            <div class="p-3 rounded border h-100">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="fw-semibold text-black">Ringkasan</div>
                                    <div class="text-muted small">Basis: peserta PAID yang punya akses sesi hari ini.</div>
                                </div>
                                <div class="mt-2">
                                    <div class="d-flex justify-content-between small">
                                        <span class="text-muted">Hadir</span>
                                        <span class="fw-semibold text-black">{{ $hadirPct }}%</span>
                                    </div>
                                    <div class="progress mt-1" style="height: 10px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $hadirPct }}%;" aria-valuenow="{{ $hadirPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $tidakHadirPct }}%;" aria-valuenow="{{ $tidakHadirPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>

                                <div class="table-responsive mt-3">
                                    <table class="table table-sm mb-0" aria-label="Ringkasan kehadiran">
                                        <tbody>
                                            <tr>
                                                <td class="text-muted">Peserta terdaftar (akses hari ini)</td>
                                                <td class="text-end fw-semibold text-black">{{ $expectedCount }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Hadir (check-in/join)</td>
                                                <td class="text-end fw-semibold text-black">{{ $hadirCount }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Belum hadir</td>
                                                <td class="text-end fw-semibold text-black">{{ $tidakHadirCount }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Sedang online (belum leave)</td>
                                                <td class="text-end fw-semibold text-black">{{ $onlineNowCount }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-3">
                                    @if (Route::has('admin.laporan.kehadiran.index'))
                                        <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.laporan.kehadiran.index', ['from' => $now->toDateString(), 'to' => $now->toDateString()]) }}">
                                            <i class="la la-clipboard-list me-1" aria-hidden="true"></i> Detail Kehadiran
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-xl-6">
                            <div class="p-3 rounded border h-100">
                                <div class="fw-semibold text-black">Peserta prioritas belum hadir</div>
                                <div class="text-muted small">
                                    @if ($prioritySession)
                                        Fokus sesi: {{ $prioritySession->judul_sesi }} ({{ $prioritySession->waktu_mulai?->format('H:i') ?? '-' }}–{{ $prioritySession->waktu_selesai?->format('H:i') ?? '-' }})
                                    @else
                                        Tidak ada sesi aktif/upcoming hari ini.
                                    @endif
                                </div>

                                <div class="mt-3">
                                    @if ($prioritySession && $priorityAbsentUsers->isNotEmpty())
                                        <div class="list-group">
                                            @foreach ($priorityAbsentUsers as $u)
                                                <div class="list-group-item d-flex flex-wrap align-items-center justify-content-between gap-2">
                                                    <div class="me-auto">
                                                        <div class="fw-semibold text-black">{{ $u->nama }}</div>
                                                        <div class="text-muted small">{{ $u->email }}</div>
                                                    </div>
                                                    <span class="{{ $badgeClass[$u->dash_status_tone] }}">{{ $u->status_akun ?: 'unknown' }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif ($prioritySession)
                                        <div class="text-muted">Semua peserta prioritas sudah hadir, atau belum ada peserta terdaftar untuk sesi ini.</div>
                                    @else
                                        <div class="text-muted">Daftar prioritas akan muncul saat ada sesi aktif/upcoming.</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mb-4" aria-label="Alert penting">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h2 class="h5 mb-0 dash-card-title">Alert Penting</h2>
                            <div class="text-muted">Notifikasi urgent yang butuh perhatian segera, diurutkan berdasarkan level.</div>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-3">
                    @if ($alerts->isEmpty())
                        <div class="alert alert-light mb-0" role="status">
                            Tidak ada alert urgent saat ini.
                        </div>
                    @else
                        <div class="list-group">
                            @foreach ($alerts as $a)
                                <div class="list-group-item d-flex flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-auto">
                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                            <span class="{{ $badgeClass[$a['tone']] }}">{{ $a['level'] }}</span>
                                            <div class="fw-semibold text-black">{{ $a['title'] }}</div>
                                        </div>
                                        <div class="text-muted small">{{ $a['desc'] }}</div>
                                    </div>
                                    <div>
                                        <a class="btn btn-outline-primary btn-sm" href="{{ $a['actionUrl'] }}">{{ $a['actionLabel'] }}</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <section class="mb-4" aria-label="Event mendatang">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h2 class="h5 mb-0 dash-card-title">Event Mendatang</h2>
                            <div class="text-muted">Kalender mini dan daftar event 7 hari ke depan.</div>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            @if (Route::has('admin.events.index'))
                                <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.events.index') }}">Lihat Lebih Banyak</a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body pt-3">
                    <div class="row g-3">
                        <div class="col-12 col-lg-5">
                            <div class="p-3 rounded border h-100">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="fw-semibold text-black">{{ $now->format('F Y') }}</div>
                                    <span class="badge bg-light text-dark">Tanggal penting</span>
                                </div>
                                <div class="table-responsive mt-2">
                                    <table class="table table-borderless mb-0 dash-mini-calendar" aria-label="Kalender mini">
                                        <thead>
                                            <tr class="text-muted small">
                                                <th scope="col">Sen</th>
                                                <th scope="col">Sel</th>
                                                <th scope="col">Rab</th>
                                                <th scope="col">Kam</th>
                                                <th scope="col">Jum</th>
                                                <th scope="col">Sab</th>
                                                <th scope="col">Min</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($calendarRows as $week)
                                                <tr>
                                                    @foreach ($week as $cell)
                                                        <td>
                                                            <span class="day {{ $cell['bgClass'] }} {{ $cell['textClass'] }}">{{ $cell['day'] }}</span>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-muted small mt-2">
                                    <span class="badge bg-primary me-1"> </span> ada event &nbsp;
                                    <span class="badge bg-success me-1"> </span> hari ini
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-7">
                            <div class="p-3 rounded border h-100">
                                <div class="fw-semibold text-black mb-2">Dalam 7 hari ke depan</div>
                                @if ($upcomingEvents->isEmpty())
                                    <div class="text-muted">Tidak ada event terjadwal dalam 7 hari ke depan.</div>
                                @else
                                    <div class="list-group">
                                        @foreach ($upcomingEvents as $event)
                                            <a href="{{ $event->dash_url }}" class="list-group-item list-group-item-action d-flex flex-wrap align-items-center justify-content-between gap-2">
                                                <div class="me-auto">
                                                    <div class="fw-semibold text-black">{{ $event->judul }}</div>
                                                    <div class="text-muted small">
                                                        {{ $event->tanggal_mulai?->format('d M Y') ?? '-' }}
                                                        @if (! empty($event->lokasi))
                                                            <span class="text-muted">·</span> {{ $event->lokasi }}
                                                        @endif
                                                    </div>
                                                </div>
                                                <span class="{{ $badgeClass[$event->dash_status_tone] }}">{{ $event->dash_status_text }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
