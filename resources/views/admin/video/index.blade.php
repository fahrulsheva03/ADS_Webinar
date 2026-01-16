@extends('admin.partials.app')

@section('content')
    @php
        $query = request()->query();
        $sortState = [
            'sort' => $sort ?? (string) request()->query('sort', 'created_at'),
            'dir' => $dir ?? (string) request()->query('dir', 'desc'),
        ];

        $sortUrl = function (string $column) use ($query, $sortState) {
            $isActive = ($sortState['sort'] === $column);
            $nextDir = $isActive && strtolower($sortState['dir']) === 'asc' ? 'desc' : 'asc';
            return url()->current() . '?' . http_build_query(array_merge($query, [
                'sort' => $column,
                'dir' => $nextDir,
            ]));
        };

        $sortIndicator = function (string $column) use ($sortState) {
            if ($sortState['sort'] !== $column) {
                return '';
            }
            return strtolower($sortState['dir']) === 'asc' ? '↑' : '↓';
        };

        $exportUrl = function (string $format) use ($query) {
            return route('admin.video.export', array_merge($query, ['format' => $format]));
        };

        $badgeStatus = function (?string $status) {
            $status = strtolower((string) $status);
            return match ($status) {
                'published' => ['bg-success', 'Published'],
                'draft' => ['bg-secondary', 'Draft'],
                default => ['bg-light text-dark', $status ?: '-'],
            };
        };

        $formatBytes = function (?int $bytes) {
            $bytes = (int) ($bytes ?? 0);
            if ($bytes <= 0) return '-';
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            $i = 0;
            $v = (float) $bytes;
            while ($v >= 1024 && $i < count($units) - 1) {
                $v /= 1024;
                $i++;
            }
            return number_format($v, $i === 0 ? 0 : 2) . ' ' . $units[$i];
        };

        $tagsText = function ($tags) {
            $tags = is_array($tags) ? $tags : [];
            $tags = array_values(array_filter(array_map('trim', $tags)));
            return implode(', ', $tags);
        };
    @endphp

    <main id="main-content" tabindex="-1" class="pb-4" role="main" aria-label="Manajemen video">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Rekaman & Video</li>
            </ol>
        </nav>

        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div class="me-auto">
                <h1 class="h3 mb-1">Upload & Manage Video</h1>
                <div class="text-muted">Upload rekaman sesi, atur metadata, tagging, dan kelola status publish.</div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <div class="btn-group" role="group" aria-label="Ekspor video">
                    <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        Ekspor
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ $exportUrl('csv') }}">CSV</a></li>
                        <li><a class="dropdown-item" href="{{ $exportUrl('xls') }}">Excel</a></li>
                    </ul>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="fw-semibold">Sukses</div>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="fw-semibold">Gagal</div>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="fw-semibold">Validasi gagal</div>
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        <div id="notify-area" class="mb-3" aria-live="polite" aria-atomic="true"></div>

        <section class="mb-4" aria-label="Panel upload video">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h2 class="h5 mb-0">Upload Video</h2>
                </div>
                <div class="card-body pt-3">
                    <div class="row g-3">
                        <div class="col-12 col-lg-4">
                            <label class="form-label text-black" for="upload-event">Event</label>
                            <select id="upload-event" class="form-select">
                                <option value="">Pilih event…</option>
                                @foreach ($events as $event)
                                    <option value="{{ $event->id }}" @selected((string) ($eventId ?? request('event_id')) === (string) $event->id)>{{ $event->judul }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-lg-4">
                            <label class="form-label text-black" for="upload-sesi">Sesi</label>
                            <select id="upload-sesi" class="form-select">
                                <option value="">Pilih sesi…</option>
                                @foreach ($sesi as $row)
                                    <option value="{{ $row->id }}" data-event-id="{{ $row->event_id }}" @selected((string) ($sesiId ?? request('event_sesi_id')) === (string) $row->id)>
                                        {{ $row->event?->judul ? $row->event->judul . ' — ' : '' }}{{ $row->judul_sesi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-lg-4">
                            <label class="form-label text-black" for="upload-status">Status</label>
                            <select id="upload-status" class="form-select">
                                <option value="published" @selected(($status ?? '') === 'published')>Published</option>
                                <option value="draft" @selected(($status ?? '') === 'draft')>Draft</option>
                            </select>
                        </div>

                        <div class="col-12 col-lg-8">
                            <label class="form-label text-black" for="upload-tags">Tag</label>
                            <div class="input-group">
                                <span class="input-group-text" data-bs-toggle="tooltip" title="Pisahkan dengan koma. Contoh: day1, materi, qna">#</span>
                                <input id="upload-tags" type="text" class="form-control" placeholder="day1, materi, qna">
                            </div>
                            <div class="form-text">Tag membantu filter dan pencarian video.</div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <label class="form-label text-black" for="upload-title">Judul (opsional)</label>
                            <input id="upload-title" type="text" class="form-control" maxlength="150" placeholder="Default: nama file">
                        </div>

                        <div class="col-12">
                            <div
                                class="p-4 border rounded bg-light text-center"
                                id="dropzone"
                                role="button"
                                tabindex="0"
                                aria-label="Area drag and drop upload video"
                            >
                                <div class="d-flex flex-column align-items-center gap-2">
                                    <div class="text-primary">
                                        <i class="la la-cloud-upload-alt" aria-hidden="true" style="font-size: 44px;"></i>
                                    </div>
                                    <div class="fw-semibold text-black">Drag & drop video ke sini</div>
                                    <div class="text-muted">atau klik untuk memilih file</div>
                                    <div class="text-muted small">
                                        Format: MP4, MOV, AVI · Maks: 500MB per file
                                    </div>
                                </div>
                                <input
                                    id="file-input"
                                    type="file"
                                    class="d-none"
                                    accept=".mp4,.mov,.avi,video/mp4,video/quicktime,video/x-msvideo"
                                    multiple
                                >
                            </div>
                        </div>

                        <div class="col-12">
                            <div id="upload-queue" class="d-grid gap-2"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mb-4" aria-label="Filter video">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.video.index') }}" class="row g-3 align-items-end">
                        <div class="col-12 col-lg-4">
                            <label class="form-label text-black" for="filter-q">Cari</label>
                            <input id="filter-q" name="q" type="search" class="form-control" placeholder="Judul, nama file, tag, sesi, event…" value="{{ $q ?? request('q') }}">
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label text-black" for="filter-event">Event</label>
                            <select id="filter-event" name="event_id" class="form-select">
                                <option value="">Semua event</option>
                                @foreach ($events as $event)
                                    <option value="{{ $event->id }}" @selected((string) ($eventId ?? request('event_id')) === (string) $event->id)>{{ $event->judul }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label text-black" for="filter-sesi">Sesi</label>
                            <select id="filter-sesi" name="event_sesi_id" class="form-select">
                                <option value="">Semua sesi</option>
                                @foreach ($sesi as $row)
                                    <option value="{{ $row->id }}" data-event-id="{{ $row->event_id }}" @selected((string) ($sesiId ?? request('event_sesi_id')) === (string) $row->id)>{{ $row->judul_sesi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-lg-2">
                            <label class="form-label text-black" for="filter-status">Status</label>
                            <select id="filter-status" name="status" class="form-select">
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(($status ?? request('status')) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-lg-12 d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary">Terapkan</button>
                            <a href="{{ route('admin.video.index') }}" class="btn btn-outline-secondary">Reset</a>
                            <div class="ms-auto d-flex flex-wrap gap-2">
                                <input type="hidden" name="sort" value="{{ $sortState['sort'] }}">
                                <input type="hidden" name="dir" value="{{ $sortState['dir'] }}">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <section aria-label="Daftar video">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <h2 class="h5 mb-0">Daftar Video</h2>
                        <div class="input-group input-group-sm" style="max-width: 340px;">
                            <span class="input-group-text">
                                <i class="flaticon-381-search-2 text-primary" aria-hidden="true"></i>
                            </span>
                            <input id="table-search" type="search" class="form-control" placeholder="Pencarian cepat di tabel…" autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="card-body pt-3">
                    <form id="bulk-form" method="POST" action="{{ route('admin.video.bulk', request()->query()) }}" class="mb-3" aria-label="Bulk actions video">
                        @csrf
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-md-4 col-lg-3">
                                <label class="form-label text-black" for="bulk-action">Bulk action</label>
                                <select id="bulk-action" name="action" class="form-select form-select-sm" required>
                                    <option value="">Pilih aksi…</option>
                                    <option value="set_status">Set status</option>
                                    <option value="set_tags">Set tags</option>
                                    <option value="delete">Hapus</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-4 col-lg-3 d-none" id="bulk-status-wrap">
                                <label class="form-label text-black" for="bulk-status">Status</label>
                                <select id="bulk-status" name="status" class="form-select form-select-sm">
                                    <option value="published">Published</option>
                                    <option value="draft">Draft</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-4 col-lg-4 d-none" id="bulk-tags-wrap">
                                <label class="form-label text-black" for="bulk-tags">Tags</label>
                                <input id="bulk-tags" name="tags" type="text" class="form-control form-control-sm" placeholder="day1, materi, qna">
                            </div>
                            <div class="col-12 col-lg-2 d-grid">
                                <button type="submit" class="btn btn-outline-primary btn-sm" id="bulk-apply" disabled>
                                    Terapkan
                                    <span class="spinner-border spinner-border-sm d-none" id="bulk-spinner" aria-hidden="true"></span>
                                </button>
                            </div>
                            <div class="col-12 col-lg-auto">
                                <div class="text-muted small" id="bulk-selected">0 dipilih</div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="video-table" aria-label="Tabel video">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 36px;">
                                        <input type="checkbox" class="form-check-input" id="check-all" aria-label="Pilih semua">
                                    </th>
                                    <th scope="col">Preview</th>
                                    <th scope="col" style="width: 84px;">Thumbnail</th>
                                    <th scope="col">
                                        <a class="text-decoration-none" href="{{ $sortUrl('judul_video') }}">Nama {!! $sortIndicator('judul_video') !!}</a>
                                    </th>
                                    <th scope="col">
                                        <a class="text-decoration-none" href="{{ $sortUrl('file_size_bytes') }}">Ukuran {!! $sortIndicator('file_size_bytes') !!}</a>
                                    </th>
                                    <th scope="col">
                                        <a class="text-decoration-none" href="{{ $sortUrl('durasi_menit') }}">Durasi {!! $sortIndicator('durasi_menit') !!}</a>
                                    </th>
                                    <th scope="col">
                                        <a class="text-decoration-none" href="{{ $sortUrl('created_at') }}">Tanggal Upload {!! $sortIndicator('created_at') !!}</a>
                                    </th>
                                    <th scope="col">
                                        <a class="text-decoration-none" href="{{ $sortUrl('status') }}">Status {!! $sortIndicator('status') !!}</a>
                                    </th>
                                    <th scope="col">Tag</th>
                                    <th scope="col" class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($videos as $video)
                                    @php
                                        [$badgeClass, $badgeText] = $badgeStatus($video->status);
                                        $thumbUrl = $video->thumbnail_path ? Storage::disk('public')->url($video->thumbnail_path) : null;
                                        $publicUrl = $video->file_path ? Storage::disk('public')->url($video->file_path) : $video->url_video;
                                        $payload = [
                                            'id' => $video->id,
                                            'judul_video' => $video->judul_video,
                                            'status' => $video->status,
                                            'tags' => $tagsText($video->tags ?? []),
                                            'update_url' => route('admin.video.update', ['video' => $video->id]),
                                            'delete_url' => route('admin.video.destroy', ['video' => $video->id]),
                                            'public_url' => $publicUrl,
                                            'thumbnail_url' => $thumbUrl,
                                            'file_name' => $video->file_name,
                                            'file_size_bytes' => (int) ($video->file_size_bytes ?? 0),
                                            'durasi_menit' => (int) ($video->durasi_menit ?? 0),
                                            'created_at' => optional($video->created_at)->format('Y-m-d H:i'),
                                            'event' => $video->sesi?->event?->judul,
                                            'sesi' => $video->sesi?->judul_sesi,
                                        ];
                                    @endphp
                                    <tr data-search-row data-row-id="{{ $video->id }}">
                                        <td>
                                            <input class="form-check-input bulk-checkbox" type="checkbox" name="ids[]" value="{{ $video->id }}" form="bulk-form" aria-label="Pilih video {{ $video->id }}">
                                        </td>
                                        <td>
                                            <button
                                                type="button"
                                                class="btn btn-outline-secondary btn-xxs"
                                                data-bs-toggle="modal"
                                                data-bs-target="#previewModal"
                                                data-video='@json($payload)'
                                                title="Preview"
                                            >
                                                <i class="la la-play" aria-hidden="true"></i>
                                            </button>
                                        </td>
                                        <td>
                                            @if ($thumbUrl)
                                                <img
                                                    src="{{ $thumbUrl }}"
                                                    alt="Thumbnail {{ $video->judul_video }}"
                                                    class="rounded"
                                                    style="width: 72px; height: 40px; object-fit: cover;"
                                                    loading="lazy"
                                                >
                                            @else
                                                <div class="bg-light rounded d-inline-flex align-items-center justify-content-center text-muted" style="width: 72px; height: 40px;">
                                                    <i class="la la-image" aria-hidden="true"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-black">{{ $video->judul_video }}</div>
                                            <div class="text-muted small">{{ $video->file_name ?? '-' }}</div>
                                            <div class="text-muted small">
                                                {{ $video->sesi?->event?->judul ?? '-' }} — {{ $video->sesi?->judul_sesi ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="text-muted">{{ $formatBytes((int) ($video->file_size_bytes ?? 0)) }}</td>
                                        <td class="text-muted">
                                            @if (!empty($video->durasi_menit))
                                                {{ (int) $video->durasi_menit }} menit
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-muted">{{ optional($video->created_at)->format('Y-m-d H:i') ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $badgeClass }}">{{ $badgeText }}</span>
                                        </td>
                                        <td class="text-muted">{{ $tagsText($video->tags ?? []) ?: '-' }}</td>
                                        <td class="text-end">
                                            <div class="d-inline-flex flex-wrap justify-content-end gap-2">
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-primary btn-xxs"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editModal"
                                                    data-video='@json($payload)'
                                                    title="Edit metadata"
                                                >
                                                    <i class="la la-pen" aria-hidden="true"></i>
                                                </button>
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-danger btn-xxs"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal"
                                                    data-video='@json($payload)'
                                                    title="Hapus"
                                                >
                                                    <i class="la la-trash" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10">
                                            <div class="text-center py-5">
                                                <div class="fw-semibold text-black mb-1">Belum ada video</div>
                                                <div class="text-muted">Upload video untuk mulai mengisi daftar.</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-4">
                        <div class="text-muted">
                            Menampilkan {{ $videos->firstItem() ?? 0 }}–{{ $videos->lastItem() ?? 0 }} dari {{ $videos->total() ?? 0 }}
                        </div>
                        <div>
                            {{ $videos->onEachSide(1)->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="previewModalTitle">Preview video</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="ratio ratio-16x9 bg-light rounded overflow-hidden">
                            <video id="preview-video" controls class="w-100 h-100" preload="metadata"></video>
                        </div>
                        <div class="mt-3">
                            <div class="fw-semibold text-black" id="preview-title"></div>
                            <div class="text-muted small" id="preview-meta"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="#" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary" id="preview-open">Buka di tab baru</a>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <form id="edit-form" method="POST" action="#">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h2 class="modal-title h5" id="editModalTitle">Edit metadata</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label text-black" for="edit-judul">Judul video</label>
                                    <input id="edit-judul" name="judul_video" type="text" class="form-control" maxlength="150" required>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="edit-status">Status</label>
                                    <select id="edit-status" name="status" class="form-select" required>
                                        <option value="published">Published</option>
                                        <option value="draft">Draft</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="edit-tags">Tags</label>
                                    <input id="edit-tags" name="tags" type="text" class="form-control" placeholder="day1, materi, qna">
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-black" for="edit-thumbnail">Thumbnail (opsional)</label>
                                    <input id="edit-thumbnail" name="thumbnail" type="file" class="form-control" accept="image/*">
                                    <div class="form-text">Jika kosong, thumbnail tetap memakai yang lama.</div>
                                </div>
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded">
                                        <div class="text-muted small">Info</div>
                                        <div class="fw-semibold text-black" id="edit-info"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="edit-submit">
                                Simpan
                                <span class="spinner-border spinner-border-sm d-none" id="edit-spinner" aria-hidden="true"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalTitle" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="deleteModalTitle">Hapus video</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-muted">Video ini akan dihapus permanen.</div>
                        <div class="mt-2 fw-semibold text-black" id="delete-title"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-danger" id="delete-confirm">
                            Hapus
                            <span class="spinner-border spinner-border-sm d-none" id="delete-spinner" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div
            id="page-loading"
            class="d-none position-fixed top-0 start-0 w-100 h-100 justify-content-center align-items-center"
            style="background: rgba(255,255,255,.55); z-index: 1055;"
            aria-hidden="true"
        >
            <div class="d-flex align-items-center gap-2 bg-white shadow-sm rounded px-3 py-2">
                <div class="spinner-border text-primary" role="status" aria-label="Memproses"></div>
                <div class="text-muted">Memproses…</div>
            </div>
        </div>

        <script>
            (function () {
                const csrfToken = @json(csrf_token());
                const storeUrl = @json(route('admin.video.store'));

                const dropzone = document.getElementById('dropzone');
                const fileInput = document.getElementById('file-input');
                const uploadQueue = document.getElementById('upload-queue');

                const uploadEvent = document.getElementById('upload-event');
                const uploadSesi = document.getElementById('upload-sesi');
                const uploadStatus = document.getElementById('upload-status');
                const uploadTags = document.getElementById('upload-tags');
                const uploadTitle = document.getElementById('upload-title');

                const filterEvent = document.getElementById('filter-event');
                const filterSesi = document.getElementById('filter-sesi');

                const tableSearch = document.getElementById('table-search');
                const table = document.getElementById('video-table');

                const bulkForm = document.getElementById('bulk-form');
                const bulkAction = document.getElementById('bulk-action');
                const bulkStatusWrap = document.getElementById('bulk-status-wrap');
                const bulkTagsWrap = document.getElementById('bulk-tags-wrap');
                const bulkApply = document.getElementById('bulk-apply');
                const bulkSpinner = document.getElementById('bulk-spinner');
                const bulkSelected = document.getElementById('bulk-selected');
                const checkAll = document.getElementById('check-all');

                const pageLoading = document.getElementById('page-loading');
                const notifyArea = document.getElementById('notify-area');

                const previewModalEl = document.getElementById('previewModal');
                const previewVideo = document.getElementById('preview-video');
                const previewTitle = document.getElementById('preview-title');
                const previewMeta = document.getElementById('preview-meta');
                const previewOpen = document.getElementById('preview-open');

                const editModalEl = document.getElementById('editModal');
                const editForm = document.getElementById('edit-form');
                const editJudul = document.getElementById('edit-judul');
                const editStatus = document.getElementById('edit-status');
                const editTags = document.getElementById('edit-tags');
                const editInfo = document.getElementById('edit-info');
                const editSubmit = document.getElementById('edit-submit');
                const editSpinner = document.getElementById('edit-spinner');

                const deleteModalEl = document.getElementById('deleteModal');
                const deleteTitle = document.getElementById('delete-title');
                const deleteConfirm = document.getElementById('delete-confirm');
                const deleteSpinner = document.getElementById('delete-spinner');

                let deleteUrl = '';

                function setPageLoading(on) {
                    if (!pageLoading) return;
                    pageLoading.classList.toggle('d-none', !on);
                    pageLoading.classList.toggle('d-flex', on);
                }

                function escapeHtml(value) {
                    return String(value ?? '')
                        .replaceAll('&', '&amp;')
                        .replaceAll('<', '&lt;')
                        .replaceAll('>', '&gt;')
                        .replaceAll('"', '&quot;')
                        .replaceAll("'", '&#039;');
                }

                function pushNotice(type, title, message) {
                    if (!notifyArea) return;
                    const wrapper = document.createElement('div');
                    wrapper.className = `alert alert-${type} alert-dismissible fade show`;
                    wrapper.setAttribute('role', 'alert');
                    wrapper.innerHTML = `
                        <div class="fw-semibold">${escapeHtml(title)}</div>
                        <div>${escapeHtml(message)}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                    `;
                    notifyArea.prepend(wrapper);
                }

                function beepSuccess() {
                    try {
                        const ctx = new (window.AudioContext || window.webkitAudioContext)();
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.type = 'sine';
                        osc.frequency.value = 880;
                        gain.gain.value = 0.06;
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.start();
                        setTimeout(() => {
                            osc.stop();
                            ctx.close();
                        }, 110);
                    } catch (_) {}
                }

                function filterSesiByEvent(selectEventEl, selectSesiEl) {
                    if (!selectEventEl || !selectSesiEl) return;
                    const eventId = selectEventEl.value;
                    const options = Array.from(selectSesiEl.options);
                    options.forEach((opt) => {
                        if (!opt.value) {
                            opt.hidden = false;
                            return;
                        }
                        const optEvent = opt.getAttribute('data-event-id') || '';
                        opt.hidden = !!eventId && String(optEvent) !== String(eventId);
                    });

                    if (selectSesiEl.value) {
                        const selected = selectSesiEl.selectedOptions[0];
                        if (selected && selected.hidden) {
                            selectSesiEl.value = '';
                        }
                    }
                }

                function formatBytes(bytes) {
                    const b = Number(bytes || 0);
                    if (!b) return '-';
                    const units = ['B', 'KB', 'MB', 'GB', 'TB'];
                    let v = b;
                    let i = 0;
                    while (v >= 1024 && i < units.length - 1) {
                        v /= 1024;
                        i++;
                    }
                    return `${v.toFixed(i === 0 ? 0 : 2)} ${units[i]}`;
                }

                function validFile(file) {
                    const maxBytes = 500 * 1024 * 1024;
                    const name = (file.name || '').toLowerCase();
                    const okExt = name.endsWith('.mp4') || name.endsWith('.mov') || name.endsWith('.avi');
                    if (!okExt) return { ok: false, message: 'Format file tidak didukung. Gunakan MP4, MOV, atau AVI.' };
                    if (file.size > maxBytes) return { ok: false, message: 'Ukuran file melebihi 500MB.' };
                    return { ok: true };
                }

                function createQueueItem(file) {
                    const wrap = document.createElement('div');
                    wrap.className = 'p-3 border rounded bg-white';
                    wrap.innerHTML = `
                        <div class="d-flex flex-wrap align-items-start justify-content-between gap-2">
                            <div>
                                <div class="fw-semibold text-black">${escapeHtml(file.name)}</div>
                                <div class="text-muted small">${escapeHtml(formatBytes(file.size))}</div>
                            </div>
                            <div class="text-muted small" data-status>Menunggu…</div>
                        </div>
                        <div class="progress mt-2" style="height: 10px;">
                            <div class="progress-bar" role="progressbar" style="width:0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    `;
                    uploadQueue.appendChild(wrap);
                    return {
                        el: wrap,
                        statusEl: wrap.querySelector('[data-status]'),
                        barEl: wrap.querySelector('.progress-bar'),
                    };
                }

                function extractMetadata(file) {
                    return new Promise((resolve) => {
                        const video = document.createElement('video');
                        video.preload = 'metadata';
                        video.muted = true;
                        const url = URL.createObjectURL(file);
                        video.src = url;

                        const done = (result) => {
                            URL.revokeObjectURL(url);
                            resolve(result);
                        };

                        video.onloadedmetadata = () => {
                            const duration = Number(video.duration || 0);
                            if (!Number.isFinite(duration) || duration <= 0) {
                                done({ durasiDetik: null, thumbnailBlob: null });
                                return;
                            }
                            const captureAt = Math.min(0.5, Math.max(0, duration / 10));
                            let settled = false;

                            const tryCapture = () => {
                                if (settled) return;
                                try {
                                    const canvas = document.createElement('canvas');
                                    canvas.width = 640;
                                    canvas.height = Math.round((640 / (video.videoWidth || 16)) * (video.videoHeight || 9));
                                    const ctx = canvas.getContext('2d');
                                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                                    canvas.toBlob((blob) => {
                                        settled = true;
                                        done({ durasiDetik: Math.round(duration), thumbnailBlob: blob || null });
                                    }, 'image/jpeg', 0.82);
                                } catch (_) {
                                    settled = true;
                                    done({ durasiDetik: Math.round(duration), thumbnailBlob: null });
                                }
                            };

                            video.currentTime = captureAt;
                            video.onseeked = () => {
                                tryCapture();
                            };

                            setTimeout(() => {
                                if (!settled) {
                                    settled = true;
                                    done({ durasiDetik: Math.round(duration), thumbnailBlob: null });
                                }
                            }, 1500);
                        };

                        video.onerror = () => done({ durasiDetik: null, thumbnailBlob: null });
                    });
                }

                function addRowToTable(data) {
                    if (!table) return;
                    const tbody = table.querySelector('tbody');
                    if (!tbody) return;

                    const tags = Array.isArray(data.tags) ? data.tags.join(', ') : '';
                    const status = String(data.status || '').toLowerCase();
                    const badge = status === 'published'
                        ? { cls: 'bg-success', label: 'Published' }
                        : status === 'draft'
                            ? { cls: 'bg-secondary', label: 'Draft' }
                            : { cls: 'bg-light text-dark', label: status || '-' };

                    const payload = {
                        id: data.id,
                        judul_video: data.judul_video,
                        status: data.status,
                        tags: tags,
                        update_url: data.update_url,
                        delete_url: data.delete_url,
                        public_url: data.public_url,
                        thumbnail_url: data.thumbnail_url,
                        file_name: data.file_name,
                        file_size_bytes: data.file_size_bytes,
                        durasi_menit: data.durasi_menit,
                        created_at: data.created_at,
                        event: data.event_judul,
                        sesi: data.sesi_judul,
                    };

                    const thumbHtml = payload.thumbnail_url
                        ? `<img src="${escapeHtml(payload.thumbnail_url)}" alt="Thumbnail ${escapeHtml(payload.judul_video || '')}" class="rounded" style="width:72px;height:40px;object-fit:cover;" loading="lazy">`
                        : '<div class="bg-light rounded d-inline-flex align-items-center justify-content-center text-muted" style="width:72px;height:40px;"><i class="la la-image" aria-hidden="true"></i></div>';

                    const tr = document.createElement('tr');
                    tr.setAttribute('data-search-row', '');
                    tr.dataset.rowId = String(data.id);
                    tr.innerHTML = `
                        <td><input class="form-check-input bulk-checkbox" type="checkbox" name="ids[]" value="${escapeHtml(data.id)}" form="bulk-form" aria-label="Pilih video ${escapeHtml(data.id)}"></td>
                        <td>
                            <button type="button" class="btn btn-outline-secondary btn-xxs" data-bs-toggle="modal" data-bs-target="#previewModal" data-video='${escapeHtml(JSON.stringify(payload))}' title="Preview">
                                <i class="la la-play" aria-hidden="true"></i>
                            </button>
                        </td>
                        <td>${thumbHtml}</td>
                        <td>
                            <div class="fw-semibold text-black">${escapeHtml(data.judul_video || '')}</div>
                            <div class="text-muted small">${escapeHtml(data.file_name || '-')}</div>
                            <div class="text-muted small">${escapeHtml((data.event_judul || '-') + ' — ' + (data.sesi_judul || '-'))}</div>
                        </td>
                        <td class="text-muted">${escapeHtml(formatBytes(data.file_size_bytes || 0))}</td>
                        <td class="text-muted">${data.durasi_menit ? escapeHtml(String(data.durasi_menit)) + ' menit' : '-'}</td>
                        <td class="text-muted">${escapeHtml((data.created_at || '').slice(0, 16).replace('T', ' ') || '-')}</td>
                        <td><span class="badge ${badge.cls}">${badge.label}</span></td>
                        <td class="text-muted">${escapeHtml(tags || '-')}</td>
                        <td class="text-end">
                            <div class="d-inline-flex flex-wrap justify-content-end gap-2">
                                <button type="button" class="btn btn-outline-primary btn-xxs" data-bs-toggle="modal" data-bs-target="#editModal" data-video='${escapeHtml(JSON.stringify(payload))}' title="Edit metadata">
                                    <i class="la la-pen" aria-hidden="true"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-xxs" data-bs-toggle="modal" data-bs-target="#deleteModal" data-video='${escapeHtml(JSON.stringify(payload))}' title="Hapus">
                                    <i class="la la-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </td>
                    `;
                    tbody.prepend(tr);
                }

                function uploadFile(file, meta) {
                    const sesiId = uploadSesi.value;
                    if (!sesiId) {
                        pushNotice('warning', 'Butuh sesi', 'Pilih sesi sebelum upload.');
                        return;
                    }

                    const item = createQueueItem(file);
                    item.statusEl.textContent = 'Menyiapkan…';

                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', storeUrl, true);
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                    xhr.upload.onprogress = (e) => {
                        if (!e.lengthComputable) return;
                        const percent = Math.round((e.loaded / e.total) * 100);
                        item.barEl.style.width = `${percent}%`;
                        item.barEl.setAttribute('aria-valuenow', String(percent));
                        item.statusEl.textContent = `Uploading… ${percent}%`;
                    };

                    xhr.upload.onload = () => {
                        item.barEl.style.width = '100%';
                        item.barEl.setAttribute('aria-valuenow', '100');
                        item.statusEl.textContent = 'Memproses…';
                    };

                    xhr.onload = () => {
                        try {
                            const json = JSON.parse(xhr.responseText || '{}');
                            if (xhr.status >= 200 && xhr.status < 300) {
                                item.statusEl.textContent = 'Selesai';
                                item.barEl.style.width = '100%';
                                item.barEl.setAttribute('aria-valuenow', '100');
                                item.barEl.classList.add('bg-success');
                                pushNotice('success', 'Sukses', json.message || 'Upload berhasil.');
                                beepSuccess();
                                if (json.data) {
                                    addRowToTable(json.data);
                                }
                            } else {
                                item.statusEl.textContent = 'Gagal';
                                item.barEl.style.width = '100%';
                                item.barEl.setAttribute('aria-valuenow', '100');
                                item.barEl.classList.add('bg-danger');
                                pushNotice('danger', 'Gagal', (json && json.message) ? json.message : 'Upload gagal.');
                            }
                        } catch (_) {
                            item.statusEl.textContent = 'Gagal';
                            item.barEl.style.width = '100%';
                            item.barEl.setAttribute('aria-valuenow', '100');
                            item.barEl.classList.add('bg-danger');
                            pushNotice('danger', 'Gagal', 'Upload gagal diproses.');
                        }
                    };

                    xhr.onerror = () => {
                        item.statusEl.textContent = 'Gagal';
                        item.barEl.classList.add('bg-danger');
                        pushNotice('danger', 'Gagal', 'Koneksi bermasalah saat upload.');
                    };

                    const form = new FormData();
                    form.set('event_sesi_id', sesiId);
                    form.set('status', uploadStatus.value || 'published');
                    if ((uploadTags.value || '').trim()) form.set('tags', (uploadTags.value || '').trim());
                    if ((uploadTitle.value || '').trim()) form.set('judul_video', (uploadTitle.value || '').trim());
                    if (meta && meta.durasiDetik != null) form.set('durasi_detik', String(meta.durasiDetik));
                    if (meta && meta.thumbnailBlob) form.set('thumbnail', meta.thumbnailBlob, 'thumbnail.jpg');
                    form.set('file', file, file.name);

                    item.statusEl.textContent = 'Uploading…';
                    xhr.send(form);
                }

                async function handleFiles(files) {
                    const list = Array.from(files || []);
                    for (const file of list) {
                        const check = validFile(file);
                        if (!check.ok) {
                            pushNotice('warning', 'File ditolak', `${file.name}: ${check.message}`);
                            continue;
                        }
                        const meta = await extractMetadata(file);
                        uploadFile(file, meta);
                    }
                }

                function updateBulkState() {
                    const boxes = Array.from(document.querySelectorAll('.bulk-checkbox'));
                    const selected = boxes.filter((c) => c.checked).length;
                    if (bulkSelected) bulkSelected.textContent = `${selected} dipilih`;
                    const needsValue = bulkAction.value === 'set_status' || bulkAction.value === 'set_tags' || bulkAction.value === 'delete';
                    bulkApply.disabled = selected === 0 || !needsValue || !bulkAction.value;
                }

                function wireBulk() {
                    if (!bulkAction) return;
                    bulkAction.addEventListener('change', () => {
                        const isStatus = bulkAction.value === 'set_status';
                        const isTags = bulkAction.value === 'set_tags';
                        bulkStatusWrap.classList.toggle('d-none', !isStatus);
                        bulkTagsWrap.classList.toggle('d-none', !isTags);
                        updateBulkState();
                    });
                    document.querySelectorAll('.bulk-checkbox').forEach((c) => c.addEventListener('change', updateBulkState));
                    if (checkAll) {
                        checkAll.addEventListener('change', () => {
                            const checked = checkAll.checked;
                            document.querySelectorAll('.bulk-checkbox').forEach((c) => (c.checked = checked));
                            updateBulkState();
                        });
                    }
                    updateBulkState();

                    if (bulkForm) {
                        bulkForm.addEventListener('submit', (e) => {
                            const selected = Array.from(document.querySelectorAll('.bulk-checkbox')).filter((c) => c.checked).length;
                            if (selected === 0) {
                                e.preventDefault();
                                return;
                            }
                            if (bulkAction.value === 'delete') {
                                const ok = window.confirm('Hapus semua video terpilih?');
                                if (!ok) {
                                    e.preventDefault();
                                    return;
                                }
                            }
                            setPageLoading(true);
                            bulkApply.disabled = true;
                            bulkSpinner.classList.remove('d-none');
                        });
                    }
                }

                function wireQuickSearch() {
                    if (!tableSearch || !table) return;
                    tableSearch.addEventListener('input', () => {
                        const q = (tableSearch.value || '').toLowerCase().trim();
                        const rows = Array.from(table.querySelectorAll('tbody tr[data-search-row]'));
                        rows.forEach((row) => {
                            const hay = (row.innerText || '').toLowerCase();
                            row.style.display = !q || hay.includes(q) ? '' : 'none';
                        });
                    });
                }

                async function requestJson(url, options) {
                    const res = await fetch(url, {
                        ...options,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken,
                            ...(options && options.headers ? options.headers : {}),
                        },
                    });
                    const contentType = res.headers.get('content-type') || '';
                    const payload = contentType.includes('application/json') ? await res.json() : null;
                    if (!res.ok) {
                        const msg = payload && payload.message ? payload.message : 'Permintaan gagal diproses.';
                        throw new Error(msg);
                    }
                    return payload;
                }

                if (previewModalEl) {
                    previewModalEl.addEventListener('show.bs.modal', (e) => {
                        const trigger = e.relatedTarget;
                        const payload = trigger?.getAttribute('data-video');
                        const data = payload ? JSON.parse(payload) : null;
                        if (!data) return;
                        const url = data.public_url || '';
                        if (previewVideo) {
                            previewVideo.src = url;
                            previewVideo.load();
                        }
                        if (previewTitle) previewTitle.textContent = data.judul_video || '';
                        if (previewMeta) {
                            previewMeta.textContent = `${data.event || '-'} — ${data.sesi || '-'} · ${data.file_name || '-'}`;
                        }
                        if (previewOpen) previewOpen.href = url || '#';
                    });
                    previewModalEl.addEventListener('hidden.bs.modal', () => {
                        if (previewVideo) {
                            previewVideo.pause();
                            previewVideo.removeAttribute('src');
                            previewVideo.load();
                        }
                    });
                }

                if (editModalEl) {
                    editModalEl.addEventListener('show.bs.modal', (e) => {
                        const trigger = e.relatedTarget;
                        const payload = trigger?.getAttribute('data-video');
                        const data = payload ? JSON.parse(payload) : null;
                        if (!data) return;
                        editForm.setAttribute('action', data.update_url || '#');
                        editJudul.value = data.judul_video || '';
                        editStatus.value = data.status || 'published';
                        editTags.value = data.tags || '';
                        editInfo.textContent = `${data.event || '-'} — ${data.sesi || '-'} · ${data.file_name || '-'}`;
                    });
                }

                if (editForm) {
                    editForm.addEventListener('submit', async (e) => {
                        e.preventDefault();
                        const action = editForm.getAttribute('action');
                        if (!action || action === '#') return;
                        try {
                            editSubmit.disabled = true;
                            editSpinner.classList.remove('d-none');
                            setPageLoading(true);
                            const formData = new FormData(editForm);
                            formData.set('_method', 'PUT');
                            const payload = await requestJson(action, { method: 'POST', body: formData });
                            pushNotice('success', 'Sukses', payload.message || 'Metadata diperbarui.');
                            window.location.reload();
                        } catch (err) {
                            pushNotice('danger', 'Gagal', err.message || 'Gagal menyimpan perubahan.');
                        } finally {
                            editSubmit.disabled = false;
                            editSpinner.classList.add('d-none');
                            setPageLoading(false);
                        }
                    });
                }

                if (deleteModalEl) {
                    deleteModalEl.addEventListener('show.bs.modal', (e) => {
                        const trigger = e.relatedTarget;
                        const payload = trigger?.getAttribute('data-video');
                        const data = payload ? JSON.parse(payload) : null;
                        deleteUrl = data?.delete_url || '';
                        deleteTitle.textContent = data?.judul_video || '';
                    });
                }

                if (deleteConfirm) {
                    deleteConfirm.addEventListener('click', async () => {
                        if (!deleteUrl) return;
                        try {
                            deleteConfirm.disabled = true;
                            deleteSpinner.classList.remove('d-none');
                            setPageLoading(true);
                            const form = new FormData();
                            form.set('_method', 'DELETE');
                            const payload = await requestJson(deleteUrl, { method: 'POST', body: form });
                            pushNotice('success', 'Sukses', payload.message || 'Video dihapus.');
                            window.location.reload();
                        } catch (err) {
                            pushNotice('danger', 'Gagal', err.message || 'Gagal menghapus video.');
                        } finally {
                            deleteConfirm.disabled = false;
                            deleteSpinner.classList.add('d-none');
                            setPageLoading(false);
                        }
                    });
                }

                if (dropzone && fileInput) {
                    dropzone.addEventListener('click', () => fileInput.click());
                    dropzone.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            fileInput.click();
                        }
                    });
                    fileInput.addEventListener('change', async () => {
                        await handleFiles(fileInput.files);
                        fileInput.value = '';
                    });
                    dropzone.addEventListener('dragover', (e) => {
                        e.preventDefault();
                        dropzone.classList.add('border-primary');
                    });
                    dropzone.addEventListener('dragleave', () => dropzone.classList.remove('border-primary'));
                    dropzone.addEventListener('drop', async (e) => {
                        e.preventDefault();
                        dropzone.classList.remove('border-primary');
                        const files = e.dataTransfer?.files;
                        await handleFiles(files);
                    });
                }

                if (uploadEvent && uploadSesi) {
                    filterSesiByEvent(uploadEvent, uploadSesi);
                    uploadEvent.addEventListener('change', () => filterSesiByEvent(uploadEvent, uploadSesi));
                }

                if (filterEvent && filterSesi) {
                    filterSesiByEvent(filterEvent, filterSesi);
                    filterEvent.addEventListener('change', () => filterSesiByEvent(filterEvent, filterSesi));
                }

                wireBulk();
                wireQuickSearch();

                const tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltips.map((el) => new bootstrap.Tooltip(el));
            })();
        </script>
    </main>
@endsection
