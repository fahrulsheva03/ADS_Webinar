<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventSesi;
use App\Models\VideoSesi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');
        $eventId = (string) $request->query('event_id', '');
        $sesiId = (string) $request->query('event_sesi_id', '');
        $status = (string) $request->query('status', '');

        $sort = (string) $request->query('sort', 'created_at');
        $dir = strtolower((string) $request->query('dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $sortable = [
            'id' => 'id',
            'judul_video' => 'judul_video',
            'file_name' => 'file_name',
            'durasi_menit' => 'durasi_menit',
            'file_size_bytes' => 'file_size_bytes',
            'status' => 'status',
            'created_at' => 'created_at',
        ];

        $query = VideoSesi::query()->with(['sesi.event']);

        if ($q !== '') {
            $query->where(function (Builder $sub) use ($q) {
                $sub->where('judul_video', 'like', "%{$q}%")
                    ->orWhere('file_name', 'like', "%{$q}%")
                    ->orWhereJsonContains('tags', $q)
                    ->orWhereHas('sesi', function (Builder $sesiQuery) use ($q) {
                        $sesiQuery->where('judul_sesi', 'like', "%{$q}%")
                            ->orWhereHas('event', fn (Builder $eventQuery) => $eventQuery->where('judul', 'like', "%{$q}%"));
                    });
            });
        }

        if ($eventId !== '') {
            $query->whereHas('sesi', fn (Builder $sesiQuery) => $sesiQuery->where('event_id', $eventId));
        }

        if ($sesiId !== '') {
            $query->where('event_sesi_id', $sesiId);
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        $orderColumn = $sortable[$sort] ?? 'created_at';
        $query->orderBy($orderColumn, $dir);

        $videos = $query->paginate(15)->withQueryString();

        $events = Event::query()->orderBy('tanggal_mulai', 'desc')->get(['id', 'judul']);
        $sesi = EventSesi::query()
            ->with('event')
            ->orderBy('waktu_mulai', 'desc')
            ->get(['id', 'event_id', 'judul_sesi', 'waktu_mulai']);

        $statusOptions = [
            '' => 'Semua status',
            'published' => 'Published',
            'draft' => 'Draft',
        ];

        return view('admin.video.index', [
            'videos' => $videos,
            'events' => $events,
            'sesi' => $sesi,
            'q' => $q,
            'eventId' => $eventId,
            'sesiId' => $sesiId,
            'status' => $status,
            'sort' => $sort,
            'dir' => $dir,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'event_sesi_id' => 'required|integer|exists:event_sesi,id',
            'judul_video' => 'nullable|string|max:150',
            'tags' => 'nullable|string|max:500',
            'status' => 'nullable|in:published,draft',
            'durasi_detik' => 'nullable|integer|min:0',
            'thumbnail' => 'nullable|image|max:5120',
            'file' => 'required|file|max:512000|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/avi',
        ]);

        $file = $request->file('file');
        $filePath = $file->store('videos', 'public');

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('videos/thumbnails', 'public');
        }

        $tags = $this->parseTags($data['tags'] ?? null);

        $durasiMenit = null;
        if (isset($data['durasi_detik'])) {
            $durasiMenit = (int) ceil(((int) $data['durasi_detik']) / 60);
        }

        $judul = trim((string) ($data['judul_video'] ?? ''));
        if ($judul === '') {
            $judul = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) ?: 'Video';
        }

        $video = VideoSesi::create([
            'event_sesi_id' => (int) $data['event_sesi_id'],
            'judul_video' => $judul,
            'url_video' => Storage::disk('public')->url($filePath),
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_size_bytes' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'thumbnail_path' => $thumbnailPath,
            'tags' => $tags,
            'status' => $data['status'] ?? 'published',
            'durasi_menit' => $durasiMenit,
        ]);

        if (empty($thumbnailPath)) {
            $video->generateThumbnailIfMissing();
            $video->refresh();
        }

        $video->load(['sesi.event']);

        return response()->json([
            'message' => 'Video berhasil diupload.',
            'data' => $this->serializeVideo($video),
        ]);
    }

    public function update(Request $request, VideoSesi $video): JsonResponse
    {
        $data = $request->validate([
            'judul_video' => 'required|string|max:150',
            'tags' => 'nullable|string|max:500',
            'status' => 'required|in:published,draft',
            'thumbnail' => 'nullable|image|max:5120',
        ]);

        $payload = [
            'judul_video' => $data['judul_video'],
            'tags' => $this->parseTags($data['tags'] ?? null),
            'status' => $data['status'],
        ];

        if ($request->hasFile('thumbnail')) {
            $newThumb = $request->file('thumbnail')->store('videos/thumbnails', 'public');
            $old = $video->thumbnail_path;
            $payload['thumbnail_path'] = $newThumb;
            if ($old) {
                Storage::disk('public')->delete($old);
            }
        }

        $video->update($payload);
        $video->load(['sesi.event']);

        return response()->json([
            'message' => 'Metadata video berhasil diperbarui.',
            'data' => $this->serializeVideo($video),
        ]);
    }

    public function destroy(VideoSesi $video): JsonResponse
    {
        try {
            $filePath = $video->file_path;
            $thumbPath = $video->thumbnail_path;
            $video->delete();

            if ($filePath) {
                Storage::disk('public')->delete($filePath);
            }
            if ($thumbPath) {
                Storage::disk('public')->delete($thumbPath);
            }

            return response()->json([
                'message' => 'Video berhasil dihapus.',
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Video gagal dihapus.',
            ], 422);
        }
    }

    public function bulk(Request $request)
    {
        $data = $request->validate([
            'action' => 'required|in:delete,set_status,set_tags',
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:video_sesi,id',
            'status' => 'nullable|in:published,draft',
            'tags' => 'nullable|string|max:500',
        ]);

        $videos = VideoSesi::query()->whereIn('id', $data['ids'])->get();

        if ($data['action'] === 'set_status') {
            abort_if(empty($data['status']), 422);
            VideoSesi::query()->whereIn('id', $data['ids'])->update(['status' => $data['status']]);

            return redirect()
                ->route('admin.video.index', $request->query())
                ->with('success', 'Status video terpilih berhasil diperbarui.');
        }

        if ($data['action'] === 'set_tags') {
            $tags = $this->parseTags($data['tags'] ?? null);
            foreach ($videos as $video) {
                $video->update(['tags' => $tags]);
            }

            return redirect()
                ->route('admin.video.index', $request->query())
                ->with('success', 'Tag video terpilih berhasil diperbarui.');
        }

        foreach ($videos as $video) {
            $filePath = $video->file_path;
            $thumbPath = $video->thumbnail_path;
            $video->delete();

            if ($filePath) {
                Storage::disk('public')->delete($filePath);
            }
            if ($thumbPath) {
                Storage::disk('public')->delete($thumbPath);
            }
        }

        return redirect()
            ->route('admin.video.index', $request->query())
            ->with('success', 'Video terpilih berhasil dihapus.');
    }

    public function export(Request $request, string $format): StreamedResponse
    {
        $format = strtolower($format);
        abort_unless(in_array($format, ['csv', 'xls'], true), 404);

        $rows = $this->exportQuery($request)->get();
        $filename = 'video-'.now()->format('Ymd-His').'.'.$format;

        if ($format === 'csv') {
            return response()->streamDownload(function () use ($rows) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['ID', 'Judul', 'Nama File', 'Ukuran (bytes)', 'Durasi (menit)', 'Status', 'Event', 'Sesi', 'Tanggal Upload', 'Tags']);
                foreach ($rows as $row) {
                    fputcsv($out, [
                        $row->id,
                        $row->judul_video,
                        $row->file_name,
                        $row->file_size_bytes,
                        $row->durasi_menit,
                        $row->status,
                        $row->sesi?->event?->judul ?? '-',
                        $row->sesi?->judul_sesi ?? '-',
                        optional($row->created_at)->format('Y-m-d H:i:s') ?? '-',
                        implode(', ', $row->tags ?? []),
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
            echo '<tr><th>ID</th><th>Judul</th><th>Nama File</th><th>Ukuran (bytes)</th><th>Durasi (menit)</th><th>Status</th><th>Event</th><th>Sesi</th><th>Tanggal Upload</th><th>Tags</th></tr>';
            foreach ($rows as $row) {
                echo '<tr>';
                echo '<td>'.e($row->id).'</td>';
                echo '<td>'.e($row->judul_video).'</td>';
                echo '<td>'.e($row->file_name).'</td>';
                echo '<td>'.e((string) $row->file_size_bytes).'</td>';
                echo '<td>'.e((string) $row->durasi_menit).'</td>';
                echo '<td>'.e((string) $row->status).'</td>';
                echo '<td>'.e($row->sesi?->event?->judul ?? '-').'</td>';
                echo '<td>'.e($row->sesi?->judul_sesi ?? '-').'</td>';
                echo '<td>'.e(optional($row->created_at)->format('Y-m-d H:i:s') ?? '-').'</td>';
                echo '<td>'.e(implode(', ', $row->tags ?? [])).'</td>';
                echo '</tr>';
            }
            echo '</table>';
            echo '</body></html>';
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    private function exportQuery(Request $request): Builder
    {
        $q = (string) $request->query('q', '');
        $eventId = (string) $request->query('event_id', '');
        $sesiId = (string) $request->query('event_sesi_id', '');
        $status = (string) $request->query('status', '');

        $query = VideoSesi::query()->with(['sesi.event'])->orderByDesc('created_at');

        if ($q !== '') {
            $query->where(function (Builder $sub) use ($q) {
                $sub->where('judul_video', 'like', "%{$q}%")
                    ->orWhere('file_name', 'like', "%{$q}%")
                    ->orWhereJsonContains('tags', $q)
                    ->orWhereHas('sesi', function (Builder $sesiQuery) use ($q) {
                        $sesiQuery->where('judul_sesi', 'like', "%{$q}%")
                            ->orWhereHas('event', fn (Builder $eventQuery) => $eventQuery->where('judul', 'like', "%{$q}%"));
                    });
            });
        }

        if ($eventId !== '') {
            $query->whereHas('sesi', fn (Builder $sesiQuery) => $sesiQuery->where('event_id', $eventId));
        }

        if ($sesiId !== '') {
            $query->where('event_sesi_id', $sesiId);
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        return $query;
    }

    private function parseTags(?string $raw): array
    {
        $raw = trim((string) $raw);
        if ($raw === '') {
            return [];
        }

        $tags = collect(preg_split('/[,\n]+/', $raw))
            ->map(fn ($t) => trim((string) $t))
            ->filter()
            ->map(fn ($t) => mb_substr($t, 0, 30))
            ->unique()
            ->values()
            ->all();

        return $tags;
    }

    private function serializeVideo(VideoSesi $video): array
    {
        $publicUrl = null;
        if (! empty($video->file_path)) {
            $publicUrl = Storage::disk('public')->url($video->file_path);
        } elseif (! empty($video->url_video)) {
            $publicUrl = $video->url_video;
        }

        $thumbUrl = null;
        if (! empty($video->thumbnail_path)) {
            $thumbUrl = Storage::disk('public')->url($video->thumbnail_path);
        }

        return [
            'id' => $video->id,
            'judul_video' => $video->judul_video,
            'file_name' => $video->file_name,
            'file_size_bytes' => (int) ($video->file_size_bytes ?? 0),
            'durasi_menit' => (int) ($video->durasi_menit ?? 0),
            'status' => $video->status,
            'tags' => $video->tags ?? [],
            'created_at' => optional($video->created_at)->format('Y-m-d H:i:s'),
            'event_judul' => $video->sesi?->event?->judul,
            'sesi_judul' => $video->sesi?->judul_sesi,
            'event_id' => $video->sesi?->event_id,
            'event_sesi_id' => $video->event_sesi_id,
            'public_url' => $publicUrl,
            'thumbnail_url' => $thumbUrl,
            'update_url' => route('admin.video.update', ['video' => $video->id]),
            'delete_url' => route('admin.video.destroy', ['video' => $video->id]),
        ];
    }
}
