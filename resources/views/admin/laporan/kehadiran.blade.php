@extends('admin.partials.app')

@section('content')
    @php
        use Illuminate\Support\Facades\Route;

        $query = request()->query();

        $rows = $rows ?? $history ?? $kehadiran ?? $attendance ?? null;

        $events = $events ?? collect();
        $sesi = $sesi ?? collect();
        $departments = $departments ?? $departmentOptions ?? [];

        $q = $q ?? (string) request()->query('q', '');
        $eventId = $eventId ?? (string) request()->query('event_id', '');
        $sesiId = $sesiId ?? (string) request()->query('event_sesi_id', '');
        $from = $from ?? (string) request()->query('from', '');
        $to = $to ?? (string) request()->query('to', '');
        $department = $department ?? (string) request()->query('department', '');
        $status = $status ?? (string) request()->query('status', '');

        $sortState = [
            'sort' => $sort ?? (string) request()->query('sort', 'tanggal'),
            'dir' => $dir ?? (string) request()->query('dir', 'desc'),
        ];

        $sortUrl = function (string $column) use ($query, $sortState) {
            $isActive = $sortState['sort'] === $column;
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

        $statusLabel = function (?string $value, $row = null) {
            $value = strtolower(trim((string) $value));

            if ($value === '') {
                $leave = $row?->waktu_leave ?? null;
                $value = empty($leave) ? 'hadir' : 'keluar';
            }

            return match ($value) {
                'hadir', 'present' => ['bg-success', 'Hadir'],
                'keluar', 'done' => ['bg-secondary', 'Keluar'],
                'cuti', 'leave' => ['bg-info', 'Cuti'],
                'sakit', 'sick' => ['bg-warning text-dark', 'Sakit'],
                'alfa', 'absent' => ['bg-danger', 'Alfa'],
                'terlambat', 'late' => ['bg-warning text-dark', 'Terlambat'],
                default => ['bg-light text-dark', $value ?: '-'],
            };
        };

        $exportExcelUrl = Route::has('admin.laporan.kehadiran.export')
            ? route('admin.laporan.kehadiran.export', array_merge($query, ['format' => 'xls']))
            : (Route::has('admin.scan.export') ? route('admin.scan.export', array_merge($query, ['format' => 'xls'])) : null);

        $exportCsvUrl = Route::has('admin.laporan.kehadiran.export')
            ? route('admin.laporan.kehadiran.export', array_merge($query, ['format' => 'csv']))
            : (Route::has('admin.scan.export') ? route('admin.scan.export', array_merge($query, ['format' => 'csv'])) : null);
    @endphp

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            .content-body {
                padding: 0 !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>

    <main id="main-content" tabindex="-1" class="pb-4" role="main" aria-label="Laporan kehadiran">
        <nav aria-label="breadcrumb" class="mb-3 no-print">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Laporan Kehadiran</li>
            </ol>
        </nav>

        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4 no-print">
            <div class="me-auto">
                <h1 class="h3 mb-1">Laporan Kehadiran</h1>
                <div class="text-muted">Pantau kehadiran peserta per sesi, kelola status, dan ekspor data.</div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <div class="btn-group" role="group" aria-label="Ekspor laporan kehadiran">
                    <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        Ekspor
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item @if (!$exportExcelUrl) disabled @endif" href="{{ $exportExcelUrl ?: '#' }}" @if (!$exportExcelUrl) aria-disabled="true" @endif>
                                Excel
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item @if (!$exportCsvUrl) disabled @endif" href="{{ $exportCsvUrl ?: '#' }}" @if (!$exportCsvUrl) aria-disabled="true" @endif>
                                CSV
                            </a>
                        </li>
                        <li><button type="button" class="dropdown-item" id="btn-print">PDF (Print)</button></li>
                    </ul>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show no-print" role="alert">
                <div class="fw-semibold">Sukses</div>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show no-print" role="alert">
                <div class="fw-semibold">Gagal</div>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show no-print" role="alert">
                <div class="fw-semibold">Validasi gagal</div>
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        <div id="notify-area" class="mb-3 no-print" aria-live="polite" aria-atomic="true"></div>

        <section class="mb-4 no-print" aria-label="Filter laporan kehadiran">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ url()->current() }}" class="row g-3 align-items-end" id="filter-form" novalidate>
                        <div class="col-12 col-lg-4">
                            <label class="form-label text-black" for="filter-q">Cari</label>
                            <input
                                id="filter-q"
                                name="q"
                                type="search"
                                class="form-control"
                                placeholder="Nama, email, event, sesi…"
                                value="{{ $q }}"
                            >
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label text-black" for="filter-event">Event</label>
                            <select id="filter-event" name="event_id" class="form-select">
                                <option value="">Semua event</option>
                                @foreach ($events as $event)
                                    <option value="{{ $event->id }}" @selected((string) $eventId === (string) $event->id)>{{ $event->judul }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label text-black" for="filter-sesi">Sesi</label>
                            <select id="filter-sesi" name="event_sesi_id" class="form-select">
                                <option value="">Semua sesi</option>
                                @foreach ($sesi as $row)
                                    <option value="{{ $row->id }}" data-event-id="{{ $row->event_id }}" @selected((string) $sesiId === (string) $row->id)>{{ $row->judul_sesi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-lg-1">
                            <label class="form-label text-black" for="filter-from">Dari</label>
                            <input id="filter-from" name="from" type="date" class="form-control" value="{{ $from }}">
                            <div class="invalid-feedback">Tanggal tidak valid.</div>
                        </div>
                        <div class="col-6 col-lg-1">
                            <label class="form-label text-black" for="filter-to">Sampai</label>
                            <input id="filter-to" name="to" type="date" class="form-control" value="{{ $to }}">
                            <div class="invalid-feedback">Tanggal tidak valid.</div>
                        </div>

                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label text-black" for="filter-department">Department</label>
                            <select id="filter-department" name="department" class="form-select" @if (empty($departments)) disabled @endif>
                                <option value="">Semua department</option>
                                @foreach ($departments as $key => $label)
                                    @php
                                        $value = is_int($key) ? $label : $key;
                                        $text = is_int($key) ? $label : $label;
                                    @endphp
                                    <option value="{{ $value }}" @selected((string) $department === (string) $value)>{{ $text }}</option>
                                @endforeach
                            </select>
                            @if (empty($departments))
                                <div class="form-text">Opsi department belum tersedia di data proyek ini.</div>
                            @endif
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label text-black" for="filter-status">Status</label>
                            <select id="filter-status" name="status" class="form-select">
                                <option value="">Semua status</option>
                                <option value="hadir" @selected((string) $status === 'hadir')>Hadir</option>
                                <option value="keluar" @selected((string) $status === 'keluar')>Keluar</option>
                                <option value="cuti" @selected((string) $status === 'cuti')>Cuti</option>
                                <option value="sakit" @selected((string) $status === 'sakit')>Sakit</option>
                                <option value="alfa" @selected((string) $status === 'alfa')>Alfa</option>
                                <option value="terlambat" @selected((string) $status === 'terlambat')>Terlambat</option>
                            </select>
                        </div>

                        <div class="col-12 col-lg-12 d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary" id="btn-apply">Terapkan</button>
                            <a href="{{ url()->current() }}" class="btn btn-outline-secondary">Reset</a>
                            <div class="ms-auto d-flex align-items-center gap-2">
                                <div class="text-muted small" id="filter-hint">Gunakan filter server untuk data besar.</div>
                            </div>
                        </div>

                        <input type="hidden" name="sort" value="{{ $sortState['sort'] }}">
                        <input type="hidden" name="dir" value="{{ $sortState['dir'] }}">
                    </form>
                </div>
            </div>
        </section>

        <section aria-label="Data laporan kehadiran">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <h2 class="h5 mb-0">Tabel Kehadiran</h2>
                        <div class="input-group input-group-sm no-print" style="max-width: 340px;">
                            <span class="input-group-text">
                                <i class="flaticon-381-search-2 text-primary" aria-hidden="true"></i>
                            </span>
                            <input id="table-search" type="search" class="form-control" placeholder="Pencarian instan di tabel…" autocomplete="off" aria-label="Pencarian instan">
                        </div>
                    </div>
                </div>

                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="attendance-table" aria-label="Tabel laporan kehadiran">
                            <thead>
                                <tr>
                                    <th scope="col">
                                        <a class="text-decoration-none" href="{{ $sortUrl('nama') }}">Nama {!! $sortIndicator('nama') !!}</a>
                                    </th>
                                    <th scope="col">
                                        <a class="text-decoration-none" href="{{ $sortUrl('tanggal') }}">Tanggal {!! $sortIndicator('tanggal') !!}</a>
                                    </th>
                                    <th scope="col">
                                        <a class="text-decoration-none" href="{{ $sortUrl('jam_masuk') }}">Jam Masuk {!! $sortIndicator('jam_masuk') !!}</a>
                                    </th>
                                    <th scope="col">
                                        <a class="text-decoration-none" href="{{ $sortUrl('jam_keluar') }}">Jam Keluar {!! $sortIndicator('jam_keluar') !!}</a>
                                    </th>
                                    <th scope="col">
                                        <a class="text-decoration-none" href="{{ $sortUrl('status') }}">Status {!! $sortIndicator('status') !!}</a>
                                    </th>
                                    <th scope="col" class="text-end no-print">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $iterable = $rows ?? [];
                                @endphp
                                @forelse ($iterable as $row)
                                    @php
                                        $nama = $row->nama ?? $row->user?->nama ?? '-';
                                        $email = $row->email ?? $row->user?->email ?? '';

                                        $tanggalRaw = $row->tanggal ?? $row->waktu_join ?? $row->created_at ?? null;
                                        $tanggal = $tanggalRaw ? optional($tanggalRaw)->format('Y-m-d') : '-';
                                        $jamMasuk = optional($row->waktu_join ?? $row->jam_masuk ?? null)->format('H:i:s') ?? '-';
                                        $jamKeluar = optional($row->waktu_leave ?? $row->jam_keluar ?? null)->format('H:i:s') ?? '-';

                                        $statusVal = $row->status ?? $row->status_kehadiran ?? '';
                                        [$badgeClass, $badgeText] = $statusLabel($statusVal, $row);

                                        $editUrl = Route::has('admin.laporan.kehadiran.update') ? route('admin.laporan.kehadiran.update', ['kehadiran' => $row->id]) : null;
                                        $deleteUrl = Route::has('admin.laporan.kehadiran.destroy') ? route('admin.laporan.kehadiran.destroy', ['kehadiran' => $row->id]) : null;
                                        $verifyUrl = Route::has('admin.laporan.kehadiran.verify') ? route('admin.laporan.kehadiran.verify', ['kehadiran' => $row->id]) : null;

                                        $payload = [
                                            'id' => $row->id,
                                            'nama' => $nama,
                                            'email' => $email,
                                            'tanggal' => $tanggal,
                                            'jam_masuk' => $jamMasuk,
                                            'jam_keluar' => $jamKeluar,
                                            'status' => $badgeText,
                                            'edit_url' => $editUrl,
                                            'delete_url' => $deleteUrl,
                                            'verify_url' => $verifyUrl,
                                        ];
                                    @endphp

                                    <tr data-search-row>
                                        <td>
                                            <div class="fw-semibold text-black">{{ $nama }}</div>
                                            @if ($email)
                                                <div class="text-muted small">{{ $email }}</div>
                                            @endif
                                        </td>
                                        <td class="text-muted">{{ $tanggal }}</td>
                                        <td class="text-muted">{{ $jamMasuk }}</td>
                                        <td class="text-muted">{{ $jamKeluar }}</td>
                                        <td><span class="badge {{ $badgeClass }}">{{ $badgeText }}</span></td>
                                        <td class="text-end no-print">
                                            <div class="d-inline-flex flex-wrap justify-content-end gap-2">
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-primary btn-xxs"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editModal"
                                                    data-record='@json($payload)'
                                                    @if (!$editUrl) disabled @endif
                                                    aria-label="Edit kehadiran {{ $row->id }}"
                                                    title="Edit"
                                                >
                                                    <i class="la la-pen" aria-hidden="true"></i>
                                                </button>
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-success btn-xxs"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#verifyModal"
                                                    data-record='@json($payload)'
                                                    @if (!$verifyUrl) disabled @endif
                                                    aria-label="Verifikasi kehadiran {{ $row->id }}"
                                                    title="Verifikasi"
                                                >
                                                    <i class="la la-check" aria-hidden="true"></i>
                                                </button>
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-danger btn-xxs"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal"
                                                    data-record='@json($payload)'
                                                    @if (!$deleteUrl) disabled @endif
                                                    aria-label="Hapus kehadiran {{ $row->id }}"
                                                    title="Hapus"
                                                >
                                                    <i class="la la-trash" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <div class="text-center py-5">
                                                <div class="fw-semibold text-black mb-1">Belum ada data kehadiran</div>
                                                <div class="text-muted">Data akan muncul setelah peserta melakukan check-in atau join sesi.</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if (is_object($rows) && method_exists($rows, 'links'))
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-4 no-print">
                            <div class="text-muted">
                                Menampilkan {{ $rows->firstItem() ?? 0 }}–{{ $rows->lastItem() ?? 0 }} dari {{ $rows->total() ?? 0 }}
                            </div>
                            <div>
                                {{ $rows->onEachSide(1)->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <form id="edit-form" method="POST" action="#">
                        @csrf
                        <div class="modal-header">
                            <h2 class="modal-title h5" id="editModalTitle">Edit kehadiran</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded">
                                        <div class="text-muted small">Peserta</div>
                                        <div class="fw-semibold text-black" id="edit-info"></div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label text-black" for="edit-tanggal">Tanggal</label>
                                    <input id="edit-tanggal" name="tanggal" type="date" class="form-control" required>
                                    <div class="invalid-feedback">Tanggal wajib diisi.</div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label text-black" for="edit-jam-masuk">Jam masuk</label>
                                    <input id="edit-jam-masuk" name="jam_masuk" type="time" step="1" class="form-control" required>
                                    <div class="invalid-feedback">Jam masuk wajib diisi.</div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label text-black" for="edit-jam-keluar">Jam keluar</label>
                                    <input id="edit-jam-keluar" name="jam_keluar" type="time" step="1" class="form-control">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label text-black" for="edit-status">Status</label>
                                    <select id="edit-status" name="status" class="form-select" required>
                                        <option value="hadir">Hadir</option>
                                        <option value="keluar">Keluar</option>
                                        <option value="cuti">Cuti</option>
                                        <option value="sakit">Sakit</option>
                                        <option value="alfa">Alfa</option>
                                        <option value="terlambat">Terlambat</option>
                                    </select>
                                    <div class="invalid-feedback">Status wajib dipilih.</div>
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

        <div class="modal fade" id="verifyModal" tabindex="-1" aria-labelledby="verifyModalTitle" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="verifyModalTitle">Verifikasi kehadiran</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-muted">Pastikan data kehadiran sudah benar sebelum verifikasi.</div>
                        <div class="mt-2 fw-semibold text-black" id="verify-info"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-success" id="verify-confirm">
                            Verifikasi
                            <span class="spinner-border spinner-border-sm d-none" id="verify-spinner" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalTitle" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="deleteModalTitle">Hapus record</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-muted">Record ini akan dihapus permanen.</div>
                        <div class="mt-2 fw-semibold text-black" id="delete-info"></div>
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

                const notifyArea = document.getElementById('notify-area');
                const pageLoading = document.getElementById('page-loading');

                const filterForm = document.getElementById('filter-form');
                const fromEl = document.getElementById('filter-from');
                const toEl = document.getElementById('filter-to');
                const btnApply = document.getElementById('btn-apply');

                const filterEvent = document.getElementById('filter-event');
                const filterSesi = document.getElementById('filter-sesi');

                const tableSearch = document.getElementById('table-search');
                const table = document.getElementById('attendance-table');

                const btnPrint = document.getElementById('btn-print');

                const editModalEl = document.getElementById('editModal');
                const editForm = document.getElementById('edit-form');
                const editInfo = document.getElementById('edit-info');
                const editTanggal = document.getElementById('edit-tanggal');
                const editJamMasuk = document.getElementById('edit-jam-masuk');
                const editJamKeluar = document.getElementById('edit-jam-keluar');
                const editStatus = document.getElementById('edit-status');
                const editSubmit = document.getElementById('edit-submit');
                const editSpinner = document.getElementById('edit-spinner');

                const verifyModalEl = document.getElementById('verifyModal');
                const verifyInfo = document.getElementById('verify-info');
                const verifyConfirm = document.getElementById('verify-confirm');
                const verifySpinner = document.getElementById('verify-spinner');
                let verifyUrl = '';

                const deleteModalEl = document.getElementById('deleteModal');
                const deleteInfo = document.getElementById('delete-info');
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

                function isValidDateValue(v) {
                    if (!v) return true;
                    const d = new Date(v);
                    return !Number.isNaN(d.getTime());
                }

                function validateDateRange() {
                    const fromVal = fromEl ? fromEl.value : '';
                    const toVal = toEl ? toEl.value : '';

                    let ok = true;

                    if (fromEl) {
                        const valid = isValidDateValue(fromVal);
                        fromEl.classList.toggle('is-invalid', !valid);
                        ok = ok && valid;
                    }

                    if (toEl) {
                        const valid = isValidDateValue(toVal);
                        toEl.classList.toggle('is-invalid', !valid);
                        ok = ok && valid;
                    }

                    if (fromVal && toVal) {
                        const f = new Date(fromVal);
                        const t = new Date(toVal);
                        const inOrder = f.getTime() <= t.getTime();
                        if (fromEl) fromEl.classList.toggle('is-invalid', !inOrder);
                        if (toEl) toEl.classList.toggle('is-invalid', !inOrder);
                        ok = ok && inOrder;
                    }

                    return ok;
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

                function wireQuickSearch() {
                    if (!tableSearch || !table) return;

                    let t = null;
                    tableSearch.addEventListener('input', () => {
                        if (t) window.clearTimeout(t);
                        t = window.setTimeout(() => {
                            const q = (tableSearch.value || '').toLowerCase().trim();
                            const rows = Array.from(table.querySelectorAll('tbody tr[data-search-row]'));
                            rows.forEach((row) => {
                                const hay = (row.innerText || '').toLowerCase();
                                row.style.display = !q || hay.includes(q) ? '' : 'none';
                            });
                        }, 150);
                    });
                }

                if (filterEvent && filterSesi) {
                    filterSesiByEvent(filterEvent, filterSesi);
                    filterEvent.addEventListener('change', () => filterSesiByEvent(filterEvent, filterSesi));
                }

                if (filterForm) {
                    filterForm.addEventListener('submit', (e) => {
                        const ok = validateDateRange();
                        if (!ok) {
                            e.preventDefault();
                            pushNotice('warning', 'Validasi', 'Periksa rentang tanggal filter.');
                            return;
                        }
                        if (btnApply) btnApply.disabled = true;
                        setPageLoading(true);
                    });
                }

                if (btnPrint) {
                    btnPrint.addEventListener('click', () => window.print());
                }

                if (editModalEl) {
                    editModalEl.addEventListener('show.bs.modal', (e) => {
                        const trigger = e.relatedTarget;
                        const payload = trigger?.getAttribute('data-record');
                        const data = payload ? JSON.parse(payload) : null;
                        if (!data) return;
                        editForm.setAttribute('action', data.edit_url || '#');
                        if (editInfo) editInfo.textContent = `${data.nama || '-'}${data.email ? ' · ' + data.email : ''}`;
                        if (editTanggal) editTanggal.value = data.tanggal || '';
                        if (editJamMasuk) editJamMasuk.value = data.jam_masuk ? String(data.jam_masuk).slice(0, 8) : '';
                        if (editJamKeluar) editJamKeluar.value = data.jam_keluar ? String(data.jam_keluar).slice(0, 8) : '';
                        if (editStatus) editStatus.value = String(data.status || '').toLowerCase() || 'hadir';
                    });
                }

                if (editForm) {
                    editForm.addEventListener('submit', async (e) => {
                        e.preventDefault();
                        const action = editForm.getAttribute('action');
                        if (!action || action === '#') return;

                        if (!editForm.checkValidity()) {
                            editForm.classList.add('was-validated');
                            pushNotice('warning', 'Validasi', 'Lengkapi field yang wajib.');
                            return;
                        }

                        try {
                            editSubmit.disabled = true;
                            editSpinner.classList.remove('d-none');
                            setPageLoading(true);

                            const formData = new FormData(editForm);
                            formData.set('_method', 'PUT');
                            const payload = await requestJson(action, { method: 'POST', body: formData });
                            pushNotice('success', 'Sukses', payload.message || 'Data kehadiran diperbarui.');
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

                if (verifyModalEl) {
                    verifyModalEl.addEventListener('show.bs.modal', (e) => {
                        const trigger = e.relatedTarget;
                        const payload = trigger?.getAttribute('data-record');
                        const data = payload ? JSON.parse(payload) : null;
                        if (!data) return;
                        verifyUrl = data.verify_url || '';
                        if (verifyInfo) verifyInfo.textContent = `${data.nama || '-'} · ${data.tanggal || '-'}`;
                    });
                }

                if (verifyConfirm) {
                    verifyConfirm.addEventListener('click', async () => {
                        if (!verifyUrl) return;
                        try {
                            verifyConfirm.disabled = true;
                            verifySpinner.classList.remove('d-none');
                            setPageLoading(true);
                            const payload = await requestJson(verifyUrl, { method: 'POST' });
                            pushNotice('success', 'Sukses', payload.message || 'Record berhasil diverifikasi.');
                            window.location.reload();
                        } catch (err) {
                            pushNotice('danger', 'Gagal', err.message || 'Gagal verifikasi record.');
                        } finally {
                            verifyConfirm.disabled = false;
                            verifySpinner.classList.add('d-none');
                            setPageLoading(false);
                        }
                    });
                }

                if (deleteModalEl) {
                    deleteModalEl.addEventListener('show.bs.modal', (e) => {
                        const trigger = e.relatedTarget;
                        const payload = trigger?.getAttribute('data-record');
                        const data = payload ? JSON.parse(payload) : null;
                        if (!data) return;
                        deleteUrl = data.delete_url || '';
                        if (deleteInfo) deleteInfo.textContent = `${data.nama || '-'} · ${data.tanggal || '-'}`;
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
                            pushNotice('success', 'Sukses', payload.message || 'Record berhasil dihapus.');
                            window.location.reload();
                        } catch (err) {
                            pushNotice('danger', 'Gagal', err.message || 'Gagal menghapus record.');
                        } finally {
                            deleteConfirm.disabled = false;
                            deleteSpinner.classList.add('d-none');
                            setPageLoading(false);
                        }
                    });
                }

                wireQuickSearch();
            })();
        </script>
    </main>
@endsection
