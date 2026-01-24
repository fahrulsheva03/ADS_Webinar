@extends('admin.partials.app')

@section('content')
    <main id="main-content" tabindex="-1" class="pb-4" role="main" aria-label="Buat berita">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div class="me-auto">
                <h1 class="h3 mb-1">Buat Berita</h1>
                <div class="text-muted">Isi detail berita, konten, dan SEO sebelum dipublikasikan.</div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.news.index') }}">
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

        <form
            id="news-form"
            method="POST"
            action="{{ route('admin.news.store') }}"
            enctype="multipart/form-data"
            class="card"
            novalidate
        >
            @csrf

            <div class="card-body">
                <div class="row g-4">
                    <div class="col-12 col-lg-8">
                        <div class="mb-3">
                            <label class="form-label text-black" for="judul">Judul</label>
                            <input
                                id="judul"
                                name="judul"
                                type="text"
                                class="form-control @error('judul') is-invalid @enderror"
                                value="{{ old('judul') }}"
                                required
                                maxlength="255"
                                autocomplete="off"
                            >
                            @error('judul')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback">Judul wajib diisi.</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-black" for="slug">Slug</label>
                            <input
                                id="slug"
                                name="slug"
                                type="text"
                                class="form-control @error('slug') is-invalid @enderror"
                                value="{{ old('slug') }}"
                                pattern="^[a-z0-9]+(?:-[a-z0-9]+)*$"
                                maxlength="255"
                                autocomplete="off"
                            >
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="form-text">Otomatis dibuat dari judul. Gunakan huruf kecil, angka, dan tanda minus.</div>
                                <div class="invalid-feedback">Slug tidak valid.</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-black" for="konten-editor">Konten</label>
                            <input
                                id="konten"
                                name="konten"
                                type="hidden"
                                value="{{ old('konten') }}"
                            >
                            <div
                                id="konten-editor"
                                class="border rounded"
                                style="min-height: 380px; background: #ffffff;"
                            ></div>
                            @error('konten')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-lg-6">
                                <label class="form-label text-black" for="meta_description">Meta Description</label>
                                <textarea
                                    id="meta_description"
                                    name="meta_description"
                                    class="form-control @error('meta_description') is-invalid @enderror"
                                    rows="3"
                                    maxlength="255"
                                >{{ old('meta_description') }}</textarea>
                                @error('meta_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Ringkasan singkat untuk SEO (maks 255 karakter).</div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <label class="form-label text-black" for="meta_keywords">Keywords</label>
                                <textarea
                                    id="meta_keywords"
                                    name="meta_keywords"
                                    class="form-control @error('meta_keywords') is-invalid @enderror"
                                    rows="3"
                                    maxlength="255"
                                >{{ old('meta_keywords') }}</textarea>
                                @error('meta_keywords')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Pisahkan dengan koma. Contoh: webinar, produk, bisnis.</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-4">
                        <div class="mb-3">
                            <label class="form-label text-black" for="news_category_id">Kategori</label>
                            <select
                                id="news_category_id"
                                name="news_category_id"
                                class="form-select @error('news_category_id') is-invalid @enderror"
                                required
                            >
                                <option value="">Pilih kategori…</option>
                                @foreach ($categories as $c)
                                    <option value="{{ $c->id }}" @selected((string) old('news_category_id') === (string) $c->id)>
                                        {{ $c->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('news_category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback">Kategori wajib dipilih.</div>
                            @enderror
                            @if (($categories ?? collect())->isEmpty())
                                <div class="form-text">Belum ada kategori. Tambahkan kategori via database sebelum membuat berita.</div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-black" for="status">Status Publikasi</label>
                            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="draft" @selected(old('status', 'draft') === 'draft')>Draft</option>
                                <option value="published" @selected(old('status') === 'published')>Published</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback">Status wajib dipilih.</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-black" for="gambar_utama">Gambar Utama</label>
                            <input
                                id="gambar_utama"
                                name="gambar_utama"
                                type="file"
                                class="form-control @error('gambar_utama') is-invalid @enderror"
                                accept="image/*"
                            >
                            @error('gambar_utama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">JPEG/PNG/WebP, maks 2MB.</div>
                        </div>

                        <div class="mb-3">
                            <div class="rounded border bg-light d-flex align-items-center justify-content-center" style="height: 220px; overflow: hidden;">
                                <img
                                    id="gambar-preview"
                                    alt="Preview gambar utama"
                                    style="max-width: 100%; max-height: 100%; object-fit: cover; display: none;"
                                >
                                <div id="gambar-placeholder" class="text-muted small">Preview gambar akan muncul di sini.</div>
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
                <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </main>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">
    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>

    <script>
        (function () {
            const form = document.getElementById('news-form');
            const titleInput = document.getElementById('judul');
            const slugInput = document.getElementById('slug');
            const kontenInput = document.getElementById('konten');
            const gambarInput = document.getElementById('gambar_utama');
            const gambarPreview = document.getElementById('gambar-preview');
            const gambarPlaceholder = document.getElementById('gambar-placeholder');

            let slugTouched = slugInput.value.trim() !== '';

            const slugify = (value) => {
                return value
                    .toString()
                    .normalize('NFKD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
            };

            slugInput.addEventListener('input', () => {
                slugTouched = slugInput.value.trim() !== '';
            });

            titleInput.addEventListener('input', () => {
                if (slugTouched) {
                    return;
                }
                slugInput.value = slugify(titleInput.value);
            });

            const quill = new Quill('#konten-editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ header: [2, 3, 4, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        [{ align: [] }],
                        ['blockquote'],
                        ['link'],
                        ['clean']
                    ]
                }
            });

            if (kontenInput.value && kontenInput.value.trim() !== '') {
                quill.clipboard.dangerouslyPasteHTML(kontenInput.value);
            }

            const syncKonten = () => {
                kontenInput.value = quill.root.innerHTML;
            };

            quill.on('text-change', syncKonten);
            syncKonten();

            gambarInput.addEventListener('change', () => {
                const file = gambarInput.files && gambarInput.files[0];
                if (!file) {
                    gambarPreview.src = '';
                    gambarPreview.style.display = 'none';
                    gambarPlaceholder.style.display = 'block';
                    return;
                }

                if (!file.type || !file.type.startsWith('image/')) {
                    gambarPreview.src = '';
                    gambarPreview.style.display = 'none';
                    gambarPlaceholder.style.display = 'block';
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    gambarPreview.src = e.target.result;
                    gambarPreview.style.display = 'block';
                    gambarPlaceholder.style.display = 'none';
                };
                reader.readAsDataURL(file);
            });

            form.addEventListener('submit', (event) => {
                syncKonten();

                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                if (!kontenInput.value || kontenInput.value.replace(/<[^>]+>/g, '').trim() === '') {
                    event.preventDefault();
                    event.stopPropagation();
                    const editor = document.getElementById('konten-editor');
                    editor.classList.add('border-danger');
                    editor.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }

                form.classList.add('was-validated');
            });
        })();
    </script>
@endsection
