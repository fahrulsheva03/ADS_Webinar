@extends('admin.partials.app')

@section('content')
    @php
        $query = request()->query();

        $exportUrl = function (string $format) use ($query) {
            return route('admin.scan.export', array_merge($query, ['format' => $format]));
        };
    @endphp

    <main id="main-content" tabindex="-1" class="pb-4" role="main" aria-label="Scan QR admin">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Scan QR</li>
            </ol>
        </nav>

        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div class="me-auto">
                <h1 class="h3 mb-1">Scan QR</h1>
                <div class="text-muted">Check-in peserta offline dengan kamera atau input token manual.</div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <div class="btn-group" role="group" aria-label="Ekspor riwayat scan">
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

        <div id="notify-area" class="mb-3" aria-live="polite" aria-atomic="true"></div>

        <section class="mb-4" aria-label="Scanner QR dan kontrol">
            <div class="row g-3">
                <div class="col-12 col-xl-5">
                    <div class="card h-100">
                        <div class="card-header border-0 pb-0">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <h2 class="h5 mb-0">Scanner</h2>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-primary btn-sm" id="btn-scan-start">
                                        Buka kamera
                                        <span class="spinner-border spinner-border-sm d-none" id="scan-start-spinner" aria-hidden="true"></span>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-scan-stop" disabled>Stop</button>
                                </div>
                            </div>
                        </div>

                        <div class="card-body pt-3">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label text-black" for="scan-event">Pilih event</label>
                                    <select id="scan-event" class="form-select">
                                        <option value="">Pilih event…</option>
                                        @foreach ($events as $event)
                                            <option value="{{ $event->id }}" @selected((string) ($eventId ?? '') === (string) $event->id)>{{ $event->judul }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label text-black" for="scan-sesi">Pilih sesi</label>
                                    <select id="scan-sesi" class="form-select">
                                        <option value="">Pilih sesi…</option>
                                        @foreach ($sesi as $row)
                                            <option
                                                value="{{ $row->id }}"
                                                data-event-id="{{ $row->event_id }}"
                                                @selected((string) ($sesiId ?? '') === (string) $row->id)
                                            >
                                                {{ $row->event?->judul ? $row->event->judul . ' — ' : '' }}{{ $row->judul_sesi }}
                                                @if ($row->waktu_mulai) ({{ $row->waktu_mulai->format('Y-m-d H:i') }}) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Pastikan sesi dipilih agar scan langsung memproses check-in.</div>
                                </div>

                                <div class="col-12">
                                    <div
                                        class="ratio ratio-4x3 rounded border bg-light overflow-hidden"
                                        id="scanner-frame"
                                        style="border-width: 2px !important;"
                                        aria-label="Area kamera scanner"
                                    >
                                        <div class="d-flex align-items-center justify-content-center text-muted" id="scanner-placeholder">
                                            Kamera belum aktif. Klik “Buka kamera”.
                                        </div>
                                        <div id="qr-reader" class="w-100 h-100"></div>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-2">
                                        <div class="text-muted small" id="scanner-hint">Gunakan kamera belakang untuk hasil terbaik.</div>
                                        <div class="d-flex flex-wrap gap-2">
                                            <select id="camera-select" class="form-select form-select-sm" style="max-width: 240px;" aria-label="Pilih kamera" disabled>
                                                <option value="">Kamera</option>
                                            </select>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-torch" disabled>Flash</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label text-black" for="scan-token">Token manual</label>
                                    <div class="input-group">
                                        <input
                                            id="scan-token"
                                            type="text"
                                            class="form-control"
                                            placeholder="Tempel token (kode pesanan) di sini"
                                            autocomplete="off"
                                            inputmode="text"
                                        >
                                        <button type="button" class="btn btn-outline-primary" id="btn-submit-token">Submit</button>
                                    </div>
                                    <div class="form-text">Gunakan bila kamera tidak tersedia atau QR sulit terbaca.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-7">
                    <div class="card h-100">
                        <div class="card-header border-0 pb-0">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <h2 class="h5 mb-0">Hasil Scan</h2>
                                <div class="badge bg-light text-dark" id="scan-status">Siap</div>
                            </div>
                        </div>
                        <div class="card-body pt-3">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded">
                                        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                                            <div>
                                                <div class="text-muted small">Peserta</div>
                                                <div class="fw-semibold text-black" id="result-nama">-</div>
                                                <div class="text-muted" id="result-email"></div>
                                            </div>
                                            <div class="text-end">
                                                <div class="text-muted small">Waktu</div>
                                                <div class="fw-semibold text-black" id="result-waktu">-</div>
                                            </div>
                                        </div>
                                        <hr class="my-3">
                                        <div class="row g-2">
                                            <div class="col-12 col-md-6">
                                                <div class="text-muted small">Event</div>
                                                <div class="fw-semibold text-black" id="result-event">-</div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="text-muted small">Sesi</div>
                                                <div class="fw-semibold text-black" id="result-sesi">-</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="accordion" id="scanHelp">
                                        <div class="accordion-item">
                                            <h3 class="accordion-header" id="scanHelpHeading">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#scanHelpBody" aria-expanded="false" aria-controls="scanHelpBody">
                                                    Panduan & Maintenance
                                                </button>
                                            </h3>
                                            <div id="scanHelpBody" class="accordion-collapse collapse" aria-labelledby="scanHelpHeading" data-bs-parent="#scanHelp">
                                                <div class="accordion-body">
                                                    <div class="fw-semibold text-black mb-2">Cara pakai</div>
                                                    <ol class="mb-3">
                                                        <li>Pilih event dan sesi.</li>
                                                        <li>Klik “Buka kamera”. Arahkan QR ke area scan.</li>
                                                        <li>Jika kamera bermasalah, gunakan Token manual.</li>
                                                    </ol>
                                                    <div class="fw-semibold text-black mb-2">Catatan teknis</div>
                                                    <ul class="mb-0">
                                                        <li>Scanner memakai library html5-qrcode via CDN.</li>
                                                        <li>Endpoint check-in: <span class="text-muted">POST /admin/scan/checkin</span> (JSON).</li>
                                                        <li>Token yang digunakan saat ini memetakan ke <span class="text-muted">pesanan.kode_pesanan</span>.</li>
                                                        <li>Riwayat scan diambil dari tabel <span class="text-muted">kehadiran_sesi</span> berdasarkan waktu_join.</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section aria-label="Riwayat scan">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <h2 class="h5 mb-0">Riwayat Scan</h2>
                        <div class="input-group input-group-sm" style="max-width: 340px;">
                            <span class="input-group-text">
                                <i class="flaticon-381-search-2 text-primary" aria-hidden="true"></i>
                            </span>
                            <input id="table-search" type="search" class="form-control" placeholder="Pencarian cepat di tabel…" autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="card-body pt-3">
                    <form method="GET" action="{{ route('admin.scan.index') }}" class="row g-3 align-items-end mb-3" aria-label="Filter riwayat scan">
                        <div class="col-12 col-lg-4">
                            <label class="form-label text-black" for="filter-q">Cari</label>
                            <input id="filter-q" name="q" type="search" class="form-control" placeholder="Nama, email, sesi, event…" value="{{ $q ?? request('q') }}">
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
                        <div class="col-6 col-lg-1">
                            <label class="form-label text-black" for="filter-from">Dari</label>
                            <input id="filter-from" name="from" type="date" class="form-control" value="{{ $from ?? request('from') }}">
                        </div>
                        <div class="col-6 col-lg-1">
                            <label class="form-label text-black" for="filter-to">Sampai</label>
                            <input id="filter-to" name="to" type="date" class="form-control" value="{{ $to ?? request('to') }}">
                        </div>
                        <div class="col-12 col-lg-12 d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary">Terapkan</button>
                            <a href="{{ route('admin.scan.index') }}" class="btn btn-outline-secondary">Reset</a>
                            <button type="button" class="btn btn-outline-primary ms-auto" id="btn-refresh-history">
                                Refresh
                                <span class="spinner-border spinner-border-sm d-none" id="history-spinner" aria-hidden="true"></span>
                            </button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="history-table" aria-label="Tabel riwayat scan">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Peserta</th>
                                    <th scope="col">Event</th>
                                    <th scope="col">Sesi</th>
                                    <th scope="col">Waktu Check-in</th>
                                </tr>
                            </thead>
                            <tbody id="history-tbody">
                                @forelse ($history as $row)
                                    <tr data-search-row>
                                        <td class="fw-semibold text-black">{{ $row->id }}</td>
                                        <td>
                                            <div class="fw-semibold text-black">{{ $row->user?->nama ?? '-' }}</div>
                                            <div class="text-muted small">{{ $row->user?->email ?? '' }}</div>
                                        </td>
                                        <td class="text-muted">{{ $row->sesi?->event?->judul ?? '-' }}</td>
                                        <td class="text-muted">{{ $row->sesi?->judul_sesi ?? '-' }}</td>
                                        <td class="text-muted">{{ optional($row->waktu_join)->format('Y-m-d H:i:s') ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="text-center py-4">
                                                <div class="fw-semibold text-black mb-1">Belum ada data scan</div>
                                                <div class="text-muted">Hasil scan yang berhasil akan muncul di sini.</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-4">
                        <div class="text-muted" id="history-meta">
                            Menampilkan {{ $history->firstItem() ?? 0 }}–{{ $history->lastItem() ?? 0 }} dari {{ $history->total() ?? 0 }}
                        </div>
                        <div>
                            {{ $history->onEachSide(1)->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

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

        <script src="https://unpkg.com/html5-qrcode@2.3.10/html5-qrcode.min.js"></script>
        <script>
            (function () {
                const csrfToken = @json(csrf_token());
                const checkinUrl = @json(route('admin.scan.checkin'));
                const historyUrl = @json(route('admin.scan.history', array_merge(request()->query(), ['page' => 1])));

                const notifyArea = document.getElementById('notify-area');
                const pageLoading = document.getElementById('page-loading');

                const scanEvent = document.getElementById('scan-event');
                const scanSesi = document.getElementById('scan-sesi');
                const scanToken = document.getElementById('scan-token');

                const btnStart = document.getElementById('btn-scan-start');
                const btnStop = document.getElementById('btn-scan-stop');
                const startSpinner = document.getElementById('scan-start-spinner');
                const cameraSelect = document.getElementById('camera-select');
                const btnTorch = document.getElementById('btn-torch');
                const scannerFrame = document.getElementById('scanner-frame');
                const scannerPlaceholder = document.getElementById('scanner-placeholder');

                const resultNama = document.getElementById('result-nama');
                const resultEmail = document.getElementById('result-email');
                const resultEvent = document.getElementById('result-event');
                const resultSesi = document.getElementById('result-sesi');
                const resultWaktu = document.getElementById('result-waktu');
                const scanStatus = document.getElementById('scan-status');

                const btnSubmitToken = document.getElementById('btn-submit-token');

                const historyTbody = document.getElementById('history-tbody');
                const historyMeta = document.getElementById('history-meta');
                const btnRefreshHistory = document.getElementById('btn-refresh-history');
                const historySpinner = document.getElementById('history-spinner');
                const tableSearch = document.getElementById('table-search');
                const historyTable = document.getElementById('history-table');

                let html5Qr = null;
                let scanning = false;
                let torchOn = false;
                let activeCameraId = '';
                let lastDecoded = { text: '', at: 0 };

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

                function setStatus(label, variant) {
                    if (!scanStatus) return;
                    scanStatus.textContent = label;
                    scanStatus.className = `badge ${variant || 'bg-light text-dark'}`;
                }

                function setResult(data) {
                    if (!data) return;
                    resultNama.textContent = data.nama_peserta || '-';
                    resultEmail.textContent = '';
                    resultEvent.textContent = data.event || '-';
                    resultSesi.textContent = data.sesi || '-';
                    resultWaktu.textContent = data.waktu || '-';
                }

                function beepSuccess() {
                    try {
                        const ctx = new (window.AudioContext || window.webkitAudioContext)();
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.type = 'sine';
                        osc.frequency.value = 880;
                        gain.gain.value = 0.07;
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.start();
                        setTimeout(() => {
                            osc.stop();
                            ctx.close();
                        }, 120);
                    } catch (_) {}
                    if (navigator.vibrate) {
                        navigator.vibrate(80);
                    }
                }

                function flashFrame(ok) {
                    if (!scannerFrame) return;
                    const base = 'ratio ratio-4x3 rounded border bg-light overflow-hidden';
                    scannerFrame.className = base + (ok ? ' border-success' : ' border-danger');
                    setTimeout(() => {
                        scannerFrame.className = base;
                    }, 250);
                }

                function filterSesiByEvent(selectEventEl, selectSesiEl) {
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

                async function submitCheckin(token) {
                    const sesiId = scanSesi.value;
                    if (!sesiId) {
                        pushNotice('warning', 'Butuh sesi', 'Pilih sesi terlebih dahulu sebelum scan.');
                        flashFrame(false);
                        return;
                    }
                    if (!token) return;

                    setStatus('Memproses…', 'bg-primary');
                    setPageLoading(true);
                    try {
                        const form = new FormData();
                        form.set('qr_token', token);
                        form.set('event_sesi_id', sesiId);
                        const payload = await requestJson(checkinUrl, { method: 'POST', body: form });

                        if (payload.status !== 'success') {
                            throw new Error(payload.message || 'Check-in gagal.');
                        }

                        setResult(payload.data);
                        setStatus('Berhasil', 'bg-success');
                        beepSuccess();
                        flashFrame(true);
                        pushNotice('success', 'Sukses', payload.message || 'Check-in berhasil.');
                        await refreshHistory();
                    } catch (e) {
                        setStatus('Gagal', 'bg-danger');
                        flashFrame(false);
                        pushNotice('danger', 'Gagal', e.message || 'Check-in gagal.');
                    } finally {
                        setPageLoading(false);
                        setTimeout(() => setStatus('Siap', 'bg-light text-dark'), 800);
                    }
                }

                async function refreshHistory() {
                    if (!btnRefreshHistory) return;
                    btnRefreshHistory.disabled = true;
                    historySpinner.classList.remove('d-none');
                    try {
                        const res = await fetch(historyUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                        const json = res.ok ? await res.json() : null;
                        if (!json || !Array.isArray(json.data)) return;

                        historyTbody.innerHTML = '';
                        json.data.forEach((row) => {
                            const tr = document.createElement('tr');
                            tr.setAttribute('data-search-row', '');
                            tr.innerHTML = `
                                <td class="fw-semibold text-black">${escapeHtml(row.id)}</td>
                                <td>
                                    <div class="fw-semibold text-black">${escapeHtml(row.user_nama || '-')}</div>
                                    <div class="text-muted small">${escapeHtml(row.user_email || '')}</div>
                                </td>
                                <td class="text-muted">${escapeHtml(row.event_judul || '-')}</td>
                                <td class="text-muted">${escapeHtml(row.sesi_judul || '-')}</td>
                                <td class="text-muted">${escapeHtml(row.waktu_join || '-')}</td>
                            `;
                            historyTbody.appendChild(tr);
                        });

                        if (historyMeta && json.meta) {
                            const from = json.meta.from || 0;
                            const to = json.meta.to || 0;
                            const total = json.meta.total || 0;
                            historyMeta.textContent = `Menampilkan ${from}–${to} dari ${total}`;
                        }
                    } catch (_) {
                    } finally {
                        btnRefreshHistory.disabled = false;
                        historySpinner.classList.add('d-none');
                    }
                }

                function wireTableQuickSearch() {
                    if (!tableSearch || !historyTable) return;
                    tableSearch.addEventListener('input', () => {
                        const q = (tableSearch.value || '').toLowerCase().trim();
                        const rows = Array.from(historyTable.querySelectorAll('tbody tr[data-search-row]'));
                        rows.forEach((row) => {
                            const hay = (row.innerText || '').toLowerCase();
                            row.style.display = !q || hay.includes(q) ? '' : 'none';
                        });
                    });
                }

                async function loadCameras() {
                    if (!window.Html5Qrcode) return [];
                    try {
                        const devices = await Html5Qrcode.getCameras();
                        return Array.isArray(devices) ? devices : [];
                    } catch (_) {
                        return [];
                    }
                }

                function setStartBusy(busy) {
                    btnStart.disabled = !!busy;
                    if (startSpinner) startSpinner.classList.toggle('d-none', !busy);
                }

                async function startScanner(cameraId) {
                    if (!window.Html5Qrcode) {
                        pushNotice('danger', 'Scanner tidak tersedia', 'Gagal memuat library scanner.');
                        return;
                    }
                    if (scanning) return;

                    const targetId = 'qr-reader';
                    if (!html5Qr) {
                        html5Qr = new Html5Qrcode(targetId, { verbose: false });
                    }

                    setStartBusy(true);
                    setStatus('Menyalakan kamera…', 'bg-primary');
                    try {
                        if (scannerPlaceholder) scannerPlaceholder.classList.add('d-none');
                        btnStop.disabled = false;
                        cameraSelect.disabled = false;

                        const config = {
                            fps: 12,
                            qrbox: { width: 260, height: 260 },
                            aspectRatio: 1.333,
                            disableFlip: true,
                        };

                        const cameraConfig = cameraId
                            ? { deviceId: { exact: cameraId } }
                            : { facingMode: { ideal: 'environment' } };

                        scanning = true;
                        await html5Qr.start(cameraConfig, config, async (decodedText) => {
                            const now = Date.now();
                            if (decodedText === lastDecoded.text && now - lastDecoded.at < 1500) return;
                            lastDecoded = { text: decodedText, at: now };
                            await submitCheckin(decodedText);
                        });

                        setStatus('Scanning…', 'bg-success');
                    } catch (e) {
                        scanning = false;
                        btnStop.disabled = true;
                        setStatus('Gagal memulai', 'bg-danger');
                        pushNotice('danger', 'Kamera gagal', e.message || 'Tidak bisa mengakses kamera.');
                        if (scannerPlaceholder) scannerPlaceholder.classList.remove('d-none');
                    } finally {
                        setStartBusy(false);
                        setTimeout(() => setStatus('Siap', 'bg-light text-dark'), 1000);
                    }
                }

                async function stopScanner() {
                    if (!html5Qr || !scanning) return;
                    setStatus('Menghentikan…', 'bg-warning text-dark');
                    try {
                        if (torchOn) {
                            await setTorch(false);
                        }
                        await html5Qr.stop();
                        await html5Qr.clear();
                    } catch (_) {
                    } finally {
                        scanning = false;
                        btnStop.disabled = true;
                        btnTorch.disabled = true;
                        if (scannerPlaceholder) scannerPlaceholder.classList.remove('d-none');
                        setStatus('Siap', 'bg-light text-dark');
                        if (scanToken) scanToken.focus();
                    }
                }

                async function setTorch(on) {
                    if (!html5Qr || !scanning) return false;
                    try {
                        await html5Qr.applyVideoConstraints({ advanced: [{ torch: !!on }] });
                        torchOn = !!on;
                        btnTorch.classList.toggle('btn-outline-secondary', !torchOn);
                        btnTorch.classList.toggle('btn-warning', torchOn);
                        btnTorch.textContent = torchOn ? 'Flash ON' : 'Flash';
                        return true;
                    } catch (_) {
                        return false;
                    }
                }

                async function init() {
                    if (scanEvent && scanSesi) {
                        filterSesiByEvent(scanEvent, scanSesi);
                        scanEvent.addEventListener('change', () => filterSesiByEvent(scanEvent, scanSesi));
                    }

                    const filterEvent = document.getElementById('filter-event');
                    const filterSesi = document.getElementById('filter-sesi');
                    if (filterEvent && filterSesi) {
                        filterSesiByEvent(filterEvent, filterSesi);
                        filterEvent.addEventListener('change', () => filterSesiByEvent(filterEvent, filterSesi));
                    }

                    wireTableQuickSearch();

                    if (btnRefreshHistory) {
                        btnRefreshHistory.addEventListener('click', refreshHistory);
                    }

                    if (btnSubmitToken) {
                        btnSubmitToken.addEventListener('click', async () => {
                            const token = (scanToken.value || '').trim();
                            await submitCheckin(token);
                        });
                    }

                    if (scanToken) {
                        scanToken.addEventListener('keydown', async (e) => {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                const token = (scanToken.value || '').trim();
                                await submitCheckin(token);
                            }
                        });
                        scanToken.focus();
                    }

                    if (btnStart) {
                        btnStart.addEventListener('click', async () => {
                            const cams = await loadCameras();
                            cameraSelect.innerHTML = '<option value="">Kamera</option>';
                            cams.forEach((d) => {
                                const opt = document.createElement('option');
                                opt.value = d.id;
                                opt.textContent = d.label || `Camera ${d.id}`;
                                cameraSelect.appendChild(opt);
                            });

                            const preferred = cams.find((d) => (d.label || '').toLowerCase().includes('back')) || cams[0];
                            activeCameraId = preferred ? preferred.id : '';
                            if (activeCameraId) {
                                cameraSelect.value = activeCameraId;
                            }

                            await startScanner(activeCameraId);
                            btnTorch.disabled = !scanning;
                        });
                    }

                    if (cameraSelect) {
                        cameraSelect.addEventListener('change', async () => {
                            const id = cameraSelect.value;
                            activeCameraId = id;
                            if (!scanning) return;
                            await stopScanner();
                            await startScanner(activeCameraId);
                            btnTorch.disabled = !scanning;
                        });
                    }

                    if (btnStop) {
                        btnStop.addEventListener('click', stopScanner);
                    }

                    if (btnTorch) {
                        btnTorch.addEventListener('click', async () => {
                            const ok = await setTorch(!torchOn);
                            if (!ok) {
                                pushNotice('warning', 'Flash tidak tersedia', 'Perangkat/browser tidak mendukung torch.');
                            }
                        });
                    }

                    window.addEventListener('beforeunload', () => {
                        if (html5Qr && scanning) {
                            html5Qr.stop().catch(() => {});
                        }
                    });
                }

                init();
            })();
        </script>
    </main>
@endsection
