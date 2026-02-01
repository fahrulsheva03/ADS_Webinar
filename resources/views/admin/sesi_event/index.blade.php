@extends(request()->boolean('embed') ? 'admin.partials.embed' : 'admin.partials.app')

@section('content')
    @php
        $query = request()->query();
        $sortState = [
            'sort' => $sort ?? (string) request()->query('sort', 'waktu_mulai'),
            'dir' => $dir ?? (string) request()->query('dir', 'asc'),
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
        ];
    @endphp

    <main id="main-content" tabindex="-1" class="pb-4" role="main" aria-label="Manajemen sesi event">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div class="me-auto">
                <h1 class="h3 mb-1">Manajemen Sesi Event</h1>
                <div class="text-muted">Kelola jadwal sesi, status, dan relasinya ke event.</div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <button
                    type="button"
                    class="btn btn-primary btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#sesiModal"
                    data-sesi-mode="create"
                >
                    Tambah sesi
                </button>
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

        <section class="mb-4" aria-label="Filter sesi">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.sesi-event.index') }}" class="row g-3 align-items-end">
                        <div class="col-12 col-lg-4">
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
                            <label class="form-label text-black" for="filter-status">Status</label>
                            <select id="filter-status" name="status" class="form-select">
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(($status ?? request('status')) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-lg-2 d-grid">
                            <button type="submit" class="btn btn-primary">Terapkan</button>
                        </div>

                        <input type="hidden" name="sort" value="{{ $sortState['sort'] }}">
                        <input type="hidden" name="dir" value="{{ $sortState['dir'] }}">
                    </form>
                </div>
            </div>
        </section>

        <section aria-label="Daftar sesi event">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <h2 class="h5 mb-0">List Sesi</h2>
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
                        <table class="table table-striped align-middle mb-0" aria-label="Tabel sesi" id="sesi-table">
                            <thead>
                                <tr>
                                    <th scope="col">
                                        <a class="text-black" href="{{ $sortUrl('judul_sesi') }}">Nama sesi {{ $sortIndicator('judul_sesi') }}</a>
                                    </th>
                                    <th scope="col">
                                        <a class="text-black" href="{{ $sortUrl('waktu_mulai') }}">Tanggal {{ $sortIndicator('waktu_mulai') }}</a>
                                    </th>
                                    <th scope="col">Waktu mulai</th>
                                    <th scope="col">
                                        <a class="text-black" href="{{ $sortUrl('waktu_selesai') }}">Waktu selesai {{ $sortIndicator('waktu_selesai') }}</a>
                                    </th>
                                    <th scope="col">
                                        <a class="text-black" href="{{ $sortUrl('status_sesi') }}">Status {{ $sortIndicator('status_sesi') }}</a>
                                    </th>
                                    <th scope="col" class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sesi as $row)
                                    @php
                                        $isActive = $row->status_sesi === 'live';
                                        $payload = [
                                            'id' => $row->id,
                                            'event_id' => $row->event_id,
                                            'judul_sesi' => $row->judul_sesi,
                                            'tanggal' => optional($row->waktu_mulai)->format('Y-m-d'),
                                            'waktu_mulai' => optional($row->waktu_mulai)->format('H:i'),
                                            'waktu_selesai' => optional($row->waktu_selesai)->format('H:i'),
                                            'aktif' => $isActive ? 1 : 0,
                                        ];
                                    @endphp
                                    <tr data-search-row>
                                        <th scope="row" class="text-black">
                                            <div class="fw-semibold">{{ $row->judul_sesi }}</div>
                                            <div class="text-muted small">{{ $row->event?->judul ?? 'Event tidak ditemukan' }}</div>
                                        </th>
                                        <td class="text-muted">{{ optional($row->waktu_mulai)->format('d M Y') }}</td>
                                        <td class="text-muted">{{ optional($row->waktu_mulai)->format('H:i') }}</td>
                                        <td class="text-muted">{{ optional($row->waktu_selesai)->format('H:i') }}</td>
                                        <td>
                                            @if ($isActive)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Non-aktif</span>
                                            @endif
                                            <div class="text-muted small">({{ $row->status_sesi }})</div>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group" role="group" aria-label="Aksi sesi">
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-primary btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#sesiModal"
                                                    data-sesi-mode="edit"
                                                    data-sesi='@json($payload)'
                                                >
                                                    <i class="la la-pencil-alt" aria-hidden="true"></i>
                                                    <span class="ms-1 d-none d-sm-inline">Edit</span>
                                                </button>
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-danger btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal"
                                                    data-delete-action="{{ route('admin.sesi-event.destroy', $row) }}"
                                                    data-delete-title="{{ $row->judul_sesi }}"
                                                >
                                                    <i class="la la-trash" aria-hidden="true"></i>
                                                    <span class="ms-1 d-none d-sm-inline">Hapus</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            Tidak ada sesi ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3">
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
                    <form id="sesi-form" method="POST" action="{{ route('admin.sesi-event.store') }}">
                        @csrf
                        <input type="hidden" name="_method" id="sesi-form-method" value="PUT" disabled>
                        <div class="modal-header">
                            <h2 class="modal-title h5" id="sesiModalTitle">Tambah sesi</h2>
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
                                    <input id="sesi-judul" name="judul_sesi" type="text" class="form-control" required maxlength="100" placeholder="Contoh: Day 1">
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label text-black" for="sesi-tanggal">Tanggal</label>
                                    <input id="sesi-tanggal" name="tanggal" type="date" class="form-control" required>
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label text-black" for="sesi-mulai">Waktu mulai</label>
                                    <input id="sesi-mulai" name="waktu_mulai" type="time" class="form-control" required>
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label text-black" for="sesi-selesai">Waktu selesai</label>
                                    <input id="sesi-selesai" name="waktu_selesai" type="time" class="form-control" required>
                                </div>

                                <div class="col-12">
                                    <div class="p-3 bg-light rounded">
                                        <div class="fw-semibold text-black mb-2">Status</div>
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="aktif" id="status-aktif" value="1" required>
                                                <label class="form-check-label text-black" for="status-aktif">Aktif</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="aktif" id="status-nonaktif" value="0" required>
                                                <label class="form-check-label text-black" for="status-nonaktif">Non-aktif</label>
                                            </div>
                                        </div>
                                        <div class="text-muted mt-2">Status ini dipetakan ke status_sesi (Aktif → live, Non-aktif → upcoming).</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="sesi-submit">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalTitle" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" id="delete-form" action="#">
                        @csrf
                        @method('DELETE')
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
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            (function () {
                const tableSearch = document.getElementById('table-search');
                const table = document.getElementById('sesi-table');
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

                const sesiModal = document.getElementById('sesiModal');
                const sesiForm = document.getElementById('sesi-form');
                const sesiMethod = document.getElementById('sesi-form-method');
                const sesiTitle = document.getElementById('sesiModalTitle');
                const sesiSubmit = document.getElementById('sesi-submit');

                const inputEvent = document.getElementById('sesi-event');
                const inputJudul = document.getElementById('sesi-judul');
                const inputTanggal = document.getElementById('sesi-tanggal');
                const inputMulai = document.getElementById('sesi-mulai');
                const inputSelesai = document.getElementById('sesi-selesai');
                const radioAktif = document.getElementById('status-aktif');
                const radioNonaktif = document.getElementById('status-nonaktif');

                const storeUrl = @json(route('admin.sesi-event.store'));
                const updateUrlTemplate = @json(route('admin.sesi-event.update', ['sesi' => '__SESI__']));

                if (sesiModal) {
                    sesiModal.addEventListener('show.bs.modal', (e) => {
                        const trigger = e.relatedTarget;
                        const mode = trigger?.getAttribute('data-sesi-mode') || 'create';

                        if (mode === 'edit') {
                            const payload = trigger.getAttribute('data-sesi');
                            const data = payload ? JSON.parse(payload) : null;
                            if (!data) return;

                            sesiForm.setAttribute('action', updateUrlTemplate.replace('__SESI__', data.id));
                            sesiMethod.disabled = false;

                            sesiTitle.textContent = 'Edit sesi';
                            sesiSubmit.textContent = 'Update';

                            inputEvent.value = String(data.event_id || '');
                            inputJudul.value = data.judul_sesi || '';
                            inputTanggal.value = data.tanggal || '';
                            inputMulai.value = data.waktu_mulai || '';
                            inputSelesai.value = data.waktu_selesai || '';
                            radioAktif.checked = String(data.aktif) === '1';
                            radioNonaktif.checked = String(data.aktif) === '0';
                            return;
                        }

                        sesiForm.setAttribute('action', storeUrl);
                        sesiMethod.disabled = true;

                        sesiTitle.textContent = 'Tambah sesi';
                        sesiSubmit.textContent = 'Simpan';

                        inputEvent.value = '';
                        inputJudul.value = '';
                        inputTanggal.value = '';
                        inputMulai.value = '';
                        inputSelesai.value = '';
                        radioAktif.checked = true;
                        radioNonaktif.checked = false;
                    });
                }

                const deleteModal = document.getElementById('deleteModal');
                const deleteForm = document.getElementById('delete-form');
                const deleteTitle = document.getElementById('delete-sesi-title');

                if (deleteModal) {
                    deleteModal.addEventListener('show.bs.modal', (e) => {
                        const trigger = e.relatedTarget;
                        const action = trigger?.getAttribute('data-delete-action') || '#';
                        const title = trigger?.getAttribute('data-delete-title') || '';

                        deleteForm.setAttribute('action', action);
                        deleteTitle.textContent = title;
                    });
                }
            })();
        </script>
    </main>
@endsection
