@extends('peserta.partials.app')

@section('content')
    
    <!-- BANNER SECTION START -->
    @php
        $bannerTitle = trim(konten('about', 'banner', 'title'));
        $bannerTitle = $bannerTitle !== '' ? $bannerTitle : 'ABOUT US';
        $bannerDescription = trim(konten('about', 'banner', 'description'));
        $bannerDescription =
            $bannerDescription !== ''
                ? $bannerDescription
                : "Inspiring Talks, Meet the Best Product People Around the World,\nand Party Together After the Event!";
    @endphp
    <section class="sub-banner-main-section w-100 float-left">
        <div class="container">
            <div class="sub-banner-inner-con">
                <h1 data-aos="fade-up" data-aos-duration="700">{{ $bannerTitle }}</h1>
                <p data-aos="fade-up" data-aos-duration="700">{!! nl2br(e($bannerDescription)) !!}</p>
                <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-duration="700">
                    <ol class="breadcrumb d-inline-block mb-0">
                        <li class="breadcrumb-item d-inline-block"><a href="{{ route('peserta.index') }}">HOME</a></li>
                        <li class="breadcrumb-item active d-inline-block" aria-current="page">ABOUT</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>
    <!-- BANNER SECTION END -->


    <!-- ABOUT SECTION START-->
    @php
        $aboutSmallText = trim(konten('about', 'about', 'small_text'));
        $aboutSmallText = $aboutSmallText !== '' ? $aboutSmallText : 'ABOUT CONFX';

        $aboutTitle = trim(konten('about', 'about', 'title'));
        $aboutTitle = $aboutTitle !== '' ? $aboutTitle : 'Digital Innovations Conference';

        $aboutP1 = trim(konten('about', 'about', 'paragraph_1'));
        $aboutP1 =
            $aboutP1 !== ''
                ? $aboutP1
                : 'We direct our conference to specialists from financial and investment industry.';

        $aboutP2 = trim(konten('about', 'about', 'paragraph_2'));
        $aboutP2 =
            $aboutP2 !== ''
                ? $aboutP2
                : 'Thanks to the wide range of speakers, this is the most recognizable conference in the country.';

        $aboutButtonText = trim(konten('about', 'about', 'button_text'));
        $aboutButtonText = $aboutButtonText !== '' ? $aboutButtonText : 'VIEW SCHEDULE';
        $aboutButtonUrl = trim(konten('about', 'about', 'button_url'));
        $aboutButtonUrl = $aboutButtonUrl !== '' ? $aboutButtonUrl : route('peserta.event');

        $assetUrl = function (string $path, string $fallback) {
            $path = trim($path);
            if ($path === '') {
                $path = $fallback;
            }
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                return $path;
            }
            return asset($path);
        };

        $investmentValue = trim(konten('about', 'about', 'investment_value'));
        $investmentValue = $investmentValue !== '' ? $investmentValue : '1B+';
        $investmentLabel = trim(konten('about', 'about', 'investment_label'));
        $investmentLabel = $investmentLabel !== '' ? $investmentLabel : 'Investment Funds';

        $coImageUrl = $assetUrl(konten('about', 'about', 'co_image'), 'assets/images/co-image.jpg');
        $coName = trim(konten('about', 'about', 'co_name'));
        $coName = $coName !== '' ? $coName : 'EVE OWENS';
        $coRole = trim(konten('about', 'about', 'co_role'));
        $coRole = $coRole !== '' ? $coRole : 'CO';

        $ceoImageUrl = $assetUrl(konten('about', 'about', 'ceo_image'), 'assets/images/ceo-image.jpg');
        $ceoName = trim(konten('about', 'about', 'ceo_name'));
        $ceoName = $ceoName !== '' ? $ceoName : 'JHON SMITH';
        $ceoRole = trim(konten('about', 'about', 'ceo_role'));
        $ceoRole = $ceoRole !== '' ? $ceoRole : 'CEO';

        $attendeesValue = trim(konten('about', 'about', 'attendees_value'));
        $attendeesValue = $attendeesValue !== '' ? $attendeesValue : '2K+';
        $attendeesLabel = trim(konten('about', 'about', 'attendees_label'));
        $attendeesLabel = $attendeesLabel !== '' ? $attendeesLabel : 'Attendees';

        $c1Value = trim(konten('about', 'about', 'counter_1_value'));
        $c1Value = $c1Value !== '' ? $c1Value : '20';
        $c1Label = trim(konten('about', 'about', 'counter_1_label'));
        $c1Label = $c1Label !== '' ? $c1Label : 'Skilled Speakers';

        $c2Value = trim(konten('about', 'about', 'counter_2_value'));
        $c2Value = $c2Value !== '' ? $c2Value : '5';
        $c2Label = trim(konten('about', 'about', 'counter_2_label'));
        $c2Label = $c2Label !== '' ? $c2Label : 'Days Full of Inspiration';

        $c3Value = trim(konten('about', 'about', 'counter_3_value'));
        $c3Value = $c3Value !== '' ? $c3Value : '15';
        $c3Label = trim(konten('about', 'about', 'counter_3_label'));
        $c3Label = $c3Label !== '' ? $c3Label : 'Unique Workshops';

        $c4Value = trim(konten('about', 'about', 'counter_4_value'));
        $c4Value = $c4Value !== '' ? $c4Value : '2';
        $c4Suffix = trim(konten('about', 'about', 'counter_4_suffix'));
        $c4Suffix = $c4Suffix !== '' ? $c4Suffix : 'X';
        $c4Label = trim(konten('about', 'about', 'counter_4_label'));
        $c4Label = $c4Label !== '' ? $c4Label : 'Networking with Industry';
    @endphp
    <section class="about-main-section about-another-con w-100 float-left padding-top padding-bottom">
        <div class="container">
            <div class="about-inner-con">
                <div class="about-left-con">
                    <div class="generic-title">
                        <span class="small-text" data-aos="fade-up" data-aos-duration="700">{{ $aboutSmallText }}</span>
                        <h2 data-aos="fade-up" data-aos-duration="700">{{ $aboutTitle }}</h2>
                    </div>
                    <p data-aos="fade-up" data-aos-duration="700">{{ $aboutP1 }}</p>
                    <p data-aos="fade-up" data-aos-duration="700">{{ $aboutP2 }}</p>
                    <div class="generic-btn" data-aos="fade-up" data-aos-duration="700">
                        <a href="{{ $aboutButtonUrl }}">{{ $aboutButtonText }} <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="about-right-con">
                    <div class="co-box" data-aos="fade-up" data-aos-duration="700">
                        <div class="investment d-flex">
                            <span>{{ $investmentValue }}</span>
                            <small>{!! nl2br(e($investmentLabel)) !!}</small>
                        </div>
                        <div class="co-image-box">
                            <figure class="mb-0">
                                <img src="{{ $coImageUrl }}" alt="co-image">
                            </figure>
                            <div class="status">
                                {{ $coName }} - <span class="d-inline-block">{{ $coRole }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="ceo-box" data-aos="fade-up" data-aos-duration="700">
                        <div class="ceo-image-box">
                            <figure class="mb-0">
                                <img src="{{ $ceoImageUrl }}" alt="ceo-image">
                            </figure>
                            <div class="status">
                                {{ $ceoName }} - <span class="d-inline-block">{{ $ceoRole }}</span>
                            </div>
                        </div>
                        <div class="attendees align-items-center justify-content-center">
                            <div class="attendents">
                                <figure class="mb-0">
                                    <img src="{{ asset('assets/images/attendents-img.png') }}" alt="attendents-img">
                                </figure>
                                <figure class="mb-0">
                                    <img src="{{ asset('assets/images/attendents-img.png') }}" alt="attendents-img">
                                </figure>
                                <figure class="mb-0">
                                    <img src="{{ asset('assets/images/attendents-img.png') }}" alt="attendents-img">
                                </figure>
                                <figure class="mb-0">
                                    <img src="{{ asset('assets/images/attendents-img.png') }}" alt="attendents-img">
                                </figure>
                            </div>
                            <div class="numbers">
                                <span>{{ $attendeesValue }}</span>
                                <small>{{ $attendeesLabel }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="counter-sec">
                <ul class="list-unstyled mb-0" data-aos="fade-up" data-aos-duration="700">
                    <li class="position-relative">
                        <div class="count d-inline-block">{{ $c1Value }}</div>
                        <span class="d-block client-status">{{ $c1Label }}</span>
                    </li>
                    <li class="position-relative">
                        <div class="count d-inline-block">{{ $c2Value }}</div>
                        <span class="d-block client-status">{{ $c2Label }}</span>
                    </li>
                    <li class="position-relative">
                        <div class="count d-inline-block">{{ $c3Value }}</div>
                        <span class="d-block client-status">{{ $c3Label }}</span>
                    </li>
                    <li class="position-relative">
                        <div class="counter-box position-relative">
                            <div class="count d-inline-block">{{ $c4Value }}</div>
                            <div class="plus d-inline-block"><span>{{ $c4Suffix }}</span></div>
                        </div>
                        <span class="d-block client-status">{{ $c4Label }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </section>
    <!-- ABOUT SECTION END-->
   
    @include('peserta.partials.sponsors', [
        'page' => 'about',
        'wrapperClass' => 'sponsers-main-section about-sponsers w-100 float-left',
    ])

    @php
        $regSmallText = trim(konten('about', 'registration', 'small_text'));
        $regSmallText = $regSmallText !== '' ? $regSmallText : 'JOIN THE CONFX';
        $regTitle = trim(konten('about', 'registration', 'title'));
        $regTitle = $regTitle !== '' ? $regTitle : 'The Biggest Conference of the Year';
        $regButtonText = trim(konten('about', 'registration', 'button_text'));
        $regButtonText = $regButtonText !== '' ? $regButtonText : 'REGISTER FOR FREE';
        $regButtonUrl = trim(konten('about', 'registration', 'button_url'));
        $regButtonUrl = $regButtonUrl !== '' ? $regButtonUrl : route('peserta.contact');
    @endphp
    <section class="registration-main-section w-100 float-left">
        <div class="container">
            <div class="registration-inner-con">
                <div class="generic-title mb-0">
                    <span class="small-text" data-aos="fade-up" data-aos-duration="700">{{ $regSmallText }}</span>
                    <h2 data-aos="fade-up" data-aos-duration="700">{{ $regTitle }}</h2>
                    <div class="generic-btn" data-aos="fade-up" data-aos-duration="700">
                        <a href="{{ $regButtonUrl }}">{{ $regButtonText }} <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- REGISTRATION SECTION END -->
@endsection
