@extends('admin.partials.app')

@section('content')
    <main id="main-content" tabindex="-1" class="pb-4" role="main" aria-label="Manajemen speakers">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div class="me-auto">
                <h1 class="h3 mb-1">Speakers</h1>
                <div class="text-muted">Kelola data speaker yang ditampilkan pada halaman peserta.</div>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a class="btn btn-primary btn-sm" href="{{ route('speakers.create') }}">
                    <i class="la la-plus" aria-hidden="true"></i>
                    Tambah Speaker
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

        <section class="mb-4" aria-label="Pencarian speaker">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('speakers.index') }}" class="row g-3 align-items-end">
                        <div class="col-12 col-lg-8">
                            <label class="form-label text-black" for="filter-q">Cari</label>
                            <input
                                id="filter-q"
                                name="q"
                                type="search"
                                class="form-control"
                                placeholder="Nama, jabatan, atau perusahaan…"
                                value="{{ $q ?? request('q') }}"
                                autocomplete="off"
                            >
                        </div>
                        <div class="col-12 col-md-6 col-lg-2 d-grid">
                            <button type="submit" class="btn btn-primary">Cari</button>
                        </div>
                        <div class="col-12 col-md-6 col-lg-2 d-grid">
                            <a class="btn btn-outline-secondary" href="{{ route('speakers.index') }}">Reset</a>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <section aria-label="Daftar speaker">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h2 class="h6 mb-0">Daftar Speaker</h2>
                            <div class="text-muted">Total hasil: {{ $speakers->total() }}</div>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" aria-label="Tabel speaker">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-nowrap">Foto</th>
                                    <th scope="col">Nama</th>
                                    <th scope="col">Jabatan</th>
                                    <th scope="col">Perusahaan</th>
                                    <th scope="col" class="text-nowrap">Status</th>
                                    <th scope="col" class="text-nowrap">Urutan</th>
                                    <th scope="col" class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($speakers as $speaker)
                                    <tr>
                                        <td>
                                            @if ($speaker->foto_url)
                                                <img
                                                    src="{{ $speaker->foto_url }}"
                                                    alt="Foto {{ $speaker->nama }}"
                                                    style="width: 44px; height: 44px; object-fit: cover; border-radius: 12px;"
                                                >
                                            @else
                                                <div class="rounded bg-light border d-inline-flex align-items-center justify-content-center text-muted"
                                                    style="width: 44px; height: 44px; border-radius: 12px;">
                                                    <i class="la la-user" aria-hidden="true"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-black fw-semibold">{{ $speaker->nama }}</td>
                                        <td class="text-muted">{{ $speaker->jabatan }}</td>
                                        <td class="text-muted">{{ $speaker->perusahaan }}</td>
                                        <td>
                                            @if ($speaker->is_active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="text-muted">{{ $speaker->urutan }}</td>
                                        <td class="text-end">
                                            <div class="d-inline-flex flex-wrap gap-2 justify-content-end">
                                                <a class="btn btn-outline-primary btn-sm" href="{{ route('speakers.show', $speaker) }}" title="Detail">
                                                    <i class="la la-eye" aria-hidden="true"></i>
                                                </a>
                                                <a class="btn btn-outline-secondary btn-sm" href="{{ route('speakers.edit', $speaker) }}" title="Edit">
                                                    <i class="la la-edit" aria-hidden="true"></i>
                                                </a>
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-danger btn-sm"
                                                    title="Hapus"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal"
                                                    data-delete-action="{{ route('speakers.destroy', $speaker) }}"
                                                    data-delete-title="{{ $speaker->nama }}"
                                                >
                                                    <i class="la la-trash" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">Belum ada speaker.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 d-flex justify-content-end">
                        {{ $speakers->links() }}
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
                            <h2 class="modal-title h6" id="deleteModalTitle">Hapus Speaker</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-black fw-semibold mb-1">Konfirmasi penghapusan</div>
                            <div class="text-muted">
                                Speaker <span id="delete-speaker-title" class="text-black fw-semibold">-</span> akan dihapus permanen.
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
            const deleteTitle = document.getElementById('delete-speaker-title');

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
