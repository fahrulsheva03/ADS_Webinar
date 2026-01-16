<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventSesi;
use App\Models\KehadiranSesi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanKehadiranController extends Controller
{
    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');
        $eventId = (string) $request->query('event_id', '');
        $sesiId = (string) $request->query('event_sesi_id', '');
        $from = (string) $request->query('from', '');
        $to = (string) $request->query('to', '');
        $status = (string) $request->query('status', '');

        $sort = (string) $request->query('sort', 'tanggal');
        $dir = strtolower((string) $request->query('dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $sortable = [
            'nama' => 'nama',
            'tanggal' => 'waktu_join',
            'jam_masuk' => 'waktu_join',
            'jam_keluar' => 'waktu_leave',
            'status' => 'waktu_leave',
        ];

        $query = $this->buildQuery($request)->with(['user', 'sesi.event']);

        if ($sort === 'nama') {
            $query->leftJoin('users', 'users.id', '=', 'kehadiran_sesi.user_id')
                ->select('kehadiran_sesi.*')
                ->orderBy('users.nama', $dir);
        } else {
            $orderColumn = $sortable[$sort] ?? 'waktu_join';
            $query->orderBy($orderColumn, $dir);
        }

        $rows = $query->paginate(20)->withQueryString();

        $events = Event::query()->orderBy('tanggal_mulai', 'desc')->get(['id', 'judul']);
        $sesi = EventSesi::query()
            ->with('event')
            ->orderBy('waktu_mulai', 'desc')
            ->get(['id', 'event_id', 'judul_sesi', 'waktu_mulai']);

        return view('admin.laporan.kehadiran', [
            'rows' => $rows,
            'events' => $events,
            'sesi' => $sesi,
            'q' => $q,
            'eventId' => $eventId,
            'sesiId' => $sesiId,
            'from' => $from,
            'to' => $to,
            'status' => $status,
            'sort' => $sort,
            'dir' => $dir,
        ]);
    }

    public function export(Request $request, string $format): StreamedResponse
    {
        $format = strtolower($format);
        abort_unless(in_array($format, ['csv', 'xls'], true), 404);

        $delimiter = $format === 'csv' ? ',' : "\t";
        $mime = $format === 'csv' ? 'text/csv' : 'application/vnd.ms-excel';
        $filename = 'laporan-kehadiran-'.now()->format('Ymd-His').'.'.$format;

        $query = $this->buildQuery($request)
            ->with(['user', 'sesi.event'])
            ->orderByDesc('waktu_join');

        $headers = ['ID', 'Nama', 'Email', 'Tanggal', 'Jam Masuk', 'Jam Keluar', 'Status', 'Event', 'Sesi'];

        return response()->streamDownload(function () use ($query, $headers, $delimiter, $format) {
            $out = fopen('php://output', 'w');
            if ($format === 'csv') {
                fwrite($out, "\xEF\xBB\xBF");
            }
            fputcsv($out, $headers, $delimiter);

            $query->chunkById(1000, function ($rows) use ($out, $delimiter) {
                foreach ($rows as $row) {
                    $status = empty($row->waktu_leave) ? 'Hadir' : 'Keluar';
                    fputcsv($out, [
                        $row->id,
                        $row->user?->nama ?? '-',
                        $row->user?->email ?? '',
                        optional($row->waktu_join)->format('Y-m-d') ?? '-',
                        optional($row->waktu_join)->format('H:i:s') ?? '-',
                        optional($row->waktu_leave)->format('H:i:s') ?? '-',
                        $status,
                        $row->sesi?->event?->judul ?? '-',
                        $row->sesi?->judul_sesi ?? '-',
                    ], $delimiter);
                }
            });

            fclose($out);
        }, $filename, [
            'Content-Type' => $mime.'; charset=UTF-8',
        ]);
    }

    private function buildQuery(Request $request): Builder
    {
        $q = (string) $request->query('q', '');
        $eventId = (string) $request->query('event_id', '');
        $sesiId = (string) $request->query('event_sesi_id', '');
        $from = (string) $request->query('from', '');
        $to = (string) $request->query('to', '');
        $status = (string) $request->query('status', '');

        $query = KehadiranSesi::query();

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

        if ($status !== '') {
            $statusLower = strtolower($status);
            if ($statusLower === 'hadir') {
                $query->whereNull('waktu_leave');
            } elseif ($statusLower === 'keluar') {
                $query->whereNotNull('waktu_leave');
            }
        }

        return $query;
    }
}
