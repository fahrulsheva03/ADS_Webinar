<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventSesi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EventSesiController extends Controller
{
    private function isEmbedRequest(Request $request): bool
    {
        if ($request->boolean('embed')) {
            return true;
        }

        $referer = (string) $request->headers->get('referer', '');
        if ($referer === '') {
            return false;
        }

        $parts = parse_url($referer);
        if (! is_array($parts)) {
            return false;
        }

        $query = [];
        parse_str((string) ($parts['query'] ?? ''), $query);

        return (bool) ($query['embed'] ?? false);
    }

    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');
        $status = (string) $request->query('status', '');
        $eventId = (string) $request->query('event_id', '');

        $sort = (string) $request->query('sort', 'waktu_mulai');
        $dir = strtolower((string) $request->query('dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        $sortable = [
            'judul_sesi' => 'judul_sesi',
            'waktu_mulai' => 'waktu_mulai',
            'waktu_selesai' => 'waktu_selesai',
            'status_sesi' => 'status_sesi',
        ];

        $query = EventSesi::query()->with('event');

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('judul_sesi', 'like', "%{$q}%")
                    ->orWhere('deskripsi_sesi', 'like', "%{$q}%")
                    ->orWhereHas('event', function ($eventQuery) use ($q) {
                        $eventQuery->where('judul', 'like', "%{$q}%");
                    });
            });
        }

        if ($eventId !== '') {
            $query->where('event_id', $eventId);
        }

        if ($status !== '') {
            if ($status === 'aktif') {
                $query->where('status_sesi', 'live');
            } elseif ($status === 'nonaktif') {
                $query->where('status_sesi', '!=', 'live');
            } else {
                $query->where('status_sesi', $status);
            }
        }

        $orderColumn = $sortable[$sort] ?? 'waktu_mulai';
        $query->orderBy($orderColumn, $dir);

        $events = Event::query()->orderBy('tanggal_mulai', 'desc')->get(['id', 'judul']);
        $sesi = $query->paginate(10)->withQueryString();

        return view('admin.sesi_event.index', [
            'events' => $events,
            'sesi' => $sesi,
            'q' => $q,
            'status' => $status,
            'eventId' => $eventId,
            'sort' => $sort,
            'dir' => $dir,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateForm($request);

        [$start, $end] = $this->buildDateTimes($data);
        $this->assertEndAfterStart($start, $end);

        EventSesi::create([
            'event_id' => (int) $data['event_id'],
            'judul_sesi' => $data['judul_sesi'],
            'waktu_mulai' => $start,
            'waktu_selesai' => $end,
            'status_sesi' => ($data['aktif'] ?? false) ? 'live' : 'upcoming',
        ]);

        $params = $this->isEmbedRequest($request) ? ['embed' => 1] : [];

        return redirect()
            ->route('admin.sesi-event.index', $params)
            ->with('success', 'Sesi berhasil dibuat.');
    }

    public function update(Request $request, EventSesi $sesi)
    {
        $data = $this->validateForm($request);

        [$start, $end] = $this->buildDateTimes($data);
        $this->assertEndAfterStart($start, $end);

        $sesi->update([
            'event_id' => (int) $data['event_id'],
            'judul_sesi' => $data['judul_sesi'],
            'waktu_mulai' => $start,
            'waktu_selesai' => $end,
            'status_sesi' => ($data['aktif'] ?? false) ? 'live' : 'upcoming',
        ]);

        $params = $this->isEmbedRequest($request) ? ['embed' => 1] : [];

        return redirect()
            ->route('admin.sesi-event.index', $params)
            ->with('success', 'Perubahan sesi berhasil disimpan.');
    }

    public function destroy(Request $request, EventSesi $sesi)
    {
        $sesi->delete();

        $params = $this->isEmbedRequest($request) ? ['embed' => 1] : [];

        return redirect()
            ->route('admin.sesi-event.index', $params)
            ->with('success', 'Sesi berhasil dihapus.');
    }

    private function validateForm(Request $request): array
    {
        return $request->validate([
            'event_id' => 'required|integer|exists:events,id',
            'judul_sesi' => 'required|string|max:100',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i',
            'aktif' => 'required|boolean',
        ]);
    }

    private function buildDateTimes(array $data): array
    {
        $tanggal = $data['tanggal'];
        $start = Carbon::createFromFormat('Y-m-d H:i', $tanggal.' '.$data['waktu_mulai']);
        $end = Carbon::createFromFormat('Y-m-d H:i', $tanggal.' '.$data['waktu_selesai']);

        return [$start, $end];
    }

    private function assertEndAfterStart(Carbon $start, Carbon $end): void
    {
        if ($end->lt($start)) {
            throw ValidationException::withMessages([
                'waktu_selesai' => 'Waktu selesai harus setelah waktu mulai.',
            ]);
        }
    }
}
