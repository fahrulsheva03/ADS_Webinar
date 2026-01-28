<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventSesi;
use App\Models\KehadiranSesi;
use App\Models\Pesanan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

class DashboardController extends Controller
{
    public function index()
    {
        $now = now();
        $todayStart = $now->copy()->startOfDay();
        $todayEnd = $now->copy()->endOfDay();

        $badgeClass = $this->buildBadgeClasses([
            'success',
            'primary',
            'danger',
            'secondary',
            'warning',
            'light',
            'info',
        ]);

        $allActiveEvents = Event::query()
            ->where('status', 'active')
            ->orderByDesc('tanggal_mulai')
            ->get();

        $ongoingEvents = $allActiveEvents
            ->filter(fn (Event $event) => $this->eventIsOngoing($event, $now))
            ->values();

        $ongoingEvents->each(function (Event $event) use ($now) {
            $event->dash_progress_pct = $this->eventProgress($event, $now);
            $event->dash_url = Route::has('admin.events.index')
                ? route('admin.events.index', ['q' => $event->judul])
                : '#';
        });

        $ongoingCount = $ongoingEvents->count();
        $allActiveCount = $allActiveEvents->count();
        $ongoingAvgProgress = $ongoingCount > 0
            ? (int) round($ongoingEvents->map(fn (Event $event) => (int) $event->dash_progress_pct)->avg() ?? 0)
            : 0;

        $todaySessions = EventSesi::query()
            ->with('event')
            ->where('waktu_mulai', '<', $todayEnd)
            ->where('waktu_selesai', '>', $todayStart)
            ->orderBy('waktu_mulai')
            ->get();

        $todaySessionIds = $todaySessions->pluck('id')->values();

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

        $todaySessions->each(function (EventSesi $sesi) use ($now) {
            $status = $this->sessionStatus($sesi, $now);
            $sesi->dash_status_tone = $status['tone'];
            $sesi->dash_status_text = $status['text'];

            $start = $sesi->waktu_mulai;
            $end = $sesi->waktu_selesai;
            $sesi->dash_time_label = ($start ? $start->format('H:i') : '-').'–'.($end ? $end->format('H:i') : '-');

            $sesi->dash_lokasi = ! empty($sesi->zoom_link)
                ? 'Online (Zoom)'
                : ($sesi->event?->lokasi ?: '-');

            $sesi->dash_action_live_url = Route::has('admin.live.index')
                ? route('admin.live.index', ['q' => $sesi->judul_sesi, 'status' => ''])
                : '#';

            $sesi->dash_action_scan_url = Route::has('admin.scan.index')
                ? route('admin.scan.index')
                : '#';
        });

        $prioritySession = $todaySessions
            ->first(fn (EventSesi $sesi) => $sesi->waktu_selesai && $now->lte($sesi->waktu_selesai));

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

                $priorityAbsentUsers->each(function (User $user) {
                    $user->dash_status_tone = $user->status_akun === 'aktif' ? 'success' : 'secondary';
                });
            }
        }

        $upcomingSoonCount = $todaySessions
            ->filter(function (EventSesi $sesi) use ($now) {
                if (! $sesi->waktu_mulai) {
                    return false;
                }

                if ($sesi->status_sesi === 'live') {
                    return false;
                }

                return $sesi->waktu_mulai->gt($now) && $sesi->waktu_mulai->diffInMinutes($now) <= 30;
            })
            ->count();

        $overrunLiveCount = EventSesi::query()
            ->where('status_sesi', 'live')
            ->whereNotNull('waktu_selesai')
            ->where('waktu_selesai', '<', $now->copy()->subMinutes(10))
            ->count();

        $pendingPaymentCount = Pesanan::query()
            ->where('status_pembayaran', 'pending')
            ->count();

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
                'actionUrl' => Route::has('admin.laporan.kehadiran.index')
                    ? route('admin.laporan.kehadiran.index', ['from' => $now->toDateString(), 'to' => $now->toDateString()])
                    : '#',
            ]);
        }
        if ($pendingPaymentCount > 0) {
            $alerts->push([
                'tone' => 'secondary',
                'level' => 'Low',
                'title' => "Ada {$pendingPaymentCount} transaksi Pending.",
                'desc' => 'Tinjau pembayaran untuk memperlancar akses peserta.',
                'actionLabel' => 'Buka Transaksi',
                'actionUrl' => Route::has('admin.transaksi.index')
                    ? route('admin.transaksi.index', ['status' => 'pending'])
                    : '#',
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

        $upcomingEvents->each(function (Event $event) use ($now) {
            $event->dash_url = Route::has('admin.events.index')
                ? route('admin.events.index', ['q' => $event->judul])
                : '#';
            $event->dash_status_tone = $this->eventIsOngoing($event, $now)
                ? 'success'
                : ($event->status === 'active' ? 'primary' : 'secondary');
            $event->dash_status_text = $event->status === 'active'
                ? 'Aktif'
                : ($event->status ?: 'Draft');
        });

        $calendarRows = $this->buildCalendarRows($now);

        return view('admin.index', [
            'now' => $now,
            'badgeClass' => $badgeClass,
            'ongoingEvents' => $ongoingEvents,
            'ongoingCount' => $ongoingCount,
            'allActiveCount' => $allActiveCount,
            'ongoingAvgProgress' => $ongoingAvgProgress,
            'todaySessions' => $todaySessions,
            'expectedCount' => $expectedCount,
            'hadirCount' => $hadirCount,
            'tidakHadirCount' => $tidakHadirCount,
            'hadirPct' => $hadirPct,
            'tidakHadirPct' => $tidakHadirPct,
            'onlineNowCount' => $onlineNowCount,
            'prioritySession' => $prioritySession,
            'priorityAbsentUsers' => $priorityAbsentUsers,
            'alerts' => $alerts,
            'calendarRows' => $calendarRows,
            'upcomingEvents' => $upcomingEvents,
        ]);
    }

    private function buildBadgeClasses(array $tones): array
    {
        $out = [];
        foreach ($tones as $tone) {
            $out[$tone] = $this->badgeClass($tone);
        }

        return $out;
    }

    private function badgeClass(string $tone): string
    {
        $tone = strtolower($tone);
        $className = "badge bg-{$tone}";
        if (in_array($tone, ['warning', 'light', 'info'], true)) {
            $className .= ' text-dark';
        }

        return $className;
    }

    private function eventIsOngoing(Event $event, Carbon $now): bool
    {
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
    }

    private function eventProgress(Event $event, Carbon $now): int
    {
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
    }

    private function sessionStatus(EventSesi $sesi, Carbon $now): array
    {
        $start = $sesi->waktu_mulai;
        $end = $sesi->waktu_selesai;

        $tone = 'primary';
        $text = 'Terjadwal';

        if ($start && $end && $now->gte($start) && $now->lte($end)) {
            $tone = 'success';
            $text = 'Berjalan';
        } elseif ($end && $now->gt($end)) {
            $tone = 'secondary';
            $text = 'Selesai';
        } elseif ($start && $start->gt($now) && $start->diffInMinutes($now) <= 30) {
            $tone = 'warning';
            $text = 'Mulai Sebentar Lagi';
        }

        if ($sesi->status_sesi === 'live' && $text !== 'Selesai') {
            $tone = 'success';
            $text = 'Live';
        }

        if ($sesi->status_sesi === 'selesai') {
            $tone = 'secondary';
            $text = 'Selesai';
        }

        return ['tone' => $tone, 'text' => $text];
    }

    private function buildCalendarRows(Carbon $now): array
    {
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

        $rows = [];
        $cursor = $calendarStart->copy();
        while ($cursor->lte($calendarEnd)) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $date = $cursor->copy();
                $key = $date->toDateString();
                $inMonth = $date->month === $now->month;
                $isToday = $key === $now->toDateString();
                $hasEvent = isset($calendarEventSet[$key]);

                $bgClass = '';
                $textClass = $inMonth ? 'text-black' : 'text-muted';
                if ($hasEvent) {
                    $bgClass = $isToday ? 'bg-success text-white' : 'bg-primary text-white';
                    $textClass = '';
                } elseif ($isToday) {
                    $bgClass = 'bg-light text-dark';
                    $textClass = '';
                }

                $week[] = [
                    'day' => $date->day,
                    'bgClass' => $bgClass,
                    'textClass' => $textClass,
                ];

                $cursor->addDay();
            }

            $rows[] = $week;
        }

        return $rows;
    }
}
