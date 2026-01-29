@extends('admin.partials.app')

@section('content')
    <main id="main-content" tabindex="-1" class="pb-4" role="main" aria-label="Tambah e-book">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div class="me-auto">
                <h1 class="h3 mb-1">Tambah E-book</h1>
                <div class="text-muted">Unggah cover dan PDF, atur harga, stok, dan status penjualan.</div>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.ebooks.index') }}">
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

        <form method="POST" action="{{ route('admin.ebooks.store') }}" enctype="multipart/form-data" class="card" novalidate>
            @csrf

            <div class="card-body">
                <div class="row g-4">
                    <div class="col-12 col-lg-8">
                        <div class="mb-3">
                            <label class="form-label text-black" for="title">Judul</label>
                            <input
                                id="title"
                                name="title"
                                type="text"
                                class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title') }}"
                                required
                                maxlength="255"
                                autocomplete="off"
                            >
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback">Judul wajib diisi.</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-black" for="author">Penulis</label>
                            <input
                                id="author"
                                name="author"
                                type="text"
                                class="form-control @error('author') is-invalid @enderror"
                                value="{{ old('author') }}"
                                required
                                maxlength="255"
                                autocomplete="off"
                            >
                            @error('author')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback">Penulis wajib diisi.</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-black" for="description">Deskripsi</label>
                            <textarea
                                id="description"
                                name="description"
                                class="form-control @error('description') is-invalid @enderror"
                                rows="6"
                                placeholder="Ringkasan e-book…"
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label text-black" for="price">Harga</label>
                                <input
                                    id="price"
                                    name="price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control @error('price') is-invalid @enderror"
                                    value="{{ old('price', 0) }}"
                                    required
                                >
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label text-black" for="stock">Stok</label>
                                <input
                                    id="stock"
                                    name="stock"
                                    type="number"
                                    min="0"
                                    class="form-control @error('stock') is-invalid @enderror"
                                    value="{{ old('stock', 0) }}"
                                    required
                                >
                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-4">
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

                        <div class="row g-3 mt-1">
                            <div class="col-12 col-md-6">
                                <label class="form-label text-black" for="cover_image">Cover</label>
                                <input
                                    id="cover_image"
                                    name="cover_image"
                                    type="file"
                                    class="form-control @error('cover_image') is-invalid @enderror"
                                    accept=".jpg,.jpeg,.png,image/*"
                                    required
                                >
                                @error('cover_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">JPG/PNG, maks 2MB.</div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label text-black" for="pdf_file">File PDF</label>
                                <input
                                    id="pdf_file"
                                    name="pdf_file"
                                    type="file"
                                    class="form-control @error('pdf_file') is-invalid @enderror"
                                    accept=".pdf,application/pdf"
                                    required
                                >
                                @error('pdf_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">PDF, maks 10MB.</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-4">
                        <div class="rounded border bg-light d-flex align-items-center justify-content-center" style="height: 260px; overflow: hidden;">
                            <img
                                id="cover-preview"
                                alt="Preview cover e-book"
                                style="width: 100%; height: 100%; object-fit: cover; display: none;"
                            >
                            <div id="cover-placeholder" class="text-muted small text-center px-3">
                                Preview cover akan muncul di sini.
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
                <a href="{{ route('admin.ebooks.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </main>

    <script>
        (function () {
            const input = document.getElementById('cover_image');
            const preview = document.getElementById('cover-preview');
            const placeholder = document.getElementById('cover-placeholder');

            let lastObjectUrl = null;

            const clearPreview = () => {
                if (lastObjectUrl) {
                    URL.revokeObjectURL(lastObjectUrl);
                    lastObjectUrl = null;
                }
                preview.style.display = 'none';
                preview.removeAttribute('src');
                placeholder.style.display = 'block';
            };

            const setPreview = (file) => {
                if (!file) {
                    clearPreview();
                    return;
                }

                if (lastObjectUrl) {
                    URL.revokeObjectURL(lastObjectUrl);
                }

                lastObjectUrl = URL.createObjectURL(file);
                preview.src = lastObjectUrl;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            };

            if (input) {
                input.addEventListener('change', () => {
                    const file = input.files && input.files.length ? input.files[0] : null;
                    setPreview(file);
                });
            }
        })();
    </script>
@endsection
