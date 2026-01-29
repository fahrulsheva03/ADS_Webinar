@extends('admin.partials.app')

@section('content')
    <main id="main-content" tabindex="-1" class="pb-4" role="main" aria-label="Manajemen e-book">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div class="me-auto">
                <h1 class="h3 mb-1">E-book</h1>
                <div class="text-muted">Kelola katalog e-book PDF untuk penjualan.</div>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a class="btn btn-primary btn-sm" href="{{ route('admin.ebooks.create') }}">
                    <i class="la la-plus" aria-hidden="true"></i>
                    Tambah E-book Baru
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

        <section class="mb-4" aria-label="Pencarian e-book">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.ebooks.index') }}" class="row g-3 align-items-end">
                        <div class="col-12 col-lg-5">
                            <label class="form-label text-black" for="filter-q">Judul</label>
                            <input
                                id="filter-q"
                                name="q"
                                type="search"
                                class="form-control"
                                placeholder="Cari judul…"
                                value="{{ $filters['q'] ?? request('q') }}"
                                autocomplete="off"
                            >
                        </div>
                        <div class="col-12 col-lg-4">
                            <label class="form-label text-black" for="filter-author">Penulis</label>
                            <input
                                id="filter-author"
                                name="author"
                                type="search"
                                class="form-control"
                                placeholder="Cari penulis…"
                                value="{{ $filters['author'] ?? request('author') }}"
                                autocomplete="off"
                            >
                        </div>
                        <div class="col-12 col-lg-3">
                            <label class="form-label text-black" for="filter-status">Status</label>
                            <select id="filter-status" name="status" class="form-select">
                                <option value="">Semua</option>
                                <option value="active" @selected(($filters['status'] ?? request('status')) === 'active')>Aktif</option>
                                <option value="inactive" @selected(($filters['status'] ?? request('status')) === 'inactive')>Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-lg-2 d-grid">
                            <button type="submit" class="btn btn-primary">Cari</button>
                        </div>
                        <div class="col-12 col-md-6 col-lg-2 d-grid">
                            <a class="btn btn-outline-secondary" href="{{ route('admin.ebooks.index') }}">Reset</a>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <section aria-label="Daftar e-book">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h2 class="h6 mb-0">Daftar E-book</h2>
                            <div class="text-muted">Total hasil: {{ $ebooks->total() }}</div>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" aria-label="Tabel e-book">
                            <thead>
                                <tr>
                                    <th scope="col">Judul</th>
                                    <th scope="col">Penulis</th>
                                    <th scope="col" class="text-nowrap">Harga</th>
                                    <th scope="col" class="text-nowrap">Stok</th>
                                    <th scope="col" class="text-nowrap">Status</th>
                                    <th scope="col" class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($ebooks as $ebook)
                                    <tr>
                                        <td class="text-black fw-semibold">{{ $ebook->title }}</td>
                                        <td class="text-muted">{{ $ebook->author }}</td>
                                        <td class="text-muted">Rp {{ number_format((float) $ebook->price, 0, ',', '.') }}</td>
                                        <td class="text-muted">{{ $ebook->stock }}</td>
                                        <td>
                                            @if ($ebook->is_active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="d-inline-flex flex-wrap gap-2 justify-content-end">
                                                <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.ebooks.show', $ebook) }}" title="Detail">
                                                    <i class="la la-eye" aria-hidden="true"></i>
                                                </a>
                                                <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.ebooks.edit', $ebook) }}" title="Edit">
                                                    <i class="la la-edit" aria-hidden="true"></i>
                                                </a>
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-danger btn-sm"
                                                    title="Hapus"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal"
                                                    data-delete-action="{{ route('admin.ebooks.destroy', $ebook) }}"
                                                    data-delete-title="{{ $ebook->title }}"
                                                >
                                                    <i class="la la-trash" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Belum ada e-book.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 d-flex justify-content-end">
                        {{ $ebooks->links() }}
                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalTitle" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="delete-form" method="POST" action="#">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header">
                            <h2 class="modal-title h6" id="deleteModalTitle">Hapus E-book</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-black fw-semibold mb-1">Konfirmasi penghapusan</div>
                            <div class="text-muted">
                                E-book <span id="delete-ebook-title" class="text-black fw-semibold">-</span> akan dihapus.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        (function () {
            const deleteModal = document.getElementById('deleteModal');
            const deleteForm = document.getElementById('delete-form');
            const deleteTitle = document.getElementById('delete-ebook-title');

            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', (e) => {
                    const trigger = e.relatedTarget;
                    const action = trigger?.getAttribute('data-delete-action') || '#';
                    const title = trigger?.getAttribute('data-delete-title') || '';

                    deleteForm.setAttribute('action', action);
                    deleteTitle.textContent = title || '-';
                });
            }
        })();
    </script>
@endsection
