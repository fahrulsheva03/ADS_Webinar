@extends('admin.partials.app')

@section('content')
    @php
        $userBadge = function (?string $status) {
            $status = strtolower((string) $status);
            return match ($status) {
                'aktif' => ['bg-success', 'Aktif'],
                'nonaktif' => ['bg-secondary', 'Nonaktif'],
                default => ['bg-light text-dark', $status ?: '-'],
            };
        };

        $eventBadge = function (?string $status) {
            $status = strtolower((string) $status);
            return match ($status) {
                'active' => ['bg-success', 'Active'],
                'published' => ['bg-primary', 'Published'],
                'draft' => ['bg-warning text-dark', 'Draft'],
                'finished' => ['bg-secondary', 'Finished'],
                default => ['bg-light text-dark', $status ?: '-'],
            };
        };
    @endphp

    <main id="main-content" tabindex="-1" class="pb-4" role="main" aria-label="Detail peserta">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div class="me-auto">
                <h1 class="h3 mb-1">Detail Peserta</h1>
                <div class="text-muted">Informasi akun dan event yang diikuti.</div>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.peserta.index') }}">
                    <i class="la la-arrow-left" aria-hidden="true"></i>
                    <span class="ms-1">Kembali</span>
                </a>
            </div>
        </div>

        <section class="mb-4" aria-label="Ringkasan peserta">
            <div class="row">
                <div class="col-12 col-lg-5 mb-3">
                    @php([$badgeClass, $badgeLabel] = $userBadge($user->status_akun))
                    <div class="card h-100">
                        <div class="card-header border-0 pb-0">
                            <h2 class="h6 mb-0">Profil</h2>
                        </div>
                        <div class="card-body pt-3">
                            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                                <div>
                                    <div class="fs-18 fw-semibold text-black">{{ $user->nama }}</div>
                                    <div class="text-muted">{{ $user->email }}</div>
                                </div>
                                <span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                            </div>
                            <hr class="my-3">
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <div class="text-muted">User ID</div>
                                    <div class="text-black fw-semibold">{{ $user->id }}</div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="text-muted">Role</div>
                                    <div class="text-black fw-semibold">{{ $user->role }}</div>
                                </div>
                                <div class="col-12">
                                    <div class="text-muted">Dibuat</div>
                                    <div class="text-black fw-semibold">{{ optional($user->created_at)->format('d M Y H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-7 mb-3">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="text-muted">Event diikuti</div>
                                    <div class="fs-30 fw-semibold text-black">{{ $totalEvents ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="text-muted">Transaksi paid</div>
                                    <div class="fs-30 fw-semibold text-black">{{ $totalPaid ?? 0 }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                        <div class="fw-semibold text-black">Status event peserta</div>
                                        <div class="text-muted">Berdasarkan transaksi paid.</div>
                                    </div>
                                    <div class="mt-3 text-muted small">
                                        Jika peserta belum memiliki transaksi paid, maka dianggap belum mengikuti event apa pun.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section aria-label="Event yang diikuti">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h2 class="h6 mb-0">Event yang Diikuti</h2>
                            <div class="text-muted">Ringkasan event dari transaksi paid.</div>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-3">
                    @if (($eventRows ?? collect())->isEmpty())
                        <div class="border rounded p-4 bg-light">
                            <div class="d-flex align-items-start gap-3">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-secondary">
                                        <i class="la la-info-circle" aria-hidden="true"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="fw-semibold text-black">Belum mengikuti event</div>
                                    <div class="text-muted">Peserta ini belum memiliki transaksi paid untuk event mana pun.</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" aria-label="Tabel event peserta">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Event</th>
                                        <th scope="col" class="text-nowrap">Tanggal</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Paket</th>
                                        <th scope="col" class="text-nowrap">Terakhir bayar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($eventRows as $row)
                                        @php
                                            $event = $row['event'];
                                            [$badgeClass, $badgeLabel] = $eventBadge($event->status ?? null);
                                            $paketNames = $row['paket_names'] ?? collect();
                                        @endphp
                                        <tr>
                                            <td class="text-black fw-semibold">{{ $event->id }}</td>
                                            <td>
                                                <div class="text-black fw-semibold">{{ $event->judul }}</div>
                                                @if (!empty($event->lokasi))
                                                    <div class="text-muted small">{{ $event->lokasi }}</div>
                                                @endif
                                            </td>
                                            <td class="text-muted text-nowrap">
                                                {{ optional($event->tanggal_mulai)->format('d M Y') }} – {{ optional($event->tanggal_selesai)->format('d M Y') }}
                                            </td>
                                            <td><span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span></td>
                                            <td class="text-muted">
                                                @if ($paketNames->isEmpty())
                                                    -
                                                @else
                                                    {{ $paketNames->join(', ') }}
                                                @endif
                                            </td>
                                            <td class="text-muted text-nowrap">
                                                {{ $row['last_paid_at'] ? \Carbon\Carbon::parse($row['last_paid_at'])->format('d M Y H:i') : '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </main>
@endsection
