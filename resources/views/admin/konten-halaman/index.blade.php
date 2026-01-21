@extends('admin.partials.app')

@section('content')
    <main
        id="main-content"
        tabindex="-1"
        class="pb-4 p-3 p-lg-4 rounded-3"
        style="background-color: #f5f6f8;"
        role="main"
        aria-label="Konten halaman {{ $page ?? '' }}"
    >
        <style>
            #main-content .form-control,
            #main-content .form-select {
                background-color: #d7dbe0;
                border-color: #c6cbd2;
            }

            #main-content .form-control:focus,
            #main-content .form-select:focus {
                background-color: #e0e4e8;
                border-color: #aab1bb;
            }

            #main-content .bg-light {
                background-color: #d7dbe0 !important;
            }
        </style>
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Konten Halaman</li>
            </ol>
        </nav>

        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div class="me-auto">
                <h1 class="h3 mb-1">Konten Halaman</h1>
                <div class="text-muted">Kelola konten dinamis untuk halaman peserta ({{ $page ?? '-' }}).</div>
            </div>
        </div>

        <div id="notify-area"></div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="fw-semibold">Sukses</div>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <div class="fw-semibold">Validasi gagal</div>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $sections = ($fieldsBySection ?? collect())->keys()->values();
        @endphp

        <form method="POST" action="{{ route('admin.konten-halaman.home.update') }}" id="content-form">
            @csrf

            <div class="card mb-4">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3">
                        <div class="me-auto">
                            <div class="fw-semibold text-black">Editor Konten</div>
                            <div class="text-muted small">Pilih tab Banner/Journey, lalu ubah field yang diperlukan.</div>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <label class="visually-hidden" for="field-search">Cari</label>
                            <input
                                id="field-search"
                                type="search"
                                class="form-control"
                                style="width: min(360px, 100%);"
                                placeholder="Cari label atau key…"
                                autocomplete="off"
                            >
                        </div>
                    </div>
                </div>

                <div class="card-body pt-3">
                    <ul class="nav nav-tabs" id="sectionTabs" role="tablist">
                        @foreach ($sections as $i => $section)
                            @php
                                $label = str_replace('_', ' ', $section);
                                $count = count($fieldsBySection[$section] ?? []);
                            @endphp
                            <li class="nav-item" role="presentation">
                                <button
                                    class="nav-link @if ($i === 0) active @endif"
                                    id="tab-{{ $section }}"
                                    type="button"
                                    role="tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#pane-{{ $section }}"
                                    aria-controls="pane-{{ $section }}"
                                    aria-selected="{{ $i === 0 ? 'true' : 'false' }}"
                                    data-tab-btn
                                    data-section="{{ $section }}"
                                >
                                    <span class="text-capitalize">{{ $label }}</span>
                                    <span class="badge bg-light text-dark ms-2" data-tab-badge data-initial="{{ $count }}">{{ $count }}</span>
                                </button>
                            </li>
                        @endforeach
                    </ul>

                    <div class="tab-content pt-3" id="sectionTabsContent">
                        @foreach ($sections as $i => $section)
                            @php
                                $fields = $fieldsBySection[$section] ?? collect();
                            @endphp
                            <div
                                class="tab-pane fade @if ($i === 0) show active @endif"
                                id="pane-{{ $section }}"
                                role="tabpanel"
                                aria-labelledby="tab-{{ $section }}"
                                tabindex="0"
                                data-tab-pane
                                data-tab-section="{{ $section }}"
                            >
                                <div class="row g-3">
                                    @foreach ($fields as $field)
                                        @php
                                            $key = $field['key'];
                                            $name = "contents[{$section}][{$key}]";
                                            $oldKey = "contents.{$section}.{$key}";
                                            $val = old($oldKey, $values[$section][$key] ?? '');
                                            $type = $field['type'] ?? 'textarea';
                                            $rows = (int) ($field['rows'] ?? 4);
                                            $placeholder = $field['placeholder'] ?? null;
                                            $help = $field['help'] ?? null;

                                            $rawVal = (string) $val;
                                            $previewUrl = '';
                                            if ($rawVal !== '') {
                                                $previewUrl = preg_match('/^https?:\\/\\//i', $rawVal) ? $rawVal : asset(ltrim($rawVal, '/'));
                                            }

                                            $colClass = match ($type) {
                                                'textarea' => 'col-12',
                                                'image' => 'col-12 col-xxl-6',
                                                'number' => 'col-12 col-md-6 col-xxl-4',
                                                default => 'col-12 col-md-6',
                                            };
                                        @endphp
                                        <div
                                            class="{{ $colClass }}"
                                            data-field-item
                                            data-hay="{{ strtolower($field['label'] . ' ' . $section . ' ' . $key) }}"
                                        >
                                            @if ($type === 'image')
                                                <div
                                                    class="border rounded p-3 h-100"
                                                    data-image-field
                                                    data-page="{{ $page ?? 'home' }}"
                                                    data-section="{{ $section }}"
                                                    data-key="{{ $key }}"
                                                >
                                                    <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-2">
                                                        <div>
                                                            <div class="form-label text-black mb-0">{{ $field['label'] }}</div>
                                                            @if ($help)
                                                                <div class="text-muted small">{{ $help }}</div>
                                                            @endif
                                                        </div>
                                                        <button type="button" class="btn btn-outline-danger btn-xxs" data-image-clear>
                                                            <i class="la la-trash" aria-hidden="true"></i>
                                                            Hapus
                                                        </button>
                                                    </div>

                                                    <input type="hidden" name="{{ $name }}" value="{{ $rawVal }}" data-image-value>

                                                    <div class="row g-3">
                                                        <div class="col-12 col-md-7">
                                                            <div
                                                                class="border rounded p-3 text-center bg-light h-100 d-flex flex-column justify-content-center"
                                                                role="button"
                                                                tabindex="0"
                                                                data-image-dropzone
                                                            >
                                                                <div class="fw-semibold text-black">Upload gambar</div>
                                                                <div class="text-muted small">Klik atau drag & drop</div>
                                                                <div class="mt-2 small text-muted" data-image-status></div>
                                                            </div>
                                                            <input type="file" class="d-none" accept="image/jpeg,image/png" data-image-file>
                                                            <div class="progress mt-3 d-none" style="height: 8px;" data-image-progress>
                                                                <div
                                                                    class="progress-bar"
                                                                    role="progressbar"
                                                                    style="width: 0%"
                                                                    aria-valuemin="0"
                                                                    aria-valuemax="100"
                                                                    aria-valuenow="0"
                                                                    data-image-progress-bar
                                                                ></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-5">
                                                            <div class="ratio ratio-16x9 bg-light border rounded overflow-hidden">
                                                                <img
                                                                    src="{{ $previewUrl }}"
                                                                    alt="Preview {{ $field['label'] }}"
                                                                    class="w-100 h-100 object-fit-cover @if (!$previewUrl) d-none @endif"
                                                                    data-image-preview
                                                                >
                                                                <div
                                                                    class="d-flex align-items-center justify-content-center text-muted small @if ($previewUrl) d-none @endif"
                                                                    data-image-empty
                                                                >
                                                                    Belum ada gambar
                                                                </div>
                                                            </div>
                                                            <div class="d-flex flex-wrap gap-2 mt-2">
                                                                <a
                                                                    class="btn btn-outline-secondary btn-xxs @if (!$previewUrl) disabled @endif"
                                                                    href="{{ $previewUrl ?: '#' }}"
                                                                    target="_blank"
                                                                    rel="noopener"
                                                                    data-image-open
                                                                >
                                                                    <i class="la la-external-link" aria-hidden="true"></i>
                                                                    Buka
                                                                </a>
                                                                <button type="button" class="btn btn-outline-secondary btn-xxs" data-image-copy>
                                                                    <i class="la la-copy" aria-hidden="true"></i>
                                                                    Copy path
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label text-black" for="{{ $section }}-{{ $key }}-path">Path</label>
                                                            <input
                                                                id="{{ $section }}-{{ $key }}-path"
                                                                type="text"
                                                                class="form-control"
                                                                value="{{ $rawVal }}"
                                                                placeholder="Contoh: assetsAdmin/uploads/konten-halaman/…"
                                                                data-image-path
                                                            >
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <label class="form-label text-black" for="{{ $section }}-{{ $key }}">
                                                    {{ $field['label'] }}
                                                </label>
                                                @if ($type === 'textarea')
                                                    <textarea
                                                        id="{{ $section }}-{{ $key }}"
                                                        name="{{ $name }}"
                                                        class="form-control @error($oldKey) is-invalid @enderror"
                                                        rows="{{ $rows }}"
                                                        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
                                                    >{{ $val }}</textarea>
                                                @else
                                                    <input
                                                        id="{{ $section }}-{{ $key }}"
                                                        name="{{ $name }}"
                                                        type="{{ $type === 'url' ? 'url' : ($type === 'number' ? 'number' : 'text') }}"
                                                        class="form-control @error($oldKey) is-invalid @enderror"
                                                        value="{{ $rawVal }}"
                                                        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
                                                        @if ($type === 'number') inputmode="numeric" @endif
                                                    >
                                                @endif

                                                @if ($help)
                                                    <div class="text-muted small">{{ $help }}</div>
                                                @endif

                                                @error($oldKey)
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                <div class="alert alert-light border d-none mt-3 mb-0" data-empty-state>
                                    Tidak ada field yang cocok dengan pencarian.
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="text-muted small">Perubahan tersimpan ke database saat menekan Simpan.</div>
                    <button type="submit" class="btn btn-primary" id="save-btn">
                        <span
                            class="spinner-border spinner-border-sm d-none me-2"
                            role="status"
                            aria-hidden="true"
                            id="save-spinner"
                        ></span>
                        Simpan
                    </button>
                </div>
            </div>
        </form>

        <script>
            (function () {
                const csrfToken = @json(csrf_token());
                const page = @json($page ?? 'home');
                const uploadUrl = @json(route('admin.konten-halaman.upload-image'));

                const form = document.getElementById('content-form');
                const saveBtn = document.getElementById('save-btn');
                const saveSpinner = document.getElementById('save-spinner');
                const notifyArea = document.getElementById('notify-area');
                const fieldSearch = document.getElementById('field-search');

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

                function setSaving(on) {
                    if (saveBtn) saveBtn.disabled = !!on;
                    if (saveSpinner) saveSpinner.classList.toggle('d-none', !on);
                }

                if (form) {
                    form.addEventListener('submit', () => setSaving(true));
                }

                function isImageFile(file) {
                    const okType = ['image/jpeg', 'image/png'].includes(file.type);
                    const okSize = file.size <= 2 * 1024 * 1024;
                    if (!okType) return { ok: false, message: 'Format harus JPEG atau PNG.' };
                    if (!okSize) return { ok: false, message: 'Ukuran maksimal 2MB.' };
                    return { ok: true, message: '' };
                }

                function buildAssetUrl(path) {
                    const raw = String(path || '').trim();
                    if (!raw) return '';
                    if (/^https?:\/\//i.test(raw)) return raw;
                    return @json(url('/')) + '/' + raw.replace(/^\/+/, '');
                }

                function copyText(text) {
                    const raw = String(text || '');
                    if (!raw) return;
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(raw).catch(() => {});
                        return;
                    }
                    const area = document.createElement('textarea');
                    area.value = raw;
                    area.setAttribute('readonly', 'readonly');
                    area.style.position = 'fixed';
                    area.style.left = '-9999px';
                    document.body.appendChild(area);
                    area.select();
                    try { document.execCommand('copy'); } catch (_) {}
                    area.remove();
                }

                function wireImageField(wrapper) {
                    const section = wrapper.dataset.section || '';
                    const key = wrapper.dataset.key || '';

                    const valueEl = wrapper.querySelector('[data-image-value]');
                    const dropzone = wrapper.querySelector('[data-image-dropzone]');
                    const fileInput = wrapper.querySelector('[data-image-file]');
                    const statusEl = wrapper.querySelector('[data-image-status]');
                    const progressWrap = wrapper.querySelector('[data-image-progress]');
                    const progressBar = wrapper.querySelector('[data-image-progress-bar]');
                    const preview = wrapper.querySelector('[data-image-preview]');
                    const empty = wrapper.querySelector('[data-image-empty]');
                    const openBtn = wrapper.querySelector('[data-image-open]');
                    const pathInput = wrapper.querySelector('[data-image-path]');
                    const clearBtn = wrapper.querySelector('[data-image-clear]');
                    const copyBtn = wrapper.querySelector('[data-image-copy]');

                    function setPreview(url) {
                        const has = !!url;
                        if (preview) {
                            preview.src = url || '';
                            preview.classList.toggle('d-none', !has);
                        }
                        if (empty) empty.classList.toggle('d-none', has);
                        if (openBtn) {
                            openBtn.classList.toggle('disabled', !has);
                            openBtn.setAttribute('href', has ? url : '#');
                        }
                    }

                    function setValue(path, previewUrl) {
                        const raw = String(path || '').trim();
                        if (valueEl) valueEl.value = raw;
                        if (pathInput && pathInput.value !== raw) pathInput.value = raw;
                        setPreview(previewUrl || buildAssetUrl(raw));
                    }

                    function setUploading(on) {
                        if (dropzone) dropzone.classList.toggle('opacity-50', !!on);
                        if (dropzone) dropzone.classList.toggle('pe-none', !!on);
                    }

                    function setProgress(percent) {
                        if (!progressWrap || !progressBar) return;
                        progressWrap.classList.remove('d-none');
                        const p = Math.max(0, Math.min(100, Number(percent || 0)));
                        progressBar.style.width = `${p}%`;
                        progressBar.setAttribute('aria-valuenow', String(p));
                    }

                    function resetProgress() {
                        if (!progressWrap || !progressBar) return;
                        progressWrap.classList.add('d-none');
                        progressBar.style.width = '0%';
                        progressBar.setAttribute('aria-valuenow', '0');
                        progressBar.classList.remove('bg-danger', 'bg-success');
                    }

                    function uploadFile(file) {
                        const check = isImageFile(file);
                        if (!check.ok) {
                            pushNotice('warning', 'File ditolak', check.message);
                            return;
                        }

                        const objectUrl = URL.createObjectURL(file);
                        setPreview(objectUrl);

                        setUploading(true);
                        resetProgress();
                        setProgress(1);
                        if (statusEl) statusEl.textContent = `Uploading… ${file.name}`;

                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', uploadUrl, true);
                        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                        xhr.upload.onprogress = (e) => {
                            if (!e.lengthComputable) return;
                            const percent = Math.round((e.loaded / e.total) * 100);
                            setProgress(percent);
                        };

                        xhr.onload = () => {
                            let payload = null;
                            try { payload = JSON.parse(xhr.responseText || '{}'); } catch (_) {}

                            if (xhr.status >= 200 && xhr.status < 300 && payload && payload.data) {
                                if (progressBar) progressBar.classList.add('bg-success');
                                if (statusEl) statusEl.textContent = 'Selesai';
                                setValue(payload.data.path || '', payload.data.url || '');
                            } else {
                                if (progressBar) progressBar.classList.add('bg-danger');
                                if (statusEl) statusEl.textContent = 'Gagal upload';
                                const msg = payload && payload.message ? payload.message : 'Upload gagal diproses.';
                                pushNotice('danger', 'Gagal', msg);
                            }

                            setUploading(false);
                            setTimeout(() => {
                                resetProgress();
                                if (statusEl) statusEl.textContent = '';
                            }, 900);
                            try { URL.revokeObjectURL(objectUrl); } catch (_) {}
                        };

                        xhr.onerror = () => {
                            if (progressBar) progressBar.classList.add('bg-danger');
                            if (statusEl) statusEl.textContent = 'Gagal upload';
                            pushNotice('danger', 'Gagal', 'Koneksi bermasalah saat upload.');
                            setUploading(false);
                        };

                        const form = new FormData();
                        form.set('page', page);
                        form.set('section', section);
                        form.set('key', key);
                        form.set('file', file, file.name);
                        xhr.send(form);
                    }

                    if (dropzone && fileInput) {
                        dropzone.addEventListener('click', () => fileInput.click());
                        dropzone.addEventListener('keydown', (e) => {
                            if (e.key === 'Enter' || e.key === ' ') {
                                e.preventDefault();
                                fileInput.click();
                            }
                        });
                        dropzone.addEventListener('dragover', (e) => {
                            e.preventDefault();
                            dropzone.classList.add('border-primary');
                        });
                        dropzone.addEventListener('dragleave', () => dropzone.classList.remove('border-primary'));
                        dropzone.addEventListener('drop', (e) => {
                            e.preventDefault();
                            dropzone.classList.remove('border-primary');
                            const file = e.dataTransfer?.files?.[0];
                            if (file) uploadFile(file);
                        });
                        fileInput.addEventListener('change', () => {
                            const file = fileInput.files?.[0];
                            if (file) uploadFile(file);
                            fileInput.value = '';
                        });
                    }

                    if (pathInput) {
                        pathInput.addEventListener('input', () => {
                            const raw = String(pathInput.value || '').trim();
                            setValue(raw, buildAssetUrl(raw));
                        });
                    }

                    if (clearBtn) {
                        clearBtn.addEventListener('click', () => {
                            setValue('', '');
                            if (statusEl) statusEl.textContent = '';
                            resetProgress();
                        });
                    }

                    if (copyBtn) {
                        copyBtn.addEventListener('click', () => {
                            const raw = valueEl ? valueEl.value : (pathInput ? pathInput.value : '');
                            copyText(raw);
                            pushNotice('success', 'Tersalin', 'Path gambar berhasil disalin.');
                        });
                    }
                }

                document.querySelectorAll('[data-image-field]').forEach((el) => wireImageField(el));

                if (fieldSearch) {
                    const items = Array.from(document.querySelectorAll('[data-field-item]'));
                    const tabPanes = Array.from(document.querySelectorAll('[data-tab-pane]'));
                    const tabButtons = Array.from(document.querySelectorAll('[data-tab-btn]'));
                    const tabsKey = `kontenHalaman.activeTab.${page}`;

                    function showTabBySection(section) {
                        const btn = document.querySelector(`[data-tab-btn][data-section="${section}"]`);
                        if (!btn) return;
                        const tab = new bootstrap.Tab(btn);
                        tab.show();
                    }

                    function getActiveSection() {
                        const activeBtn = document.querySelector('[data-tab-btn].active');
                        return activeBtn ? (activeBtn.getAttribute('data-section') || '') : '';
                    }

                    try {
                        const saved = localStorage.getItem(tabsKey);
                        if (saved) showTabBySection(saved);
                    } catch (_) {}

                    tabButtons.forEach((btn) => {
                        btn.addEventListener('shown.bs.tab', () => {
                            const section = btn.getAttribute('data-section') || '';
                            try {
                                localStorage.setItem(tabsKey, section);
                            } catch (_) {}
                        });
                    });

                    function applyFilter() {
                        const q = String(fieldSearch.value || '').toLowerCase().trim();
                        const matchedBySection = new Map();

                        items.forEach((item) => {
                            const hay = String(item.dataset.hay || '');
                            const show = !q || hay.includes(q);
                            item.classList.toggle('d-none', !show);
                            const section = item.closest('[data-tab-pane]')?.getAttribute('data-tab-section') || '';
                            matchedBySection.set(section, (matchedBySection.get(section) || 0) + (show ? 1 : 0));
                        });

                        tabPanes.forEach((pane) => {
                            const section = pane.getAttribute('data-tab-section') || '';
                            const count = matchedBySection.get(section) || 0;
                            const emptyState = pane.querySelector('[data-empty-state]');
                            if (emptyState) emptyState.classList.toggle('d-none', !q || count > 0);
                        });

                        tabButtons.forEach((btn) => {
                            const section = btn.getAttribute('data-section') || '';
                            const count = matchedBySection.get(section) || 0;
                            btn.closest('li')?.classList.toggle('d-none', q && count === 0);
                            const badge = btn.querySelector('[data-tab-badge]');
                            if (badge) {
                                const initial = badge.getAttribute('data-initial') || String(count);
                                badge.textContent = q ? String(count) : String(initial);
                            }
                        });

                        const activeSection = getActiveSection();
                        const activeVisible = !q || (matchedBySection.get(activeSection) || 0) > 0;
                        if (!activeVisible) {
                            const firstVisible = tabButtons.find((btn) => !(btn.closest('li')?.classList.contains('d-none')));
                            if (firstVisible) {
                                const section = firstVisible.getAttribute('data-section') || '';
                                showTabBySection(section);
                            }
                        }
                    }

                    fieldSearch.addEventListener('input', applyFilter);
                    applyFilter();
                }
            })();
        </script>
    </main>
@endsection
