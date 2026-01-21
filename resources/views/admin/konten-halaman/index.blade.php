@extends('admin.partials.app')

@section('content')
    <main id="main-content" tabindex="-1" class="pb-4" role="main" aria-label="Konten halaman {{ $page ?? '' }}">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Konten Halaman</li>
            </ol>
        </nav>

        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div class="me-auto">
                <h1 class="h3 mb-1">Konten Halaman</h1>
                <div class="text-muted">Kelola konten dinamis untuk halaman peserta ({{ $page ?? '-' }}).</div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="fw-semibold">Sukses</div>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <div class="fw-semibold">Validasi gagal</div>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.konten-halaman.home.update') }}">
            @csrf

            @foreach (($fieldsBySection ?? collect()) as $section => $fields)
                <section class="mb-4" aria-label="Section {{ $section }}">
                    <div class="card">
                        <div class="card-header border-0 pb-0">
                            <h2 class="h5 mb-0 text-capitalize">{{ str_replace('_', ' ', $section) }}</h2>
                        </div>
                        <div class="card-body pt-3">
                            <div class="row g-3">
                                @foreach ($fields as $field)
                                    @php
                                        $key = $field['key'];
                                        $name = "contents[{$section}][{$key}]";
                                        $oldKey = "contents.{$section}.{$key}";
                                        $val = old($oldKey, $values[$section][$key] ?? '');
                                    @endphp
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label text-black" for="{{ $section }}-{{ $key }}">
                                            {{ $field['label'] }}
                                        </label>
                                        <textarea
                                            id="{{ $section }}-{{ $key }}"
                                            name="{{ $name }}"
                                            class="form-control"
                                            rows="4"
                                        >{{ $val }}</textarea>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>
            @endforeach

            <div class="d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </main>
@endsection

