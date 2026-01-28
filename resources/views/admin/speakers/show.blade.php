@extends('admin.partials.app')

@section('content')
    <main id="main-content" tabindex="-1" class="pb-4" role="main" aria-label="Detail speaker">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div class="me-auto">
                <h1 class="h3 mb-1">Detail Speaker</h1>
                <div class="text-muted">Informasi lengkap speaker.</div>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('speakers.index') }}">
                    <i class="la la-arrow-left" aria-hidden="true"></i>
                    Kembali
                </a>
                <a class="btn btn-outline-primary btn-sm" href="{{ route('speakers.edit', $speaker) }}">
                    <i class="la la-edit" aria-hidden="true"></i>
                    Edit
                </a>
                <button
                    type="button"
                    class="btn btn-outline-danger btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#deleteModal"
                    data-delete-action="{{ route('speakers.destroy', $speaker) }}"
                    data-delete-title="{{ $speaker->nama }}"
                >
                    <i class="la la-trash" aria-hidden="true"></i>
                    Hapus
                </button>
            </div>
        </div>

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="fw-semibold">Gagal</div>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-12 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="rounded border bg-light d-flex align-items-center justify-content-center"
                            style="height: 260px; overflow: hidden;">
                            @if ($speaker->foto_url)
                                <img
                                    src="{{ $speaker->foto_url }}"
                                    alt="Foto {{ $speaker->nama }}"
                                    style="width: 100%; height: 100%; object-fit: cover;"
                                >
                            @else
                                <div class="text-muted small text-center px-3">Belum ada foto.</div>
                            @endif
                        </div>
                        <div class="mt-3">
                            <div class="text-muted small">ID</div>
                            <div class="text-black fw-semibold">{{ $speaker->id }}</div>
                        </div>
                        <div class="mt-3">
                            <div class="text-muted small">Status</div>
                            @if ($speaker->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Nonaktif</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-8">
                <div class="card h-100">
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4 text-muted">Nama</dt>
                            <dd class="col-sm-8 text-black fw-semibold">{{ $speaker->nama }}</dd>

                            <dt class="col-sm-4 text-muted">Jabatan</dt>
                            <dd class="col-sm-8">{{ $speaker->jabatan }}</dd>

                            <dt class="col-sm-4 text-muted">Perusahaan</dt>
                            <dd class="col-sm-8">{{ $speaker->perusahaan }}</dd>

                            <dt class="col-sm-4 text-muted">Urutan</dt>
                            <dd class="col-sm-8">{{ $speaker->urutan }}</dd>

                            <dt class="col-sm-4 text-muted">LinkedIn</dt>
                            <dd class="col-sm-8">
                                @if ($speaker->linkedin_url)
                                    <a href="{{ $speaker->linkedin_url }}" target="_blank" rel="noopener">
                                        {{ $speaker->linkedin_url }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </dd>

                            <dt class="col-sm-4 text-muted">Foto (raw)</dt>
                            <dd class="col-sm-8">
                                @if ($speaker->foto)
                                    <span class="text-muted">{{ $speaker->foto }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </dd>

                            <dt class="col-sm-4 text-muted">Dibuat</dt>
                            <dd class="col-sm-8">{{ $speaker->created_at?->format('d M Y H:i') ?? '-' }}</dd>

                            <dt class="col-sm-4 text-muted">Diperbarui</dt>
                            <dd class="col-sm-8">{{ $speaker->updated_at?->format('d M Y H:i') ?? '-' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

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
