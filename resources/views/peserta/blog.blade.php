@extends('peserta.partials.app')

@section('content')
    @php
        $items = $news ?? collect();
    @endphp
    <!-- BANNER SECTION START -->
    <section class="sub-banner-main-section event-banner-section w-100 float-left">
        <div class="container">
            <div class="sub-banner-inner-con">
                <h1 data-aos="fade-up" data-aos-duration="700">BLOG</h1>
                <p data-aos="fade-up" data-aos-duration="700">Berita terbaru seputar event, materi, dan update penting,
                    <br> and Party Together After the Event!</p>
                <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-duration="700">
                    <ol class="breadcrumb d-inline-block mb-0">
                        <li class="breadcrumb-item d-inline-block"><a href="{{ route('peserta.index') }}">HOME</a></li>
                        <li class="breadcrumb-item active d-inline-block" aria-current="page">BLOG</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>
    <!-- BANNER SECTION END -->
    <!-- MAIN SECTION -->
    <!--End Slider Section-->
    <section class="blog-posts blogpage-section w-100 float-left">
        <div class="container">
            <div class="row wow fadeInUp" style="visibility: visible; animation-name: fadeInUp;">
                <div id="blog" class="col-xl-12">
                    <!-- threecolumn-blog  -->
                    <div class="row">
                        @forelse ($items as $row)
                            @php
                                $gambar = (string) ($row->gambar_utama ?? '');
                                if ($gambar === '') {
                                    $imageUrl = asset('assets/images/blog-image1.jpg');
                                } elseif (str_starts_with($gambar, 'http://') || str_starts_with($gambar, 'https://')) {
                                    $imageUrl = $gambar;
                                } elseif (str_starts_with($gambar, 'storage/')) {
                                    $imageUrl = asset($gambar);
                                } else {
                                    $imageUrl = asset('storage/'.$gambar);
                                }
                                $author = $row->creator?->nama ?: 'Admin';
                                $category = $row->category?->nama ?: 'Umum';
                                $date = $row->published_at ?? $row->created_at;
                            @endphp
                            <div class="col-lg-4 col-md-6 col-sm-6 col-12" data-aos="fade-up" data-aos-duration="700">
                                <div class="blog-box blog-box1">
                                    <figure class="blog-image mb-0">
                                        <img src="{{ $imageUrl }}" alt="{{ $row->judul }}" class="img-fluid">
                                    </figure>
                                    <div class="lower-portion">
                                        <i class="fas fa-user"></i>
                                        <span class="text-size-14 text-mr">By : {{ $author }}</span>
                                        <i class="tag-mb fas fa-tag"></i>
                                        <span class="text-size-14">{{ $category }}</span>
                                        <h5>{{ $row->judul }}</h5>
                                    </div>
                                    <div class="button-portion ">
                                        <div class="date">
                                            <i class="mb-0 calendar-ml fas fa-calendar-alt"></i>
                                            <span class="mb-0 text-size-14">{{ optional($date)->format('M d, Y') }}</span>
                                        </div>
                                        <div class="button">
                                            <a class="mb-0 read_more text-decoration-none" href="{{ route('peserta.blog.show', $row->slug) }}">Read More</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center text-muted py-5">Belum ada berita yang dipublikasikan.</div>
                            </div>
                        @endforelse
                    </div>
                    @if ($items instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div class="d-flex justify-content-center mt-4">
                            {{ $items->onEachSide(1)->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

@endsection
