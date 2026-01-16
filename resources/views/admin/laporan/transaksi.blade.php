@extends('admin.partials.app')

@section('content')
    @php
        use Carbon\Carbon;
        use Illuminate\Support\Facades\Route;

        $filters = $filters ?? [];

        $q = (string) ($filters['q'] ?? request()->query('q', ''));
        $eventId = (string) ($filters['event_id'] ?? request()->query('event_id', ''));
        $paketId = (string) ($filters['paket_id'] ?? request()->query('paket_id', ''));
        $from = (string) ($filters['from'] ?? request()->query('from', ''));
        $to = (string) ($filters['to'] ?? request()->query('to', ''));

        $selectedStatus = $filters['status'] ?? request()->query('status', []);
        $selectedStatus = is_array($selectedStatus) ? $selectedStatus : [$selectedStatus];
        $selectedStatus = array_values(array_filter($selectedStatus, fn ($v) => (string) $v !== ''));

        $selectedMetode = $filters['metode'] ?? request()->query('metode', []);
        $selectedMetode = is_array($selectedMetode) ? $selectedMetode : [$selectedMetode];
        $selectedMetode = array_values(array_filter($selectedMetode, fn ($v) => (string) $v !== ''));

        $events = $events ?? collect();
        $paket = $paket ?? collect();
        $transaksi = $transaksi ?? null;

        $stats = $stats ?? [
            'total_transaksi' => 0,
            'paid_count' => 0,
            'paid_nominal' => 0,
            'pending_count' => 0,
            'revenue' => 0,
        ];

        $fmtRp = function ($amount): string {
            $n = is_numeric($amount) ? (float) $amount : 0;
            return 'Rp '.number_format($n, 0, ',', '.');
        };

        $fmtDt = function ($value): string {
            if (empty($value)) {
                return '-';
            }
            try {
                return Carbon::parse($value)->format('d-m-Y H:i');
            } catch (Throwable) {
                return '-';
            }
        };

        $trxCode = function ($row): string {
            $code = (string) ($row->kode_pesanan ?? '');
            if (preg_match('/^TRX-\d{6,}$/', $code) === 1) {
                return $code;
            }
            if ($code !== '') {
                return $code;
            }

            return 'TRX-'.str_pad((string) ($row->id ?? 0), 6, '0', STR_PAD_LEFT);
        };

        $badgeStatus = function (?string $status): array {
            $s = strtolower(trim((string) $status));
            return match ($s) {
                'paid' => ['bg-success', 'Paid'],
                'pending' => ['bg-warning text-dark', 'Pending'],
                'failed' => ['bg-danger', 'Failed'],
                'expired' => ['bg-secondary', 'Expired'],
                default => ['bg-light text-dark', $status ?: '-'],
            };
        };

        $emailOk = function (?string $email): bool {
            $email = (string) $email;
            return $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        };

        $exportCsvUrl = Route::has('admin.laporan.transaksi.export')
            ? route('admin.laporan.transaksi.export', array_merge(request()->query(), ['format' => 'csv']))
            : null;
        $exportXlsxUrl = Route::has('admin.laporan.transaksi.export')
            ? route('admin.laporan.transaksi.export', array_merge(request()->query(), ['format' => 'xlsx']))
            : null;

        $statusOptions = [
            'paid' => 'Paid',
            'pending' => 'Pending',
            'failed' => 'Failed',
            'expired' => 'Expired',
        ];

        $metodeOptions = [
            'bank_transfer' => 'Bank Transfer',
            'e_wallet' => 'E-Wallet',
        ];
    @endphp

    <main id="main-content" tabindex="-1" class="pb-4" role="main" aria-label="Laporan transaksi">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Laporan Transaksi</li>
            </ol>
        </nav>

        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div class="me-auto">
                <h1 class="h3 mb-1">Laporan Transaksi</h1>
                <div class="text-muted">Pantau transaksi peserta, filter data, dan ekspor laporan.</div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <div class="btn-group" role="group" aria-label="Ekspor laporan transaksi">
                    <button
                        type="button"
                        class="btn btn-primary btn-sm dropdown-toggle"
                        id="exportMenuBtn"
                        aria-haspopup="true"
                        aria-expanded="false"
                    >
                        <i class="la la-download" aria-hidden="true"></i>
                        Export
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" role="menu" aria-label="Export menu" id="exportMenu">
                        <a
                            href="{{ $exportXlsxUrl ?: '#' }}"
                            class="dropdown-item @if (!$exportXlsxUrl) disabled @endif"
                            data-export-link
                        >
                            Excel (.xlsx)
                        </a>
                        <a
                            href="{{ $exportCsvUrl ?: '#' }}"
                            class="dropdown-item @if (!$exportCsvUrl) disabled @endif"
                            data-export-link
                        >
                            CSV (.csv)
                        </a>
                    </div>
                </div>

                @if (Route::has('admin.transaksi.index'))
                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.transaksi.index') }}">
                        <i class="la la-list" aria-hidden="true"></i>
                        Halaman Transaksi
                    </a>
                @endif
            </div>
        </div>

        <section class="mb-4" aria-label="Ringkasan transaksi">
            <div class="row g-3">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card h-100 mb-0">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-2">
                                <div>
                                    <div class="text-muted">Total Transaksi</div>
                                    <div class="fs-30 fw-semibold text-black">{{ number_format((int) ($stats['total_transaksi'] ?? 0)) }}</div>
                                    <div class="text-muted small">Semua status</div>
                                </div>
                                <i class="la la-shopping-cart fs-2 text-primary" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card h-100 mb-0">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-2">
                                <div>
                                    <div class="text-muted">Total Paid</div>
                                    <div class="fs-30 fw-semibold text-black">{{ number_format((int) ($stats['paid_count'] ?? 0)) }}</div>
                                    <div class="text-muted small">Nominal: {{ $fmtRp($stats['paid_nominal'] ?? 0) }}</div>
                                </div>
                                <i class="la la-check-circle fs-2 text-success" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card h-100 mb-0">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-2">
                                <div>
                                    <div class="text-muted">Total Pending</div>
                                    <div class="fs-30 fw-semibold text-black">{{ number_format((int) ($stats['pending_count'] ?? 0)) }}</div>
                                    <div class="text-muted small">Butuh tindak lanjut</div>
                                </div>
                                <i class="la la-hourglass-half fs-2 text-warning" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card h-100 mb-0">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-2">
                                <div>
                                    <div class="text-muted">Total Revenue</div>
                                    <div class="fs-30 fw-semibold text-black">{{ $fmtRp($stats['revenue'] ?? 0) }}</div>
                                    <div class="text-muted small">Hanya status paid</div>
                                </div>
                                <i class="la la-coins fs-2 text-primary" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mb-4" aria-label="Filter laporan transaksi">
            <div class="card">
                <div class="card-body">
                    <form method="GET" id="filterForm" class="row g-3 align-items-end">
                        <div class="col-12 col-lg-3">
                            <label for="filterEvent" class="form-label text-black">Event</label>
                            <select id="filterEvent" name="event_id" class="form-select">
                                <option value="">Semua event</option>
                                @foreach ($events as $e)
                                    <option value="{{ $e->id }}" @selected((string) $eventId === (string) $e->id)>{{ $e->judul }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-lg-3">
                            <label for="filterPaket" class="form-label text-black">Paket</label>
                            <select id="filterPaket" name="paket_id" class="form-select">
                                <option value="">Semua paket</option>
                                @foreach ($paket as $p)
                                    <option value="{{ $p->id }}" data-event-id="{{ $p->event_id }}" @selected((string) $paketId === (string) $p->id)>
                                        {{ $p->nama_paket }} @if ($p->event) — {{ $p->event->judul }} @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6 col-lg-2">
                            <label for="filterStatus" class="form-label text-black">Status Pembayaran</label>
                            <select id="filterStatus" name="status[]" multiple class="form-select">
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(in_array($value, $selectedStatus, true))>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6 col-lg-2">
                            <label for="filterMetode" class="form-label text-black">Metode Pembayaran</label>
                            <select id="filterMetode" name="metode[]" multiple class="form-select">
                                @foreach ($metodeOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(in_array($value, $selectedMetode, true))>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6 col-lg-2">
                            <label for="filterFrom" class="form-label text-black">Dari</label>
                            <input id="filterFrom" name="from" type="date" value="{{ $from }}" class="form-control">
                        </div>

                        <div class="col-12 col-md-6 col-lg-2">
                            <label for="filterTo" class="form-label text-black">Sampai</label>
                            <input id="filterTo" name="to" type="date" value="{{ $to }}" class="form-control">
                        </div>

                        <div class="col-12 col-lg-4">
                            <label for="filterQ" class="form-label text-black">Search</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="flaticon-381-search-2 text-primary" aria-hidden="true"></i>
                                </span>
                                <input
                                    id="filterQ"
                                    name="q"
                                    type="search"
                                    value="{{ $q }}"
                                    placeholder="Cari email peserta atau kode pesanan…"
                                    class="form-control"
                                    autocomplete="off"
                                >
                            </div>
                            <div class="text-muted small mt-1">Pencarian berdasarkan email peserta atau kode pesanan.</div>
                        </div>

                        <div class="col-12 d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary" id="btnApply">
                                <i class="la la-filter" aria-hidden="true"></i>
                                Terapkan Filter
                            </button>
                            <a href="{{ url()->current() }}" class="btn btn-outline-secondary">
                                <i class="la la-refresh" aria-hidden="true"></i>
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <section aria-label="Data transaksi">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h2 class="h5 mb-0">Data Transaksi</h2>
                            <div class="text-muted">Pagination aktif, aman untuk data besar.</div>
                        </div>

                        @if (is_object($transaksi) && method_exists($transaksi, 'total'))
                            <span class="badge bg-light text-dark">Total: {{ number_format((int) $transaksi->total()) }}</span>
                        @endif
                    </div>
                </div>

                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" aria-label="Tabel laporan transaksi">
                            <thead>
                                <tr>
                                    <th scope="col">Kode Pesanan</th>
                                    <th scope="col">Peserta</th>
                                    <th scope="col">Event</th>
                                    <th scope="col">Paket</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Metode</th>
                                    <th scope="col" class="text-end">Total Bayar</th>
                                    <th scope="col">Waktu Pesan</th>
                                    <th scope="col">Waktu Bayar</th>
                                    <th scope="col" class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $rows = is_object($transaksi) ? $transaksi : collect();
                                @endphp

                                @forelse ($rows as $row)
                                    @php
                                        $nama = $row->user?->nama ?? '-';
                                        $email = $row->user?->email ?? '';
                                        $eventNama = $row->paket?->event?->judul ?? '-';
                                        $paketNama = $row->paket?->nama_paket ?? '-';
                                        $statusVal = $row->status_pembayaran ?? '';
                                        [$badgeCls, $badgeText] = $badgeStatus($statusVal);
                                        $metodeVal = (string) ($row->metode_pembayaran ?? '');

                                        $createdAt = $row->created_at ?? null;
                                        $paidAt = $row->waktu_bayar ?? null;
                                        $payload = [
                                            'kode' => $trxCode($row),
                                            'nama' => $nama,
                                            'email' => $email,
                                            'event' => $eventNama,
                                            'paket' => $paketNama,
                                            'status' => $badgeText,
                                            'metode' => $metodeVal !== '' ? $metodeVal : '-',
                                            'total' => $fmtRp($row->total_bayar ?? 0),
                                            'waktu_pesan' => $fmtDt($createdAt),
                                            'waktu_bayar' => $fmtDt($paidAt),
                                            'raw' => [
                                                'status' => $statusVal,
                                                'metode' => $metodeVal,
                                            ],
                                        ];
                                    @endphp

                                    <tr>
                                        <td class="fw-semibold text-black text-nowrap">{{ $trxCode($row) }}</td>
                                        <td>
                                            <div class="fw-semibold text-black">{{ $nama }}</div>
                                            <div class="text-muted small d-flex flex-wrap align-items-center gap-2">
                                                <span>{{ $email ?: '-' }}</span>
                                                @if ($email)
                                                    @if ($emailOk($email))
                                                        <span class="badge bg-success">valid</span>
                                                    @else
                                                        <span class="badge bg-danger">invalid</span>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-black">{{ $eventNama }}</td>
                                        <td class="text-black">{{ $paketNama }}</td>
                                        <td class="text-nowrap">
                                            <span class="badge {{ $badgeCls }}">{{ $badgeText }}</span>
                                        </td>
                                        <td class="text-muted text-nowrap">
                                            @if ($metodeVal === 'bank_transfer')
                                                Bank Transfer
                                            @elseif ($metodeVal === 'e_wallet')
                                                E-Wallet
                                            @else
                                                {{ $metodeVal ?: '-' }}
                                            @endif
                                        </td>
                                        <td class="text-end fw-semibold text-black text-nowrap">{{ $fmtRp($row->total_bayar ?? 0) }}</td>
                                        <td class="text-muted text-nowrap">{{ $fmtDt($createdAt) }}</td>
                                        <td class="text-muted text-nowrap">{{ $fmtDt($paidAt) }}</td>
                                        <td class="text-end text-nowrap">
                                            <button
                                                type="button"
                                                class="btn btn-outline-primary btn-sm"
                                                data-detail-btn
                                                data-payload='@json($payload)'
                                            >
                                                <i class="la la-eye" aria-hidden="true"></i>
                                                Detail
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-5">
                                            Tidak ada data transaksi. Coba ubah filter atau rentang tanggal.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if (is_object($transaksi) && method_exists($transaksi, 'links'))
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 border-top pt-3 mt-3">
                            <div class="text-muted small">
                                Menampilkan
                                <span class="fw-semibold text-black">{{ $transaksi->firstItem() ?? 0 }}</span>
                                –
                                <span class="fw-semibold text-black">{{ $transaksi->lastItem() ?? 0 }}</span>
                                dari
                                <span class="fw-semibold text-black">{{ $transaksi->total() ?? 0 }}</span>
                            </div>
                            <div class="overflow-auto">
                                {{ $transaksi->onEachSide(1)->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </main>

    <div class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-none align-items-center justify-content-center p-3" id="loadingOverlay" aria-hidden="true" style="z-index: 1055;">
        <div class="card" style="max-width: 360px;">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded bg-light" style="width: 44px; height: 44px;">
                        <div class="spinner-border text-primary" role="status" aria-label="Loading"></div>
                    </div>
                    <div>
                        <div class="fw-semibold text-black">Memproses…</div>
                        <div class="text-muted small">Mohon tunggu sebentar.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-none p-3" id="detailModalBackdrop" aria-hidden="true" style="z-index: 1050;">
        <div class="card shadow-lg mx-auto mt-4" style="max-width: 880px;">
            <div class="card-header bg-white d-flex align-items-start justify-content-between gap-2">
                <div>
                    <div class="fw-semibold text-black">Detail Transaksi</div>
                    <div class="text-muted small" id="detailSubtitle">-</div>
                </div>
                <button type="button" class="btn-close" id="detailCloseBtn" aria-label="Tutup"></button>
            </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <div class="border rounded p-3 h-100">
                            <div class="text-muted small fw-semibold">Kode Pesanan</div>
                            <div class="fw-semibold text-black mt-1" id="dKode">-</div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="border rounded p-3 h-100">
                            <div class="text-muted small fw-semibold">Status</div>
                            <div class="fw-semibold text-black mt-1" id="dStatus">-</div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="border rounded p-3 h-100">
                            <div class="text-muted small fw-semibold">Nama Peserta</div>
                            <div class="fw-semibold text-black mt-1" id="dNama">-</div>
                            <div class="text-muted small mt-1" id="dEmail">-</div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="border rounded p-3 h-100">
                            <div class="text-muted small fw-semibold">Metode Pembayaran</div>
                            <div class="fw-semibold text-black mt-1" id="dMetode">-</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="border rounded p-3 h-100">
                            <div class="text-muted small fw-semibold">Event & Paket</div>
                            <div class="fw-semibold text-black mt-1" id="dEvent">-</div>
                            <div class="text-muted mt-1" id="dPaket">-</div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="border rounded p-3 h-100">
                            <div class="text-muted small fw-semibold">Total Bayar</div>
                            <div class="fs-18 fw-semibold text-black mt-1" id="dTotal">-</div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="border rounded p-3 h-100">
                            <div class="text-muted small fw-semibold">Waktu</div>
                            <div class="text-muted mt-1">
                                <div><span class="text-muted">Pesan:</span> <span class="fw-semibold text-black" id="dWaktuPesan">-</span></div>
                                <div><span class="text-muted">Bayar:</span> <span class="fw-semibold text-black" id="dWaktuBayar">-</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-outline-secondary" id="detailCloseBtn2">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const loadingOverlay = document.getElementById('loadingOverlay');
            const filterForm = document.getElementById('filterForm');
            const btnApply = document.getElementById('btnApply');

            const exportBtn = document.getElementById('exportMenuBtn');
            const exportMenu = document.getElementById('exportMenu');

            function showLoading() {
                if (!loadingOverlay) return;
                loadingOverlay.classList.remove('d-none');
                loadingOverlay.classList.add('d-flex');
                loadingOverlay.setAttribute('aria-hidden', 'false');
            }

            function hideLoading() {
                if (!loadingOverlay) return;
                loadingOverlay.classList.add('d-none');
                loadingOverlay.classList.remove('d-flex');
                loadingOverlay.setAttribute('aria-hidden', 'true');
            }

            if (filterForm) {
                filterForm.addEventListener('submit', () => {
                    if (btnApply) btnApply.disabled = true;
                    showLoading();
                });
            }

            document.querySelectorAll('[data-export-link]').forEach((el) => {
                el.addEventListener('click', (e) => {
                    const href = el.getAttribute('href') || '#';
                    if (href === '#') {
                        e.preventDefault();
                        return;
                    }
                    showLoading();
                    setTimeout(hideLoading, 2000);
                });
            });

            function toggleExportMenu(open) {
                if (!exportMenu || !exportBtn) return;
                const isOpen = open !== undefined ? open : !exportMenu.classList.contains('show');
                exportMenu.classList.toggle('show', isOpen);
                exportBtn.classList.toggle('show', isOpen);
                exportBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            }

            if (exportBtn && exportMenu) {
                exportBtn.addEventListener('click', () => toggleExportMenu());
                document.addEventListener('click', (e) => {
                    if (!exportMenu.contains(e.target) && !exportBtn.contains(e.target)) {
                        toggleExportMenu(false);
                    }
                });
            }

            const filterEvent = document.getElementById('filterEvent');
            const filterPaket = document.getElementById('filterPaket');
            if (filterEvent && filterPaket) {
                const applyPaketFilter = () => {
                    const selected = filterEvent.value;
                    const options = Array.from(filterPaket.querySelectorAll('option'));
                    options.forEach((opt) => {
                        const eventId = opt.getAttribute('data-event-id');
                        if (!eventId) return;
                        opt.hidden = !!selected && eventId !== selected;
                    });
                };

                applyPaketFilter();
                filterEvent.addEventListener('change', applyPaketFilter);
            }

            const backdrop = document.getElementById('detailModalBackdrop');
            const closeBtn = document.getElementById('detailCloseBtn');
            const closeBtn2 = document.getElementById('detailCloseBtn2');

            function setText(id, value) {
                const el = document.getElementById(id);
                if (!el) return;
                el.textContent = value || '-';
            }

            function openDetail(payload) {
                if (!backdrop) return;
                setText('detailSubtitle', payload ? payload.kode : '-');
                setText('dKode', payload ? payload.kode : '-');
                setText('dStatus', payload ? payload.status : '-');
                setText('dNama', payload ? payload.nama : '-');
                setText('dEmail', payload ? payload.email : '-');
                setText('dMetode', payload ? payload.metode : '-');
                setText('dEvent', payload ? payload.event : '-');
                setText('dPaket', payload ? payload.paket : '-');
                setText('dTotal', payload ? payload.total : '-');
                setText('dWaktuPesan', payload ? payload.waktu_pesan : '-');
                setText('dWaktuBayar', payload ? payload.waktu_bayar : '-');

                backdrop.classList.remove('d-none');
                backdrop.setAttribute('aria-hidden', 'false');
            }

            function closeDetail() {
                if (!backdrop) return;
                backdrop.classList.add('d-none');
                backdrop.setAttribute('aria-hidden', 'true');
            }

            if (closeBtn) closeBtn.addEventListener('click', closeDetail);
            if (closeBtn2) closeBtn2.addEventListener('click', closeDetail);
            if (backdrop) {
                backdrop.addEventListener('click', (e) => {
                    if (e.target === backdrop) closeDetail();
                });
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') closeDetail();
                });
            }

            document.querySelectorAll('[data-detail-btn]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const raw = btn.getAttribute('data-payload');
                    if (!raw) return;
                    try {
                        const payload = JSON.parse(raw);
                        openDetail(payload);
                    } catch (e) {}
                });
            });

            hideLoading();
        })();
    </script>
@endsection
