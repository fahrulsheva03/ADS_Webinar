@extends('admin.partials.app')

@section('content')
    <main id="main-content" tabindex="-1" class="pb-4" role="main" aria-label="Detail e-book">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div class="me-auto">
                <h1 class="h3 mb-1">{{ $ebook->title }}</h1>
                <div class="text-muted">Detail e-book dan tautan file.</div>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.ebooks.index') }}">
                    <i class="la la-arrow-left" aria-hidden="true"></i>
                    Kembali
                </a>
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.ebooks.edit', $ebook) }}">
                    <i class="la la-edit" aria-hidden="true"></i>
                    Edit
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="fw-semibold">Sukses</div>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        <section aria-label="Ringkasan e-book">
            <div class="card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-12 col-lg-4">
                            <div class="rounded border bg-light d-flex align-items-center justify-content-center" style="height: 320px; overflow: hidden;">
                                @if ($ebook->cover_image_url)
                                    <img
                                        src="{{ $ebook->cover_image_url }}"
                                        alt="Cover {{ $ebook->title }}"
                                        style="width: 100%; height: 100%; object-fit: cover;"
                                    >
                                @else
                                    <div class="text-muted small text-center px-3">
                                        Cover belum tersedia.
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-12 col-lg-8">
                            <div class="d-flex flex-column gap-2">
                                <div>
                                    <div class="text-muted">Penulis</div>
                                    <div class="text-black fw-semibold">{{ $ebook->author }}</div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12 col-md-4">
                                        <div class="text-muted">Harga</div>
                                        <div class="text-black fw-semibold">Rp {{ number_format((float) $ebook->price, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="text-muted">Stok</div>
                                        <div class="text-black fw-semibold">{{ $ebook->stock }}</div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="text-muted">Status</div>
                                        <div>
                                            @if ($ebook->is_active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Nonaktif</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <div class="text-muted">Deskripsi</div>
                                    @if (trim((string) ($ebook->description ?? '')) !== '')
                                        <div class="text-black" style="white-space: pre-line;">{{ $ebook->description }}</div>
                                    @else
                                        <div class="text-muted">Tidak ada deskripsi.</div>
                                    @endif
                                </div>
                                <div class="mt-3">
                                    <div class="text-black fw-semibold mb-2">File</div>
                                    <div class="d-flex flex-wrap gap-2">
                                        @if ($ebook->pdf_file_url)
                                            <a class="btn btn-primary btn-sm" href="{{ $ebook->pdf_file_url }}" target="_blank" rel="noopener">
                                                <i class="la la-file-pdf" aria-hidden="true"></i>
                                                Buka PDF
                                            </a>
                                        @else
                                            <span class="text-muted">PDF belum tersedia.</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex flex-wrap justify-content-end gap-2">
                    <form method="POST" action="{{ route('admin.ebooks.destroy', $ebook) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Hapus e-book ini?')">
                            <i class="la la-trash" aria-hidden="true"></i>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </main>
@endsection
