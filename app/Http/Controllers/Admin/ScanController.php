<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventSesi;
use App\Models\KehadiranSesi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ScanController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::query()
            ->orderBy('tanggal_mulai', 'desc')
            ->get(['id', 'judul']);

        $sesi = EventSesi::query()
            ->with('event')
            ->orderBy('waktu_mulai', 'desc')
            ->get(['id', 'event_id', 'judul_sesi', 'waktu_mulai']);

        $history = $this->buildHistoryQuery($request)
            ->paginate(20)
            ->withQueryString();

        return view('admin.scan.index', [
            'events' => $events,
            'sesi' => $sesi,
            'history' => $history,
            'q' => (string) $request->query('q', ''),
            'eventId' => (string) $request->query('event_id', ''),
            'sesiId' => (string) $request->query('event_sesi_id', ''),
            'from' => (string) $request->query('from', ''),
            'to' => (string) $request->query('to', ''),
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $rows = $this->buildHistoryQuery($request)
            ->paginate(20)
            ->withQueryString();

        return response()->json([
            'data' => $rows->map(fn (KehadiranSesi $row) => $this->serializeHistoryRow($row))->values(),
            'meta' => [
                'current_page' => $rows->currentPage(),
                'last_page' => $rows->lastPage(),
                'per_page' => $rows->perPage(),
                'total' => $rows->total(),
                'from' => $rows->firstItem(),
                'to' => $rows->lastItem(),
            ],
        ]);
    }

    public function export(Request $request, string $format): StreamedResponse
    {
        $format = strtolower($format);
        abort_unless(in_array($format, ['csv', 'xls'], true), 404);

        $rows = $this->buildHistoryQuery($request)->get();
        $filename = 'scan-history-'.now()->format('Ymd-His').'.'.$format;

        if ($format === 'csv') {
            return response()->streamDownload(function () use ($rows) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['ID', 'Peserta', 'Email', 'Event', 'Sesi', 'Waktu Check-in']);
                foreach ($rows as $row) {
                    fputcsv($out, [
                        $row->id,
                        $row->user?->nama ?? '-',
                        $row->user?->email ?? '-',
                        $row->sesi?->event?->judul ?? '-',
                        $row->sesi?->judul_sesi ?? '-',
                        optional($row->waktu_join)->format('Y-m-d H:i:s') ?? '-',
                    ]);
                }
                fclose($out);
            }, $filename, [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        return response()->streamDownload(function () use ($rows) {
            echo '<html><head><meta charset="utf-8"></head><body>';
            echo '<table border="1">';
            echo '<tr><th>ID</th><th>Peserta</th><th>Email</th><th>Event</th><th>Sesi</th><th>Waktu Check-in</th></tr>';
            foreach ($rows as $row) {
                echo '<tr>';
                echo '<td>'.e($row->id).'</td>';
                echo '<td>'.e($row->user?->nama ?? '-').'</td>';
                echo '<td>'.e($row->user?->email ?? '-').'</td>';
                echo '<td>'.e($row->sesi?->event?->judul ?? '-').'</td>';
                echo '<td>'.e($row->sesi?->judul_sesi ?? '-').'</td>';
                echo '<td>'.e(optional($row->waktu_join)->format('Y-m-d H:i:s') ?? '-').'</td>';
                echo '</tr>';
            }
            echo '</table>';
            echo '</body></html>';
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    private function buildHistoryQuery(Request $request): Builder
    {
        $q = (string) $request->query('q', '');
        $eventId = (string) $request->query('event_id', '');
        $sesiId = (string) $request->query('event_sesi_id', '');
        $from = (string) $request->query('from', '');
        $to = (string) $request->query('to', '');

        $query = KehadiranSesi::query()
            ->with(['user', 'sesi.event'])
            ->orderByDesc('waktu_join');

        if ($q !== '') {
            $query->where(function (Builder $sub) use ($q) {
                $sub->whereHas('user', function (Builder $userQuery) use ($q) {
                    $userQuery->where('nama', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                })->orWhereHas('sesi', function (Builder $sesiQuery) use ($q) {
                    $sesiQuery->where('judul_sesi', 'like', "%{$q}%")
                        ->orWhereHas('event', function (Builder $eventQuery) use ($q) {
                            $eventQuery->where('judul', 'like', "%{$q}%");
                        });
                });
            });
        }

        if ($eventId !== '') {
            $query->whereHas('sesi', fn (Builder $sesiQuery) => $sesiQuery->where('event_id', $eventId));
        }

        if ($sesiId !== '') {
            $query->where('event_sesi_id', $sesiId);
        }

        if ($from !== '') {
            $query->whereDate('waktu_join', '>=', $from);
        }

        if ($to !== '') {
            $query->whereDate('waktu_join', '<=', $to);
        }

        return $query;
    }

    private function serializeHistoryRow(KehadiranSesi $row): array
    {
        return [
            'id' => $row->id,
            'user_nama' => $row->user?->nama,
            'user_email' => $row->user?->email,
            'event_judul' => $row->sesi?->event?->judul,
            'sesi_judul' => $row->sesi?->judul_sesi,
            'waktu_join' => optional($row->waktu_join)->format('Y-m-d H:i:s'),
            'event_id' => $row->sesi?->event_id,
            'event_sesi_id' => $row->event_sesi_id,
        ];
    }
}
