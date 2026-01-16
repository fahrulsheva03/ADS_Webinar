@extends('admin.partials.app')

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

        $statusBadge = function (?string $status) {
            $status = strtolower((string) $status);
            return match ($status) {
                'paid' => ['bg-success', 'Paid'],
                'pending' => ['bg-warning text-dark', 'Pending'],
                'expired' => ['bg-secondary', 'Expired'],
                'failed' => ['bg-danger', 'Failed'],
                default => ['bg-light text-dark', $status ?: '-'],
            };
        };
    @endphp

    <main id="main-content" tabindex="-1" class="pb-4" role="main" aria-label="Daftar transaksi">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Transaksi</li>
            </ol>
        </nav>

        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div class="me-auto">
                <h1 class="h3 mb-1">Daftar Transaksi</h1>
                <div class="text-muted">Kelola transaksi harian, filter data, dan ekspor laporan.</div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <div class="btn-group" role="group" aria-label="Ekspor transaksi">
                    <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        Ekspor
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.transaksi.export', array_merge(request()->query(), ['format' => 'csv'])) }}">CSV</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.transaksi.export', array_merge(request()->query(), ['format' => 'xls'])) }}">Excel</a>
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

        <section class="mb-4" aria-label="Filter transaksi">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.transaksi.index') }}" class="row g-3 align-items-end">
                        <div class="col-12 col-lg-4">
                            <label class="form-label text-black" for="filter-q">Cari</label>
                            <input
                                id="filter-q"
                                name="q"
                                type="search"
                                class="form-control"
                                placeholder="Kode, nama, email, paket…"
                                value="{{ $q ?? request('q') }}"
                                autocomplete="off"
                            >
                        </div>
                        <div class="col-12 col-md-6 col-lg-2">
                            <label class="form-label text-black" for="filter-from">Dari</label>
                            <input id="filter-from" name="from" type="date" class="form-control" value="{{ $from ?? request('from') }}">
                        </div>
                        <div class="col-12 col-md-6 col-lg-2">
                            <label class="form-label text-black" for="filter-to">Sampai</label>
                            <input id="filter-to" name="to" type="date" class="form-control" value="{{ $to ?? request('to') }}">
                        </div>
                        <div class="col-12 col-md-6 col-lg-2">
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

                        <input type="hidden" name="sort" value="{{ $sortState['sort'] }}">
                        <input type="hidden" name="dir" value="{{ $sortState['dir'] }}">
                    </form>
                </div>
            </div>
        </section>

        <section aria-label="Daftar transaksi">
            <div class="card position-relative">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h2 class="h6 mb-0">Transaksi</h2>
                            <div class="text-muted">Total hasil: {{ $transaksi->total() }}</div>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <div class="input-group" style="min-width: 240px;">
                                <span class="input-group-text" aria-hidden="true">
                                    <i class="flaticon-381-search-2 text-primary"></i>
                                </span>
                                <input id="table-search" type="search" class="form-control" placeholder="Filter cepat…" autocomplete="off">
                            </div>
                            <select id="table-sort" class="form-select form-select-sm" style="min-width: 180px;" aria-label="Sortir cepat">
                                <option value="">Sortir cepat…</option>
                                <option value="id">ID Transaksi</option>
                                <option value="tanggal">Tanggal</option>
                                <option value="nama">Nama Pelanggan</option>
                                <option value="total">Total Pembayaran</option>
                                <option value="status">Status</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card-body pt-3">
                    <form id="bulk-form" method="POST" action="{{ route('admin.transaksi.bulk') }}">
                        @csrf
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <select class="form-select form-select-sm" name="action" id="bulk-action" style="min-width: 200px;">
                                    <option value="">Bulk action…</option>
                                    <option value="set_status">Set status</option>
                                    <option value="delete">Delete</option>
                                </select>
                                <select class="form-select form-select-sm d-none" name="status_pembayaran" id="bulk-status" style="min-width: 180px;">
                                    <option value="pending">Pending</option>
                                    <option value="paid">Paid</option>
                                    <option value="expired">Expired</option>
                                    <option value="failed">Failed</option>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm" id="bulk-apply" disabled>
                                    Terapkan
                                    <span class="spinner-border spinner-border-sm d-none" id="bulk-spinner" aria-hidden="true"></span>
                                </button>
                                <span class="text-muted small" id="bulk-selected">0 dipilih</span>
                            </div>
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.transaksi.index') }}">Reset</a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="transaksi-table" aria-label="Tabel transaksi">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-nowrap">
                                            <input class="form-check-input" type="checkbox" id="check-all" aria-label="Pilih semua">
                                        </th>
                                        <th scope="col" class="text-nowrap">
                                            <a class="text-black text-decoration-none" href="{{ $sortUrl('id') }}">
                                                ID Transaksi {{ $sortIndicator('id') }}
                                            </a>
                                        </th>
                                        <th scope="col" class="text-nowrap">
                                            <a class="text-black text-decoration-none" href="{{ $sortUrl('waktu') }}">
                                                Tanggal {{ $sortIndicator('waktu') }}
                                            </a>
                                        </th>
                                        <th scope="col">Nama Pelanggan</th>
                                        <th scope="col" class="text-nowrap">
                                            <a class="text-black text-decoration-none" href="{{ $sortUrl('total') }}">
                                                Total Pembayaran {{ $sortIndicator('total') }}
                                            </a>
                                        </th>
                                        <th scope="col">
                                            <a class="text-black text-decoration-none" href="{{ $sortUrl('status') }}">
                                                Status {{ $sortIndicator('status') }}
                                            </a>
                                        </th>
                                        <th scope="col" class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($transaksi as $trx)
                                        @php
                                            [$badgeClass, $badgeLabel] = $statusBadge($trx->status_pembayaran);
                                            $tanggal = $trx->waktu_bayar ?: $trx->created_at;
                                            $eventTitle = $trx->paket?->event?->judul;
                                            $paketName = $trx->paket?->nama_paket;
                                        @endphp
                                        <tr
                                            data-search-row
                                            data-id="{{ $trx->id }}"
                                            data-tanggal="{{ optional($tanggal)->timestamp ?? 0 }}"
                                            data-nama="{{ strtolower((string) $trx->user?->nama) }}"
                                            data-total="{{ (float) $trx->total_bayar }}"
                                            data-status="{{ strtolower((string) $trx->status_pembayaran) }}"
                                        >
                                            <td>
                                                <input class="form-check-input bulk-checkbox" type="checkbox" name="ids[]" value="{{ $trx->id }}" aria-label="Pilih transaksi {{ $trx->id }}">
                                            </td>
                                            <td class="text-black fw-semibold">{{ $trx->id }}</td>
                                            <td class="text-muted text-nowrap">{{ optional($tanggal)->format('d M Y H:i') }}</td>
                                            <td>
                                                <div class="text-black fw-semibold">{{ $trx->user?->nama ?: '-' }}</div>
                                                <div class="text-muted small">{{ $trx->user?->email ?: '-' }}</div>
                                            </td>
                                            <td class="text-black text-nowrap">Rp {{ number_format((float) $trx->total_bayar, 0, ',', '.') }}</td>
                                            <td><span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span></td>
                                            <td class="text-end">
                                                <div class="d-inline-flex gap-1">
                                                    <button
                                                        type="button"
                                                        class="btn btn-outline-primary btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#quickViewModal"
                                                        data-trx-id="{{ $trx->id }}"
                                                        data-trx-kode="{{ $trx->kode_pesanan }}"
                                                        data-trx-tanggal="{{ optional($tanggal)->format('d M Y H:i') }}"
                                                        data-trx-nama="{{ $trx->user?->nama }}"
                                                        data-trx-email="{{ $trx->user?->email }}"
                                                        data-trx-total="{{ number_format((float) $trx->total_bayar, 0, ',', '.') }}"
                                                        data-trx-status="{{ $trx->status_pembayaran }}"
                                                        data-trx-event="{{ $eventTitle }}"
                                                        data-trx-paket="{{ $paketName }}"
                                                        data-trx-user-url="{{ $trx->user ? route('admin.peserta.show', $trx->user) : '' }}"
                                                        title="Quick view"
                                                    >
                                                        <i class="la la-eye" aria-hidden="true"></i>
                                                    </button>
                                                    <button
                                                        type="button"
                                                        class="btn btn-outline-secondary btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editModal"
                                                        data-edit-id="{{ $trx->id }}"
                                                        data-edit-status="{{ $trx->status_pembayaran }}"
                                                        title="Edit status"
                                                    >
                                                        <i class="la la-pen" aria-hidden="true"></i>
                                                    </button>
                                                    <button
                                                        type="button"
                                                        class="btn btn-outline-danger btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal"
                                                        data-delete-action="{{ route('admin.transaksi.destroy', $trx) }}"
                                                        data-delete-title="Transaksi #{{ $trx->id }}"
                                                        title="Delete"
                                                    >
                                                        <i class="la la-trash" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">Belum ada transaksi.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </form>

                    <div class="mt-3 d-flex justify-content-end">
                        {{ $transaksi->links() }}
                    </div>
                </div>

                <div
                    class="position-absolute top-0 start-0 w-100 h-100 d-none align-items-center justify-content-center"
                    id="page-loading"
                    style="background: rgba(255, 255, 255, 0.75); z-index: 10;"
                >
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status" aria-label="Loading"></div>
                        <div class="mt-2 text-muted">Memproses…</div>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="quickViewModalLabel">Quick View</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="text-muted">Transaksi</div>
                                <div class="text-black fw-semibold" id="qv-id">-</div>
                                <div class="text-muted small" id="qv-kode">-</div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="text-muted">Tanggal</div>
                                <div class="text-black fw-semibold" id="qv-tanggal">-</div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="text-muted">Pelanggan</div>
                                <div class="text-black fw-semibold" id="qv-nama">-</div>
                                <div class="text-muted small" id="qv-email">-</div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="text-muted">Total</div>
                                <div class="text-black fw-semibold">Rp <span id="qv-total">0</span></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="text-muted">Event</div>
                                <div class="text-black fw-semibold" id="qv-event">-</div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="text-muted">Paket</div>
                                <div class="text-black fw-semibold" id="qv-paket">-</div>
                            </div>
                            <div class="col-12">
                                <div class="text-muted">Status</div>
                                <span class="badge" id="qv-status-badge">-</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a class="btn btn-outline-primary" id="qv-user-link" href="#" target="_self" rel="noopener">Lihat peserta</a>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="edit-form" method="POST" action="#">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h2 class="modal-title h6" id="editModalLabel">Edit transaksi</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label text-black" for="edit-status">Status pembayaran</label>
                                <select id="edit-status" name="status_pembayaran" class="form-select" required>
                                    <option value="pending">Pending</option>
                                    <option value="paid">Paid</option>
                                    <option value="expired">Expired</option>
                                    <option value="failed">Failed</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" id="edit-submit">
                                Simpan
                                <span class="spinner-border spinner-border-sm d-none" id="edit-spinner" aria-hidden="true"></span>
                            </button>
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
                            <h2 class="modal-title h6" id="deleteModalLabel">Hapus transaksi</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-muted">Aksi ini tidak dapat dibatalkan.</div>
                            <div class="mt-2 fw-semibold text-black" id="delete-trx-title"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger" id="delete-submit">
                                Hapus
                                <span class="spinner-border spinner-border-sm d-none" id="delete-spinner" aria-hidden="true"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            (function () {
                const table = document.getElementById('transaksi-table');
                const tableSearch = document.getElementById('table-search');
                const tableSort = document.getElementById('table-sort');

                const bulkForm = document.getElementById('bulk-form');
                const bulkAction = document.getElementById('bulk-action');
                const bulkStatus = document.getElementById('bulk-status');
                const bulkApply = document.getElementById('bulk-apply');
                const bulkSpinner = document.getElementById('bulk-spinner');
                const bulkSelected = document.getElementById('bulk-selected');
                const checkAll = document.getElementById('check-all');
                const pageLoading = document.getElementById('page-loading');

                const bulkCheckboxes = Array.from(document.querySelectorAll('.bulk-checkbox'));

                function setLoading(on) {
                    if (!pageLoading) return;
                    pageLoading.classList.toggle('d-none', !on);
                    pageLoading.classList.toggle('d-flex', on);
                }

                function updateBulkState() {
                    const selected = bulkCheckboxes.filter((c) => c.checked).length;
                    bulkSelected.textContent = `${selected} dipilih`;
                    bulkApply.disabled = selected === 0 || !bulkAction.value;
                }

                if (checkAll) {
                    checkAll.addEventListener('change', () => {
                        const checked = checkAll.checked;
                        bulkCheckboxes.forEach((c) => (c.checked = checked));
                        updateBulkState();
                    });
                }

                bulkCheckboxes.forEach((c) => c.addEventListener('change', updateBulkState));
                updateBulkState();

                if (bulkAction) {
                    bulkAction.addEventListener('change', () => {
                        const isSetStatus = bulkAction.value === 'set_status';
                        bulkStatus.classList.toggle('d-none', !isSetStatus);
                        updateBulkState();
                    });
                }

                if (bulkForm) {
                    bulkForm.addEventListener('submit', (e) => {
                        const selected = bulkCheckboxes.filter((c) => c.checked).length;
                        if (selected === 0) {
                            e.preventDefault();
                            return;
                        }
                        if (bulkAction.value === 'delete') {
                            const ok = window.confirm('Hapus semua transaksi terpilih?');
                            if (!ok) {
                                e.preventDefault();
                                return;
                            }
                        }

                        setLoading(true);
                        bulkApply.disabled = true;
                        bulkSpinner.classList.remove('d-none');
                    });
                }

                function filterRows() {
                    if (!table || !tableSearch) return;
                    const q = (tableSearch.value || '').toLowerCase().trim();
                    const rows = Array.from(table.querySelectorAll('tbody tr[data-search-row]'));
                    rows.forEach((row) => {
                        const hay = (row.innerText || '').toLowerCase();
                        row.style.display = !q || hay.includes(q) ? '' : 'none';
                    });
                }

                if (tableSearch) {
                    tableSearch.addEventListener('input', filterRows);
                }

                function sortRows(key) {
                    if (!table) return;
                    const tbody = table.querySelector('tbody');
                    const rows = Array.from(tbody.querySelectorAll('tr[data-search-row]'));

                    const getVal = (row) => {
                        if (key === 'id') return Number(row.dataset.id || 0);
                        if (key === 'tanggal') return Number(row.dataset.tanggal || 0);
                        if (key === 'nama') return String(row.dataset.nama || '');
                        if (key === 'total') return Number(row.dataset.total || 0);
                        if (key === 'status') return String(row.dataset.status || '');
                        return 0;
                    };

                    rows.sort((a, b) => {
                        const va = getVal(a);
                        const vb = getVal(b);
                        if (typeof va === 'number' && typeof vb === 'number') {
                            return vb - va;
                        }
                        return String(va).localeCompare(String(vb));
                    });

                    rows.forEach((row) => tbody.appendChild(row));
                }

                if (tableSort) {
                    tableSort.addEventListener('change', () => {
                        const key = tableSort.value;
                        if (!key) return;
                        sortRows(key);
                    });
                }

                const quickViewModal = document.getElementById('quickViewModal');
                const qvId = document.getElementById('qv-id');
                const qvKode = document.getElementById('qv-kode');
                const qvTanggal = document.getElementById('qv-tanggal');
                const qvNama = document.getElementById('qv-nama');
                const qvEmail = document.getElementById('qv-email');
                const qvTotal = document.getElementById('qv-total');
                const qvStatusBadge = document.getElementById('qv-status-badge');
                const qvEvent = document.getElementById('qv-event');
                const qvPaket = document.getElementById('qv-paket');
                const qvUserLink = document.getElementById('qv-user-link');

                const statusTone = {
                    paid: { className: 'bg-success', label: 'Paid' },
                    pending: { className: 'bg-warning text-dark', label: 'Pending' },
                    expired: { className: 'bg-secondary', label: 'Expired' },
                    failed: { className: 'bg-danger', label: 'Failed' },
                };

                if (quickViewModal) {
                    quickViewModal.addEventListener('show.bs.modal', (e) => {
                        const t = e.relatedTarget;
                        const status = (t?.getAttribute('data-trx-status') || '').toLowerCase();
                        const tone = statusTone[status] || { className: 'bg-light text-dark', label: status || '-' };

                        qvId.textContent = `#${t?.getAttribute('data-trx-id') || '-'}`;
                        qvKode.textContent = t?.getAttribute('data-trx-kode') || '-';
                        qvTanggal.textContent = t?.getAttribute('data-trx-tanggal') || '-';
                        qvNama.textContent = t?.getAttribute('data-trx-nama') || '-';
                        qvEmail.textContent = t?.getAttribute('data-trx-email') || '-';
                        qvTotal.textContent = t?.getAttribute('data-trx-total') || '0';
                        qvEvent.textContent = t?.getAttribute('data-trx-event') || '-';
                        qvPaket.textContent = t?.getAttribute('data-trx-paket') || '-';

                        qvStatusBadge.className = `badge ${tone.className}`;
                        qvStatusBadge.textContent = tone.label;

                        const userUrl = t?.getAttribute('data-trx-user-url') || '';
                        if (userUrl) {
                            qvUserLink.classList.remove('disabled');
                            qvUserLink.href = userUrl;
                        } else {
                            qvUserLink.classList.add('disabled');
                            qvUserLink.href = '#';
                        }
                    });
                }

                const editModal = document.getElementById('editModal');
                const editForm = document.getElementById('edit-form');
                const editStatus = document.getElementById('edit-status');
                const editSpinner = document.getElementById('edit-spinner');
                const editSubmit = document.getElementById('edit-submit');
                const updateUrlTemplate = @json(route('admin.transaksi.update', ['transaksi' => '__TRX__']));

                if (editModal) {
                    editModal.addEventListener('show.bs.modal', (e) => {
                        const t = e.relatedTarget;
                        const id = t?.getAttribute('data-edit-id') || '';
                        const status = t?.getAttribute('data-edit-status') || 'pending';
                        editForm.setAttribute('action', updateUrlTemplate.replace('__TRX__', id));
                        editStatus.value = status;
                        editSpinner.classList.add('d-none');
                        editSubmit.disabled = false;
                    });
                }

                if (editForm) {
                    editForm.addEventListener('submit', () => {
                        setLoading(true);
                        editSubmit.disabled = true;
                        editSpinner.classList.remove('d-none');
                    });
                }

                const deleteModal = document.getElementById('deleteModal');
                const deleteForm = document.getElementById('delete-form');
                const deleteTitle = document.getElementById('delete-trx-title');
                const deleteSpinner = document.getElementById('delete-spinner');
                const deleteSubmit = document.getElementById('delete-submit');

                if (deleteModal) {
                    deleteModal.addEventListener('show.bs.modal', (e) => {
                        const t = e.relatedTarget;
                        deleteForm.setAttribute('action', t?.getAttribute('data-delete-action') || '#');
                        deleteTitle.textContent = t?.getAttribute('data-delete-title') || '';
                        deleteSpinner.classList.add('d-none');
                        deleteSubmit.disabled = false;
                    });
                }

                if (deleteForm) {
                    deleteForm.addEventListener('submit', () => {
                        setLoading(true);
                        deleteSubmit.disabled = true;
                        deleteSpinner.classList.remove('d-none');
                    });
                }
            })();
        </script>
    </main>
@endsection
