@extends('admin.partials.app')

@section('content')
    @php
        $badgeClass = function (string $tone) {
            $tone = strtolower($tone);
            $className = "badge bg-{$tone}";
            if (in_array($tone, ['warning', 'light'], true)) {
                $className .= ' text-dark';
            }
            return $className;
        };

        $query = request()->query();
        $sortState = [
            'sort' => $sort ?? (string) request()->query('sort', 'tanggal_mulai'),
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

        $statusOptions = [
            '' => 'Semua status',
            'aktif' => 'Aktif',
            'nonaktif' => 'Non-aktif',
            'draft' => 'Draft',
            'published' => 'Published',
            'active' => 'Active',
            'finished' => 'Finished',
        ];
    @endphp

    <main id="main-content" tabindex="-1" class="pb-4" role="main" aria-label="Manajemen event">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div class="me-auto">
                <h1 class="h3 mb-1">Manajemen Event</h1>
                <div class="text-muted">Kelola daftar event, status, dan aset pendukung.</div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <div class="btn-group" role="group" aria-label="Aksi cepat event">
                    <button
                        type="button"
                        class="btn btn-primary btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#eventModal"
                        data-event-mode="create"
                    >
                        Buat event
                    </button>
                    <button
                        type="button"
                        class="btn btn-outline-secondary btn-sm dropdown-toggle"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        Ekspor
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.events.export', array_merge(request()->query(), ['format' => 'csv'])) }}">CSV</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.events.export', array_merge(request()->query(), ['format' => 'xls'])) }}">Excel</a>
                        </li>
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

        <section class="mb-4" aria-label="Statistik event">
            <div class="row">
                <div class="col-12 col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="text-muted">Total event</div>
                            <div class="fs-30 fw-semibold text-black">{{ $totalCount ?? 0 }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="text-muted">Aktif</div>
                            <div class="d-flex align-items-baseline gap-2">
                                <div class="fs-30 fw-semibold text-black">{{ $aktifCount ?? 0 }}</div>
                                <span class="{{ $badgeClass('success') }}">Aktif</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="text-muted">Non-aktif</div>
                            <div class="d-flex align-items-baseline gap-2">
                                <div class="fs-30 fw-semibold text-black">{{ $nonaktifCount ?? 0 }}</div>
                                <span class="{{ $badgeClass('secondary') }}">Non-aktif</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mb-4" aria-label="Filter event">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.events.index') }}" class="row g-3 align-items-end">
                        <div class="col-12 col-lg-4">
                            <label class="form-label text-black" for="filter-q">Cari</label>
                            <input
                                id="filter-q"
                                name="q"
                                type="search"
                                class="form-control"
                                placeholder="Judul atau deskripsi…"
                                value="{{ $q ?? request('q') }}"
                                autocomplete="off"
                            >
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label text-black" for="filter-status">Status</label>
                            <select id="filter-status" name="status" class="form-select">
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(($status ?? request('status')) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-3 col-lg-2">
                            <label class="form-label text-black" for="filter-from">Dari</label>
                            <input id="filter-from" name="from" type="date" class="form-control" value="{{ $from ?? request('from') }}">
                        </div>
                        <div class="col-6 col-md-3 col-lg-2">
                            <label class="form-label text-black" for="filter-to">Sampai</label>
                            <input id="filter-to" name="to" type="date" class="form-control" value="{{ $to ?? request('to') }}">
                        </div>
                        <div class="col-12 col-lg-1 d-grid">
                            <button type="submit" class="btn btn-primary">Terapkan</button>
                        </div>

                        <input type="hidden" name="sort" value="{{ $sortState['sort'] }}">
                        <input type="hidden" name="dir" value="{{ $sortState['dir'] }}">
                    </form>
                </div>
            </div>
        </section>

        <section aria-label="Daftar event">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <h2 class="h5 mb-0">Daftar Event</h2>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <div class="input-group input-group-sm" style="max-width: 340px;">
                                <span class="input-group-text">
                                    <i class="flaticon-381-search-2 text-primary" aria-hidden="true"></i>
                                </span>
                                <input id="table-search" type="search" class="form-control" placeholder="Filter cepat di halaman ini…" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body pt-3">
                    <form method="POST" action="{{ route('admin.events.bulk') }}" id="bulk-form">
                        @csrf

                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <div class="fw-semibold text-black">Bulk actions</div>
                                <span class="text-muted" id="bulk-selected">0 dipilih</span>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <select class="form-select form-select-sm" name="action" style="min-width: 220px;" required>
                                    <option value="" selected disabled>Pilih aksi…</option>
                                    <option value="activate">Aktifkan</option>
                                    <option value="deactivate">Nonaktifkan</option>
                                    <option value="delete">Hapus</option>
                                </select>
                                <button type="submit" class="btn btn-outline-primary btn-sm" id="bulk-apply" disabled>Terapkan</button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped align-middle mb-0" aria-label="Tabel event" id="events-table">
                                <thead>
                                    <tr>
                                        <th scope="col" style="width: 40px;">
                                            <input type="checkbox" class="form-check-input" id="checkAll" aria-label="Pilih semua">
                                        </th>
                                        <th scope="col">
                                            <a class="text-black" href="{{ $sortUrl('judul') }}">Judul event {{ $sortIndicator('judul') }}</a>
                                        </th>
                                        <th scope="col">
                                            <a class="text-black" href="{{ $sortUrl('tanggal_mulai') }}">Tanggal pelaksanaan {{ $sortIndicator('tanggal_mulai') }}</a>
                                        </th>
                                        <th scope="col">Lokasi</th>
                                        <th scope="col">
                                            <a class="text-black" href="{{ $sortUrl('status') }}">Status {{ $sortIndicator('status') }}</a>
                                        </th>
                                        <th scope="col" class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($events as $event)
                                        @php
                                            $isActive = $event->status === 'active';
                                            $statusLabel = $isActive ? 'Aktif' : 'Non-aktif';
                                            $statusTone = $isActive ? 'success' : 'secondary';
                                            $gambar = (string) ($event->gambar_utama ?? '');
                                            if ($gambar === '') {
                                                $imageUrl = '';
                                            } elseif (str_starts_with($gambar, 'http://') || str_starts_with($gambar, 'https://')) {
                                                $imageUrl = $gambar;
                                            } else {
                                                $imageUrl = route('admin.events.image', $event);
                                            }
                                        @endphp
                                        <tr data-search-row>
                                            <td>
                                                <input type="checkbox" class="form-check-input bulk-checkbox" name="ids[]" value="{{ $event->id }}" aria-label="Pilih event {{ $event->judul }}">
                                            </td>
                                            <th scope="row" class="text-black">
                                                <div class="fw-semibold">{{ $event->judul }}</div>
                                                <div class="text-muted small">
                                                    Dibuat oleh: {{ $event->creator?->nama ?? '-' }}
                                                </div>
                                            </th>
                                            <td class="text-muted">
                                                {{ optional($event->tanggal_mulai)->format('d M Y') }}
                                                <span class="text-muted">–</span>
                                                {{ optional($event->tanggal_selesai)->format('d M Y') }}
                                            </td>
                                            <td class="text-muted">{{ $event->lokasi ?? '-' }}</td>
                                            <td>
                                                <span class="{{ $badgeClass($statusTone) }}">{{ $statusLabel }}</span>
                                                <div class="text-muted small">({{ $event->status }})</div>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group" role="group" aria-label="Aksi event">
                                                    <button
                                                        type="button"
                                                        class="btn btn-outline-primary btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#eventModal"
                                                        data-event-mode="edit"
                                                        data-event-id="{{ $event->id }}"
                                                        data-event-judul="{{ $event->judul }}"
                                                        data-event-deskripsi="{{ $event->deskripsi }}"
                                                        data-event-tanggal-mulai="{{ optional($event->tanggal_mulai)->format('Y-m-d') }}"
                                                        data-event-tanggal-selesai="{{ optional($event->tanggal_selesai)->format('Y-m-d') }}"
                                                        data-event-lokasi="{{ $event->lokasi }}"
                                                        data-event-aktif="{{ $isActive ? '1' : '0' }}"
                                                        data-event-image="{{ $imageUrl }}"
                                                    >
                                                        Edit
                                                    </button>
                                                    <button
                                                        type="button"
                                                        class="btn btn-outline-danger btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal"
                                                        data-delete-action="{{ route('admin.events.destroy', $event) }}"
                                                        data-delete-title="{{ $event->judul }}"
                                                    >
                                                        Hapus
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                Tidak ada event ditemukan.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </form>

                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3">
                        <div class="text-muted">
                            Menampilkan {{ $events->firstItem() ?? 0 }}–{{ $events->lastItem() ?? 0 }} dari {{ $events->total() ?? 0 }}
                        </div>
                        <div>
                            {{ $events->onEachSide(1)->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @php
            $activeEventTab = (string) request()->query('tab', 'sesi-event');
            if (! in_array($activeEventTab, ['sesi-event', 'paket'], true)) {
                $activeEventTab = 'sesi-event';
            }

            $activePaketTab = (string) request()->query('paket_tab', 'index');
            if (! in_array($activePaketTab, ['index', 'akses'], true)) {
                $activePaketTab = 'index';
            }

            $sesiEmbedUrl = route('admin.sesi-event.index', ['embed' => 1]);
            $paketIndexEmbedUrl = route('admin.paket.index', ['embed' => 1]);
            $paketAksesEmbedUrl = route('admin.paket.akses', ['embed' => 1]);
        @endphp

        <section class="mt-4" aria-label="Manajemen sesi event dan paket">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h2 class="h5 mb-0">Sesi Event & Paket</h2>
                            <div class="text-muted">Kelola sesi dan paket tanpa meninggalkan halaman event.</div>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-3">
                    <ul class="nav nav-tabs flex-nowrap overflow-auto" id="event-management-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link {{ $activeEventTab === 'sesi-event' ? 'active' : '' }}"
                                id="event-tab-sesi-button"
                                type="button"
                                role="tab"
                                data-bs-toggle="tab"
                                data-bs-target="#event-tab-sesi"
                                aria-controls="event-tab-sesi"
                                aria-selected="{{ $activeEventTab === 'sesi-event' ? 'true' : 'false' }}"
                                data-tab-key="sesi-event"
                            >
                                Sesi Event
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link {{ $activeEventTab === 'paket' ? 'active' : '' }}"
                                id="event-tab-paket-button"
                                type="button"
                                role="tab"
                                data-bs-toggle="tab"
                                data-bs-target="#event-tab-paket"
                                aria-controls="event-tab-paket"
                                aria-selected="{{ $activeEventTab === 'paket' ? 'true' : 'false' }}"
                                data-tab-key="paket"
                            >
                                Paket
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content mt-3" id="event-management-tabs-content">
                        <div
                            class="tab-pane fade {{ $activeEventTab === 'sesi-event' ? 'show active' : '' }}"
                            id="event-tab-sesi"
                            role="tabpanel"
                            aria-labelledby="event-tab-sesi-button"
                            tabindex="0"
                        >
                            <iframe
                                data-embed-frame
                                title="Manajemen Sesi Event"
                                class="w-100 border rounded"
                                style="min-height: 70vh;"
                                loading="lazy"
                                data-src="{{ $sesiEmbedUrl }}"
                                src="{{ $activeEventTab === 'sesi-event' ? $sesiEmbedUrl : '' }}"
                            ></iframe>
                        </div>

                        <div
                            class="tab-pane fade {{ $activeEventTab === 'paket' ? 'show active' : '' }}"
                            id="event-tab-paket"
                            role="tabpanel"
                            aria-labelledby="event-tab-paket-button"
                            tabindex="0"
                        >
                            <ul class="nav nav-pills flex-nowrap overflow-auto mb-3" id="paket-management-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button
                                        class="nav-link {{ $activePaketTab === 'index' ? 'active' : '' }}"
                                        id="paket-tab-index-button"
                                        type="button"
                                        role="tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#paket-tab-index"
                                        aria-controls="paket-tab-index"
                                        aria-selected="{{ $activePaketTab === 'index' ? 'true' : 'false' }}"
                                        data-paket-tab="index"
                                    >
                                        List Paket
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button
                                        class="nav-link {{ $activePaketTab === 'akses' ? 'active' : '' }}"
                                        id="paket-tab-akses-button"
                                        type="button"
                                        role="tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#paket-tab-akses"
                                        aria-controls="paket-tab-akses"
                                        aria-selected="{{ $activePaketTab === 'akses' ? 'true' : 'false' }}"
                                        data-paket-tab="akses"
                                    >
                                        Assign Akses
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content" id="paket-management-tabs-content">
                                <div
                                    class="tab-pane fade {{ $activePaketTab === 'index' ? 'show active' : '' }}"
                                    id="paket-tab-index"
                                    role="tabpanel"
                                    aria-labelledby="paket-tab-index-button"
                                    tabindex="0"
                                >
                                    <iframe
                                        data-embed-frame
                                        title="Manajemen Paket"
                                        class="w-100 border rounded"
                                        style="min-height: 70vh;"
                                        loading="lazy"
                                        data-src="{{ $paketIndexEmbedUrl }}"
                                        src="{{ $activeEventTab === 'paket' && $activePaketTab === 'index' ? $paketIndexEmbedUrl : '' }}"
                                    ></iframe>
                                </div>
                                <div
                                    class="tab-pane fade {{ $activePaketTab === 'akses' ? 'show active' : '' }}"
                                    id="paket-tab-akses"
                                    role="tabpanel"
                                    aria-labelledby="paket-tab-akses-button"
                                    tabindex="0"
                                >
                                    <iframe
                                        data-embed-frame
                                        title="Assign Paket dan Sesi"
                                        class="w-100 border rounded"
                                        style="min-height: 70vh;"
                                        loading="lazy"
                                        data-src="{{ $paketAksesEmbedUrl }}"
                                        src="{{ $activeEventTab === 'paket' && $activePaketTab === 'akses' ? $paketAksesEmbedUrl : '' }}"
                                    ></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <form
                        id="event-form"
                        method="POST"
                        action="{{ route('admin.events.store') }}"
                        enctype="multipart/form-data"
                    >
                        @csrf
                        <input type="hidden" name="_method" id="event-form-method" value="PUT" disabled>
                        <div class="modal-header">
                            <h2 class="modal-title h5" id="eventModalTitle">Buat event</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body overflow-auto" style="max-height: calc(100vh - 220px);">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label text-black" for="event-judul">Judul event</label>
                                    <input id="event-judul" name="judul" type="text" class="form-control" required maxlength="255">
                                </div>

                                <div class="col-12">
                                    <label class="form-label text-black" for="ckeditor">Deskripsi</label>
                                    <textarea id="ckeditor" name="deskripsi" class="form-control" rows="6"></textarea>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="event-tanggal-mulai">Tanggal mulai</label>
                                    <input id="event-tanggal-mulai" name="tanggal_mulai" type="date" class="form-control" required>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="event-tanggal-selesai">Tanggal selesai</label>
                                    <input id="event-tanggal-selesai" name="tanggal_selesai" type="date" class="form-control" required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label text-black" for="event-lokasi">Lokasi</label>
                                    <input id="event-lokasi" name="lokasi" type="text" class="form-control" list="location-suggestions" placeholder="Ketik lokasi…">
                                    <datalist id="location-suggestions">
                                        <option value="Jakarta"></option>
                                        <option value="Bandung"></option>
                                        <option value="Surabaya"></option>
                                        <option value="Yogyakarta"></option>
                                        <option value="Bali"></option>
                                    </datalist>
                                </div>

                                <div class="col-12">
                                    <label class="form-label text-black" for="event-gambar">Gambar utama</label>
                                    <input id="event-gambar" name="gambar_utama" type="file" class="form-control" accept="image/*">
                                    <div class="mt-2 d-none" id="event-image-wrap">
                                        <div class="text-muted small mb-2">Preview</div>
                                        <img id="event-image-preview" class="img-fluid rounded" alt="Preview gambar event">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="d-flex align-items-center justify-content-between gap-2 p-3 bg-light rounded">
                                        <div>
                                            <div class="fw-semibold text-black">Status aktif</div>
                                            <div class="text-muted">Jika nonaktif, event tidak dianggap aktif di sistem.</div>
                                        </div>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" role="switch" id="event-aktif" name="aktif" value="1">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batalkan</button>
                            <button type="submit" class="btn btn-primary" id="event-submit">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <style>
            #eventModal .ck-editor__editable {
                max-height: 40vh;
                overflow-y: auto;
            }
        </style>

        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalTitle" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" id="delete-form" action="#">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header">
                            <h2 class="modal-title h5" id="deleteModalTitle">Hapus event</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-muted">Event ini akan dihapus permanen.</div>
                            <div class="mt-2 fw-semibold text-black" id="delete-event-title"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            (function () {
                const bulkForm = document.getElementById('bulk-form');
                const bulkApply = document.getElementById('bulk-apply');
                const bulkSelected = document.getElementById('bulk-selected');
                const bulkCheckboxes = Array.from(document.querySelectorAll('.bulk-checkbox'));
                const tableSearch = document.getElementById('table-search');
                const table = document.getElementById('events-table');

                function updateBulkState() {
                    const selected = bulkCheckboxes.filter((c) => c.checked).length;
                    if (bulkSelected) bulkSelected.textContent = `${selected} dipilih`;
                    if (bulkApply) bulkApply.disabled = selected === 0;
                }

                bulkCheckboxes.forEach((c) => c.addEventListener('change', updateBulkState));
                updateBulkState();

                if (bulkForm) {
                    bulkForm.addEventListener('submit', (e) => {
                        const action = bulkForm.querySelector('select[name="action"]')?.value || '';
                        const selected = bulkCheckboxes.filter((c) => c.checked).length;
                        if (selected === 0) {
                            e.preventDefault();
                            return;
                        }
                        if (action === 'delete') {
                            const ok = window.confirm('Hapus semua event terpilih?');
                            if (!ok) e.preventDefault();
                        }
                    });
                }

                if (tableSearch && table) {
                    tableSearch.addEventListener('input', () => {
                        const q = (tableSearch.value || '').toLowerCase().trim();
                        const rows = Array.from(table.querySelectorAll('tbody tr[data-search-row]'));
                        rows.forEach((row) => {
                            const hay = (row.innerText || '').toLowerCase();
                            row.style.display = !q || hay.includes(q) ? '' : 'none';
                        });
                    });
                }

                const eventModal = document.getElementById('eventModal');
                const eventForm = document.getElementById('event-form');
                const eventMethod = document.getElementById('event-form-method');
                const eventTitle = document.getElementById('eventModalTitle');
                const eventSubmit = document.getElementById('event-submit');
                const inputJudul = document.getElementById('event-judul');
                const inputTanggalMulai = document.getElementById('event-tanggal-mulai');
                const inputTanggalSelesai = document.getElementById('event-tanggal-selesai');
                const inputLokasi = document.getElementById('event-lokasi');
                const inputAktif = document.getElementById('event-aktif');
                const inputGambar = document.getElementById('event-gambar');
                const imageWrap = document.getElementById('event-image-wrap');
                const imagePreview = document.getElementById('event-image-preview');
                const textareaDeskripsi = document.getElementById('ckeditor');

                const storeUrl = @json(route('admin.events.store'));
                const updateUrlTemplate = @json(route('admin.events.update', ['event' => '__EVENT__']));

                function setDeskripsi(value) {
                    const text = value || '';
                    if (window.editor && typeof window.editor.setData === 'function') {
                        window.editor.setData(text);
                    } else if (textareaDeskripsi) {
                        textareaDeskripsi.value = text;
                    }
                }

                function setImagePreview(url) {
                    if (!imageWrap || !imagePreview) return;
                    if (!url) {
                        imageWrap.classList.add('d-none');
                        imagePreview.removeAttribute('src');
                        return;
                    }
                    imagePreview.src = url;
                    imageWrap.classList.remove('d-none');
                }

                if (inputGambar) {
                    inputGambar.addEventListener('change', () => {
                        const file = inputGambar.files && inputGambar.files[0];
                        if (!file) {
                            return;
                        }
                        const url = URL.createObjectURL(file);
                        setImagePreview(url);
                    });
                }

                if (eventModal) {
                    eventModal.addEventListener('show.bs.modal', (e) => {
                        const trigger = e.relatedTarget;
                        const mode = trigger?.getAttribute('data-event-mode') || 'create';

                        if (mode === 'edit') {
                            const id = trigger.getAttribute('data-event-id');
                            const action = updateUrlTemplate.replace('__EVENT__', id);
                            eventForm.setAttribute('action', action);
                            eventMethod.disabled = false;

                            eventTitle.textContent = 'Edit event';
                            eventSubmit.textContent = 'Simpan perubahan';

                            inputJudul.value = trigger.getAttribute('data-event-judul') || '';
                            inputTanggalMulai.value = trigger.getAttribute('data-event-tanggal-mulai') || '';
                            inputTanggalSelesai.value = trigger.getAttribute('data-event-tanggal-selesai') || '';
                            inputLokasi.value = trigger.getAttribute('data-event-lokasi') || '';
                            inputAktif.checked = trigger.getAttribute('data-event-aktif') === '1';

                            setDeskripsi(trigger.getAttribute('data-event-deskripsi') || '');
                            setImagePreview(trigger.getAttribute('data-event-image') || '');
                            if (inputGambar) inputGambar.value = '';
                            return;
                        }

                        eventForm.setAttribute('action', storeUrl);
                        eventMethod.disabled = true;

                        eventTitle.textContent = 'Buat event';
                        eventSubmit.textContent = 'Simpan';

                        inputJudul.value = '';
                        inputTanggalMulai.value = '';
                        inputTanggalSelesai.value = '';
                        inputLokasi.value = '';
                        inputAktif.checked = true;

                        setDeskripsi('');
                        setImagePreview('');
                        if (inputGambar) inputGambar.value = '';
                    });
                }

                const deleteModal = document.getElementById('deleteModal');
                const deleteForm = document.getElementById('delete-form');
                const deleteTitle = document.getElementById('delete-event-title');

                if (deleteModal) {
                    deleteModal.addEventListener('show.bs.modal', (e) => {
                        const trigger = e.relatedTarget;
                        const action = trigger?.getAttribute('data-delete-action') || '#';
                        const title = trigger?.getAttribute('data-delete-title') || '';

                        deleteForm.setAttribute('action', action);
                        deleteTitle.textContent = title;
                    });
                }

                const iframeByWindow = new Map();

                function ensureIframesLoaded(container) {
                    const frames = Array.from((container || document).querySelectorAll('iframe[data-embed-frame]'));
                    frames.forEach((frame) => {
                        if (frame.getAttribute('src')) return;
                        const src = frame.getAttribute('data-src');
                        if (src) frame.setAttribute('src', src);
                    });
                }

                ensureIframesLoaded(document.querySelector('.tab-pane.show.active') || document);

                window.addEventListener('message', (event) => {
                    if (event.origin !== window.location.origin) return;
                    const data = event.data;
                    if (!data || data.type !== 'embed-resize') return;
                    const height = Number(data.height || 0);
                    if (!height || height < 100) return;

                    let frame = iframeByWindow.get(event.source);
                    if (!frame) {
                        const all = Array.from(document.querySelectorAll('iframe[data-embed-frame]'));
                        frame = all.find((f) => f.contentWindow === event.source);
                        if (frame) iframeByWindow.set(event.source, frame);
                    }
                    if (!frame) return;
                    frame.style.height = `${height}px`;
                });

                Array.from(document.querySelectorAll('iframe[data-embed-frame]')).forEach((frame) => {
                    frame.addEventListener('load', () => {
                        try {
                            if (frame.contentWindow) iframeByWindow.set(frame.contentWindow, frame);
                        } catch (e) {
                        }
                    });
                });

                function setQueryParam(key, value) {
                    const url = new URL(window.location.href);
                    if (value === null || value === undefined || value === '') {
                        url.searchParams.delete(key);
                    } else {
                        url.searchParams.set(key, value);
                    }
                    window.history.replaceState({}, '', url);
                }

                const mainTabEls = Array.from(document.querySelectorAll('#event-management-tabs [data-bs-toggle="tab"]'));
                mainTabEls.forEach((el) => {
                    el.addEventListener('shown.bs.tab', (e) => {
                        const target = e.target;
                        const tabKey = target?.getAttribute('data-tab-key') || '';
                        if (tabKey) setQueryParam('tab', tabKey);

                        const targetSelector = target?.getAttribute('data-bs-target') || target?.getAttribute('href') || '';
                        const pane = targetSelector ? document.querySelector(targetSelector) : null;
                        ensureIframesLoaded(pane || document);
                    });
                });

                const paketTabEls = Array.from(document.querySelectorAll('#paket-management-tabs [data-bs-toggle="tab"]'));
                paketTabEls.forEach((el) => {
                    el.addEventListener('shown.bs.tab', (e) => {
                        const target = e.target;
                        const tabKey = target?.getAttribute('data-paket-tab') || '';
                        if (tabKey) {
                            setQueryParam('tab', 'paket');
                            setQueryParam('paket_tab', tabKey);
                        }

                        const targetSelector = target?.getAttribute('data-bs-target') || target?.getAttribute('href') || '';
                        const pane = targetSelector ? document.querySelector(targetSelector) : null;
                        ensureIframesLoaded(pane || document);
                    });
                });

                (function initTabsFromUrl() {
                    if (!window.bootstrap || !window.bootstrap.Tab) return;
                    const params = new URLSearchParams(window.location.search);
                    const tab = params.get('tab');
                    const paketTab = params.get('paket_tab');

                    const mainKey = tab === 'paket' ? 'paket' : 'sesi-event';
                    const mainBtn = document.querySelector(`#event-management-tabs [data-tab-key="${mainKey}"]`);
                    if (mainBtn) {
                        window.bootstrap.Tab.getOrCreateInstance(mainBtn).show();
                    }

                    if (mainKey === 'paket') {
                        const paketKey = paketTab === 'akses' ? 'akses' : 'index';
                        const paketBtn = document.querySelector(`#paket-management-tabs [data-paket-tab="${paketKey}"]`);
                        if (paketBtn) {
                            window.bootstrap.Tab.getOrCreateInstance(paketBtn).show();
                        }
                    }

                    ensureIframesLoaded(document.querySelector('.tab-pane.show.active') || document);
                })();
            })();
        </script>
    </main>
@endsection
