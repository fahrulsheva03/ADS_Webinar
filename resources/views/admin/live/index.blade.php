@extends('admin.partials.app')

@section('content')
    @php
        $query = request()->query();
        $sortState = [
            'sort' => $sort ?? (string) request()->query('sort', 'waktu_mulai'),
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

        $statusBadge = function (?string $status) {
            $status = strtolower((string) $status);
            return match ($status) {
                'live' => ['bg-success', 'Live'],
                'upcoming' => ['bg-warning text-dark', 'Upcoming'],
                'selesai' => ['bg-secondary', 'Selesai'],
                default => ['bg-light text-dark', $status ?: '-'],
            };
        };

        $statusLabel = function (?string $status) {
            $status = strtolower((string) $status);
            return match ($status) {
                'live' => 'Live',
                'upcoming' => 'Upcoming',
                'selesai' => 'Selesai',
                default => $status ?: '-',
            };
        };
    @endphp

    <main id="main-content" tabindex="-1" class="pb-4" role="main" aria-label="Kontrol sesi live">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Kontrol Sesi Live</li>
            </ol>
        </nav>

        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div class="me-auto">
                <h1 class="h3 mb-1">Kontrol Sesi Live</h1>
                <div class="text-muted">Mulai, hentikan, dan pantau status sesi secara real-time.</div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <button
                    type="button"
                    class="btn btn-primary btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#sesiModal"
                    data-sesi-mode="create"
                >
                    Mulai Sesi Baru
                </button>
            </div>
        </div>

        <div id="notify-area" class="mb-3" aria-live="polite" aria-atomic="true"></div>

        <section class="mb-4" aria-label="Filter sesi live">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.live.index') }}" class="row g-3 align-items-end">
                        <div class="col-12 col-lg-5">
                            <label class="form-label text-black" for="filter-q">Cari</label>
                            <input
                                id="filter-q"
                                name="q"
                                type="search"
                                class="form-control"
                                placeholder="Nama sesi atau judul event…"
                                value="{{ $q ?? request('q') }}"
                                autocomplete="off"
                            >
                        </div>
                        <div class="col-12 col-md-6 col-lg-4">
                            <label class="form-label text-black" for="filter-status">Status</label>
                            <select id="filter-status" name="status" class="form-select">
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(($status ?? request('status')) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3 d-grid">
                            <button type="submit" class="btn btn-primary">Terapkan</button>
                        </div>

                        <input type="hidden" name="sort" value="{{ $sortState['sort'] }}">
                        <input type="hidden" name="dir" value="{{ $sortState['dir'] }}">
                    </form>
                </div>
            </div>
        </section>

        <section aria-label="Daftar sesi">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <h2 class="h5 mb-0">Sesi</h2>
                        <div class="input-group input-group-sm" style="max-width: 340px;">
                            <span class="input-group-text">
                                <i class="flaticon-381-search-2 text-primary" aria-hidden="true"></i>
                            </span>
                            <input id="table-search" type="search" class="form-control" placeholder="Pencarian cepat di tabel…" autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="live-table" aria-label="Daftar sesi live">
                            <thead>
                                <tr>
                                    <th scope="col">
                                        <a class="text-decoration-none" href="{{ $sortUrl('id') }}">ID {!! $sortIndicator('id') !!}</a>
                                    </th>
                                    <th scope="col">
                                        <a class="text-decoration-none" href="{{ $sortUrl('judul_sesi') }}">Nama {!! $sortIndicator('judul_sesi') !!}</a>
                                    </th>
                                    <th scope="col">Event</th>
                                    <th scope="col">
                                        <a class="text-decoration-none" href="{{ $sortUrl('status_sesi') }}">Status {!! $sortIndicator('status_sesi') !!}</a>
                                    </th>
                                    <th scope="col">
                                        <a class="text-decoration-none" href="{{ $sortUrl('waktu_mulai') }}">Waktu Mulai {!! $sortIndicator('waktu_mulai') !!}</a>
                                    </th>
                                    <th scope="col">
                                        <a class="text-decoration-none" href="{{ $sortUrl('jumlah_penonton') }}">Penonton {!! $sortIndicator('jumlah_penonton') !!}</a>
                                    </th>
                                    <th scope="col" class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sesi as $row)
                                    @php
                                        [$badgeClass, $badgeText] = $statusBadge($row->status_sesi);
                                        $rowPayload = [
                                            'id' => $row->id,
                                            'event_id' => $row->event_id,
                                            'judul_sesi' => $row->judul_sesi,
                                            'deskripsi_sesi' => $row->deskripsi_sesi,
                                            'waktu_mulai' => optional($row->waktu_mulai)->format('Y-m-d\TH:i'),
                                            'waktu_selesai' => optional($row->waktu_selesai)->format('Y-m-d\TH:i'),
                                            'status_sesi' => $row->status_sesi,
                                            'zoom_link' => $row->zoom_link,
                                        ];
                                    @endphp
                                    <tr
                                        data-row-id="{{ $row->id }}"
                                        data-row-status="{{ (string) $row->status_sesi }}"
                                        data-row-judul="{{ (string) $row->judul_sesi }}"
                                        data-search-row
                                    >
                                        <td class="fw-semibold text-black">{{ $row->id }}</td>
                                        <td>
                                            <div class="fw-semibold text-black">{{ $row->judul_sesi }}</div>
                                            @if (!empty($row->zoom_link))
                                                <a href="{{ $row->zoom_link }}" target="_blank" rel="noopener noreferrer" class="text-primary small">
                                                    Buka link Zoom
                                                </a>
                                            @endif
                                        </td>
                                        <td class="text-muted">{{ $row->event?->judul ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $badgeClass }}" data-field="status_badge">{{ $badgeText }}</span>
                                        </td>
                                        <td class="text-muted" data-field="waktu_mulai">
                                            {{ optional($row->waktu_mulai)->format('Y-m-d H:i') ?? '-' }}
                                        </td>
                                        <td class="text-muted" data-field="jumlah_penonton">{{ (int) ($row->jumlah_penonton ?? 0) }}</td>
                                        <td class="text-end">
                                            <div class="d-inline-flex flex-wrap justify-content-end gap-2">
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-success btn-xxs live-action-start @if ((string) $row->status_sesi === 'live') d-none @endif"
                                                    data-action="{{ route('admin.live.start', ['sesi' => $row->id]) }}"
                                                    aria-label="Mulai sesi {{ $row->judul_sesi }}"
                                                >
                                                    Mulai
                                                    <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
                                                </button>

                                                <button
                                                    type="button"
                                                    class="btn btn-outline-warning btn-xxs live-action-stop @if ((string) $row->status_sesi !== 'live') d-none @endif"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#stopModal"
                                                    data-action="{{ route('admin.live.stop', ['sesi' => $row->id]) }}"
                                                    data-title="{{ $row->judul_sesi }}"
                                                    aria-label="Hentikan sesi {{ $row->judul_sesi }}"
                                                >
                                                    Hentikan
                                                </button>

                                                <button
                                                    type="button"
                                                    class="btn btn-outline-primary btn-xxs"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#sesiModal"
                                                    data-sesi-mode="edit"
                                                    data-sesi='@json($rowPayload)'
                                                    aria-label="Edit sesi {{ $row->judul_sesi }}"
                                                >
                                                    Edit
                                                </button>

                                                <button
                                                    type="button"
                                                    class="btn btn-outline-danger btn-xxs"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal"
                                                    data-delete-action="{{ route('admin.live.destroy', ['sesi' => $row->id]) }}"
                                                    data-delete-title="{{ $row->judul_sesi }}"
                                                    aria-label="Hapus sesi {{ $row->judul_sesi }}"
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="text-center py-5">
                                                <div class="fw-semibold text-black mb-1">Tidak ada sesi</div>
                                                <div class="text-muted">Coba ubah filter atau buat sesi baru.</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-4">
                        <div class="text-muted">
                            Menampilkan {{ $sesi->firstItem() ?? 0 }}–{{ $sesi->lastItem() ?? 0 }} dari {{ $sesi->total() ?? 0 }}
                        </div>
                        <div>
                            {{ $sesi->onEachSide(1)->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade" id="sesiModal" tabindex="-1" aria-labelledby="sesiModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <form id="sesi-form" method="POST" action="{{ route('admin.live.store') }}">
                        @csrf
                        <input type="hidden" name="_method" id="sesi-form-method" value="PUT" disabled>
                        <div class="modal-header">
                            <h2 class="modal-title h5" id="sesiModalTitle">Mulai sesi baru</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="sesi-event">Event</label>
                                    <select id="sesi-event" name="event_id" class="form-select" required>
                                        <option value="" selected disabled>Pilih event…</option>
                                        @foreach ($events as $event)
                                            <option value="{{ $event->id }}">{{ $event->judul }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="sesi-judul">Nama sesi</label>
                                    <input id="sesi-judul" name="judul_sesi" type="text" class="form-control" required maxlength="100" placeholder="Contoh: Pembukaan">
                                </div>

                                <div class="col-12">
                                    <label class="form-label text-black" for="sesi-deskripsi">Deskripsi</label>
                                    <textarea id="sesi-deskripsi" name="deskripsi_sesi" class="form-control" rows="3" placeholder="Opsional"></textarea>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="sesi-mulai">Waktu mulai</label>
                                    <input id="sesi-mulai" name="waktu_mulai" type="datetime-local" class="form-control" required>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="sesi-selesai">Waktu selesai</label>
                                    <input id="sesi-selesai" name="waktu_selesai" type="datetime-local" class="form-control" required>
                                </div>

                                <div class="col-12 col-md-7">
                                    <label class="form-label text-black" for="sesi-zoom">Link Zoom</label>
                                    <input id="sesi-zoom" name="zoom_link" type="url" class="form-control" placeholder="https://">
                                </div>

                                <div class="col-12 col-md-5">
                                    <label class="form-label text-black" for="sesi-status">Status</label>
                                    <select id="sesi-status" name="status_sesi" class="form-select" required>
                                        <option value="upcoming">Upcoming</option>
                                        <option value="live">Live</option>
                                        <option value="selesai">Selesai</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <div class="alert alert-info mb-0">
                                        Status live dapat diubah cepat dari tombol Mulai/Hentikan di tabel.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="sesi-submit">
                                <span id="sesi-submit-label">Simpan</span>
                                <span class="spinner-border spinner-border-sm d-none" id="sesi-spinner" aria-hidden="true"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="stopModal" tabindex="-1" aria-labelledby="stopModalTitle" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="stopModalTitle">Hentikan sesi</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-muted">Sesi ini akan dihentikan secara paksa.</div>
                        <div class="mt-2 fw-semibold text-black" id="stop-sesi-title"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-warning" id="stop-confirm">
                            Hentikan
                            <span class="spinner-border spinner-border-sm d-none" id="stop-spinner" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalTitle" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="deleteModalTitle">Hapus sesi</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-muted">Sesi ini akan dihapus permanen.</div>
                        <div class="mt-2 fw-semibold text-black" id="delete-sesi-title"></div>
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
                const pollUrl = @json(route('admin.live.poll'));
                const storeUrl = @json(route('admin.live.store'));
                const updateUrlTemplate = @json(route('admin.live.update', ['sesi' => '__SESI__']));
                const deleteUrlTemplate = @json(route('admin.live.destroy', ['sesi' => '__SESI__']));

                const notifyArea = document.getElementById('notify-area');
                const table = document.getElementById('live-table');
                const tableSearch = document.getElementById('table-search');
                const pageLoading = document.getElementById('page-loading');

                const sesiModalEl = document.getElementById('sesiModal');
                const sesiForm = document.getElementById('sesi-form');
                const sesiMethod = document.getElementById('sesi-form-method');
                const sesiTitle = document.getElementById('sesiModalTitle');
                const sesiSubmit = document.getElementById('sesi-submit');
                const sesiSubmitLabel = document.getElementById('sesi-submit-label');
                const sesiSpinner = document.getElementById('sesi-spinner');

                const inputEvent = document.getElementById('sesi-event');
                const inputJudul = document.getElementById('sesi-judul');
                const inputDeskripsi = document.getElementById('sesi-deskripsi');
                const inputMulai = document.getElementById('sesi-mulai');
                const inputSelesai = document.getElementById('sesi-selesai');
                const inputZoom = document.getElementById('sesi-zoom');
                const inputStatus = document.getElementById('sesi-status');

                const stopModalEl = document.getElementById('stopModal');
                const stopTitle = document.getElementById('stop-sesi-title');
                const stopConfirm = document.getElementById('stop-confirm');
                const stopSpinner = document.getElementById('stop-spinner');

                const deleteModalEl = document.getElementById('deleteModal');
                const deleteTitle = document.getElementById('delete-sesi-title');
                const deleteConfirm = document.getElementById('delete-confirm');
                const deleteSpinner = document.getElementById('delete-spinner');

                let stopActionUrl = '';
                let deleteActionUrl = '';
                let activeSesiId = null;

                function setPageLoading(on) {
                    if (!pageLoading) return;
                    pageLoading.classList.toggle('d-none', !on);
                    pageLoading.classList.toggle('d-flex', on);
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

                function escapeHtml(value) {
                    return String(value ?? '')
                        .replaceAll('&', '&amp;')
                        .replaceAll('<', '&lt;')
                        .replaceAll('>', '&gt;')
                        .replaceAll('"', '&quot;')
                        .replaceAll("'", '&#039;');
                }

                function statusInfo(status) {
                    const s = String(status || '').toLowerCase();
                    if (s === 'live') return { badgeClass: 'bg-success', label: 'Live' };
                    if (s === 'upcoming') return { badgeClass: 'bg-warning text-dark', label: 'Upcoming' };
                    if (s === 'selesai') return { badgeClass: 'bg-secondary', label: 'Selesai' };
                    return { badgeClass: 'bg-light text-dark', label: s || '-' };
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

                function rowById(id) {
                    if (!table) return null;
                    return table.querySelector(`tbody tr[data-row-id="${id}"]`);
                }

                function setButtonBusy(btn, busy) {
                    if (!btn) return;
                    btn.disabled = !!busy;
                    const spinner = btn.querySelector('.spinner-border');
                    if (spinner) {
                        spinner.classList.toggle('d-none', !busy);
                    }
                }

                function applyRowData(row, data) {
                    if (!row || !data) return;
                    const s = statusInfo(data.status_sesi);
                    row.dataset.rowStatus = String(data.status_sesi || '');

                    const badge = row.querySelector('[data-field="status_badge"]');
                    if (badge) {
                        badge.className = `badge ${s.badgeClass}`;
                        badge.textContent = s.label;
                    }

                    const waktuMulai = row.querySelector('[data-field="waktu_mulai"]');
                    if (waktuMulai) {
                        waktuMulai.textContent = data.waktu_mulai || '-';
                    }

                    const penonton = row.querySelector('[data-field="jumlah_penonton"]');
                    if (penonton) {
                        penonton.textContent = String(data.jumlah_penonton ?? 0);
                    }

                    const btnStart = row.querySelector('.live-action-start');
                    const btnStop = row.querySelector('.live-action-stop');
                    if (btnStart) {
                        btnStart.classList.toggle('d-none', String(data.status_sesi) === 'live');
                    }
                    if (btnStop) {
                        btnStop.classList.toggle('d-none', String(data.status_sesi) !== 'live');
                    }
                }

                function buildRow(data) {
                    if (!table) return null;
                    const tbody = table.querySelector('tbody');
                    if (!tbody) return null;

                    const s = statusInfo(data.status_sesi);
                    const row = document.createElement('tr');
                    row.dataset.rowId = String(data.id);
                    row.dataset.rowStatus = String(data.status_sesi || '');
                    row.dataset.rowJudul = String(data.judul_sesi || '');
                    row.setAttribute('data-search-row', '');

                    const editPayload = {
                        id: data.id,
                        event_id: data.event_id,
                        judul_sesi: data.judul_sesi,
                        deskripsi_sesi: data.deskripsi_sesi ?? '',
                        waktu_mulai: data.waktu_mulai_raw ?? '',
                        waktu_selesai: data.waktu_selesai_raw ?? '',
                        status_sesi: data.status_sesi,
                        zoom_link: data.zoom_link ?? '',
                    };

                    row.innerHTML = `
                        <td class="fw-semibold text-black">${data.id}</td>
                        <td>
                            <div class="fw-semibold text-black">${escapeHtml(data.judul_sesi || '')}</div>
                            ${data.zoom_link ? `<a href="${escapeHtml(data.zoom_link)}" target="_blank" rel="noopener noreferrer" class="text-primary small">Buka link Zoom</a>` : ''}
                        </td>
                        <td class="text-muted">${escapeHtml(data.event_judul || '-')}</td>
                        <td><span class="badge ${s.badgeClass}" data-field="status_badge">${s.label}</span></td>
                        <td class="text-muted" data-field="waktu_mulai">${data.waktu_mulai || '-'}</td>
                        <td class="text-muted" data-field="jumlah_penonton">${data.jumlah_penonton ?? 0}</td>
                        <td class="text-end">
                            <div class="d-inline-flex flex-wrap justify-content-end gap-2">
                                <button type="button" class="btn btn-outline-success btn-xxs live-action-start ${String(data.status_sesi) === 'live' ? 'd-none' : ''}" data-action="${data.start_url}" aria-label="Mulai sesi ${escapeHtml(data.judul_sesi || '')}">
                                    Mulai
                                    <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
                                </button>
                                <button type="button" class="btn btn-outline-warning btn-xxs live-action-stop ${String(data.status_sesi) !== 'live' ? 'd-none' : ''}" data-bs-toggle="modal" data-bs-target="#stopModal" data-action="${data.stop_url}" data-title="${escapeHtml(data.judul_sesi || '')}" aria-label="Hentikan sesi ${escapeHtml(data.judul_sesi || '')}">Hentikan</button>
                                <button type="button" class="btn btn-outline-primary btn-xxs" data-bs-toggle="modal" data-bs-target="#sesiModal" data-sesi-mode="edit" data-sesi='${escapeHtml(JSON.stringify(editPayload))}' aria-label="Edit sesi ${escapeHtml(data.judul_sesi || '')}">Edit</button>
                                <button type="button" class="btn btn-outline-danger btn-xxs" data-bs-toggle="modal" data-bs-target="#deleteModal" data-delete-action="${data.delete_url}" data-delete-title="${escapeHtml(data.judul_sesi || '')}" aria-label="Hapus sesi ${escapeHtml(data.judul_sesi || '')}">Hapus</button>
                            </div>
                        </td>
                    `;
                    tbody.prepend(row);
                    return row;
                }

                function isoLikeFromDisplay(value) {
                    if (!value) return '';
                    const parts = String(value).trim().split(' ');
                    if (parts.length < 2) return '';
                    return `${parts[0]}T${parts[1]}`;
                }

                function wireStartButtons() {
                    if (!table) return;
                    table.querySelectorAll('.live-action-start').forEach((btn) => {
                        if (btn.dataset.wired === '1') return;
                        btn.dataset.wired = '1';
                        btn.addEventListener('click', async () => {
                            const url = btn.getAttribute('data-action');
                            const row = btn.closest('tr');
                            if (!url || !row) return;

                            try {
                                setButtonBusy(btn, true);
                                const payload = await requestJson(url, { method: 'POST' });
                                applyRowData(row, payload.data);
                                pushNotice('success', 'Sukses', payload.message || 'Sesi berhasil dimulai.');
                            } catch (e) {
                                pushNotice('danger', 'Gagal', e.message || 'Sesi gagal dimulai.');
                            } finally {
                                setButtonBusy(btn, false);
                            }
                        });
                    });
                }

                function pollTick() {
                    if (!table) return;
                    const rows = Array.from(table.querySelectorAll('tbody tr[data-row-id]'));
                    const ids = rows.map((r) => r.dataset.rowId).filter(Boolean);
                    if (!ids.length) return;

                    const prev = new Map(rows.map((r) => [r.dataset.rowId, r.dataset.rowStatus]));
                    fetch(`${pollUrl}?ids=${encodeURIComponent(ids.join(','))}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then((r) => r.ok ? r.json() : null)
                        .then((json) => {
                            const list = (json && json.data) ? json.data : [];
                            list.forEach((item) => {
                                const row = rowById(item.id);
                                if (!row) return;
                                const prevStatus = prev.get(String(item.id));
                                if (prevStatus && String(prevStatus) !== String(item.status_sesi)) {
                                    const judul = row.dataset.rowJudul || `#${item.id}`;
                                    pushNotice('info', 'Update status', `${judul} berubah ke ${statusInfo(item.status_sesi).label}`);
                                }
                                applyRowData(row, item);
                            });
                            wireStartButtons();
                        })
                        .catch(() => {});
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

                if (sesiModalEl) {
                    sesiModalEl.addEventListener('show.bs.modal', (e) => {
                        const trigger = e.relatedTarget;
                        const mode = trigger?.getAttribute('data-sesi-mode') || 'create';
                        const payload = trigger?.getAttribute('data-sesi');
                        const data = payload ? JSON.parse(payload) : null;

                        if (mode === 'edit' && data) {
                            activeSesiId = data.id;
                            sesiForm.setAttribute('action', updateUrlTemplate.replace('__SESI__', data.id));
                            sesiMethod.disabled = false;

                            sesiTitle.textContent = 'Edit sesi';
                            if (sesiSubmitLabel) sesiSubmitLabel.textContent = 'Update';

                            inputEvent.value = String(data.event_id || '');
                            inputJudul.value = data.judul_sesi || '';
                            inputDeskripsi.value = data.deskripsi_sesi || '';
                            inputMulai.value = data.waktu_mulai || '';
                            inputSelesai.value = data.waktu_selesai || '';
                            inputZoom.value = data.zoom_link || '';
                            inputStatus.value = data.status_sesi || 'upcoming';
                            return;
                        }

                        activeSesiId = null;
                        sesiForm.setAttribute('action', storeUrl);
                        sesiMethod.disabled = true;

                        sesiTitle.textContent = 'Mulai sesi baru';
                        if (sesiSubmitLabel) sesiSubmitLabel.textContent = 'Simpan';

                        inputEvent.value = '';
                        inputJudul.value = '';
                        inputDeskripsi.value = '';
                        inputMulai.value = '';
                        inputSelesai.value = '';
                        inputZoom.value = '';
                        inputStatus.value = 'upcoming';
                    });
                }

                if (sesiForm) {
                    sesiForm.addEventListener('submit', async (e) => {
                        e.preventDefault();
                        const action = sesiForm.getAttribute('action');
                        if (!action) return;

                        try {
                            setPageLoading(true);
                            sesiSubmit.disabled = true;
                            sesiSpinner.classList.remove('d-none');

                            const formData = new FormData(sesiForm);
                            if (!sesiMethod.disabled) {
                                formData.set('_method', 'PUT');
                            }

                            const payload = await requestJson(action, { method: 'POST', body: formData });
                            const data = payload.data;

                            if (activeSesiId) {
                                const row = rowById(activeSesiId);
                                if (row) {
                                    applyRowData(row, data);
                                }
                                pushNotice('success', 'Sukses', payload.message || 'Sesi berhasil diperbarui.');
                            } else {
                                const extra = {
                                    ...data,
                                    deskripsi_sesi: inputDeskripsi.value || '',
                                    waktu_mulai_raw: inputMulai.value || isoLikeFromDisplay(data.waktu_mulai),
                                    waktu_selesai_raw: inputSelesai.value || isoLikeFromDisplay(data.waktu_selesai),
                                    start_url: @json(route('admin.live.start', ['sesi' => '__SESI__'])).replace('__SESI__', data.id),
                                    stop_url: @json(route('admin.live.stop', ['sesi' => '__SESI__'])).replace('__SESI__', data.id),
                                    delete_url: deleteUrlTemplate.replace('__SESI__', data.id),
                                };
                                buildRow(extra);
                                wireStartButtons();
                                pushNotice('success', 'Sukses', payload.message || 'Sesi berhasil dibuat.');
                            }

                            const modal = bootstrap.Modal.getInstance(sesiModalEl);
                            if (modal) modal.hide();
                        } catch (err) {
                            pushNotice('danger', 'Gagal', err.message || 'Permintaan gagal diproses.');
                        } finally {
                            sesiSubmit.disabled = false;
                            sesiSpinner.classList.add('d-none');
                            setPageLoading(false);
                        }
                    });
                }

                if (stopModalEl) {
                    stopModalEl.addEventListener('show.bs.modal', (e) => {
                        const trigger = e.relatedTarget;
                        stopActionUrl = trigger?.getAttribute('data-action') || '';
                        stopTitle.textContent = trigger?.getAttribute('data-title') || '';
                    });
                }

                if (stopConfirm) {
                    stopConfirm.addEventListener('click', async () => {
                        if (!stopActionUrl) return;
                        try {
                            stopConfirm.disabled = true;
                            stopSpinner.classList.remove('d-none');
                            setPageLoading(true);
                            const payload = await requestJson(stopActionUrl, { method: 'POST' });
                            const row = rowById(payload.data.id);
                            applyRowData(row, payload.data);
                            pushNotice('success', 'Sukses', payload.message || 'Sesi berhasil dihentikan.');
                            const modal = bootstrap.Modal.getInstance(stopModalEl);
                            if (modal) modal.hide();
                        } catch (e) {
                            pushNotice('danger', 'Gagal', e.message || 'Sesi gagal dihentikan.');
                        } finally {
                            stopConfirm.disabled = false;
                            stopSpinner.classList.add('d-none');
                            setPageLoading(false);
                        }
                    });
                }

                if (deleteModalEl) {
                    deleteModalEl.addEventListener('show.bs.modal', (e) => {
                        const trigger = e.relatedTarget;
                        deleteActionUrl = trigger?.getAttribute('data-delete-action') || '';
                        deleteTitle.textContent = trigger?.getAttribute('data-delete-title') || '';
                    });
                }

                if (deleteConfirm) {
                    deleteConfirm.addEventListener('click', async () => {
                        if (!deleteActionUrl) return;
                        try {
                            deleteConfirm.disabled = true;
                            deleteSpinner.classList.remove('d-none');
                            setPageLoading(true);

                            const formData = new FormData();
                            formData.set('_method', 'DELETE');
                            const payload = await requestJson(deleteActionUrl, { method: 'POST', body: formData });

                            const urlParts = deleteActionUrl.split('/');
                            const id = urlParts[urlParts.length - 1];
                            const row = rowById(id);
                            if (row) row.remove();

                            pushNotice('success', 'Sukses', payload.message || 'Sesi berhasil dihapus.');
                            const modal = bootstrap.Modal.getInstance(deleteModalEl);
                            if (modal) modal.hide();
                        } catch (e) {
                            pushNotice('danger', 'Gagal', e.message || 'Sesi gagal dihapus.');
                        } finally {
                            deleteConfirm.disabled = false;
                            deleteSpinner.classList.add('d-none');
                            setPageLoading(false);
                        }
                    });
                }

                wireStartButtons();
                setInterval(pollTick, 5000);
            })();
        </script>
    </main>
@endsection
