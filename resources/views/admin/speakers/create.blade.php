@extends('admin.partials.app')

@section('content')
    <main id="main-content" tabindex="-1" class="pb-4" role="main" aria-label="Tambah speaker">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div class="me-auto">
                <h1 class="h3 mb-1">Tambah Speaker</h1>
                <div class="text-muted">Isi profil singkat speaker beserta foto dan tautan sosial.</div>
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

        <form method="POST" action="{{ route('speakers.store') }}" enctype="multipart/form-data" class="card" novalidate>
            @csrf

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
                                value="{{ old('nama') }}"
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
                                    value="{{ old('jabatan') }}"
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
                                    value="{{ old('perusahaan') }}"
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
                                value="{{ old('linkedin_url') }}"
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
                                value="{{ old('foto') }}"
                                placeholder="Contoh: assets/images/speakers-img1.jpg"
                                maxlength="2048"
                                autocomplete="off"
                            >
                            @error('foto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Boleh URL (https://…) atau path public. Upload file juga didukung.</div>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label text-black" for="foto_file">Upload Foto</label>
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
                                <div class="form-text">JPEG/PNG/WebP, maks 2MB.</div>
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label text-black" for="urutan">Urutan</label>
                                <input
                                    id="urutan"
                                    name="urutan"
                                    type="number"
                                    class="form-control @error('urutan') is-invalid @enderror"
                                    value="{{ old('urutan', 0) }}"
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
                                        @checked(old('is_active', '1') === '1')
                                    >
                                    <label class="form-check-label" for="is_active">Aktif</label>
                                    @error('is_active')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-4">
                        <div class="rounded border bg-light d-flex align-items-center justify-content-center" style="height: 240px; overflow: hidden;">
                            <img
                                id="foto-preview"
                                alt="Preview foto speaker"
                                style="width: 100%; height: 100%; object-fit: cover; display: none;"
                            >
                            <div id="foto-placeholder" class="text-muted small text-center px-3">
                                Preview foto akan muncul di sini.
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
            const preview = document.getElementById('foto-preview');
            const placeholder = document.getElementById('foto-placeholder');

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

                setPreview(toAbsoluteSrc(fotoInput?.value || ''));
            };

            fotoInput?.addEventListener('input', syncPreview);
            fileInput?.addEventListener('change', syncPreview);
            syncPreview();
        })();
    </script>
@endsection
