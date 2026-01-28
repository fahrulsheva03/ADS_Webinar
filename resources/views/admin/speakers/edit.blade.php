@extends('admin.partials.app')

@section('content')
    <main id="main-content" tabindex="-1" class="pb-4" role="main" aria-label="Edit speaker">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div class="me-auto">
                <h1 class="h3 mb-1">Edit Speaker</h1>
                <div class="text-muted">Perbarui data speaker, foto, dan status tampil.</div>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('speakers.index') }}">
                    <i class="la la-arrow-left" aria-hidden="true"></i>
                    Kembali
                </a>
            </div>
        </div>

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="fw-semibold">Gagal</div>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('speakers.update', $speaker) }}" enctype="multipart/form-data" class="card" novalidate>
            @csrf
            @method('PUT')

            <div class="card-body">
                <div class="row g-4">
                    <div class="col-12 col-lg-8">
                        <div class="mb-3">
                            <label class="form-label text-black" for="nama">Nama</label>
                            <input
                                id="nama"
                                name="nama"
                                type="text"
                                class="form-control @error('nama') is-invalid @enderror"
                                value="{{ old('nama', $speaker->nama) }}"
                                required
                                maxlength="150"
                                autocomplete="off"
                            >
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback">Nama wajib diisi.</div>
                            @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label text-black" for="jabatan">Jabatan</label>
                                <input
                                    id="jabatan"
                                    name="jabatan"
                                    type="text"
                                    class="form-control @error('jabatan') is-invalid @enderror"
                                    value="{{ old('jabatan', $speaker->jabatan) }}"
                                    required
                                    maxlength="150"
                                    autocomplete="off"
                                >
                                @error('jabatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Jabatan wajib diisi.</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label text-black" for="perusahaan">Perusahaan</label>
                                <input
                                    id="perusahaan"
                                    name="perusahaan"
                                    type="text"
                                    class="form-control @error('perusahaan') is-invalid @enderror"
                                    value="{{ old('perusahaan', $speaker->perusahaan) }}"
                                    required
                                    maxlength="150"
                                    autocomplete="off"
                                >
                                @error('perusahaan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Perusahaan wajib diisi.</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-3 mb-3">
                            <label class="form-label text-black" for="linkedin_url">LinkedIn URL</label>
                            <input
                                id="linkedin_url"
                                name="linkedin_url"
                                type="url"
                                class="form-control @error('linkedin_url') is-invalid @enderror"
                                value="{{ old('linkedin_url', $speaker->linkedin_url) }}"
                                placeholder="https://www.linkedin.com/in/…"
                                maxlength="2048"
                                autocomplete="off"
                            >
                            @error('linkedin_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-black" for="foto">Foto (URL / path public)</label>
                            <input
                                id="foto"
                                name="foto"
                                type="text"
                                class="form-control @error('foto') is-invalid @enderror"
                                value="{{ old('foto', (string) ($speaker->foto ?? '')) }}"
                                placeholder="Contoh: assets/images/speakers-img1.jpg"
                                maxlength="2048"
                                autocomplete="off"
                            >
                            @error('foto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label text-black" for="foto_file">Upload Foto Baru</label>
                                <input
                                    id="foto_file"
                                    name="foto_file"
                                    type="file"
                                    class="form-control @error('foto_file') is-invalid @enderror"
                                    accept="image/*"
                                >
                                @error('foto_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Jika diupload, foto baru akan menggantikan foto lama.</div>
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label text-black" for="urutan">Urutan</label>
                                <input
                                    id="urutan"
                                    name="urutan"
                                    type="number"
                                    class="form-control @error('urutan') is-invalid @enderror"
                                    value="{{ old('urutan', $speaker->urutan) }}"
                                    min="0"
                                    max="1000000"
                                    required
                                >
                                @error('urutan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label text-black d-block">Status</label>
                                <input type="hidden" name="is_active" value="0">
                                <div class="form-check mt-2">
                                    <input
                                        id="is_active"
                                        name="is_active"
                                        class="form-check-input @error('is_active') is-invalid @enderror"
                                        type="checkbox"
                                        value="1"
                                        @checked(old('is_active', $speaker->is_active ? '1' : '0') === '1')
                                    >
                                    <label class="form-check-label" for="is_active">Aktif</label>
                                    @error('is_active')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="hapus_foto" value="0">
                        <div class="form-check mt-3">
                            <input
                                id="hapus_foto"
                                name="hapus_foto"
                                class="form-check-input @error('hapus_foto') is-invalid @enderror"
                                type="checkbox"
                                value="1"
                                @checked(old('hapus_foto', '0') === '1')
                            >
                            <label class="form-check-label" for="hapus_foto">Hapus foto saat ini</label>
                            @error('hapus_foto')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12 col-lg-4">
                        <div class="rounded border bg-light d-flex align-items-center justify-content-center" style="height: 240px; overflow: hidden;">
                            <img
                                id="foto-preview"
                                alt="Preview foto speaker"
                                src="{{ $speaker->foto_url ?: '' }}"
                                style="width: 100%; height: 100%; object-fit: cover; {{ $speaker->foto_url ? '' : 'display: none;' }}"
                            >
                            <div id="foto-placeholder" class="text-muted small text-center px-3" style="{{ $speaker->foto_url ? 'display: none;' : '' }}">
                                Belum ada foto.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex flex-wrap gap-2 justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="la la-save" aria-hidden="true"></i>
                    Simpan
                </button>
                <a href="{{ route('speakers.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </main>

    <script>
        (function () {
            const fotoInput = document.getElementById('foto');
            const fileInput = document.getElementById('foto_file');
            const hapusFoto = document.getElementById('hapus_foto');
            const preview = document.getElementById('foto-preview');
            const placeholder = document.getElementById('foto-placeholder');
            const currentSrc = (preview?.getAttribute('src') || '').trim();
            const savedFotoRaw = @json(trim((string) ($speaker->foto ?? '')));
            const initialFotoRaw = (fotoInput?.value || '').trim();
            const shouldUseManualOnLoad = initialFotoRaw !== '' && initialFotoRaw !== savedFotoRaw;

            let lastObjectUrl = null;

            const toAbsoluteSrc = (value) => {
                const raw = (value || '').trim();
                if (!raw) return '';
                if (raw.startsWith('http://') || raw.startsWith('https://')) return raw;
                if (raw.startsWith('/')) return raw;
                return '/' + raw;
            };

            const setPreview = (src) => {
                const finalSrc = (src || '').trim();
                if (!finalSrc) {
                    preview.style.display = 'none';
                    preview.removeAttribute('src');
                    placeholder.style.display = '';
                    return;
                }

                preview.src = finalSrc;
                preview.style.display = '';
                placeholder.style.display = 'none';
            };

            const syncPreview = () => {
                if (lastObjectUrl) {
                    URL.revokeObjectURL(lastObjectUrl);
                    lastObjectUrl = null;
                }

                const file = fileInput?.files?.[0] || null;
                if (file) {
                    lastObjectUrl = URL.createObjectURL(file);
                    setPreview(lastObjectUrl);
                    return;
                }

                const wantsDelete = !!hapusFoto?.checked;

                if (wantsDelete) {
                    setPreview('');
                    return;
                }

                const rawValue = (fotoInput?.value || '').trim();
                const hasManualValue = rawValue !== '' && (rawValue !== savedFotoRaw || shouldUseManualOnLoad);
                if (hasManualValue) {
                    setPreview(toAbsoluteSrc(rawValue));
                    return;
                }

                setPreview(currentSrc || toAbsoluteSrc(rawValue));
            };

            fotoInput?.addEventListener('input', syncPreview);
            fileInput?.addEventListener('change', syncPreview);
            hapusFoto?.addEventListener('change', syncPreview);
            syncPreview();
        })();
    </script>
@endsection
