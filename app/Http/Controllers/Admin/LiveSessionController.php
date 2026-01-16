<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventSesi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class LiveSessionController extends Controller
{
    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');
        $status = (string) $request->query('status', 'live');

        $sort = (string) $request->query('sort', 'waktu_mulai');
        $dir = strtolower((string) $request->query('dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $sortable = [
            'id' => 'id',
            'judul_sesi' => 'judul_sesi',
            'status_sesi' => 'status_sesi',
            'waktu_mulai' => 'waktu_mulai',
            'waktu_selesai' => 'waktu_selesai',
            'jumlah_penonton' => 'jumlah_penonton',
        ];

        $query = EventSesi::query()
            ->with('event')
            ->withCount([
                'kehadiran as jumlah_penonton' => function ($q) {
                    $q->whereNull('waktu_leave');
                },
            ]);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('judul_sesi', 'like', "%{$q}%")
                    ->orWhereHas('event', function ($eventQuery) use ($q) {
                        $eventQuery->where('judul', 'like', "%{$q}%");
                    });
            });
        }

        if ($status !== '') {
            $query->where('status_sesi', $status);
        }

        $orderColumn = $sortable[$sort] ?? 'waktu_mulai';
        $query->orderBy($orderColumn, $dir);

        $sesi = $query->paginate(10)->withQueryString();
        $events = Event::query()->orderBy('tanggal_mulai', 'desc')->get(['id', 'judul']);

        $statusOptions = [
            '' => 'Semua status',
            'upcoming' => 'Upcoming',
            'live' => 'Live',
            'selesai' => 'Selesai',
        ];

        return view('admin.live.index', [
            'sesi' => $sesi,
            'events' => $events,
            'q' => $q,
            'status' => $status,
            'sort' => $sort,
            'dir' => $dir,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function poll(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ids' => 'nullable|string',
        ]);

        $ids = collect(explode(',', (string) ($data['ids'] ?? '')))
            ->map(fn ($v) => trim($v))
            ->filter()
            ->map(fn ($v) => (int) $v)
            ->filter(fn ($v) => $v > 0)
            ->unique()
            ->values()
            ->all();

        $query = EventSesi::query()
            ->withCount([
                'kehadiran as jumlah_penonton' => function ($q) {
                    $q->whereNull('waktu_leave');
                },
            ])
            ->select(['id', 'status_sesi', 'waktu_mulai', 'waktu_selesai']);

        if (! empty($ids)) {
            $query->whereIn('id', $ids);
        }

        $rows = $query->get()->map(function ($s) {
            return [
                'id' => $s->id,
                'status_sesi' => $s->status_sesi,
                'waktu_mulai' => optional($s->waktu_mulai)->format('Y-m-d H:i'),
                'waktu_selesai' => optional($s->waktu_selesai)->format('Y-m-d H:i'),
                'jumlah_penonton' => (int) ($s->jumlah_penonton ?? 0),
            ];
        });

        return response()->json([
            'data' => $rows,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateForm($request);

        $sesi = EventSesi::create([
            'event_id' => (int) $data['event_id'],
            'judul_sesi' => $data['judul_sesi'],
            'deskripsi_sesi' => $data['deskripsi_sesi'] ?? null,
            'waktu_mulai' => $data['waktu_mulai'],
            'waktu_selesai' => $data['waktu_selesai'],
            'zoom_link' => $data['zoom_link'] ?? null,
            'status_sesi' => $data['status_sesi'],
        ]);

        $sesi->load('event');
        $sesi->loadCount([
            'kehadiran as jumlah_penonton' => function ($q) {
                $q->whereNull('waktu_leave');
            },
        ]);

        return response()->json([
            'message' => 'Sesi berhasil dibuat.',
            'data' => $this->serializeSesi($sesi),
        ]);
    }

    public function update(Request $request, EventSesi $sesi): JsonResponse
    {
        $data = $this->validateForm($request);

        $sesi->update([
            'event_id' => (int) $data['event_id'],
            'judul_sesi' => $data['judul_sesi'],
            'deskripsi_sesi' => $data['deskripsi_sesi'] ?? null,
            'waktu_mulai' => $data['waktu_mulai'],
            'waktu_selesai' => $data['waktu_selesai'],
            'zoom_link' => $data['zoom_link'] ?? null,
            'status_sesi' => $data['status_sesi'],
        ]);

        $sesi->load('event');
        $sesi->loadCount([
            'kehadiran as jumlah_penonton' => function ($q) {
                $q->whereNull('waktu_leave');
            },
        ]);

        return response()->json([
            'message' => 'Sesi berhasil diperbarui.',
            'data' => $this->serializeSesi($sesi),
        ]);
    }

    public function start(EventSesi $sesi): JsonResponse
    {
        $now = now();

        $payload = [
            'status_sesi' => 'live',
        ];

        if ($sesi->waktu_mulai === null || $sesi->waktu_mulai->gt($now)) {
            $payload['waktu_mulai'] = $now;
        }

        $end = $sesi->waktu_selesai;
        $startAfter = $payload['waktu_mulai'] ?? $sesi->waktu_mulai ?? $now;
        if ($end === null || $end->lte($startAfter)) {
            $payload['waktu_selesai'] = $startAfter->copy()->addHour();
        }

        $sesi->update($payload);

        $sesi->loadCount([
            'kehadiran as jumlah_penonton' => function ($q) {
                $q->whereNull('waktu_leave');
            },
        ]);

        return response()->json([
            'message' => 'Sesi berhasil dimulai.',
            'data' => $this->serializeSesi($sesi),
        ]);
    }

    public function stop(EventSesi $sesi): JsonResponse
    {
        $now = now();

        $payload = [
            'status_sesi' => 'selesai',
        ];

        if ($sesi->waktu_selesai === null || $sesi->waktu_selesai->gt($now)) {
            $payload['waktu_selesai'] = $now;
        }

        $sesi->update($payload);

        $sesi->loadCount([
            'kehadiran as jumlah_penonton' => function ($q) {
                $q->whereNull('waktu_leave');
            },
        ]);

        return response()->json([
            'message' => 'Sesi berhasil dihentikan.',
            'data' => $this->serializeSesi($sesi),
        ]);
    }

    public function destroy(EventSesi $sesi): JsonResponse
    {
        try {
            $sesi->delete();

            return response()->json([
                'message' => 'Sesi berhasil dihapus.',
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Sesi gagal dihapus.',
            ], 422);
        }
    }

    private function validateForm(Request $request): array
    {
        return $request->validate([
            'event_id' => 'required|integer|exists:events,id',
            'judul_sesi' => 'required|string|max:100',
            'deskripsi_sesi' => 'nullable|string',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
            'zoom_link' => 'nullable|url|max:255',
            'status_sesi' => 'required|in:upcoming,live,selesai',
        ]);
    }

    private function serializeSesi(EventSesi $sesi): array
    {
        return [
            'id' => $sesi->id,
            'event_id' => $sesi->event_id,
            'event_judul' => $sesi->event?->judul,
            'judul_sesi' => $sesi->judul_sesi,
            'status_sesi' => $sesi->status_sesi,
            'waktu_mulai' => optional($sesi->waktu_mulai)->format('Y-m-d H:i'),
            'waktu_selesai' => optional($sesi->waktu_selesai)->format('Y-m-d H:i'),
            'zoom_link' => $sesi->zoom_link,
            'jumlah_penonton' => (int) ($sesi->jumlah_penonton ?? 0),
        ];
    }
}
