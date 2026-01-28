@extends('peserta.partials.app')

@section('content')
    <section class="index3-banner-section w-100 float-left">
        <div class="container-fluid">
            <div class="index3-banner-outer-con" data-aos="fade-up" data-aos-duration="700">
                <div id="owl-carouselone" class="owl-carousel owl-theme">
                    <div class="item">
                        <div class="index3-banner-inner-con">
                            <div class="index3-banner-img-con">
                                <figure class="mb-0">
                                    <img src="{{ asset(konten('home', 'banner', 'slide_1_image')) }}" alt="">
                                </figure>
                            </div>
                            <div class="index3-banner-text-con">
                                <span
                                    class="d-block position-relative">{{ konten('home', 'banner', 'slide_1_date') }}</span>
                                <h1>{!! nl2br(e(konten('home', 'banner', 'slide_1_title'))) !!}</h1>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-map-marker-alt"></i>
                                        {{ konten('home', 'banner', 'slide_1_location') }}</li>
                                </ul>
                                <div class="generic-btn">
                                    <a href="{{ konten('home', 'banner', 'slide_1_button_url') }}">{{ konten('home', 'banner', 'slide_1_button_text') }}
                                        <i class="fas fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- BANNER SECTION END -->
    <!-- JOURNEY SECTION START -->
    <section class="journey-section w-100 float-left padding-top padding-bottom grey-bg position-relative">
        <div class="container">
            <div class="journey-inner-con">
                <div class="journey-text-con">
                    <h2 data-aos="fade-up" data-aos-duration="700">{!! nl2br(e(konten('home', 'journey', 'title'))) !!}</h2>
                    <p data-aos="fade-up" data-aos-duration="700">{{ konten('home', 'journey', 'description') }}</p>
                    <div class="generic-btn" data-aos="fade-up" data-aos-duration="700">
                        <a href="{{ konten('home', 'journey', 'button_url') }}">{{ konten('home', 'journey', 'button_text') }}
                            <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="journey-video-con d-inline-block">
                    <div class="video-inner-con">
                        <div class="learning-conference-video-box" data-aos="fade-up" data-aos-duration="700">
                            <figure class="mb-0 d-inline-block">
                                <img src="{{ asset(konten('home', 'journey', 'video_bg_image')) }}" alt="">
                            </figure>
                            <div class="video-play-icon d-inline-block">
                                <a href="javascript:void(0)" onclick="lightbox_open();">
                                    <figure class="mb-0 d-inline-block">
                                        <img src="{{ asset('assets/images/video-play-icon2.png') }}" alt="">
                                    </figure>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div id="light">
                        <a class="boxclose" id="boxclose" onclick="lightbox_close();"></a>
                        <div id="VisaChipCardVideo" width="600" controls>
                            <iframe width="560" height="315" src="{{ konten('home', 'journey', 'video_url') }}"
                                title="YouTube video player" frameborder="0"></iframe>
                            <!--Browser does not support <video> tag -->
                        </div>
                    </div>

                    <div id="fade" onClick="lightbox_close();"></div>
                </div>
            </div>
            <div class="digital-conference-section">
                <div class="digital-conference-img-con" data-aos="fade-up" data-aos-duration="700">
                    <figure class="mb-0 position-relative">
                        <img src="{{ asset(konten('home', 'journey', 'digital_image')) }}" alt="">
                    </figure>
                </div>
                <div class="digital-counter-con">
                    <ul class="list-unstyled mb-0" data-aos="fade-up" data-aos-duration="700">
                        <li class="position-relative">
                            <div class="count d-inline-block">{{ konten('home', 'journey', 'counter_1_value') }}</div>
                            <div class="plus-details">
                                <div class="plus d-inline-block"><span>+</span></div>
                                <span
                                    class="d-block client-status">{{ konten('home', 'journey', 'counter_1_label') }}</span>
                            </div>
                        </li>
                        <li class="position-relative">
                            <div class="count d-inline-block">{{ konten('home', 'journey', 'counter_2_value') }}</div>
                            <div class="plus-details">
                                <div class="plus d-inline-block"><span>+</span></div>
                                <span
                                    class="d-block client-status">{{ konten('home', 'journey', 'counter_2_label') }}</span>
                            </div>
                        </li>
                        <li class="position-relative">
                            <div class="count d-inline-block">{{ konten('home', 'journey', 'counter_3_value') }}</div>
                            <div class="plus-details">
                                <div class="plus d-inline-block"><span>+</span></div>
                                <span
                                    class="d-block client-status">{{ konten('home', 'journey', 'counter_3_label') }}</span>
                            </div>
                        </li>
                        <li class="position-relative">
                            <div class="counter-box position-relative">
                                <div class="2k-con">
                                    <div class="count d-inline-block">{{ konten('home', 'journey', 'counter_4_value') }}
                                    </div>
                                    <small
                                        class="d-inline-block">{{ konten('home', 'journey', 'counter_4_suffix') }}</small>
                                </div>
                                <div class="plus-details">
                                    <div class="plus d-inline-block"><span>+</span></div>
                                    <span
                                        class="d-block client-status">{{ konten('home', 'journey', 'counter_4_label') }}</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div class="digital-text-con">
                        <h3 data-aos="fade-up" data-aos-duration="700">{!! nl2br(e(konten('home', 'journey', 'digital_title'))) !!} <span
                                class="d-inline-block">{{ konten('home', 'journey', 'digital_badge') }}</span></h3>
                        <div class="generic-btn" data-aos="fade-up" data-aos-duration="700">
                            <a href="{{ konten('home', 'journey', 'digital_button_url') }}">{{ konten('home', 'journey', 'digital_button_text') }}
                                <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- JOURNEY SECTION END -->


    <!-- SPEAKERS SECTION START -->
    <section class="speakers-main-section w-100 float-left padding-top padding-bottom">
        <div class="container-fluid">
            <div class="generic-title text-center">
                <span class="small-text" data-aos="fade-up" data-aos-duration="700">WORLD BEST SPEAKERS</span>
                <h2 data-aos="fade-up" data-aos-duration="700">Meet Our Amazing Speakers</h2>
                <p data-aos="fade-up" data-aos-duration="700">8+ Inspiring Talks, Meet the Best Product People Around the
                    World, and <br> Party Together After the
                    Event!</p>
            </div>
            <div class="speakers-inner-sec" data-aos="fade-up" data-aos-duration="700">
                @forelse (($speakers ?? collect()) as $speaker)
                    <div class="speaker-box position-relative">
                        <a href="{{ $speaker->linkedin_url ?: '#' }}"
                            @if ($speaker->linkedin_url) target="_blank" rel="noopener" @endif>
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <figure class="mb-0">
                            <img src="{{ $speaker->foto_url ?: asset('assets/images/speakers-img1.jpg') }}"
                                alt="{{ $speaker->nama }}">
                        </figure>
                        <div class="speaker-status">
                            <button class="showBtn{{ $loop->iteration }}">{{ $speaker->nama }} <i
                                    class="fas fa-angle-up"></i></button>
                            <div class="staus-con data{{ $loop->iteration }}">
                                <span>{{ $speaker->jabatan }} - {{ $speaker->perusahaan }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center mb-0">Belum ada speaker.</p>
                @endforelse
            </div>
            {{-- <div class="generic-btn text-center" data-aos="fade-up" data-aos-duration="700">
                <a href="speaker.html">VIEW ALL SPEAKERS <i class="fas fa-arrow-right"></i></a>
            </div> --}}
        </div>
    </section>
    <!-- SPEAKERS SECTION END -->

    <!--  PRICING PLANS SECTION START -->
    @php
        $pricingTitle = konten('home', 'pricing', 'title');
        $pricingTitle = $pricingTitle !== '' ? $pricingTitle : "We Have Several Options\nfor Tickets";

        $currencySymbolByCode = [
            'USD' => '$',
            'IDR' => 'Rp',
            'EUR' => '€',
        ];

        $silverCurrencyCode = konten('home', 'pricing', 'silver_currency') ?: 'USD';
        $goldCurrencyCode = konten('home', 'pricing', 'gold_currency') ?: 'USD';
        $premiumCurrencyCode = konten('home', 'pricing', 'premium_currency') ?: 'USD';

        $silverCurrencySymbol = $currencySymbolByCode[$silverCurrencyCode] ?? '$';
        $goldCurrencySymbol = $currencySymbolByCode[$goldCurrencyCode] ?? '$';
        $premiumCurrencySymbol = $currencySymbolByCode[$premiumCurrencyCode] ?? '$';

        $silverActiveRaw = konten('home', 'pricing', 'silver_active');
        $goldActiveRaw = konten('home', 'pricing', 'gold_active');
        $premiumActiveRaw = konten('home', 'pricing', 'premium_active');

        $silverIsActive = $silverActiveRaw === '' ? true : $silverActiveRaw !== '0';
        $goldIsActive = $goldActiveRaw === '' ? true : $goldActiveRaw !== '0';
        $premiumIsActive = $premiumActiveRaw === '' ? true : $premiumActiveRaw !== '0';

        $silverFeaturesText = konten('home', 'pricing', 'silver_features');
        $silverFeatures =
            $silverFeaturesText !== ''
                ? preg_split("/\r\n|\r|\n/", trim($silverFeaturesText))
                : ['Full Access the Conference', 'Music, Launch and Snack', 'Meet Event Speaker'];

        $goldFeaturesText = konten('home', 'pricing', 'gold_features');
        $goldFeatures =
            $goldFeaturesText !== ''
                ? preg_split("/\r\n|\r|\n/", trim($goldFeaturesText))
                : ['Full Access the Conference', 'Music, Launch and Snack', 'Meet Event Speaker'];

        $premiumFeaturesText = konten('home', 'pricing', 'premium_features');
        $premiumFeatures =
            $premiumFeaturesText !== ''
                ? preg_split("/\r\n|\r|\n/", trim($premiumFeaturesText))
                : ['Full Access the Conference', 'Music, Launch and Snack', 'Meet Event Speaker'];

        $goldBadge = trim(konten('home', 'pricing', 'gold_badge'));
    @endphp
    <section id="pricing" class="index3-pricing-plans-section w-100 float-left padding-top padding-bottom">
        <div class="container">
            <div class="generic-title2 text-center">
                <span class="small-text" data-aos="fade-up"
                    data-aos-duration="700">{{ konten('home', 'pricing', 'small_text') ?: 'TICKET PRICING' }}</span>
                <h2 data-aos="fade-up" data-aos-duration="700">{!! nl2br(e($pricingTitle)) !!}</h2>
            </div>
            <div class="index3-plan-inner-con">
                @if ($silverIsActive)
                    <div class="ticket-details silver-ticket-details" data-aos="fade-up" data-aos-duration="700">
                        <h3>{{ konten('home', 'pricing', 'silver_title') ?: 'Silver' }}</h3>
                        <p>{{ konten('home', 'pricing', 'silver_subtitle') ?: 'For individuals' }}</p>
                        <span>Starting at:</span>
                        <div class="price">
                            <small>{{ $silverCurrencySymbol }}</small>{{ konten('home', 'pricing', 'silver_price') ?: '29' }}
                        </div>
                        <ul class="list-unstyled">
                            @foreach ($silverFeatures as $feature)
                                @if (trim((string) $feature) !== '')
                                    <li class="position-relative">{{ $feature }}</li>
                                @endif
                            @endforeach
                        </ul>
                        <div class="generic-btn">
                            <a href="{{ konten('home', 'pricing', 'silver_button_url') ?: 'shop.html' }}">{{ konten('home', 'pricing', 'silver_button_text') ?: 'BUY TICKET' }}
                                <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                @endif

                @if ($goldIsActive)
                    <div class="ticket-details gold-ticket-details" data-aos="fade-up" data-aos-duration="700">
                        <h3>{{ konten('home', 'pricing', 'gold_title') ?: 'Gold' }}</h3>
                        <p>{{ konten('home', 'pricing', 'gold_subtitle') ?: 'For individuals' }}</p>
                        <span>Starting at:</span>
                        <div class="price">
                            <small>{{ $goldCurrencySymbol }}</small>{{ konten('home', 'pricing', 'gold_price') ?: '45' }}
                        </div>
                        <ul class="list-unstyled">
                            @foreach ($goldFeatures as $feature)
                                @if (trim((string) $feature) !== '')
                                    <li class="position-relative">{{ $feature }}</li>
                                @endif
                            @endforeach
                        </ul>
                        <div class="generic-btn">
                            <a href="{{ konten('home', 'pricing', 'gold_button_url') ?: 'shop.html' }}">{{ konten('home', 'pricing', 'gold_button_text') ?: 'BUY TICKET' }}
                                <i class="fas fa-arrow-right"></i></a>
                        </div>
                        @if ($goldBadge !== '')
                            <div class="recomended-box">
                                {{ $goldBadge }}
                            </div>
                        @endif
                    </div>
                @endif

                @if ($premiumIsActive)
                    <div class="ticket-details premium-ticket-details" data-aos="fade-up" data-aos-duration="700">
                        <h3>{{ konten('home', 'pricing', 'premium_title') ?: 'Premium' }}</h3>
                        <p>{{ konten('home', 'pricing', 'premium_subtitle') ?: 'For individuals' }}</p>
                        <span>Starting at:</span>
                        <div class="price">
                            <small>{{ $premiumCurrencySymbol }}</small>{{ konten('home', 'pricing', 'premium_price') ?: '59' }}
                        </div>
                        <ul class="list-unstyled">
                            @foreach ($premiumFeatures as $feature)
                                @if (trim((string) $feature) !== '')
                                    <li class="position-relative">{{ $feature }}</li>
                                @endif
                            @endforeach
                        </ul>
                        <div class="generic-btn">
                            <a href="{{ konten('home', 'pricing', 'premium_button_url') ?: 'shop.html' }}">{{ konten('home', 'pricing', 'premium_button_text') ?: 'BUY TICKET' }}
                                <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                @endif
            </div>
            <div class="index3-plan-btn text-center">
                <p data-aos="fade-up" data-aos-duration="700">
                    {{ konten('home', 'pricing', 'bottom_text') ?: 'This is a Detailed List Event of Conference for Digital Technology 2024.' }}
                </p>
                <div class="generic-btn" data-aos="fade-up" data-aos-duration="700">
                    <a href="{{ konten('home', 'pricing', 'bottom_button_url') ?: 'pricing.html' }}">{{ konten('home', 'pricing', 'bottom_button_text') ?: 'GET VIP PASS' }}
                        <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </section>
    <!--  PRICING PLANS SECTION END -->

    <!--  REGISTRATION SECTION START -->
    @php
        $registrationImage = konten('home', 'registration', 'image');
        $registrationImageUrl =
            $registrationImage !== ''
                ? asset($registrationImage)
                : asset('assets/images/index3-registration-right-img.png');
        $registrationTitle = konten('home', 'registration', 'title');
        $registrationTitle = $registrationTitle !== '' ? $registrationTitle : "Join the Biggest\nConf-2024 of The Year";
        $registrationDescription = konten('home', 'registration', 'description');
        $registrationDescription =
            $registrationDescription !== ''
                ? $registrationDescription
                : 'Hear Highlights From Our Sponsors, or Get a Lite or Core Subscription to Watch the Full Main Stage Event on Demand.';
    @endphp
    <section id="registration" class="index3-registration-section w-100 float-left">
        <div class="container">
            <div class="index3-registration-inner-con">
                <div class="index3-registration-left-con">
                    <h2 data-aos="fade-up" data-aos-duration="700">{!! nl2br(e($registrationTitle)) !!}</h2>
                    <p data-aos="fade-up" data-aos-duration="700">{{ $registrationDescription }}</p>
                    <div class="generic-btn" data-aos="fade-up" data-aos-duration="700">
                        <a href="{{ konten('home', 'registration', 'button_url') ?: 'contact.html' }}">{{ konten('home', 'registration', 'button_text') ?: 'REGISTER NOW' }}
                            <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="index3-registration-right-con" data-aos="fade-up" data-aos-duration="700">
                    <figure class="mb-0">
                        <img src="{{ $registrationImageUrl }}" alt="index3-registration-right-img">
                    </figure>
                </div>
            </div>
        </div>
    </section>
    <!--  REGISTRATION SECTION END -->

    <!-- FAQ SECTION START -->
    @php
        $faqQ1 = konten('home', 'faq', 'q1') ?: 'What is the design process for branding?';
        $faqA1 =
            konten('home', 'faq', 'a1') ?:
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodLorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
        $faqQ2 = konten('home', 'faq', 'q2') ?: 'How much does logo design services cost?';
        $faqA2 =
            konten('home', 'faq', 'a2') ?:
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodLorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
        $faqQ3 = konten('home', 'faq', 'q3') ?: 'What is the process for a website redesign?';
        $faqA3 =
            konten('home', 'faq', 'a3') ?:
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodLorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
        $faqQ4 = konten('home', 'faq', 'q4') ?: 'What is a content strategy?';
        $faqA4 =
            konten('home', 'faq', 'a4') ?:
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodLorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
        $faqQ5 = konten('home', 'faq', 'q5') ?: 'How much does website design cost?';
        $faqA5 =
            konten('home', 'faq', 'a5') ?:
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodLorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
    @endphp
    <section id="faq"
        class="faq-main-section w-100 float-left padding-top padding-bottom position-relative light-bg">
        <div class="container">
            <div class="generic-title text-center">
                <span class="small-text" data-aos="fade-up"
                    data-aos-duration="700">{{ konten('home', 'faq', 'small_text') ?: 'FAQ' }}</span>
                <h2 data-aos="fade-up" data-aos-duration="700">
                    {{ konten('home', 'faq', 'title') ?: 'Frequently Asked Questions' }}</h2>
            </div>
            <div class="faq-inner-section">
                <div id="accordion">
                    <div class="card" data-aos="fade-up" data-aos-duration="700">
                        <div class="card-header" id="headingOne">
                            <h5 class="mb-0">
                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne"
                                    aria-expanded="true" aria-controls="collapseOne">
                                    {{ $faqQ1 }}
                                </button>
                            </h5>
                        </div>

                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne"
                            data-parent="#accordion">
                            <div class="card-body">{!! nl2br(e($faqA1)) !!}</div>
                        </div>
                    </div>
                    <div class="card" data-aos="fade-up" data-aos-duration="700">
                        <div class="card-header" id="headingTwo">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo"
                                    aria-expanded="false" aria-controls="collapseTwo">
                                    {{ $faqQ2 }}
                                </button>
                            </h5>
                        </div>
                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                            <div class="card-body">{!! nl2br(e($faqA2)) !!}</div>
                        </div>
                    </div>
                    <div class="card" data-aos="fade-up" data-aos-duration="700">
                        <div class="card-header" id="headingThree">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse"
                                    data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    {{ $faqQ3 }}
                                </button>
                            </h5>
                        </div>
                        <div id="collapseThree" class="collapse" aria-labelledby="headingThree"
                            data-parent="#accordion">
                            <div class="card-body">{!! nl2br(e($faqA3)) !!}</div>
                        </div>
                    </div>
                    <div class="card" data-aos="fade-up" data-aos-duration="700">
                        <div class="card-header" id="headingfour">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapsefour"
                                    aria-expanded="false" aria-controls="collapsefour">
                                    {{ $faqQ4 }}
                                </button>
                            </h5>
                        </div>
                        <div id="collapsefour" class="collapse" aria-labelledby="headingfour" data-parent="#accordion">
                            <div class="card-body">{!! nl2br(e($faqA4)) !!}</div>
                        </div>
                    </div>
                    <div class="card" data-aos="fade-up" data-aos-duration="700">
                        <div class="card-header" id="headingfive">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapsefive"
                                    aria-expanded="false" aria-controls="collapsefive">
                                    {{ $faqQ5 }}
                                </button>
                            </h5>
                        </div>
                        <div id="collapsefive" class="collapse" aria-labelledby="headingfive" data-parent="#accordion">
                            <div class="card-body">{!! nl2br(e($faqA5)) !!}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- FAQ SECTION END -->

    <!--  BLOG SECTION START -->
    <section class="blog-main-section index3-blog-section w-100 float-left padding-top position-relative">
        <div class="container">
            <div class="generic-title2 text-center">
                <span class="small-text" data-aos="fade-up" data-aos-duration="700">LATES NEWS</span>
                <h2 class="mb-0" data-aos="fade-up" data-aos-duration="700">Recent News Articles</h2>
            </div>
            <div class="blogs-inner-con">
                @php
                    $items = $latestNews ?? collect();
                @endphp

                @forelse ($items as $row)
                    @php
                        $gambar = (string) ($row->gambar_utama ?? '');
                        if ($gambar === '') {
                            $imageUrl = asset('assets/images/blog-img1.jpg');
                        } elseif (str_starts_with($gambar, 'http://') || str_starts_with($gambar, 'https://')) {
                            $imageUrl = $gambar;
                        } elseif (str_starts_with($gambar, 'storage/')) {
                            $imageUrl = asset($gambar);
                        } else {
                            $imageUrl = asset('storage/' . $gambar);
                        }
                        $category = $row->category?->nama ?: 'Umum';
                        $date = $row->published_at ?? $row->created_at;
                    @endphp

                    <div class="blog-box position-relative" data-aos="fade-up" data-aos-duration="700">
                        <div class="blog-img position-relative">
                            <span class="d-inline-block">{{ $category }}</span>
                            <figure class="mb-0">
                                <img src="{{ $imageUrl }}" alt="{{ $row->judul }}">
                            </figure>
                        </div>
                        <div class="blog-text">
                            <span class="d-block">{{ optional($date)->format('M d, Y') }}</span>
                            <h6 class="position-relative">
                                <a href="{{ route('peserta.blog.show', $row->slug) }}">{{ $row->judul }}</a>
                            </h6>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4" style="width: 100%;">
                        Belum ada berita yang dipublikasikan.
                    </div>
                @endforelse
            </div>
        </div>
    </section>
    <!--  BLOG SECTION END -->

    <!-- SPONSERS SECTION START -->
    <div class="index3-sponsers-main-section sponsers-main-section w-100 float-left">
        <div class="container">
            <div class="sponsers-companies">
                <ul class="list-unstyled mb-0" data-aos="fade-up" data-aos-duration="700">
                    <li>
                        <figure class="mb-0">
                            <img src="assets/images/sponsers-logo1.png" alt="sponsers-logo1">
                        </figure>
                    </li>
                    <li>
                        <figure class="mb-0">
                            <img src="assets/images/sponsers-logo2.png" alt="sponsers-logo2">
                        </figure>
                    </li>
                    <li>
                        <figure class="mb-0">
                            <img src="assets/images/sponsers-logo3.png" alt="sponsers-logo3">
                        </figure>
                    </li>
                    <li>
                        <figure class="mb-0">
                            <img src="assets/images/sponsers-logo4.png" alt="sponsers-logo4">
                        </figure>
                    </li>
                    <li>
                        <figure class="mb-0">
                            <img src="assets/images/sponsers-logo5.png" alt="sponsers-logo5">
                        </figure>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- SPONSERS SECTION END -->
@endsection
