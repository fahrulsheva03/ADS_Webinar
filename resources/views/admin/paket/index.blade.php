@extends(request()->boolean('embed') ? 'admin.partials.embed' : 'admin.partials.app')

@section('content')
    @php
        $query = request()->query();
        $sortState = [
            'sort' => $sort ?? (string) request()->query('sort', 'id'),
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
            'nonaktif' => 'Nonaktif',
        ];

        $statusBadge = function (?string $status) {
            $status = strtolower((string) $status);
            return match ($status) {
                'aktif' => ['bg-success', 'Aktif'],
                'nonaktif' => ['bg-secondary', 'Nonaktif'],
                default => ['bg-light text-dark', $status ?: '-'],
            };
        };
    @endphp

    <main id="main-content" tabindex="-1" class="pb-4" role="main" aria-label="Manajemen paket">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div class="me-auto">
                <h1 class="h3 mb-1">Manajemen Paket</h1>
                <div class="text-muted">Kelola paket, harga, dan status.</div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <button
                    type="button"
                    class="btn btn-primary btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#paketModal"
                    data-paket-mode="create"
                >
                    Buat Paket Baru
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

        <section class="mb-4" aria-label="Filter paket">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.paket.index') }}" class="row g-3 align-items-end">
                        <div class="col-12 col-lg-5">
                            <label class="form-label text-black" for="filter-q">Cari</label>
                            <input
                                id="filter-q"
                                name="q"
                                type="search"
                                class="form-control"
                                placeholder="Nama paket atau deskripsi…"
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
                        <div class="col-12 col-md-6 col-lg-2 d-grid">
                            <button type="submit" class="btn btn-primary">Terapkan</button>
                        </div>
                        <div class="col-12 col-lg-2 d-grid">
                            <a class="btn btn-outline-secondary" href="{{ route('admin.paket.index') }}">Reset</a>
                        </div>

                        <input type="hidden" name="sort" value="{{ $sortState['sort'] }}">
                        <input type="hidden" name="dir" value="{{ $sortState['dir'] }}">
                    </form>
                </div>
            </div>
        </section>

        <section aria-label="Daftar paket">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h2 class="h6 mb-0">List Paket</h2>
                            <div class="text-muted">Total: {{ $paket->total() }}</div>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" aria-label="Tabel paket">
                            <thead>
                                <tr>
                                    <th scope="col">
                                        <a class="text-black text-decoration-none" href="{{ $sortUrl('id') }}">
                                            ID {{ $sortIndicator('id') }}
                                        </a>
                                    </th>
                                    <th scope="col">
                                        <a class="text-black text-decoration-none" href="{{ $sortUrl('nama_paket') }}">
                                            Nama Paket {{ $sortIndicator('nama_paket') }}
                                        </a>
                                    </th>
                                    <th scope="col">Deskripsi</th>
                                    <th scope="col" class="text-nowrap">
                                        <a class="text-black text-decoration-none" href="{{ $sortUrl('harga') }}">
                                            Harga {{ $sortIndicator('harga') }}
                                        </a>
                                    </th>
                                    <th scope="col" class="text-nowrap">
                                        <a class="text-black text-decoration-none" href="{{ $sortUrl('status') }}">
                                            Status {{ $sortIndicator('status') }}
                                        </a>
                                    </th>
                                    <th scope="col" class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($paket as $item)
                                    @php([$badgeClass, $badgeLabel] = $statusBadge($item->status))
                                    <tr>
                                        <td class="text-black fw-semibold">{{ $item->id }}</td>
                                        <td>
                                            <div class="text-black fw-semibold">{{ $item->nama_paket }}</div>
                                            @if ($item->event)
                                                <div class="text-muted small">Event: {{ $item->event->judul }}</div>
                                            @endif
                                        </td>
                                        <td class="text-muted">
                                            {{ \Illuminate\Support\Str::limit((string) $item->deskripsi, 90) ?: '-' }}
                                        </td>
                                        <td class="text-black text-nowrap">Rp {{ number_format((float) $item->harga, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-1">
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-primary btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#paketModal"
                                                    data-paket-mode="edit"
                                                    data-paket-id="{{ $item->id }}"
                                                    data-paket-event-id="{{ $item->event_id }}"
                                                    data-paket-nama="{{ $item->nama_paket }}"
                                                    data-paket-deskripsi="{{ $item->deskripsi }}"
                                                    data-paket-harga="{{ $item->harga }}"
                                                    data-paket-status="{{ $item->status }}"
                                                    title="Edit paket"
                                                >
                                                    <i class="la la-pen" aria-hidden="true"></i>
                                                </button>
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-danger btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal"
                                                    data-delete-action="{{ route('admin.paket.destroy', $item) }}"
                                                    data-delete-title="{{ $item->nama_paket }}"
                                                    title="Hapus paket"
                                                >
                                                    <i class="la la-trash" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Belum ada paket.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 d-flex justify-content-end">
                        {{ $paket->links() }}
                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade" id="paketModal" tabindex="-1" aria-labelledby="paketModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <form id="paket-form" method="POST" action="{{ route('admin.paket.store') }}" novalidate>
                        @csrf
                        <input type="hidden" name="_method" id="paket-form-method" value="PUT" disabled>
                        <div class="modal-header">
                            <h2 class="modal-title h5" id="paketModalTitle">Buat paket</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label text-black" for="paket-event-id">Event</label>
                                    <select id="paket-event-id" name="event_id" class="form-select" required>
                                        <option value="" selected disabled>Pilih event…</option>
                                        @foreach ($events as $event)
                                            <option value="{{ $event->id }}">{{ $event->judul }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Event wajib dipilih.</div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label text-black" for="paket-nama">Nama Paket</label>
                                    <input
                                        id="paket-nama"
                                        name="nama_paket"
                                        type="text"
                                        class="form-control"
                                        required
                                        maxlength="255"
                                        placeholder="Contoh: VIP"
                                    >
                                    <div class="invalid-feedback">Nama paket wajib diisi.</div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label text-black" for="paket-deskripsi">Deskripsi</label>
                                    <textarea id="paket-deskripsi" name="deskripsi" class="form-control" rows="4" placeholder="Opsional"></textarea>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="paket-harga">Harga</label>
                                    <input
                                        id="paket-harga"
                                        name="harga"
                                        type="number"
                                        class="form-control"
                                        required
                                        min="0"
                                        step="0.01"
                                        inputmode="decimal"
                                    >
                                    <div class="invalid-feedback">Harga wajib diisi dan minimal 0.</div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="paket-status">Status</label>
                                    <select id="paket-status" name="status" class="form-select" required>
                                        <option value="aktif">Aktif</option>
                                        <option value="nonaktif">Nonaktif</option>
                                    </select>
                                    <div class="invalid-feedback">Status wajib dipilih.</div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="paket-submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="delete-form" method="POST" action="#">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header">
                            <h2 class="modal-title h6" id="deleteModalLabel">Hapus paket</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-muted">Paket ini akan dihapus permanen.</div>
                            <div class="mt-2 fw-semibold text-black" id="delete-paket-title"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            (function () {
                const paketModal = document.getElementById('paketModal');
                const paketForm = document.getElementById('paket-form');
                const paketMethod = document.getElementById('paket-form-method');
                const paketTitle = document.getElementById('paketModalTitle');
                const paketSubmit = document.getElementById('paket-submit');

                const inputEvent = document.getElementById('paket-event-id');
                const inputNama = document.getElementById('paket-nama');
                const inputDeskripsi = document.getElementById('paket-deskripsi');
                const inputHarga = document.getElementById('paket-harga');
                const inputStatus = document.getElementById('paket-status');

                const storeUrl = @json(route('admin.paket.store'));
                const updateUrlTemplate = @json(route('admin.paket.update', ['paket' => '__PAKET__']));

                function resetValidation() {
                    paketForm.classList.remove('was-validated');
                }

                if (paketForm) {
                    paketForm.addEventListener('submit', (e) => {
                        if (!paketForm.checkValidity()) {
                            e.preventDefault();
                            e.stopPropagation();
                        }
                        paketForm.classList.add('was-validated');
                    });
                }

                if (paketModal) {
                    paketModal.addEventListener('show.bs.modal', (e) => {
                        resetValidation();
                        const trigger = e.relatedTarget;
                        const mode = trigger?.getAttribute('data-paket-mode') || 'create';

                        if (mode === 'edit') {
                            const id = trigger.getAttribute('data-paket-id');
                            const action = updateUrlTemplate.replace('__PAKET__', id);
                            paketForm.setAttribute('action', action);
                            paketMethod.disabled = false;

                            paketTitle.textContent = 'Edit paket';
                            paketSubmit.textContent = 'Update';

                            inputEvent.value = trigger.getAttribute('data-paket-event-id') || '';
                            inputNama.value = trigger.getAttribute('data-paket-nama') || '';
                            inputDeskripsi.value = trigger.getAttribute('data-paket-deskripsi') || '';
                            inputHarga.value = trigger.getAttribute('data-paket-harga') || '';
                            inputStatus.value = trigger.getAttribute('data-paket-status') || 'aktif';
                            return;
                        }

                        paketForm.setAttribute('action', storeUrl);
                        paketMethod.disabled = true;

                        paketTitle.textContent = 'Buat paket';
                        paketSubmit.textContent = 'Submit';

                        inputEvent.value = '';
                        inputNama.value = '';
                        inputDeskripsi.value = '';
                        inputHarga.value = '';
                        inputStatus.value = 'aktif';
                    });
                }

                const deleteModal = document.getElementById('deleteModal');
                const deleteForm = document.getElementById('delete-form');
                const deleteTitle = document.getElementById('delete-paket-title');

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
