@extends('admin.partials.app')

@section('content')
    @php
        $statusOptions = [
            '' => 'Semua status',
            'draft' => 'Draft',
            'published' => 'Published',
        ];

        $statusBadge = function (?string $status) {
            $status = strtolower((string) $status);
            return match ($status) {
                'published' => ['bg-success', 'Published'],
                'draft' => ['bg-warning text-dark', 'Draft'],
                default => ['bg-light text-dark', $status ?: '-'],
            };
        };
    @endphp

    <main id="main-content" tabindex="-1" class="pb-4" role="main" aria-label="Manajemen berita">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div class="me-auto">
                <h1 class="h3 mb-1">Manajemen Berita</h1>
                <div class="text-muted">Kelola berita, status publikasi, dan konten yang tampil di halaman peserta.</div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <a class="btn btn-primary btn-sm" href="{{ route('admin.news.create') }}">
                    <i class="la la-plus" aria-hidden="true"></i>
                    Buat Berita
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

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="fw-semibold">Gagal</div>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        <section class="mb-4" aria-label="Filter berita">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.news.index') }}" class="row g-3 align-items-end">
                        <div class="col-12 col-lg-5">
                            <label class="form-label text-black" for="filter-q">Cari</label>
                            <input
                                id="filter-q"
                                name="q"
                                type="search"
                                class="form-control"
                                placeholder="Judul, slug, atau kata kunci konten…"
                                value="{{ $filters['q'] ?? request('q') }}"
                                autocomplete="off"
                            >
                        </div>

                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label text-black" for="filter-category">Kategori</label>
                            <select id="filter-category" name="category_id" class="form-select">
                                <option value="">Semua kategori</option>
                                @foreach ($categories as $c)
                                    <option value="{{ $c->id }}" @selected((string) ($filters['category_id'] ?? request('category_id')) === (string) $c->id)>
                                        {{ $c->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6 col-lg-2">
                            <label class="form-label text-black" for="filter-status">Status</label>
                            <select id="filter-status" name="status" class="form-select">
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}" @selected((string) ($filters['status'] ?? request('status')) === (string) $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6 col-lg-1 d-grid">
                            <button type="submit" class="btn btn-primary">Terapkan</button>
                        </div>

                        <div class="col-12 col-md-6 col-lg-1 d-grid">
                            <a class="btn btn-outline-secondary" href="{{ route('admin.news.index') }}">Reset</a>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <section aria-label="Daftar berita">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h2 class="h6 mb-0">Berita</h2>
                            <div class="text-muted">Total hasil: {{ $news->total() }}</div>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" aria-label="Tabel berita">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-nowrap">Judul</th>
                                    <th scope="col" class="text-nowrap">Kategori</th>
                                    <th scope="col" class="text-nowrap">Status</th>
                                    <th scope="col" class="text-nowrap">Dibuat</th>
                                    <th scope="col" class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($news as $n)
                                    @php
                                        $badgeClass = 'bg-light text-dark';
                                        $badgeLabel = '-';
                                        $badge = $statusBadge($n->status);
                                        if (is_array($badge) && count($badge) >= 2) {
                                            $badgeClass = (string) $badge[0];
                                            $badgeLabel = (string) $badge[1];
                                        }
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div
                                                    class="rounded border bg-light"
                                                    style="width: 56px; height: 42px; overflow: hidden; flex: 0 0 auto;"
                                                >
                                                    @php
                                                        $thumbUrl = null;
                                                        $thumb = (string) ($n->gambar_utama ?? '');
                                                        if ($thumb === '') {
                                                            $thumbUrl = null;
                                                        } elseif (str_starts_with($thumb, 'http://') || str_starts_with($thumb, 'https://')) {
                                                            $thumbUrl = $thumb;
                                                        } elseif (str_starts_with($thumb, 'storage/')) {
                                                            $thumbUrl = asset($thumb);
                                                        } else {
                                                            $thumbUrl = asset('storage/'.$thumb);
                                                        }
                                                    @endphp
                                                    @if ($thumbUrl ?? null)
                                                        <img
                                                            src="{{ $thumbUrl }}"
                                                            alt="{{ $n->judul }}"
                                                            style="width: 100%; height: 100%; object-fit: cover;"
                                                            loading="lazy"
                                                        >
                                                    @endif
                                                </div>
                                                <div style="min-width: 220px;">
                                                    <div class="text-black fw-semibold">{{ $n->judul }}</div>
                                                    <div class="text-muted small">{{ $n->slug }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-black">{{ $n->category?->nama ?? '-' }}</td>
                                        <td><span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span></td>
                                        <td class="text-muted text-nowrap">{{ optional($n->created_at)->format('d M Y H:i') }}</td>
                                        <td class="text-end text-nowrap">
                                            <a
                                                class="btn btn-outline-primary btn-sm"
                                                href="{{ route('admin.news.edit', $n) }}"
                                                title="Edit"
                                            >
                                                <i class="la la-pen" aria-hidden="true"></i>
                                            </a>

                                            <form
                                                action="{{ route('admin.news.destroy', $n) }}"
                                                method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Hapus berita ini? Tindakan ini tidak bisa dibatalkan.')"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-outline-danger btn-sm" type="submit" title="Hapus">
                                                    <i class="la la-trash" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            Belum ada berita.
                                            <div class="mt-2">
                                                <a class="btn btn-primary btn-sm" href="{{ route('admin.news.create') }}">Buat berita pertama</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 d-flex justify-content-end">
                        {{ $news->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
