@extends('admin.partials.app')

@section('content')
    @php
        $query = request()->query();
        $sortState = [
            'sort' => $sort ?? (string) request()->query('sort', 'id'),
            'dir' => $dir ?? (string) request()->query('dir', 'desc'),
        ];

        $sortUrl = function (string $column) use ($query, $sortState) {
            $isActive = ($sortState['sort'] === $column);
            $nextDir = $isActive && strtolower($sortState['dir']) === 'asc' ? 'desc' : 'asc';
            return url()->current() . '?' . http_build_query(array_merge($query, [
                'sort' => $column,
                'dir' => $nextDir,
            ]));
        };

        $sortIndicator = function (string $column) use ($sortState) {
            if ($sortState['sort'] !== $column) {
                return '';
            }
            return strtolower($sortState['dir']) === 'asc' ? '↑' : '↓';
        };

        $statusOptions = [
            '' => 'Semua status',
            'aktif' => 'Aktif',
            'nonaktif' => 'Nonaktif',
        ];

        $statusBadge = function (?string $status) {
            $status = strtolower((string) $status);
            return match ($status) {
                'aktif' => ['bg-success', 'Aktif'],
                'nonaktif' => ['bg-secondary', 'Nonaktif'],
                default => ['bg-light text-dark', $status ?: '-'],
            };
        };
    @endphp

    <main id="main-content" tabindex="-1" class="pb-4" role="main" aria-label="Manajemen peserta">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div class="me-auto">
                <h1 class="h3 mb-1">List Peserta</h1>
                <div class="text-muted">Pantau status akun dan akses event peserta.</div>
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

        <section class="mb-4" aria-label="Ringkasan peserta">
            <div class="row">
                <div class="col-12 col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="text-muted">Total peserta</div>
                            <div class="fs-30 fw-semibold text-black">{{ $totalCount ?? 0 }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="text-muted">Akun aktif</div>
                            <div class="fs-30 fw-semibold text-black">{{ $aktifCount ?? 0 }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="text-muted">Akun nonaktif</div>
                            <div class="fs-30 fw-semibold text-black">{{ $nonaktifCount ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mb-4" aria-label="Filter peserta">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.peserta.index') }}" class="row g-3 align-items-end">
                        <div class="col-12 col-lg-6">
                            <label class="form-label text-black" for="filter-q">Cari</label>
                            <input
                                id="filter-q"
                                name="q"
                                type="search"
                                class="form-control"
                                placeholder="Nama atau email…"
                                value="{{ $q ?? request('q') }}"
                                autocomplete="off"
                            >
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label text-black" for="filter-status">Status akun</label>
                            <select id="filter-status" name="status" class="form-select">
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(($status ?? request('status')) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-lg-2 d-grid">
                            <button type="submit" class="btn btn-primary">Terapkan</button>
                        </div>
                        <div class="col-12 col-lg-1 d-grid">
                            <a class="btn btn-outline-secondary" href="{{ route('admin.peserta.index') }}">Reset</a>
                        </div>

                        <input type="hidden" name="sort" value="{{ $sortState['sort'] }}">
                        <input type="hidden" name="dir" value="{{ $sortState['dir'] }}">
                    </form>
                </div>
            </div>
        </section>

        <section aria-label="Daftar peserta">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h2 class="h6 mb-0">Peserta</h2>
                            <div class="text-muted">Total hasil: {{ $users->total() }}</div>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" aria-label="Tabel peserta">
                            <thead>
                                <tr>
                                    <th scope="col">
                                        <a class="text-black text-decoration-none" href="{{ $sortUrl('id') }}">
                                            ID {{ $sortIndicator('id') }}
                                        </a>
                                    </th>
                                    <th scope="col">
                                        <a class="text-black text-decoration-none" href="{{ $sortUrl('nama') }}">
                                            Nama {{ $sortIndicator('nama') }}
                                        </a>
                                    </th>
                                    <th scope="col">
                                        <a class="text-black text-decoration-none" href="{{ $sortUrl('email') }}">
                                            Email {{ $sortIndicator('email') }}
                                        </a>
                                    </th>
                                    <th scope="col" class="text-nowrap">
                                        <a class="text-black text-decoration-none" href="{{ $sortUrl('status_akun') }}">
                                            Status {{ $sortIndicator('status_akun') }}
                                        </a>
                                    </th>
                                    <th scope="col" class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    @php([$badgeClass, $badgeLabel] = $statusBadge($user->status_akun))
                                    <tr>
                                        <td class="text-black fw-semibold">{{ $user->id }}</td>
                                        <td class="text-black fw-semibold">{{ $user->nama }}</td>
                                        <td class="text-muted">{{ $user->email }}</td>
                                        <td>
                                            <span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                                        </td>
                                        <td class="text-end">
                                            <a
                                                class="btn btn-outline-primary btn-sm"
                                                href="{{ route('admin.peserta.show', $user) }}"
                                                title="Lihat detail"
                                            >
                                                <i class="la la-eye" aria-hidden="true"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Belum ada peserta.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 d-flex justify-content-end">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
