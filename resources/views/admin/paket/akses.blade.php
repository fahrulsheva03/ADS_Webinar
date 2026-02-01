@extends(request()->boolean('embed') ? 'admin.partials.embed' : 'admin.partials.app')

@section('content')
    @php
        $statusBadge = function (?string $status) {
            $status = strtolower((string) $status);
            return match ($status) {
                'aktif' => ['bg-success', 'Aktif'],
                'nonaktif' => ['bg-secondary', 'Nonaktif'],
                default => ['bg-light text-dark', $status ?: '-'],
            };
        };

        $sesiBadge = function (?string $status) {
            $status = strtolower((string) $status);
            return match ($status) {
                'live' => ['bg-success', 'Live'],
                'upcoming' => ['bg-warning text-dark', 'Upcoming'],
                'selesai' => ['bg-secondary', 'Selesai'],
                default => ['bg-light text-dark', $status ?: '-'],
            };
        };

        $sesiData = $sesi->map(function ($s) {
            return [
                'id' => $s->id,
                'event_id' => $s->event_id,
                'judul_sesi' => $s->judul_sesi,
                'waktu_mulai' => optional($s->waktu_mulai)->format('Y-m-d H:i'),
                'waktu_selesai' => optional($s->waktu_selesai)->format('Y-m-d H:i'),
                'status_sesi' => $s->status_sesi,
                'event_judul' => $s->event ? $s->event->judul : null,
            ];
        });
    @endphp

    <main id="main-content" tabindex="-1" class="pb-4" role="main" aria-label="Assign paket dan sesi">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div class="me-auto">
                <h1 class="h3 mb-1">Assign Paket ↔ Sesi</h1>
                <div class="text-muted">Pilih paket, lalu drag & drop sesi untuk mengatur akses.</div>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.paket.index') }}">Kembali</a>
            </div>
        </div>

        <div id="akses-alert" class="d-none" role="alert"></div>

        <section aria-label="Panel assign paket dan sesi">
            <div class="row">
                <div class="col-12 col-lg-4 mb-3">
                    <div class="card h-100">
                        <div class="card-header border-0 pb-0">
                            <h2 class="h6 mb-0">Paket</h2>
                        </div>
                        <div class="card-body pt-3">
                            <div class="mb-3">
                                <label class="form-label text-black" for="paket-search">Cari paket</label>
                                <input id="paket-search" type="search" class="form-control" placeholder="Cari nama paket…" autocomplete="off">
                            </div>
                            <div class="list-group" id="paket-list" role="listbox" aria-label="Daftar paket">
                                @forelse ($paket as $p)
                                    @php([$badgeClass, $badgeLabel] = $statusBadge($p->status))
                                    <button
                                        type="button"
                                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-start gap-2"
                                        data-paket-id="{{ $p->id }}"
                                        data-search="{{ strtolower($p->nama_paket) }}"
                                    >
                                        <div class="text-start">
                                            <div class="fw-semibold text-black">{{ $p->nama_paket }}</div>
                                            <div class="small text-muted">ID: {{ $p->id }}</div>
                                        </div>
                                        <span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                                    </button>
                                @empty
                                    <div class="text-muted">Belum ada paket.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-8 mb-3">
                    <div class="card h-100 position-relative" id="sesi-panel">
                        <div class="card-header border-0 pb-0">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h2 class="h6 mb-0">Sesi</h2>
                                    <div class="text-muted" id="selected-paket-label">Pilih paket untuk mulai.</div>
                                </div>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="reset-assignment" disabled>Reset</button>
                                    <button type="button" class="btn btn-primary btn-sm" id="save-assignment" disabled>
                                        <span class="me-1">Assign</span>
                                        <span class="spinner-border spinner-border-sm d-none" id="save-spinner" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-3">
                            <div class="mb-3">
                                <label class="form-label text-black" for="sesi-search">Cari sesi</label>
                                <input id="sesi-search" type="search" class="form-control" placeholder="Cari judul sesi atau event…" autocomplete="off" disabled>
                            </div>

                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <div class="border rounded p-2 h-100">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="fw-semibold text-black">Sesi tersedia</div>
                                            <span class="badge bg-light text-dark" id="available-count">0</span>
                                        </div>
                                        <div
                                            class="list-group min-vh-25"
                                            id="available-list"
                                            role="list"
                                            aria-label="Sesi tersedia"
                                            data-drop-zone="available"
                                            style="min-height: 240px;"
                                        ></div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="border rounded p-2 h-100">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="fw-semibold text-black">Sesi ter-assign</div>
                                            <span class="badge bg-light text-dark" id="assigned-count">0</span>
                                        </div>
                                        <div
                                            class="list-group min-vh-25"
                                            id="assigned-list"
                                            role="list"
                                            aria-label="Sesi ter-assign"
                                            data-drop-zone="assigned"
                                            style="min-height: 240px;"
                                        ></div>
                                    </div>
                                </div>
                            </div>

                            <template id="sesi-item-template">
                                <div class="list-group-item d-flex justify-content-between align-items-start gap-2" draggable="true">
                                    <div class="me-auto">
                                        <div class="fw-semibold text-black sesi-title"></div>
                                        <div class="small text-muted sesi-meta"></div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge sesi-badge"></span>
                                        <button type="button" class="btn btn-outline-danger btn-xxs sesi-remove d-none" title="Unassign">
                                            <i class="la la-times" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div
                            class="position-absolute top-0 start-0 w-100 h-100 d-none align-items-center justify-content-center"
                            id="panel-loading"
                            style="background: rgba(255, 255, 255, 0.75); z-index: 10;"
                        >
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status" aria-label="Loading"></div>
                                <div class="mt-2 text-muted">Memproses…</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script>
            (function () {
                const csrfToken = @json(csrf_token());
                const sesiData = @json($sesiData);

                const badgeMap = {
                    live: { className: 'bg-success', label: 'Live' },
                    upcoming: { className: 'bg-warning text-dark', label: 'Upcoming' },
                    selesai: { className: 'bg-secondary', label: 'Selesai' },
                };

                const paketList = document.getElementById('paket-list');
                const paketSearch = document.getElementById('paket-search');
                const selectedPaketLabel = document.getElementById('selected-paket-label');

                const sesiSearch = document.getElementById('sesi-search');
                const availableList = document.getElementById('available-list');
                const assignedList = document.getElementById('assigned-list');
                const availableCount = document.getElementById('available-count');
                const assignedCount = document.getElementById('assigned-count');
                const itemTemplate = document.getElementById('sesi-item-template');

                const saveBtn = document.getElementById('save-assignment');
                const resetBtn = document.getElementById('reset-assignment');
                const saveSpinner = document.getElementById('save-spinner');
                const panelLoading = document.getElementById('panel-loading');

                const alertBox = document.getElementById('akses-alert');

                let selectedPaketId = null;
                let assignedIds = new Set();
                let initialAssignedIds = new Set();

                function showAlert(type, title, message) {
                    alertBox.className = `alert alert-${type} alert-dismissible fade show`;
                    alertBox.innerHTML = `
                        <div class="fw-semibold">${title}</div>
                        <div>${message}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                    `;
                    alertBox.classList.remove('d-none');
                }

                function setPanelLoading(isLoading) {
                    if (!panelLoading) return;
                    panelLoading.classList.toggle('d-none', !isLoading);
                    panelLoading.classList.toggle('d-flex', isLoading);
                }

                function updateCounts() {
                    availableCount.textContent = String(availableList.querySelectorAll('[data-sesi-id]').length);
                    assignedCount.textContent = String(assignedList.querySelectorAll('[data-sesi-id]').length);
                }

                function clearLists() {
                    availableList.innerHTML = '';
                    assignedList.innerHTML = '';
                }

                function formatMeta(sesi) {
                    const eventTitle = sesi.event_judul ? `Event: ${sesi.event_judul}` : 'Event: -';
                    const time = sesi.waktu_mulai ? `${sesi.waktu_mulai} → ${sesi.waktu_selesai || '-'}` : '';
                    return `${eventTitle}${time ? ' • ' + time : ''}`;
                }

                function createItem(sesi, isAssigned) {
                    const node = itemTemplate.content.firstElementChild.cloneNode(true);
                    node.dataset.sesiId = String(sesi.id);
                    node.dataset.search = `${(sesi.judul_sesi || '').toLowerCase()} ${(sesi.event_judul || '').toLowerCase()}`.trim();
                    node.querySelector('.sesi-title').textContent = sesi.judul_sesi;
                    node.querySelector('.sesi-meta').textContent = formatMeta(sesi);

                    const badgeInfo = badgeMap[sesi.status_sesi] || { className: 'bg-light text-dark', label: sesi.status_sesi || '-' };
                    const badgeEl = node.querySelector('.sesi-badge');
                    badgeEl.className = `badge sesi-badge ${badgeInfo.className}`;
                    badgeEl.textContent = badgeInfo.label;

                    const removeBtn = node.querySelector('.sesi-remove');
                    removeBtn.addEventListener('click', () => {
                        assignedIds.delete(sesi.id);
                        availableList.appendChild(node);
                        removeBtn.classList.add('d-none');
                        updateCounts();
                    });
                    if (isAssigned) {
                        removeBtn.classList.remove('d-none');
                    }

                    node.addEventListener('dragstart', (e) => {
                        e.dataTransfer.setData('text/plain', String(sesi.id));
                        e.dataTransfer.effectAllowed = 'move';
                    });

                    return node;
                }

                function renderAllSesi() {
                    clearLists();
                    const byId = new Map(sesiData.map((s) => [s.id, s]));

                    sesiData.forEach((sesi) => {
                        const isAssigned = assignedIds.has(sesi.id);
                        const item = createItem(sesi, isAssigned);
                        (isAssigned ? assignedList : availableList).appendChild(item);
                    });
                    updateCounts();

                    return byId;
                }

                function enableSessionUI(enabled) {
                    sesiSearch.disabled = !enabled;
                    saveBtn.disabled = !enabled;
                    resetBtn.disabled = !enabled;
                }

                function filterSesi() {
                    const q = (sesiSearch.value || '').toLowerCase().trim();
                    const items = Array.from(document.querySelectorAll('[data-sesi-id]'));
                    items.forEach((item) => {
                        const hay = item.dataset.search || '';
                        item.style.display = !q || hay.includes(q) ? '' : 'none';
                    });
                }

                function attachDropZones() {
                    const zones = [availableList, assignedList];
                    zones.forEach((zone) => {
                        zone.addEventListener('dragover', (e) => {
                            e.preventDefault();
                            e.dataTransfer.dropEffect = 'move';
                        });
                        zone.addEventListener('drop', (e) => {
                            e.preventDefault();
                            const sesiId = Number(e.dataTransfer.getData('text/plain'));
                            if (!sesiId) return;
                            const item = document.querySelector(`[data-sesi-id="${sesiId}"]`);
                            if (!item) return;

                            const toAssigned = zone === assignedList;
                            if (toAssigned) {
                                assignedIds.add(sesiId);
                                const removeBtn = item.querySelector('.sesi-remove');
                                if (removeBtn) removeBtn.classList.remove('d-none');
                                assignedList.appendChild(item);
                            } else {
                                assignedIds.delete(sesiId);
                                const removeBtn = item.querySelector('.sesi-remove');
                                if (removeBtn) removeBtn.classList.add('d-none');
                                availableList.appendChild(item);
                            }
                            updateCounts();
                        });
                    });
                }

                async function loadAssignments(paketId, paketLabel) {
                    selectedPaketId = paketId;
                    selectedPaketLabel.textContent = `Paket terpilih: ${paketLabel}`;
                    enableSessionUI(true);
                    sesiSearch.value = '';
                    filterSesi();

                    setPanelLoading(true);
                    try {
                        const url = @json(route('admin.paket.sesi.assigned', ['paket' => '__PAKET__'])).replace('__PAKET__', String(paketId));
                        const res = await fetch(url, {
                            headers: {
                                'Accept': 'application/json',
                            },
                        });
                        if (!res.ok) {
                            throw new Error('Gagal memuat relasi.');
                        }
                        const payload = await res.json();
                        assignedIds = new Set((payload.assigned_ids || []).map((id) => Number(id)));
                        initialAssignedIds = new Set(Array.from(assignedIds));
                        renderAllSesi();
                    } catch (e) {
                        showAlert('danger', 'Gagal', e.message || 'Terjadi kesalahan saat memuat relasi.');
                    } finally {
                        setPanelLoading(false);
                    }
                }

                async function saveAssignments() {
                    if (!selectedPaketId) return;
                    saveBtn.disabled = true;
                    resetBtn.disabled = true;
                    saveSpinner.classList.remove('d-none');

                    try {
                        const url = @json(route('admin.paket.sesi.sync', ['paket' => '__PAKET__'])).replace('__PAKET__', String(selectedPaketId));
                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({
                                sesi_ids: Array.from(assignedIds),
                            }),
                        });
                        if (!res.ok) {
                            const text = await res.text();
                            throw new Error(text || 'Gagal menyimpan relasi.');
                        }
                        const payload = await res.json();
                        initialAssignedIds = new Set(Array.from(assignedIds));
                        showAlert('success', 'Sukses', payload.message || 'Relasi berhasil disimpan.');
                    } catch (e) {
                        showAlert('danger', 'Gagal', e.message || 'Terjadi kesalahan saat menyimpan relasi.');
                    } finally {
                        saveSpinner.classList.add('d-none');
                        saveBtn.disabled = false;
                        resetBtn.disabled = false;
                    }
                }

                function resetAssignments() {
                    assignedIds = new Set(Array.from(initialAssignedIds));
                    renderAllSesi();
                    sesiSearch.value = '';
                    filterSesi();
                }

                if (paketSearch) {
                    paketSearch.addEventListener('input', () => {
                        const q = (paketSearch.value || '').toLowerCase().trim();
                        const items = Array.from(paketList.querySelectorAll('[data-paket-id]'));
                        items.forEach((item) => {
                            const hay = item.dataset.search || '';
                            item.style.display = !q || hay.includes(q) ? '' : 'none';
                        });
                    });
                }

                if (paketList) {
                    paketList.addEventListener('click', (e) => {
                        const btn = e.target.closest('[data-paket-id]');
                        if (!btn) return;
                        const paketId = Number(btn.dataset.paketId);
                        const paketLabel = btn.querySelector('.fw-semibold')?.textContent || `#${paketId}`;
                        Array.from(paketList.querySelectorAll('.active')).forEach((el) => el.classList.remove('active'));
                        btn.classList.add('active');
                        loadAssignments(paketId, paketLabel);
                    });
                }

                if (sesiSearch) {
                    sesiSearch.addEventListener('input', filterSesi);
                }

                if (saveBtn) {
                    saveBtn.addEventListener('click', saveAssignments);
                }

                if (resetBtn) {
                    resetBtn.addEventListener('click', resetAssignments);
                }

                attachDropZones();
                assignedIds = new Set();
                renderAllSesi();
                enableSessionUI(false);
                updateCounts();
            })();
        </script>
    </main>
@endsection
