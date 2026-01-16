@extends('admin.partials.app')

@section('content')
    @php
        use App\Models\Event;
        use App\Models\EventSesi;
        use App\Models\KehadiranSesi;
        use App\Models\Pesanan;
        use App\Models\User;
        use Carbon\Carbon;
        use Illuminate\Support\Facades\Route;

        $now = now();
        $todayStart = $now->copy()->startOfDay();
        $todayEnd = $now->copy()->endOfDay();

        $toneBadge = function (string $tone): string {
            $tone = strtolower($tone);
            $className = "badge bg-{$tone}";
            if (in_array($tone, ['warning', 'light', 'info'], true)) {
                $className .= ' text-dark';
            }

            return $className;
        };

        $eventIsOngoing = function (Event $event) use ($now): bool {
            $mulai = $event->tanggal_mulai?->toDateString();
            $selesai = $event->tanggal_selesai?->toDateString();
            $today = $now->toDateString();

            if ($event->status !== 'active') {
                return false;
            }

            if ($mulai && $mulai > $today) {
                return false;
            }

            if ($selesai && $selesai < $today) {
                return false;
            }

            return true;
        };

        $eventProgress = function (Event $event) use ($now): int {
            $start = $event->tanggal_mulai?->copy()->startOfDay();
            $end = $event->tanggal_selesai?->copy()->startOfDay();
            if (! $start || ! $end) {
                return 0;
            }

            if ($end->lt($start)) {
                return 0;
            }

            $totalDays = max(1, $start->diffInDays($end) + 1);
            $passedDays = $start->diffInDays($now->copy()->startOfDay()) + 1;
            $passedDays = max(0, min($totalDays, $passedDays));

            return (int) round(($passedDays / $totalDays) * 100);
        };

        $allActiveEvents = Event::query()
            ->where('status', 'active')
            ->orderByDesc('tanggal_mulai')
            ->get();

        $ongoingEvents = $allActiveEvents
            ->filter(fn (Event $e) => $eventIsOngoing($e))
            ->values();

        $ongoingCount = $ongoingEvents->count();
        $allActiveCount = $allActiveEvents->count();
        $ongoingAvgProgress = $ongoingCount > 0
            ? (int) round($ongoingEvents->map(fn (Event $e) => $eventProgress($e))->avg() ?? 0)
            : 0;

        $todaySessions = EventSesi::query()
            ->with('event')
            ->where('waktu_mulai', '<', $todayEnd)
            ->where('waktu_selesai', '>', $todayStart)
            ->orderBy('waktu_mulai')
            ->get();

        $todaySessionIds = $todaySessions->pluck('id')->values();
        $todayEventIds = $todaySessions->pluck('event_id')->unique()->values();
        $todayEvents = $todayEventIds->isEmpty()
            ? collect()
            : Event::query()->whereIn('id', $todayEventIds)->orderBy('tanggal_mulai')->get();

        $expectedUserIds = $todaySessionIds->isEmpty()
            ? collect()
            : Pesanan::query()
                ->where('status_pembayaran', 'paid')
                ->whereHas('paket.sesi', fn ($q) => $q->whereIn('event_sesi.id', $todaySessionIds))
                ->distinct()
                ->pluck('user_id')
                ->values();

        $hadirUserIds = $todaySessionIds->isEmpty()
            ? collect()
            : KehadiranSesi::query()
                ->whereIn('event_sesi_id', $todaySessionIds)
                ->whereBetween('waktu_join', [$todayStart, $todayEnd])
                ->distinct()
                ->pluck('user_id')
                ->values();

        $expectedCount = $expectedUserIds->count();
        $hadirCount = $hadirUserIds->count();
        $tidakHadirCount = max(0, $expectedCount - $hadirCount);
        $hadirPct = $expectedCount > 0 ? (int) round(($hadirCount / $expectedCount) * 100) : 0;
        $tidakHadirPct = $expectedCount > 0 ? 100 - $hadirPct : 0;

        $onlineNowCount = $todaySessionIds->isEmpty()
            ? 0
            : KehadiranSesi::query()
                ->whereIn('event_sesi_id', $todaySessionIds)
                ->whereNull('waktu_leave')
                ->whereBetween('waktu_join', [$todayStart, $todayEnd])
                ->count();

        $prioritySession = $todaySessions
            ->first(fn (EventSesi $s) => $s->waktu_selesai && $now->lte($s->waktu_selesai));

        $priorityAbsentUsers = collect();
        if ($prioritySession) {
            $priorityExpected = Pesanan::query()
                ->where('status_pembayaran', 'paid')
                ->whereHas('paket.sesi', fn ($q) => $q->where('event_sesi.id', $prioritySession->id))
                ->distinct()
                ->pluck('user_id')
                ->values();

            $priorityPresent = KehadiranSesi::query()
                ->where('event_sesi_id', $prioritySession->id)
                ->whereBetween('waktu_join', [$todayStart, $todayEnd])
                ->distinct()
                ->pluck('user_id')
                ->values();

            $priorityAbsentIds = $priorityExpected->diff($priorityPresent)->values();
            if ($priorityAbsentIds->isNotEmpty()) {
                $priorityAbsentUsers = User::query()
                    ->whereIn('id', $priorityAbsentIds)
                    ->orderBy('nama')
                    ->limit(6)
                    ->get(['id', 'nama', 'email', 'status_akun']);
            }
        }

        $upcomingSoonCount = $todaySessions
            ->filter(function (EventSesi $s) use ($now) {
                if (! $s->waktu_mulai) {
                    return false;
                }

                if ($s->status_sesi === 'live') {
                    return false;
                }

                return $s->waktu_mulai->gt($now) && $s->waktu_mulai->diffInMinutes($now) <= 30;
            })
            ->count();

        $overrunLiveCount = EventSesi::query()
            ->where('status_sesi', 'live')
            ->whereNotNull('waktu_selesai')
            ->where('waktu_selesai', '<', $now->copy()->subMinutes(10))
            ->count();

        $pendingPaymentCount = Pesanan::query()->where('status_pembayaran', 'pending')->count();

        $alerts = collect();
        if ($overrunLiveCount > 0) {
            $alerts->push([
                'tone' => 'danger',
                'level' => 'High',
                'title' => "Ada {$overrunLiveCount} sesi masih Live melewati jadwal.",
                'desc' => 'Cek status sesi dan hentikan yang sudah selesai.',
                'actionLabel' => 'Buka Live Session',
                'actionUrl' => Route::has('admin.live.index') ? route('admin.live.index', ['status' => 'live']) : '#',
            ]);
        }
        if ($upcomingSoonCount > 0) {
            $alerts->push([
                'tone' => 'warning',
                'level' => 'Medium',
                'title' => "{$upcomingSoonCount} sesi mulai dalam 30 menit.",
                'desc' => 'Pastikan host siap, link aktif, dan status sesi benar.',
                'actionLabel' => 'Cek Jadwal Sesi',
                'actionUrl' => Route::has('admin.sesi-event.index') ? route('admin.sesi-event.index') : '#',
            ]);
        }
        if ($expectedCount > 0 && $hadirPct < 60) {
            $alerts->push([
                'tone' => 'warning',
                'level' => 'Medium',
                'title' => "Kehadiran hari ini baru {$hadirPct}%.",
                'desc' => 'Pertimbangkan reminder peserta atau cek kendala akses.',
                'actionLabel' => 'Buka Laporan Kehadiran',
                'actionUrl' => Route::has('admin.laporan.kehadiran.index') ? route('admin.laporan.kehadiran.index', ['from' => $now->toDateString(), 'to' => $now->toDateString()]) : '#',
            ]);
        }
        if ($pendingPaymentCount > 0) {
            $alerts->push([
                'tone' => 'secondary',
                'level' => 'Low',
                'title' => "Ada {$pendingPaymentCount} transaksi Pending.",
                'desc' => 'Tinjau pembayaran untuk memperlancar akses peserta.',
                'actionLabel' => 'Buka Transaksi',
                'actionUrl' => Route::has('admin.transaksi.index') ? route('admin.transaksi.index', ['status' => 'pending']) : '#',
            ]);
        }

        $next7Start = $now->copy()->startOfDay()->toDateString();
        $next7End = $now->copy()->addDays(7)->toDateString();
        $upcomingEvents = Event::query()
            ->whereDate('tanggal_mulai', '>=', $next7Start)
            ->whereDate('tanggal_mulai', '<=', $next7End)
            ->orderBy('tanggal_mulai')
            ->limit(20)
            ->get();

        $monthStart = $now->copy()->startOfMonth();
        $calendarStart = $monthStart->copy()->startOfWeek(Carbon::MONDAY);
        $calendarEnd = $monthStart->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);
        $calendarEventDates = Event::query()
            ->whereDate('tanggal_mulai', '>=', $calendarStart->toDateString())
            ->whereDate('tanggal_mulai', '<=', $calendarEnd->toDateString())
            ->pluck('tanggal_mulai')
            ->filter()
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->unique()
            ->values()
            ->all();
        $calendarEventSet = array_fill_keys($calendarEventDates, true);
    @endphp

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
                            <span class="{{ $toneBadge('success') }}">Berlangsung: {{ $ongoingCount }}</span>
                            <span class="{{ $toneBadge('primary') }}">Aktif: {{ $allActiveCount }}</span>
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
                                            @php
                                                $pct = $eventProgress($event);
                                                $eventUrl = Route::has('admin.events.index') ? route('admin.events.index', ['q' => $event->judul]) : '#';
                                            @endphp
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
                                                        <span class="{{ $toneBadge('success') }}">{{ $pct }}%</span>
                                                        <a class="btn btn-outline-primary btn-sm" href="{{ $eventUrl }}">Buka</a>
                                                    </div>
                                                </div>
                                                <div class="progress mt-2" style="height: 7px;">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $pct }}%;" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
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
                                        @php
                                            $start = $sesi->waktu_mulai;
                                            $end = $sesi->waktu_selesai;
                                            $statusTone = 'primary';
                                            $statusText = 'Terjadwal';
                                            if ($start && $end && $now->gte($start) && $now->lte($end)) {
                                                $statusTone = 'success';
                                                $statusText = 'Berjalan';
                                            } elseif ($end && $now->gt($end)) {
                                                $statusTone = 'secondary';
                                                $statusText = 'Selesai';
                                            } elseif ($start && $start->gt($now) && $start->diffInMinutes($now) <= 30) {
                                                $statusTone = 'warning';
                                                $statusText = 'Mulai Sebentar Lagi';
                                            }

                                            if ($sesi->status_sesi === 'live' && $statusText !== 'Selesai') {
                                                $statusTone = 'success';
                                                $statusText = 'Live';
                                            }
                                            if ($sesi->status_sesi === 'selesai') {
                                                $statusTone = 'secondary';
                                                $statusText = 'Selesai';
                                            }

                                            $lokasi = ! empty($sesi->zoom_link) ? 'Online (Zoom)' : ($sesi->event?->lokasi ?: '-');
                                            $actionLiveUrl = Route::has('admin.live.index') ? route('admin.live.index', ['q' => $sesi->judul_sesi, 'status' => '']) : '#';
                                            $actionScanUrl = Route::has('admin.scan.index') ? route('admin.scan.index') : '#';
                                        @endphp
                                        <tr>
                                            <td class="text-black fw-semibold">{{ $sesi->event?->judul ?? '-' }}</td>
                                            <td>
                                                <div class="text-black fw-semibold">{{ $sesi->judul_sesi }}</div>
                                                <div class="text-muted small">ID Sesi: {{ $sesi->id }}</div>
                                            </td>
                                            <td class="text-muted">
                                                {{ $start?->format('H:i') ?? '-' }}–{{ $end?->format('H:i') ?? '-' }}
                                            </td>
                                            <td class="text-muted">{{ $lokasi }}</td>
                                            <td><span class="{{ $toneBadge($statusTone) }}">{{ $statusText }}</span></td>
                                            <td class="text-end">
                                                <div class="d-inline-flex flex-wrap gap-2 justify-content-end">
                                                    <a class="btn btn-outline-primary btn-sm" href="{{ $actionLiveUrl }}">
                                                        <i class="la la-broadcast-tower me-1" aria-hidden="true"></i> Live
                                                    </a>
                                                    <a class="btn btn-outline-secondary btn-sm" href="{{ $actionScanUrl }}">
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
                            <span class="{{ $toneBadge('success') }}">Hadir: {{ $hadirCount }}</span>
                            <span class="{{ $toneBadge('danger') }}">Belum: {{ $tidakHadirCount }}</span>
                            <span class="{{ $toneBadge('primary') }}">Online: {{ $onlineNowCount }}</span>
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
                                                    <span class="{{ $toneBadge($u->status_akun === 'aktif' ? 'success' : 'secondary') }}">{{ $u->status_akun ?: 'unknown' }}</span>
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
                                            <span class="{{ $toneBadge($a['tone']) }}">{{ $a['level'] }}</span>
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
                                            @php
                                                $cursor = $calendarStart->copy();
                                            @endphp
                                            @while ($cursor->lte($calendarEnd))
                                                <tr>
                                                    @for ($i = 0; $i < 7; $i++)
                                                        @php
                                                            $date = $cursor->copy();
                                                            $key = $date->toDateString();
                                                            $inMonth = $date->month === $now->month;
                                                            $isToday = $key === $now->toDateString();
                                                            $hasEvent = isset($calendarEventSet[$key]);

                                                            $bg = '';
                                                            $text = $inMonth ? 'text-black' : 'text-muted';
                                                            if ($hasEvent) {
                                                                $bg = $isToday ? 'bg-success text-white' : 'bg-primary text-white';
                                                                $text = '';
                                                            } elseif ($isToday) {
                                                                $bg = 'bg-light text-dark';
                                                                $text = '';
                                                            }
                                                        @endphp
                                                        <td>
                                                            <span class="day {{ $bg }} {{ $text }}">{{ $date->day }}</span>
                                                        </td>
                                                        @php
                                                            $cursor->addDay();
                                                        @endphp
                                                    @endfor
                                                </tr>
                                            @endwhile
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
                                            @php
                                                $eventUrl = Route::has('admin.events.index') ? route('admin.events.index', ['q' => $event->judul]) : '#';
                                                $tone = $eventIsOngoing($event) ? 'success' : ($event->status === 'active' ? 'primary' : 'secondary');
                                                $statusText = $event->status === 'active' ? 'Aktif' : ($event->status ?: 'Draft');
                                            @endphp
                                            <a href="{{ $eventUrl }}" class="list-group-item list-group-item-action d-flex flex-wrap align-items-center justify-content-between gap-2">
                                                <div class="me-auto">
                                                    <div class="fw-semibold text-black">{{ $event->judul }}</div>
                                                    <div class="text-muted small">
                                                        {{ $event->tanggal_mulai?->format('d M Y') ?? '-' }}
                                                        @if (! empty($event->lokasi))
                                                            <span class="text-muted">·</span> {{ $event->lokasi }}
                                                        @endif
                                                    </div>
                                                </div>
                                                <span class="{{ $toneBadge($tone) }}">{{ $statusText }}</span>
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

