<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventSesi;
use App\Models\Paket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class PaketController extends Controller
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

        $sort = (string) $request->query('sort', 'id');
        $dir = strtolower((string) $request->query('dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $sortable = [
            'id' => 'id',
            'nama_paket' => 'nama_paket',
            'harga' => 'harga',
            'status' => 'status',
            'created_at' => 'created_at',
        ];

        $query = Paket::query()->with('event');

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_paket', 'like', "%{$q}%")
                    ->orWhere('deskripsi', 'like', "%{$q}%");
            });
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        $orderColumn = $sortable[$sort] ?? 'id';
        $query->orderBy($orderColumn, $dir);

        $paket = $query->paginate(10)->withQueryString();
        $events = Event::query()->orderBy('tanggal_mulai', 'desc')->get(['id', 'judul']);

        return view('admin.paket.index', [
            'paket' => $paket,
            'events' => $events,
            'q' => $q,
            'status' => $status,
            'sort' => $sort,
            'dir' => $dir,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateForm($request);

        Paket::create([
            'event_id' => (int) $data['event_id'],
            'nama_paket' => $data['nama_paket'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'harga' => $data['harga'],
            'status' => $data['status'],
            'akses_live' => false,
            'akses_rekaman' => false,
            'kuota' => null,
        ]);

        $params = $this->isEmbedRequest($request) ? ['embed' => 1] : [];

        return redirect()
            ->route('admin.paket.index', $params)
            ->with('success', 'Paket berhasil dibuat.');
    }

    public function update(Request $request, Paket $paket)
    {
        $data = $this->validateForm($request);

        $paket->update([
            'event_id' => (int) $data['event_id'],
            'nama_paket' => $data['nama_paket'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'harga' => $data['harga'],
            'status' => $data['status'],
        ]);

        $params = $this->isEmbedRequest($request) ? ['embed' => 1] : [];

        return redirect()
            ->route('admin.paket.index', $params)
            ->with('success', 'Perubahan paket berhasil disimpan.');
    }

    public function destroy(Request $request, Paket $paket)
    {
        $params = $this->isEmbedRequest($request) ? ['embed' => 1] : [];

        try {
            $paket->delete();

            return redirect()
                ->route('admin.paket.index', $params)
                ->with('success', 'Paket berhasil dihapus.');
        } catch (Throwable $e) {
            return redirect()
                ->route('admin.paket.index', $params)
                ->with('error', 'Paket gagal dihapus. Pastikan tidak ada pesanan atau relasi aktif.');
        }
    }

    public function akses()
    {
        $paket = Paket::query()
            ->orderBy('id', 'desc')
            ->get(['id', 'nama_paket', 'status', 'event_id']);

        $sesi = EventSesi::query()
            ->with('event')
            ->orderBy('waktu_mulai', 'asc')
            ->get(['id', 'event_id', 'judul_sesi', 'waktu_mulai', 'waktu_selesai', 'status_sesi']);

        return view('admin.paket.akses', [
            'paket' => $paket,
            'sesi' => $sesi,
        ]);
    }

    public function assignedSesi(Paket $paket): JsonResponse
    {
        return response()->json([
            'paket_id' => $paket->id,
            'assigned_ids' => $paket->sesi()->pluck('event_sesi.id')->all(),
        ]);
    }

    public function syncSesi(Request $request, Paket $paket): JsonResponse
    {
        $data = $request->validate([
            'sesi_ids' => 'present|array',
            'sesi_ids.*' => 'integer|exists:event_sesi,id',
        ]);

        DB::transaction(function () use ($paket, $data) {
            $paket->sesi()->sync($data['sesi_ids'] ?? []);
        });

        return response()->json([
            'message' => 'Relasi paket dan sesi berhasil disimpan.',
        ]);
    }

    public function detachSesi(Paket $paket, EventSesi $sesi): JsonResponse
    {
        $paket->sesi()->detach($sesi->id);

        return response()->json([
            'message' => 'Sesi berhasil di-unassign dari paket.',
        ]);
    }

    private function validateForm(Request $request): array
    {
        return $request->validate([
            'event_id' => 'required|integer|exists:events,id',
            'nama_paket' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ]);
    }
}
