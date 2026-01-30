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


    @include('peserta.partials.speakers', ['speakers' => $speakers ?? collect()])

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

        $cards = [];
        $formatPrice = function (string $raw): string {
            $raw = trim($raw);
            if ($raw === '') {
                return $raw;
            }

            if (preg_match('/^-?\d+$/', $raw) === 1) {
                return number_format((int) $raw, 0, ',', '.');
            }

            if (preg_match('/^-?\d+(?:[.,]\d+)?$/', $raw) !== 1) {
                return $raw;
            }

            $lastDot = strrpos($raw, '.');
            $lastComma = strrpos($raw, ',');
            $sepPos = $lastDot === false ? $lastComma : ($lastComma === false ? $lastDot : max($lastDot, $lastComma));
            if ($sepPos === false) {
                $intPart = preg_replace('/\D+/', '', $raw);
                return $intPart === '' ? $raw : number_format((int) $intPart, 0, ',', '.');
            }

            $sep = $raw[$sepPos];
            $intPart = substr($raw, 0, $sepPos);
            $decPart = substr($raw, $sepPos + 1);
            $intDigits = preg_replace('/\D+/', '', $intPart);
            $decDigits = preg_replace('/\D+/', '', $decPart);

            if ($intDigits === '') {
                return $raw;
            }

            $formattedInt = number_format((int) $intDigits, 0, ',', '.');
            return $decDigits === '' ? $formattedInt : ($formattedInt . $sep . $decDigits);
        };

        if ($silverIsActive) {
            $cards[] = [
                'wrapClass' => 'silver-ticket-details',
                'title' => konten('home', 'pricing', 'silver_title') ?: 'Silver',
                'subtitle' => konten('home', 'pricing', 'silver_subtitle') ?: 'For individuals',
                'currency' => $silverCurrencySymbol,
                'price' => konten('home', 'pricing', 'silver_price') ?: '29',
                'price_display' => $formatPrice((string) (konten('home', 'pricing', 'silver_price') ?: '29')),
                'features' => $silverFeatures,
                'button_text' => konten('home', 'pricing', 'silver_button_text') ?: 'BUY TICKET',
                'button_url' => konten('home', 'pricing', 'silver_button_url') ?: 'shop.html',
                'badge' => '',
            ];
        }

        if ($goldIsActive) {
            $cards[] = [
                'wrapClass' => 'gold-ticket-details',
                'title' => konten('home', 'pricing', 'gold_title') ?: 'Gold',
                'subtitle' => konten('home', 'pricing', 'gold_subtitle') ?: 'For individuals',
                'currency' => $goldCurrencySymbol,
                'price' => konten('home', 'pricing', 'gold_price') ?: '45',
                'price_display' => $formatPrice((string) (konten('home', 'pricing', 'gold_price') ?: '45')),
                'features' => $goldFeatures,
                'button_text' => konten('home', 'pricing', 'gold_button_text') ?: 'BUY TICKET',
                'button_url' => konten('home', 'pricing', 'gold_button_url') ?: 'shop.html',
                'badge' => $goldBadge,
            ];
        }

        if ($premiumIsActive) {
            $cards[] = [
                'wrapClass' => 'premium-ticket-details',
                'title' => konten('home', 'pricing', 'premium_title') ?: 'Premium',
                'subtitle' => konten('home', 'pricing', 'premium_subtitle') ?: 'For individuals',
                'currency' => $premiumCurrencySymbol,
                'price' => konten('home', 'pricing', 'premium_price') ?: '59',
                'price_display' => $formatPrice((string) (konten('home', 'pricing', 'premium_price') ?: '59')),
                'features' => $premiumFeatures,
                'button_text' => konten('home', 'pricing', 'premium_button_text') ?: 'BUY TICKET',
                'button_url' => konten('home', 'pricing', 'premium_button_url') ?: 'shop.html',
                'badge' => '',
            ];
        }

        $extraCardsRaw = trim((string) konten('home', 'pricing', 'extra_cards'));
        $extraCardsDecoded = $extraCardsRaw !== '' ? json_decode($extraCardsRaw, true) : null;
        $extraCardsDecoded = is_array($extraCardsDecoded) ? $extraCardsDecoded : [];

        $wrapCycle = ['silver-ticket-details', 'gold-ticket-details', 'premium-ticket-details'];
        $extraCardsNormalized = collect($extraCardsDecoded)
            ->filter(fn($v) => is_array($v) && (string)($v['id'] ?? '') !== '')
            ->values()
            ->map(function (array $v) use ($currencySymbolByCode, $wrapCycle, $formatPrice) {
                $active = (string) ($v['active'] ?? '0') !== '0';
                $features = isset($v['features']) && is_array($v['features']) ? $v['features'] : [];
                $features = collect($features)->map(fn($x) => trim((string) $x))->filter()->values()->all();
                $currencyCode = trim((string) ($v['currency'] ?? 'USD'));
                if ($currencyCode === '') {
                    $currencyCode = 'USD';
                }

                return [
                    'active' => $active,
                    'title' => trim((string) ($v['title'] ?? '')),
                    'subtitle' => trim((string) ($v['subtitle'] ?? '')),
                    'currency' => $currencySymbolByCode[$currencyCode] ?? '$',
                    'price' => (string) ($v['price'] ?? ''),
                    'price_display' => $formatPrice((string) ($v['price'] ?? '')),
                    'features' => $features,
                    'button_text' => trim((string) ($v['button_text'] ?? '')),
                    'button_url' => trim((string) ($v['button_url'] ?? '#')),
                    'badge' => trim((string) ($v['badge'] ?? '')),
                ];
            })
            ->filter(fn($c) => (bool) $c['active'])
            ->values();

        $baseCount = count($cards);
        foreach ($extraCardsNormalized as $i => $c) {
            $wrapClass = $wrapCycle[($baseCount + $i) % count($wrapCycle)] ?? 'silver-ticket-details';
            $cards[] = [
                'wrapClass' => $wrapClass,
                'title' => $c['title'] !== '' ? $c['title'] : 'Ticket',
                'subtitle' => $c['subtitle'] !== '' ? $c['subtitle'] : 'For individuals',
                'currency' => $c['currency'],
                'price' => $c['price'] !== '' ? $c['price'] : '0',
                'price_display' => $c['price'] !== '' ? $c['price_display'] : $formatPrice('0'),
                'features' => $c['features'],
                'button_text' => $c['button_text'] !== '' ? $c['button_text'] : 'BUY TICKET',
                'button_url' => $c['button_url'] !== '' ? $c['button_url'] : '#',
                'badge' => $c['badge'],
            ];
        }

        $useCarousel = count($cards) > 3;
    @endphp
    <section id="pricing" class="index3-pricing-plans-section w-100 float-left padding-top padding-bottom">
        <div class="container">
            <div class="generic-title2 text-center">
                <span class="small-text" data-aos="fade-up"
                    data-aos-duration="700">{{ konten('home', 'pricing', 'small_text') ?: 'TICKET PRICING' }}</span>
                <h2 data-aos="fade-up" data-aos-duration="700">{!! nl2br(e($pricingTitle)) !!}</h2>
            </div>
            <div class="index3-plan-inner-con">
                @if ($useCarousel)
                    <div class="position-relative">
                        <div id="pricing-carousel" class="owl-carousel owl-theme pricing-carousel" data-total="{{ count($cards) }}">
                            @foreach ($cards as $card)
                                <div class="item">
                                    <div class="ticket-details {{ $card['wrapClass'] }}" data-aos="fade-up" data-aos-duration="700">
                                        <h3>{{ $card['title'] }}</h3>
                                        <p>{{ $card['subtitle'] }}</p>
                                        <span>Starting at:</span>
                                        <div class="price">
                                            <small>{{ $card['currency'] }}</small>{{ $card['price_display'] ?? $card['price'] }}
                                        </div>
                                        <ul class="list-unstyled">
                                            @foreach ($card['features'] as $feature)
                                                @if (trim((string) $feature) !== '')
                                                    <li class="position-relative">{{ $feature }}</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                        <div class="generic-btn">
                                            <a href="{{ $card['button_url'] }}">{{ $card['button_text'] }}
                                                <i class="fas fa-arrow-right"></i></a>
                                        </div>
                                        @if (trim((string) $card['badge']) !== '')
                                            <div class="recomended-box">
                                                {{ $card['badge'] }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            <div class="pricing-carousel-indicator small" data-pricing-indicator aria-live="polite"></div>
                        </div>
                    </div>
                @else
                    @foreach ($cards as $card)
                        <div class="ticket-details {{ $card['wrapClass'] }}" data-aos="fade-up" data-aos-duration="700">
                            <h3>{{ $card['title'] }}</h3>
                            <p>{{ $card['subtitle'] }}</p>
                            <span>Starting at:</span>
                            <div class="price">
                                <small>{{ $card['currency'] }}</small>{{ $card['price_display'] ?? $card['price'] }}
                            </div>
                            <ul class="list-unstyled">
                                @foreach ($card['features'] as $feature)
                                    @if (trim((string) $feature) !== '')
                                        <li class="position-relative">{{ $feature }}</li>
                                    @endif
                                @endforeach
                            </ul>
                            <div class="generic-btn">
                                <a href="{{ $card['button_url'] }}">{{ $card['button_text'] }}
                                    <i class="fas fa-arrow-right"></i></a>
                            </div>
                            @if (trim((string) $card['badge']) !== '')
                                <div class="recomended-box">
                                    {{ $card['badge'] }}
                                </div>
                            @endif
                        </div>
                    @endforeach
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

    @if ($useCarousel)
        @push('styles')
            <style>
                #pricing .index3-plan-inner-con {
                    display: block;
                }
                #pricing .pricing-carousel {
                    position: relative;
                }
                #pricing .pricing-carousel .owl-item .item {
                    height: 100%;
                }
                #pricing .pricing-carousel .ticket-details {
                    width: 100%;
                    height: auto;
                    min-height: 481px;
                    margin-top: 0 !important;
                    border-radius: 10px !important;
                }
                #pricing .pricing-carousel .gold-ticket-details {
                    height: auto !important;
                    min-height: 481px;
                    margin-top: 0 !important;
                    padding: 50px 45px 45px !important;
                }
                #pricing .pricing-carousel .owl-item:not(.center) .gold-ticket-details {
                    background: var(--primary-color) !important;
                }
                #pricing .pricing-carousel .owl-item:not(.center) .gold-ticket-details h3 {
                    color: var(--dark-blue) !important;
                }
                #pricing .pricing-carousel .owl-item:not(.center) .gold-ticket-details p,
                #pricing .pricing-carousel .owl-item:not(.center) .gold-ticket-details span,
                #pricing .pricing-carousel .owl-item:not(.center) .gold-ticket-details ul li {
                    color: var(--text-color) !important;
                }
                #pricing .pricing-carousel .owl-item:not(.center) .gold-ticket-details .price,
                #pricing .pricing-carousel .owl-item:not(.center) .gold-ticket-details .price small {
                    color: var(--pink-color) !important;
                }
                #pricing .pricing-carousel .owl-item:not(.center) .gold-ticket-details ul li::before {
                    color: var(--pink-color) !important;
                }
                #pricing .pricing-carousel .owl-item:not(.center) .gold-ticket-details .generic-btn a {
                    border-color: var(--pink-color) !important;
                    background: transparent !important;
                    color: var(--pink-color) !important;
                }
                #pricing .pricing-carousel .owl-item:not(.center) .gold-ticket-details .generic-btn a:hover {
                    color: var(--primary-color) !important;
                    background: var(--pink-color) !important;
                    border-color: var(--pink-color) !important;
                }
                #pricing .pricing-carousel .owl-nav button {
                    width: 36px;
                    height: 36px;
                    border-radius: 999px;
                    background: rgba(0, 0, 0, 0.55) !important;
                    color: #fff !important;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                }
                #pricing .pricing-carousel .owl-nav button:hover {
                    background: rgba(0, 0, 0, 0.75) !important;
                }
                #pricing .pricing-carousel .owl-nav {
                    position: absolute;
                    top: 50%;
                    left: -12px;
                    right: -12px;
                    transform: translateY(-50%);
                    display: flex;
                    justify-content: space-between;
                    gap: 0;
                    margin-top: 0;
                    z-index: 2;
                    pointer-events: none;
                }
                #pricing .pricing-carousel .owl-nav button {
                    pointer-events: auto;
                }
                @media (min-width: 768px) {
                    #pricing .pricing-carousel .owl-nav {
                        left: -54px;
                        right: -54px;
                    }
                    #pricing .pricing-carousel .owl-nav button {
                        width: 42px;
                        height: 42px;
                    }
                }
                @media (max-width: 767.98px) {
                    #pricing .pricing-carousel .ticket-details {
                        text-align: center;
                        min-height: 0;
                        padding: 32px 22px 28px !important;
                    }
                    #pricing .pricing-carousel .gold-ticket-details {
                        min-height: 0 !important;
                        padding: 32px 22px 28px !important;
                    }
                    #pricing .pricing-carousel .ticket-details h3 {
                        font-size: 24px;
                        line-height: 28px;
                        margin-bottom: 6px;
                    }
                    #pricing .pricing-carousel .ticket-details p {
                        margin-bottom: 10px;
                    }
                    #pricing .pricing-carousel .ticket-details span {
                        display: block;
                        margin-bottom: 6px;
                    }
                    #pricing .pricing-carousel .ticket-details .price {
                        margin-bottom: 14px;
                    }
                    #pricing .pricing-carousel .ticket-details .price small {
                        margin-right: 6px;
                    }
                    #pricing .pricing-carousel .ticket-details ul {
                        display: flex;
                        flex-direction: column;
                        align-items: stretch;
                        gap: 8px;
                        max-width: 280px;
                        margin-left: auto;
                        margin-right: auto;
                        margin-bottom: 18px;
                    }
                    #pricing .pricing-carousel .ticket-details ul li {
                        width: 100%;
                        text-align: left;
                        padding-left: 26px;
                    }
                    #pricing .pricing-carousel .ticket-details .generic-btn {
                        display: flex;
                        justify-content: center;
                    }
                    #pricing .pricing-carousel .ticket-details .generic-btn a {
                        padding: 14px 26px;
                    }
                    #pricing .pricing-carousel .recomended-box {
                        right: 14px;
                        top: 14px;
                    }
                }
                #pricing .pricing-carousel .owl-item.center .ticket-details {
                    background: #000;
                }
                #pricing .pricing-carousel .owl-item.center .ticket-details .generic-btn a {
                    background: #fff !important;
                    border-color: #fff !important;
                    color: var(--dark-blue) !important;
                }
                #pricing .pricing-carousel .owl-item.center .ticket-details .generic-btn a:hover {
                    background: transparent !important;
                    border-color: #fff !important;
                    color: #fff !important;
                }
                #pricing .pricing-carousel .owl-item.center .ticket-details h3,
                #pricing .pricing-carousel .owl-item.center .ticket-details p,
                #pricing .pricing-carousel .owl-item.center .ticket-details span,
                #pricing .pricing-carousel .owl-item.center .ticket-details li,
                #pricing .pricing-carousel .owl-item.center .ticket-details .price,
                #pricing .pricing-carousel .owl-item.center .ticket-details .price small {
                    color: #fff;
                }
                #pricing .pricing-carousel-indicator {
                    color: rgba(0, 0, 0, 0.6);
                }
            </style>
        @endpush

        @push('scripts')
            <script>
                (function () {
                    const section = document.getElementById('pricing');
                    const carouselEl = document.getElementById('pricing-carousel');
                    const indicator = document.querySelector('[data-pricing-indicator]');
                    if (!section || !carouselEl) return;

                    function updateIndicator(e) {
                        if (!indicator || !e || !e.relatedTarget || !e.item) return;
                        const current = e.relatedTarget.relative(e.item.index) + 1;
                        const total = e.item.count || 0;
                        indicator.textContent = total > 0 ? `Slide ${current} dari ${total}` : '';
                    }

                    function init() {
                        if (typeof jQuery === 'undefined') return;
                        const $ = jQuery;
                        if (typeof $(carouselEl).owlCarousel !== 'function') return;
                        if ($(carouselEl).hasClass('owl-loaded')) return;

                        $(carouselEl).owlCarousel({
                            loop: true,
                            margin: 30,
                            nav: true,
                            dots: true,
                            autoplay: true,
                            autoplayTimeout: 3500,
                            autoplayHoverPause: true,
                            smartSpeed: 450,
                            responsive: {
                                0: { items: 1, center: false },
                                768: { items: 2, center: false },
                                1200: { items: 3, center: true }
                            },
                            onInitialized: updateIndicator,
                            onChanged: updateIndicator
                        });
                    }

                    if ('IntersectionObserver' in window) {
                        const observer = new IntersectionObserver(
                            (entries) => {
                                const hit = entries.some((x) => x.isIntersecting);
                                if (!hit) return;
                                observer.disconnect();
                                init();
                            },
                            { root: null, threshold: 0.2 }
                        );
                        observer.observe(section);
                        return;
                    }

                    init();
                })();
            </script>
        @endpush
    @endif

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

    @include('peserta.partials.faq', ['page' => 'home'])

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

    @include('peserta.partials.sponsors', [
        'page' => 'home',
        'wrapperClass' => 'index3-sponsers-main-section sponsers-main-section w-100 float-left',
    ])
@endsection
