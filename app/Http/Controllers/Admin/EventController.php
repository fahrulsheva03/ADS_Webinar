<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function image(Event $event)
    {
        $raw = (string) ($event->gambar_utama ?? '');
        $raw = trim($raw);

        if ($raw === '') {
            abort(404);
        }

        if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
            return redirect()->away($raw);
        }

        $path = ltrim($raw, '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }
        if (str_starts_with($path, 'public/')) {
            $path = substr($path, strlen('public/'));
        }

        abort_if(str_contains($path, '..'), 404);

        $disk = Storage::disk('public');
        abort_if(! $disk->exists($path), 404);

        return response()->file($disk->path($path));
    }

    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');
        $status = (string) $request->query('status', '');
        $from = (string) $request->query('from', '');
        $to = (string) $request->query('to', '');

        $sort = (string) $request->query('sort', 'tanggal_mulai');
        $dir = strtolower((string) $request->query('dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $sortable = [
            'judul' => 'judul',
            'tanggal_mulai' => 'tanggal_mulai',
            'tanggal_selesai' => 'tanggal_selesai',
            'status' => 'status',
            'created_at' => 'created_at',
        ];

        $query = Event::query()->with('creator');

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('judul', 'like', "%{$q}%")
                    ->orWhere('deskripsi', 'like', "%{$q}%");
            });
        }

        if ($status !== '') {
            if ($status === 'aktif') {
                $query->where('status', 'active');
            } elseif ($status === 'nonaktif') {
                $query->where('status', '!=', 'active');
            } else {
                $query->where('status', $status);
            }
        }

        if ($from !== '') {
            $query->whereDate('tanggal_mulai', '>=', $from);
        }

        if ($to !== '') {
            $query->whereDate('tanggal_mulai', '<=', $to);
        }

        $orderColumn = $sortable[$sort] ?? 'tanggal_mulai';
        $query->orderBy($orderColumn, $dir);

        $statsQuery = clone $query;
        $totalCount = (clone $statsQuery)->count();
        $aktifCount = (clone $statsQuery)->where('status', 'active')->count();
        $nonaktifCount = $totalCount - $aktifCount;

        $events = $query->paginate(10)->withQueryString();

        return view('admin.event.index', [
            'events' => $events,
            'totalCount' => $totalCount,
            'aktifCount' => $aktifCount,
            'nonaktifCount' => $nonaktifCount,
            'q' => $q,
            'status' => $status,
            'from' => $from,
            'to' => $to,
            'sort' => $sort,
            'dir' => $dir,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateEvent($request);

        $creatorId = $this->resolveCreatorId();

        $path = null;
        if ($request->hasFile('gambar_utama')) {
            $path = $request->file('gambar_utama')->store('events', 'public');
        }

        Event::create([
            'judul' => $data['judul'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'tanggal_mulai' => $data['tanggal_mulai'],
            'tanggal_selesai' => $data['tanggal_selesai'],
            'lokasi' => $data['lokasi'] ?? null,
            'gambar_utama' => $path,
            'status' => ($data['aktif'] ?? false) ? 'active' : 'draft',
            'created_by' => $creatorId,
        ]);

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event berhasil dibuat.');
    }

    public function update(Request $request, Event $event)
    {
        $data = $this->validateEvent($request);

        $path = $event->gambar_utama;
        if ($request->hasFile('gambar_utama')) {
            $newPath = $request->file('gambar_utama')->store('events', 'public');
            if ($path) {
                Storage::disk('public')->delete($path);
            }
            $path = $newPath;
        }

        $event->update([
            'judul' => $data['judul'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'tanggal_mulai' => $data['tanggal_mulai'],
            'tanggal_selesai' => $data['tanggal_selesai'],
            'lokasi' => $data['lokasi'] ?? null,
            'gambar_utama' => $path,
            'status' => ($data['aktif'] ?? false) ? 'active' : 'draft',
        ]);

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Perubahan event berhasil disimpan.');
    }

    public function destroy(Event $event)
    {
        $path = $event->gambar_utama;
        $event->delete();

        if ($path) {
            Storage::disk('public')->delete($path);
        }

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event berhasil dihapus.');
    }

    public function bulk(Request $request)
    {
        $data = $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:events,id',
        ]);

        $events = Event::query()->whereIn('id', $data['ids'])->get();

        if ($data['action'] === 'activate') {
            Event::query()->whereIn('id', $data['ids'])->update(['status' => 'active']);

            return redirect()->route('admin.events.index')->with('success', 'Event terpilih berhasil diaktifkan.');
        }

        if ($data['action'] === 'deactivate') {
            Event::query()->whereIn('id', $data['ids'])->update(['status' => 'draft']);

            return redirect()->route('admin.events.index')->with('success', 'Event terpilih berhasil dinonaktifkan.');
        }

        foreach ($events as $event) {
            $path = $event->gambar_utama;
            $event->delete();
            if ($path) {
                Storage::disk('public')->delete($path);
            }
        }

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event terpilih berhasil dihapus.');
    }

    public function export(Request $request, string $format)
    {
        $format = strtolower($format);
        if (! in_array($format, ['csv', 'xls'], true)) {
            abort(404);
        }

        $q = (string) $request->query('q', '');
        $status = (string) $request->query('status', '');
        $from = (string) $request->query('from', '');
        $to = (string) $request->query('to', '');

        $sort = (string) $request->query('sort', 'tanggal_mulai');
        $dir = strtolower((string) $request->query('dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $sortable = [
            'judul' => 'judul',
            'tanggal_mulai' => 'tanggal_mulai',
            'tanggal_selesai' => 'tanggal_selesai',
            'status' => 'status',
            'created_at' => 'created_at',
        ];

        $query = Event::query()->with('creator');

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('judul', 'like', "%{$q}%")
                    ->orWhere('deskripsi', 'like', "%{$q}%");
            });
        }

        if ($status !== '') {
            if ($status === 'aktif') {
                $query->where('status', 'active');
            } elseif ($status === 'nonaktif') {
                $query->where('status', '!=', 'active');
            } else {
                $query->where('status', $status);
            }
        }

        if ($from !== '') {
            $query->whereDate('tanggal_mulai', '>=', $from);
        }

        if ($to !== '') {
            $query->whereDate('tanggal_mulai', '<=', $to);
        }

        $orderColumn = $sortable[$sort] ?? 'tanggal_mulai';
        $query->orderBy($orderColumn, $dir);

        $filename = $format === 'xls' ? 'events.xls' : 'events.csv';
        $contentType = $format === 'xls'
            ? 'application/vnd.ms-excel; charset=UTF-8'
            : 'text/csv; charset=UTF-8';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Judul', 'Tanggal Mulai', 'Tanggal Selesai', 'Lokasi', 'Status', 'Dibuat Oleh', 'Dibuat Pada']);

            foreach ($query->cursor() as $event) {
                fputcsv($out, [
                    $event->judul,
                    optional($event->tanggal_mulai)->format('Y-m-d'),
                    optional($event->tanggal_selesai)->format('Y-m-d'),
                    $event->lokasi ?? '',
                    $event->status,
                    optional($event->creator)->nama ?? '',
                    optional($event->created_at)->toDateTimeString(),
                ]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => $contentType,
        ]);
    }

    private function validateEvent(Request $request): array
    {
        return $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'lokasi' => 'nullable|string|max:255',
            'gambar_utama' => 'nullable|image|max:2048',
            'aktif' => 'nullable|boolean',
        ]);
    }

    private function resolveCreatorId(): int
    {
        $id = Auth::id();
        if ($id) {
            return (int) $id;
        }

        $adminId = User::query()->where('role', 'admin')->value('id');
        if ($adminId) {
            return (int) $adminId;
        }

        $anyId = User::query()->value('id');
        if ($anyId) {
            return (int) $anyId;
        }

        $user = User::query()->create([
            'nama' => 'System Admin',
            'email' => 'system@webinar.local',
            'password' => Hash::make(Str::random(64)),
            'role' => 'admin',
            'status_akun' => 'aktif',
        ]);

        return (int) $user->id;
    }
}
