@extends('peserta.partials.app')

@section('content')
    @php
        $imageUrl = $news->gambar_utama ? Storage::disk('public')->url($news->gambar_utama) : asset('assets/images/blog-image1.jpg');
        $author = $news->creator?->nama ?: 'Admin';
        $category = $news->category?->nama ?: 'Umum';
        $date = $news->published_at ?? $news->created_at;
    @endphp

    <section class="sub-banner-main-section event-banner-section w-100 float-left">
        <div class="container">
            <div class="sub-banner-inner-con">
                <h1 data-aos="fade-up" data-aos-duration="700">{{ $news->judul }}</h1>
                <p data-aos="fade-up" data-aos-duration="700">
                    {{ $category }} · {{ optional($date)->format('d M Y') }} · By {{ $author }}
                </p>
                <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-duration="700">
                    <ol class="breadcrumb d-inline-block mb-0">
                        <li class="breadcrumb-item d-inline-block"><a href="{{ route('peserta.index') }}">HOME</a></li>
                        <li class="breadcrumb-item d-inline-block"><a href="{{ route('peserta.blog') }}">BLOG</a></li>
                        <li class="breadcrumb-item active d-inline-block" aria-current="page">DETAIL</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>

    <section class="blog-posts blogpage-section w-100 float-left">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10">
                    <div class="blog-box blog-box1">
                        <figure class="blog-image mb-0">
                            <img src="{{ $imageUrl }}" alt="{{ $news->judul }}" class="img-fluid w-100">
                        </figure>
                        <div class="lower-portion">
                            <i class="fas fa-user"></i>
                            <span class="text-size-14 text-mr">By : {{ $author }}</span>
                            <i class="tag-mb fas fa-tag"></i>
                            <span class="text-size-14">{{ $category }}</span>
                            <h5 class="mt-2">{{ $news->judul }}</h5>
                        </div>
                        <div class="button-portion ">
                            <div class="date">
                                <i class="mb-0 calendar-ml fas fa-calendar-alt"></i>
                                <span class="mb-0 text-size-14">{{ optional($date)->format('M d, Y') }}</span>
                            </div>
                            <div class="button">
                                <a class="mb-0 read_more text-decoration-none" href="{{ route('peserta.blog') }}">Kembali</a>
                            </div>
                        </div>

                        <div class="p-4 p-md-5 news-content">
                            {!! $news->konten !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .news-content {
            color: #333333;
            line-height: 1.8;
            font-size: 16px;
        }

        .news-content h2,
        .news-content h3,
        .news-content h4,
        .news-content h5 {
            margin-top: 18px;
            margin-bottom: 10px;
            font-weight: 700;
            color: #000000;
        }

        .news-content p {
            margin-bottom: 14px;
        }

        .news-content ul,
        .news-content ol {
            padding-left: 22px;
            margin-bottom: 14px;
        }

        .news-content blockquote {
            padding: 14px 16px;
            margin: 16px 0;
            border-left: 4px solid rgba(248, 0, 0, 0.7);
            background: rgba(248, 0, 0, 0.06);
        }

        .news-content a {
            text-decoration: underline;
        }

        .news-content img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }
    </style>
@endsection

