@extends('admin.partials.app')

@section('content')
    <main
        id="main-content"
        tabindex="-1"
        class="pb-4 p-3 p-lg-4 rounded-3"
        style="background-color: #f5f6f8;"
        role="main"
        aria-label="Konten halaman {{ $page ?? '' }}"
    >
        <style>
            #main-content .form-control,
            #main-content .form-select {
                background-color: #d7dbe0;
                border-color: #c6cbd2;
            }

            #main-content .form-control:focus,
            #main-content .form-select:focus {
                background-color: #e0e4e8;
                border-color: #aab1bb;
            }

            #main-content .bg-light {
                background-color: #d7dbe0 !important;
            }
        </style>
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
            <div class="d-flex align-items-center">
                <div class="btn-group" role="group" aria-label="Pilih halaman">
                    <a
                        href="{{ route('admin.konten-halaman.home') }}"
                        class="btn btn-sm {{ ($page ?? 'home') === 'home' ? 'btn-primary' : 'btn-outline-primary' }}"
                    >
                        Home
                    </a>
                    <a
                        href="{{ route('admin.konten-halaman.about') }}"
                        class="btn btn-sm {{ ($page ?? '') === 'about' ? 'btn-primary' : 'btn-outline-primary' }}"
                    >
                        About
                    </a>
                </div>
            </div>
        </div>

        <div id="notify-area"></div>

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

        @php
            $sections = ($fieldsBySection ?? collect())->keys()->values();
            $pageKey = (string) ($page ?? 'home');
            $formAction =
                $pageKey === 'about'
                    ? route('admin.konten-halaman.about.update')
                    : route('admin.konten-halaman.home.update');
        @endphp

        <form method="POST" action="{{ $formAction }}" id="content-form">
            @csrf

            <div class="card mb-4">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3">
                        <div class="me-auto">
                            <div class="fw-semibold text-black">Editor Konten</div>
                            <div class="text-muted small">Pilih tab Banner/Journey, lalu ubah field yang diperlukan.</div>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <label class="visually-hidden" for="field-search">Cari</label>
                            <input
                                id="field-search"
                                type="search"
                                class="form-control"
                                style="width: min(360px, 100%);"
                                placeholder="Cari label atau key…"
                                autocomplete="off"
                            >
                        </div>
                    </div>
                </div>

                <div class="card-body pt-3">
                    <ul class="nav nav-tabs" id="sectionTabs" role="tablist">
                        @foreach ($sections as $i => $section)
                            @php
                                $label = str_replace('_', ' ', $section);
                                $count = count($fieldsBySection[$section] ?? []);
                            @endphp
                            <li class="nav-item" role="presentation">
                                <button
                                    class="nav-link @if ($i === 0) active @endif"
                                    id="tab-{{ $section }}"
                                    type="button"
                                    role="tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#pane-{{ $section }}"
                                    aria-controls="pane-{{ $section }}"
                                    aria-selected="{{ $i === 0 ? 'true' : 'false' }}"
                                    data-tab-btn
                                    data-section="{{ $section }}"
                                >
                                    <span class="text-capitalize">{{ $label }}</span>
                                    <span class="badge bg-light text-dark ms-2" data-tab-badge data-initial="{{ $count }}">{{ $count }}</span>
                                </button>
                            </li>
                        @endforeach
                    </ul>

                    <div class="tab-content pt-3" id="sectionTabsContent">
                        @foreach ($sections as $i => $section)
                            @php
                                $fields = $fieldsBySection[$section] ?? collect();
                            @endphp
                            <div
                                class="tab-pane fade @if ($i === 0) show active @endif"
                                id="pane-{{ $section }}"
                                role="tabpanel"
                                aria-labelledby="tab-{{ $section }}"
                                tabindex="0"
                                data-tab-pane
                                data-tab-section="{{ $section }}"
                            >
                                @if ($section === 'pricing')
                                    @php
                                        $pricing = $values['pricing'] ?? [];

                                        $pricingSmallText = old('contents.pricing.small_text', $pricing['small_text'] ?? '');
                                        $pricingTitle = old('contents.pricing.title', $pricing['title'] ?? '');

                                        $pricingBottomText = old('contents.pricing.bottom_text', $pricing['bottom_text'] ?? '');
                                        $pricingBottomButtonText = old('contents.pricing.bottom_button_text', $pricing['bottom_button_text'] ?? '');
                                        $pricingBottomButtonUrl = old('contents.pricing.bottom_button_url', $pricing['bottom_button_url'] ?? '');

                                        $extraCardsJson = old('contents.pricing.extra_cards', $pricing['extra_cards'] ?? '[]');
                                        $extraCards = [];
                                        if (is_string($extraCardsJson) && trim($extraCardsJson) !== '') {
                                            $decoded = json_decode($extraCardsJson, true);
                                            if (is_array($decoded)) {
                                                $extraCards = $decoded;
                                            }
                                        }

                                        $extraCardsStoreUrl = route('admin.konten-halaman.pricing-cards.store');
                                        $extraCardsUpdateTpl = route('admin.konten-halaman.pricing-cards.update', ['cardId' => '__ID__']);
                                        $extraCardsDestroyTpl = route('admin.konten-halaman.pricing-cards.destroy', ['cardId' => '__ID__']);

                                        $cards = [
                                            [
                                                'plan' => 'silver',
                                                'label' => 'Card 1 - Basic',
                                                'badgeClass' => 'bg-primary',
                                                'wrapClass' => 'silver-ticket-details',
                                            ],
                                            [
                                                'plan' => 'gold',
                                                'label' => 'Card 2 - Pro',
                                                'badgeClass' => 'bg-warning text-dark',
                                                'wrapClass' => 'gold-ticket-details',
                                            ],
                                            [
                                                'plan' => 'premium',
                                                'label' => 'Card 3 - Enterprise',
                                                'badgeClass' => 'bg-dark',
                                                'wrapClass' => 'premium-ticket-details',
                                            ],
                                        ];

                                        $currencySymbolByCode = [
                                            'USD' => '$',
                                            'IDR' => 'Rp',
                                            'EUR' => '€',
                                        ];
                                    @endphp

                                    <div class="row g-3">
                                        <div class="col-12 col-xl-6" data-field-item data-hay="ticket pricing teks kecil pricing small_text">
                                            <label class="form-label text-black" for="pricing-small-text">Teks kecil</label>
                                            <input
                                                id="pricing-small-text"
                                                name="contents[pricing][small_text]"
                                                type="text"
                                                class="form-control @error('contents.pricing.small_text') is-invalid @enderror"
                                                value="{{ $pricingSmallText }}"
                                                placeholder="Contoh: TICKET PRICING"
                                                data-pricing-input
                                                data-plan="section"
                                                data-field="small_text"
                                            >
                                            @error('contents.pricing.small_text')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12 col-xl-6" data-field-item data-hay="ticket pricing judul pricing title">
                                            <label class="form-label text-black" for="pricing-title">Judul</label>
                                            <textarea
                                                id="pricing-title"
                                                name="contents[pricing][title]"
                                                class="form-control @error('contents.pricing.title') is-invalid @enderror"
                                                rows="3"
                                                placeholder="Contoh: We Have Several Options&#10;for Tickets"
                                                data-pricing-input
                                                data-plan="section"
                                                data-field="title"
                                            >{{ $pricingTitle }}</textarea>
                                            @error('contents.pricing.title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="accordion mt-3" id="pricing-cards-accordion">
                                        @foreach ($cards as $index => $card)
                                            @php
                                                $plan = $card['plan'];
                                                $titleKey = "contents.pricing.{$plan}_title";
                                                $subtitleKey = "contents.pricing.{$plan}_subtitle";
                                                $priceKey = "contents.pricing.{$plan}_price";
                                                $currencyKey = "contents.pricing.{$plan}_currency";
                                                $featuresKey = "contents.pricing.{$plan}_features";
                                                $ctaTextKey = "contents.pricing.{$plan}_button_text";
                                                $ctaUrlKey = "contents.pricing.{$plan}_button_url";
                                                $activeKey = "contents.pricing.{$plan}_active";

                                                $titleVal = old($titleKey, $pricing["{$plan}_title"] ?? '');
                                                $subtitleVal = old($subtitleKey, $pricing["{$plan}_subtitle"] ?? '');
                                                $priceVal = old($priceKey, $pricing["{$plan}_price"] ?? '');
                                                $currencyVal = old($currencyKey, $pricing["{$plan}_currency"] ?? 'USD');
                                                $featuresVal = old($featuresKey, $pricing["{$plan}_features"] ?? '');
                                                $ctaTextVal = old($ctaTextKey, $pricing["{$plan}_button_text"] ?? '');
                                                $ctaUrlVal = old($ctaUrlKey, $pricing["{$plan}_button_url"] ?? '');
                                                $activeVal = old($activeKey, $pricing["{$plan}_active"] ?? '1');
                                                $activeVal = $activeVal === '' ? '1' : (string) $activeVal;

                                                $features = array_values(array_filter(preg_split("/\r\n|\r|\n/", (string) $featuresVal), fn ($v) => trim((string) $v) !== ''));
                                                if (count($features) === 0) {
                                                    $features = [''];
                                                }

                                                $currencySymbol = $currencySymbolByCode[$currencyVal] ?? '$';
                                                $isActive = $activeVal !== '0';

                                                $show = $index === 0;
                                            @endphp

                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="pricing-heading-{{ $plan }}">
                                                    <button
                                                        class="accordion-button @if (!$show) collapsed @endif"
                                                        type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#pricing-collapse-{{ $plan }}"
                                                        aria-expanded="{{ $show ? 'true' : 'false' }}"
                                                        aria-controls="pricing-collapse-{{ $plan }}"
                                                    >
                                                        <span class="badge {{ $card['badgeClass'] }} me-2">{{ $card['label'] }}</span>
                                                        <span class="text-muted small" data-pricing-quick-title data-plan="{{ $plan }}">{{ $titleVal }}</span>
                                                        <span class="ms-auto badge bg-light text-dark" data-pricing-quick-status data-plan="{{ $plan }}">{{ $isActive ? 'Aktif' : 'Nonaktif' }}</span>
                                                    </button>
                                                </h2>
                                                <div
                                                    id="pricing-collapse-{{ $plan }}"
                                                    class="accordion-collapse collapse @if ($show) show @endif"
                                                    aria-labelledby="pricing-heading-{{ $plan }}"
                                                    data-bs-parent="#pricing-cards-accordion"
                                                >
                                                    <div class="accordion-body">
                                                        <div class="row g-4">
                                                            <div class="col-12 col-lg-7">
                                                                <div class="row g-3">
                                                                    <div class="col-12" data-field-item data-hay="pricing {{ $plan }} aktif status aktif nonaktif">
                                                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                                                            <div class="fw-semibold text-black">Status Card</div>
                                                                            <div class="form-check form-switch m-0">
                                                                                <input type="hidden" name="contents[pricing][{{ $plan }}_active]" value="0">
                                                                                <input
                                                                                    class="form-check-input"
                                                                                    type="checkbox"
                                                                                    role="switch"
                                                                                    id="pricing-{{ $plan }}-active"
                                                                                    name="contents[pricing][{{ $plan }}_active]"
                                                                                    value="1"
                                                                                    @if ($isActive) checked @endif
                                                                                    data-pricing-input
                                                                                    data-plan="{{ $plan }}"
                                                                                    data-field="active"
                                                                                >
                                                                                <label class="form-check-label" for="pricing-{{ $plan }}-active">
                                                                                    {{ $isActive ? 'Aktif' : 'Nonaktif' }}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        @error($activeKey)
                                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>

                                                                    <div class="col-12" data-field-item data-hay="pricing {{ $plan }} judul nama paket title">
                                                                        <label class="form-label text-black" for="pricing-{{ $plan }}-title">Judul / Nama paket</label>
                                                                        <input
                                                                            id="pricing-{{ $plan }}-title"
                                                                            name="contents[pricing][{{ $plan }}_title]"
                                                                            type="text"
                                                                            class="form-control @error($titleKey) is-invalid @enderror"
                                                                            value="{{ $titleVal }}"
                                                                            data-pricing-input
                                                                            data-plan="{{ $plan }}"
                                                                            data-field="title"
                                                                        >
                                                                        @error($titleKey)
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>

                                                                    <div class="col-12" data-field-item data-hay="pricing {{ $plan }} deskripsi singkat subtitle">
                                                                        <label class="form-label text-black" for="pricing-{{ $plan }}-subtitle">Deskripsi singkat</label>
                                                                        <input
                                                                            id="pricing-{{ $plan }}-subtitle"
                                                                            name="contents[pricing][{{ $plan }}_subtitle]"
                                                                            type="text"
                                                                            class="form-control @error($subtitleKey) is-invalid @enderror"
                                                                            value="{{ $subtitleVal }}"
                                                                            data-pricing-input
                                                                            data-plan="{{ $plan }}"
                                                                            data-field="subtitle"
                                                                        >
                                                                        @error($subtitleKey)
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>

                                                                    <div class="col-12" data-field-item data-hay="pricing {{ $plan }} currency mata uang harga price">
                                                                        <div class="d-flex flex-column flex-md-row align-items-md-end gap-3">
                                                                            <div class="w-100 flex-md-shrink-0" style="max-width: 260px;">
                                                                                <label class="form-label text-black" for="pricing-{{ $plan }}-currency">Mata uang</label>
                                                                                <select
                                                                                    id="pricing-{{ $plan }}-currency"
                                                                                    name="contents[pricing][{{ $plan }}_currency]"
                                                                                    class="form-select @error($currencyKey) is-invalid @enderror"
                                                                                    data-pricing-input
                                                                                    data-plan="{{ $plan }}"
                                                                                    data-field="currency"
                                                                                >
                                                                                    <option value="USD" @if ($currencyVal === 'USD') selected @endif>USD ($)</option>
                                                                                    <option value="IDR" @if ($currencyVal === 'IDR') selected @endif>IDR (Rp)</option>
                                                                                    <option value="EUR" @if ($currencyVal === 'EUR') selected @endif>EUR (€)</option>
                                                                                </select>
                                                                                @error($currencyKey)
                                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                                @enderror
                                                                            </div>

                                                                            <div class="w-100 flex-grow-1">
                                                                                <label class="form-label text-black" for="pricing-{{ $plan }}-price">Harga</label>
                                                                                <input
                                                                                    id="pricing-{{ $plan }}-price"
                                                                                    name="contents[pricing][{{ $plan }}_price]"
                                                                                    type="number"
                                                                                    step="0.01"
                                                                                    min="0"
                                                                                    inputmode="decimal"
                                                                                    class="form-control @error($priceKey) is-invalid @enderror"
                                                                                    value="{{ $priceVal }}"
                                                                                    data-pricing-input
                                                                                    data-plan="{{ $plan }}"
                                                                                    data-field="price"
                                                                                >
                                                                                @error($priceKey)
                                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-12" data-field-item data-hay="pricing {{ $plan }} fitur features list tambah hapus">
                                                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                                                            <div class="form-label text-black mb-0">Fitur-fitur</div>
                                                                            <button
                                                                                type="button"
                                                                                class="btn btn-outline-primary btn-xxs"
                                                                                data-features-add
                                                                                data-plan="{{ $plan }}"
                                                                            >
                                                                                <i class="la la-plus" aria-hidden="true"></i>
                                                                                Tambah fitur
                                                                            </button>
                                                                        </div>

                                                                        <div class="d-flex flex-column gap-2 mt-2" data-features-list data-plan="{{ $plan }}">
                                                                            @foreach ($features as $fIndex => $feature)
                                                                                <div class="input-group" data-feature-row>
                                                                                    <span class="input-group-text" data-feature-index>{{ $fIndex + 1 }}</span>
                                                                                    <input
                                                                                        type="text"
                                                                                        class="form-control"
                                                                                        value="{{ $feature }}"
                                                                                        data-features-input
                                                                                        data-plan="{{ $plan }}"
                                                                                    >
                                                                                    <button type="button" class="btn btn-outline-danger" data-features-remove>
                                                                                        <i class="la la-trash" aria-hidden="true"></i>
                                                                                    </button>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>

                                                                        <textarea
                                                                            class="d-none"
                                                                            name="contents[pricing][{{ $plan }}_features]"
                                                                            data-features-storage
                                                                            data-plan="{{ $plan }}"
                                                                        >{{ $featuresVal }}</textarea>

                                                                        @error($featuresKey)
                                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>

                                                                    <div class="col-12 col-md-6" data-field-item data-hay="pricing {{ $plan }} tombol cta text">
                                                                        <label class="form-label text-black" for="pricing-{{ $plan }}-cta-text">Tombol CTA (teks)</label>
                                                                        <input
                                                                            id="pricing-{{ $plan }}-cta-text"
                                                                            name="contents[pricing][{{ $plan }}_button_text]"
                                                                            type="text"
                                                                            class="form-control @error($ctaTextKey) is-invalid @enderror"
                                                                            value="{{ $ctaTextVal }}"
                                                                            data-pricing-input
                                                                            data-plan="{{ $plan }}"
                                                                            data-field="cta_text"
                                                                        >
                                                                        @error($ctaTextKey)
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>

                                                                    <div class="col-12 col-md-6" data-field-item data-hay="pricing {{ $plan }} tombol cta link url">
                                                                        <label class="form-label text-black" for="pricing-{{ $plan }}-cta-url">Tombol CTA (link)</label>
                                                                        <input
                                                                            id="pricing-{{ $plan }}-cta-url"
                                                                            name="contents[pricing][{{ $plan }}_button_url]"
                                                                            type="url"
                                                                            class="form-control @error($ctaUrlKey) is-invalid @enderror"
                                                                            value="{{ $ctaUrlVal }}"
                                                                            placeholder="https://"
                                                                            data-pricing-input
                                                                            data-plan="{{ $plan }}"
                                                                            data-field="cta_url"
                                                                        >
                                                                        @error($ctaUrlKey)
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>

                                                                    @if ($plan === 'gold')
                                                                        @php
                                                                            $badgeKey = 'contents.pricing.gold_badge';
                                                                            $badgeVal = old($badgeKey, $pricing['gold_badge'] ?? '');
                                                                        @endphp
                                                                        <div class="col-12" data-field-item data-hay="pricing gold badge recommended">
                                                                            <label class="form-label text-black" for="pricing-gold-badge">Badge (opsional)</label>
                                                                            <input
                                                                                id="pricing-gold-badge"
                                                                                name="contents[pricing][gold_badge]"
                                                                                type="text"
                                                                                class="form-control @error($badgeKey) is-invalid @enderror"
                                                                                value="{{ $badgeVal }}"
                                                                                placeholder="Contoh: RECOMMENDED"
                                                                                data-pricing-input
                                                                                data-plan="gold"
                                                                                data-field="badge"
                                                                            >
                                                                            @error($badgeKey)
                                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="col-12 col-lg-5">
                                                                <div class="fw-semibold text-black mb-2">Preview</div>
                                                                <div
                                                                    class="ticket-details {{ $card['wrapClass'] }} position-relative"
                                                                    data-pricing-preview
                                                                    data-plan="{{ $plan }}"
                                                                >
                                                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                                                        <h3 class="mb-0" data-preview-title>{{ $titleVal !== '' ? $titleVal : 'Nama Paket' }}</h3>
                                                                        <span class="badge bg-secondary @if ($isActive) d-none @endif" data-preview-inactive>Nonaktif</span>
                                                                    </div>
                                                                    <p class="mb-1" data-preview-subtitle>{{ $subtitleVal !== '' ? $subtitleVal : 'Deskripsi singkat' }}</p>
                                                                    <span>Starting at:</span>
                                                                    <div class="price">
                                                                        <small data-preview-currency>{{ $currencySymbol }}</small><span data-preview-price>{{ $priceVal !== '' ? $priceVal : '0' }}</span>
                                                                    </div>
                                                                    <ul class="list-unstyled" data-preview-features>
                                                                        @foreach ($features as $feature)
                                                                            @if (trim((string) $feature) !== '')
                                                                                <li class="position-relative">{{ $feature }}</li>
                                                                            @endif
                                                                        @endforeach
                                                                    </ul>
                                                                    <div class="generic-btn">
                                                                        <a href="{{ $ctaUrlVal !== '' ? $ctaUrlVal : '#' }}" data-preview-cta-link>
                                                                            <span data-preview-cta-text>{{ $ctaTextVal !== '' ? $ctaTextVal : 'BUY TICKET' }}</span>
                                                                            <i class="fas fa-arrow-right"></i>
                                                                        </a>
                                                                    </div>

                                                                    @if ($plan === 'gold')
                                                                        <div class="recomended-box @if ($badgeVal === '') d-none @endif" data-preview-badge>{{ $badgeVal }}</div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="border rounded p-3 mt-4" data-extra-cards>
                                        <div class="d-flex flex-wrap align-items-start justify-content-between gap-2">
                                            <div>
                                                <div class="fw-semibold text-black">Card Tambahan</div>
                                                <div class="text-muted small">Ditampilkan setelah 3 card utama di halaman peserta.</div>
                                            </div>
                                        </div>

                                        <textarea class="d-none" name="contents[pricing][extra_cards]" data-extra-cards-storage>{{ $extraCardsJson }}</textarea>

                                        <div
                                            class="d-flex flex-column gap-3 mt-3"
                                            data-extra-cards-list
                                            data-store-url="{{ $extraCardsStoreUrl }}"
                                            data-update-url-template="{{ $extraCardsUpdateTpl }}"
                                            data-destroy-url-template="{{ $extraCardsDestroyTpl }}"
                                        >
                                            @foreach ($extraCards as $card)
                                                @php
                                                    $cardId = (string) ($card['id'] ?? '');
                                                    $cardActive = (string) ($card['active'] ?? '0') !== '0';
                                                    $cardTitle = (string) ($card['title'] ?? '');
                                                    $cardSubtitle = (string) ($card['subtitle'] ?? '');
                                                    $cardCurrency = (string) ($card['currency'] ?? 'USD');
                                                    $cardPrice = (string) ($card['price'] ?? '');
                                                    $cardFeatures = $card['features'] ?? [];
                                                    $cardFeatures = is_array($cardFeatures) ? $cardFeatures : [];
                                                    $cardFeatures = collect($cardFeatures)->map(fn($v) => trim((string) $v))->filter()->values()->all();
                                                    if (count($cardFeatures) === 0) {
                                                        $cardFeatures = [''];
                                                    }
                                                    $cardFeaturesText = collect($cardFeatures)->map(fn($v) => trim((string) $v))->filter()->implode("\n");
                                                    $cardButtonText = (string) ($card['button_text'] ?? '');
                                                    $cardButtonUrl = (string) ($card['button_url'] ?? '');
                                                    $cardBadge = (string) ($card['badge'] ?? '');

                                                    $extraWrapCycle = ['silver-ticket-details', 'gold-ticket-details', 'premium-ticket-details'];
                                                    $wrapClass = $extraWrapCycle[(3 + $loop->index) % count($extraWrapCycle)] ?? 'silver-ticket-details';
                                                    $currencySymbol = $currencySymbolByCode[$cardCurrency] ?? '$';
                                                @endphp

                                                @if ($cardId !== '')
                                                    <div class="border rounded p-3 bg-white" data-extra-card data-card-id="{{ $cardId }}" data-wrap-class="{{ $wrapClass }}">
                                                        <div class="row g-4">
                                                            <div class="col-12 col-lg-7">
                                                                <div class="row g-3">
                                                                    <div class="col-12">
                                                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                                                            <div class="fw-semibold text-black">Card</div>
                                                                            <button type="button" class="btn btn-outline-danger btn-xxs" data-extra-card-delete>
                                                                                <i class="la la-trash" aria-hidden="true"></i>
                                                                                Hapus
                                                                            </button>
                                                                        </div>
                                                                        <div class="text-muted small mt-1" data-card-status></div>
                                                                    </div>

                                                                    <div class="col-12" data-field-item data-hay="pricing card tambahan aktif status aktif nonaktif">
                                                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                                                            <div class="fw-semibold text-black">Status Card</div>
                                                                            <div class="form-check form-switch m-0">
                                                                                <input class="form-check-input" type="checkbox" role="switch" @if ($cardActive) checked @endif data-card-field="active">
                                                                                <label class="form-check-label">Aktif</label>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-12" data-field-item data-hay="pricing card tambahan judul nama paket title">
                                                                        <label class="form-label text-black">Judul / Nama paket</label>
                                                                        <input type="text" class="form-control" value="{{ $cardTitle }}" data-card-field="title">
                                                                    </div>
                                                                    <div class="col-12" data-field-item data-hay="pricing card tambahan deskripsi singkat subtitle">
                                                                        <label class="form-label text-black">Deskripsi singkat</label>
                                                                        <input type="text" class="form-control" value="{{ $cardSubtitle }}" data-card-field="subtitle">
                                                                    </div>

                                                                    <div class="col-12" data-field-item data-hay="pricing card tambahan currency mata uang harga price">
                                                                        <div class="d-flex flex-column flex-md-row align-items-md-end gap-3">
                                                                            <div class="w-100 flex-md-shrink-0" style="max-width: 260px;">
                                                                                <label class="form-label text-black">Mata uang</label>
                                                                                <select class="form-select" data-card-field="currency">
                                                                                    <option value="USD" @if ($cardCurrency === 'USD') selected @endif>USD ($)</option>
                                                                                    <option value="IDR" @if ($cardCurrency === 'IDR') selected @endif>IDR (Rp)</option>
                                                                                    <option value="EUR" @if ($cardCurrency === 'EUR') selected @endif>EUR (€)</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="w-100 flex-grow-1">
                                                                                <label class="form-label text-black">Harga</label>
                                                                                <input type="number" step="0.01" min="0" inputmode="decimal" class="form-control" value="{{ $cardPrice }}" data-card-field="price">
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-12" data-field-item data-hay="pricing card tambahan fitur features list tambah hapus">
                                                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                                                            <div class="form-label text-black mb-0">Fitur-fitur</div>
                                                                            <button type="button" class="btn btn-outline-primary btn-xxs" data-extra-features-add>
                                                                                <i class="la la-plus" aria-hidden="true"></i>
                                                                                Tambah fitur
                                                                            </button>
                                                                        </div>

                                                                        <div class="d-flex flex-column gap-2 mt-2" data-extra-features-list>
                                                                            @foreach ($cardFeatures as $fIndex => $feature)
                                                                                <div class="input-group" data-extra-feature-row>
                                                                                    <span class="input-group-text" data-extra-feature-index>{{ $fIndex + 1 }}</span>
                                                                                    <input type="text" class="form-control" value="{{ $feature }}" data-extra-features-input>
                                                                                    <button type="button" class="btn btn-outline-danger" data-extra-features-remove>
                                                                                        <i class="la la-trash" aria-hidden="true"></i>
                                                                                    </button>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>

                                                                        <textarea class="d-none" data-card-field="features">{{ $cardFeaturesText }}</textarea>
                                                                    </div>

                                                                    <div class="col-12 col-md-6" data-field-item data-hay="pricing card tambahan tombol cta text">
                                                                        <label class="form-label text-black">Tombol CTA (teks)</label>
                                                                        <input type="text" class="form-control" value="{{ $cardButtonText }}" data-card-field="button_text">
                                                                    </div>
                                                                    <div class="col-12 col-md-6" data-field-item data-hay="pricing card tambahan tombol cta url">
                                                                        <label class="form-label text-black">Tombol CTA (link)</label>
                                                                        <input type="url" class="form-control" placeholder="https://" value="{{ $cardButtonUrl }}" data-card-field="button_url">
                                                                    </div>
                                                                    <div class="col-12" data-field-item data-hay="pricing card tambahan badge">
                                                                        <label class="form-label text-black">Badge (opsional)</label>
                                                                        <input type="text" class="form-control" value="{{ $cardBadge }}" data-card-field="badge">
                                                                    </div>
                                                                </div>

                                                                <div class="alert alert-danger d-none mt-3 mb-0" role="alert" data-card-error></div>
                                                            </div>

                                                            <div class="col-12 col-lg-5">
                                                                <div class="fw-semibold text-black mb-2">Preview</div>
                                                                <div class="ticket-details {{ $wrapClass }} position-relative" data-extra-card-preview>
                                                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                                                        <h3 class="mb-0" data-preview-title>{{ $cardTitle !== '' ? $cardTitle : 'Nama Paket' }}</h3>
                                                                        <span class="badge bg-secondary @if ($cardActive) d-none @endif" data-preview-inactive>Nonaktif</span>
                                                                    </div>
                                                                    <p class="mb-1" data-preview-subtitle>{{ $cardSubtitle !== '' ? $cardSubtitle : 'Deskripsi singkat' }}</p>
                                                                    <span>Starting at:</span>
                                                                    <div class="price">
                                                                        <small data-preview-currency>{{ $currencySymbol }}</small><span data-preview-price>{{ $cardPrice !== '' ? $cardPrice : '0' }}</span>
                                                                    </div>
                                                                    <ul class="list-unstyled" data-preview-features>
                                                                        @foreach ($cardFeatures as $feature)
                                                                            @if (trim((string) $feature) !== '')
                                                                                <li class="position-relative">{{ $feature }}</li>
                                                                            @endif
                                                                        @endforeach
                                                                    </ul>
                                                                    <div class="generic-btn">
                                                                        <a href="{{ $cardButtonUrl !== '' ? $cardButtonUrl : '#' }}" data-preview-cta-link>
                                                                            <span data-preview-cta-text>{{ $cardButtonText !== '' ? $cardButtonText : 'BUY TICKET' }}</span>
                                                                            <i class="fas fa-arrow-right"></i>
                                                                        </a>
                                                                    </div>
                                                                    <div class="recomended-box @if ($cardBadge === '') d-none @endif" data-preview-badge>{{ $cardBadge }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>

                                        <div class="alert alert-light border mt-3 mb-0 @if (count($extraCards) > 0) d-none @endif" data-extra-cards-empty>
                                            Belum ada card tambahan.
                                        </div>

                                        <div class="d-flex justify-content-end mt-3">
                                            <button type="button" class="btn btn-outline-primary btn-sm" data-extra-card-add>
                                                <i class="la la-plus" aria-hidden="true"></i>
                                                Tambah Card
                                            </button>
                                        </div>
                                    </div>

                                    <div class="row g-3 mt-3">
                                        <div class="col-12" data-field-item data-hay="ticket pricing teks bawah bottom_text">
                                            <label class="form-label text-black" for="pricing-bottom-text">Teks bawah</label>
                                            <textarea
                                                id="pricing-bottom-text"
                                                name="contents[pricing][bottom_text]"
                                                class="form-control @error('contents.pricing.bottom_text') is-invalid @enderror"
                                                rows="3"
                                                data-pricing-input
                                                data-plan="section"
                                                data-field="bottom_text"
                                            >{{ $pricingBottomText }}</textarea>
                                            @error('contents.pricing.bottom_text')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12 col-md-6" data-field-item data-hay="ticket pricing tombol bawah text bottom_button_text">
                                            <label class="form-label text-black" for="pricing-bottom-button-text">Teks tombol bawah</label>
                                            <input
                                                id="pricing-bottom-button-text"
                                                name="contents[pricing][bottom_button_text]"
                                                type="text"
                                                class="form-control @error('contents.pricing.bottom_button_text') is-invalid @enderror"
                                                value="{{ $pricingBottomButtonText }}"
                                            >
                                            @error('contents.pricing.bottom_button_text')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12 col-md-6" data-field-item data-hay="ticket pricing tombol bawah url bottom_button_url">
                                            <label class="form-label text-black" for="pricing-bottom-button-url">Link tombol bawah</label>
                                            <input
                                                id="pricing-bottom-button-url"
                                                name="contents[pricing][bottom_button_url]"
                                                type="url"
                                                class="form-control @error('contents.pricing.bottom_button_url') is-invalid @enderror"
                                                value="{{ $pricingBottomButtonUrl }}"
                                                placeholder="https://"
                                            >
                                            @error('contents.pricing.bottom_button_url')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @else
                                    <div class="row g-3">
                                        @foreach ($fields as $field)
                                            @php
                                                $key = $field['key'];
                                                $name = "contents[{$section}][{$key}]";
                                                $oldKey = "contents.{$section}.{$key}";
                                                $val = old($oldKey, $values[$section][$key] ?? '');
                                                $type = $field['type'] ?? 'textarea';
                                                $rows = (int) ($field['rows'] ?? 4);
                                                $placeholder = $field['placeholder'] ?? null;
                                                $help = $field['help'] ?? null;

                                                $rawVal = (string) $val;
                                                $previewUrl = '';
                                                if ($rawVal !== '') {
                                                    $previewUrl = preg_match('/^https?:\\/\\//i', $rawVal) ? $rawVal : asset(ltrim($rawVal, '/'));
                                                }

                                                $colClass = match ($type) {
                                                    'textarea' => 'col-12',
                                                    'image' => 'col-12 col-xxl-6',
                                                    'number' => 'col-12 col-md-6 col-xxl-4',
                                                    default => 'col-12 col-md-6',
                                                };
                                            @endphp
                                            <div
                                                class="{{ $colClass }}"
                                                data-field-item
                                                data-hay="{{ strtolower($field['label'] . ' ' . $section . ' ' . $key) }}"
                                            >
                                                @if ($type === 'image')
                                                    <div
                                                        class="border rounded p-3 h-100"
                                                        data-image-field
                                                        data-page="{{ $page ?? 'home' }}"
                                                        data-section="{{ $section }}"
                                                        data-key="{{ $key }}"
                                                    >
                                                        <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-2">
                                                            <div>
                                                                <div class="form-label text-black mb-0">{{ $field['label'] }}</div>
                                                                @if ($help)
                                                                    <div class="text-muted small">{{ $help }}</div>
                                                                @endif
                                                            </div>
                                                            <button type="button" class="btn btn-outline-danger btn-xxs" data-image-clear>
                                                                <i class="la la-trash" aria-hidden="true"></i>
                                                                Hapus
                                                            </button>
                                                        </div>

                                                        <input type="hidden" name="{{ $name }}" value="{{ $rawVal }}" data-image-value>

                                                        <div class="row g-3">
                                                            <div class="col-12 col-md-7">
                                                                <div
                                                                    class="border rounded p-3 text-center bg-light h-100 d-flex flex-column justify-content-center"
                                                                    role="button"
                                                                    tabindex="0"
                                                                    data-image-dropzone
                                                                >
                                                                    <div class="fw-semibold text-black">Upload gambar</div>
                                                                    <div class="text-muted small">Klik atau drag & drop</div>
                                                                    <div class="mt-2 small text-muted" data-image-status></div>
                                                                </div>
                                                                <input type="file" class="d-none" accept="image/jpeg,image/png" data-image-file>
                                                                <div class="progress mt-3 d-none" style="height: 8px;" data-image-progress>
                                                                    <div
                                                                        class="progress-bar"
                                                                        role="progressbar"
                                                                        style="width: 0%"
                                                                        aria-valuemin="0"
                                                                        aria-valuemax="100"
                                                                        aria-valuenow="0"
                                                                        data-image-progress-bar
                                                                    ></div>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 col-md-5">
                                                                <div class="ratio ratio-16x9 bg-light border rounded overflow-hidden">
                                                                    <img
                                                                        src="{{ $previewUrl }}"
                                                                        alt="Preview {{ $field['label'] }}"
                                                                        class="w-100 h-100 object-fit-cover @if (!$previewUrl) d-none @endif"
                                                                        data-image-preview
                                                                    >
                                                                    <div
                                                                        class="d-flex align-items-center justify-content-center text-muted small @if ($previewUrl) d-none @endif"
                                                                        data-image-empty
                                                                    >
                                                                        Belum ada gambar
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex flex-wrap gap-2 mt-2">
                                                                    <a
                                                                        class="btn btn-outline-secondary btn-xxs @if (!$previewUrl) disabled @endif"
                                                                        href="{{ $previewUrl ?: '#' }}"
                                                                        target="_blank"
                                                                        rel="noopener"
                                                                        data-image-open
                                                                    >
                                                                        <i class="la la-external-link" aria-hidden="true"></i>
                                                                        Buka
                                                                    </a>
                                                                    <button type="button" class="btn btn-outline-secondary btn-xxs" data-image-copy>
                                                                        <i class="la la-copy" aria-hidden="true"></i>
                                                                        Copy path
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <label class="form-label text-black" for="{{ $section }}-{{ $key }}-path">Path</label>
                                                                <input
                                                                    id="{{ $section }}-{{ $key }}-path"
                                                                    type="text"
                                                                    class="form-control"
                                                                    value="{{ $rawVal }}"
                                                                    placeholder="Contoh: assetsAdmin/uploads/konten-halaman/…"
                                                                    data-image-path
                                                                >
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <label class="form-label text-black" for="{{ $section }}-{{ $key }}">
                                                        {{ $field['label'] }}
                                                    </label>
                                                    @if ($type === 'textarea')
                                                        <textarea
                                                            id="{{ $section }}-{{ $key }}"
                                                            name="{{ $name }}"
                                                            class="form-control @error($oldKey) is-invalid @enderror"
                                                            rows="{{ $rows }}"
                                                            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
                                                        >{{ $val }}</textarea>
                                                    @else
                                                        <input
                                                            id="{{ $section }}-{{ $key }}"
                                                            name="{{ $name }}"
                                                            type="{{ $type === 'url' ? 'url' : ($type === 'number' ? 'number' : 'text') }}"
                                                            class="form-control @error($oldKey) is-invalid @enderror"
                                                            value="{{ $rawVal }}"
                                                            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
                                                            @if ($type === 'number') inputmode="numeric" @endif
                                                        >
                                                    @endif

                                                    @if ($help)
                                                        <div class="text-muted small">{{ $help }}</div>
                                                    @endif

                                                    @error($oldKey)
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="alert alert-light border d-none mt-3 mb-0" data-empty-state>
                                    Tidak ada field yang cocok dengan pencarian.
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="text-muted small">Perubahan tersimpan ke database saat menekan Simpan.</div>
                    <button type="submit" class="btn btn-primary" id="save-btn">
                        <span
                            class="spinner-border spinner-border-sm d-none me-2"
                            role="status"
                            aria-hidden="true"
                            id="save-spinner"
                        ></span>
                        Simpan
                    </button>
                </div>
            </div>
        </form>

        <script>
            (function () {
                const csrfToken = @json(csrf_token());
                const page = @json($page ?? 'home');
                const uploadUrl = @json(route('admin.konten-halaman.upload-image'));

                const form = document.getElementById('content-form');
                const saveBtn = document.getElementById('save-btn');
                const saveSpinner = document.getElementById('save-spinner');
                const notifyArea = document.getElementById('notify-area');
                const fieldSearch = document.getElementById('field-search');

                function escapeHtml(value) {
                    return String(value ?? '')
                        .replaceAll('&', '&amp;')
                        .replaceAll('<', '&lt;')
                        .replaceAll('>', '&gt;')
                        .replaceAll('"', '&quot;')
                        .replaceAll("'", '&#039;');
                }

                function pushNotice(type, title, message) {
                    if (!notifyArea) return;
                    const wrapper = document.createElement('div');
                    wrapper.className = `alert alert-${type} alert-dismissible fade show`;
                    wrapper.setAttribute('role', 'alert');
                    wrapper.innerHTML = `
                        <div class="fw-semibold">${escapeHtml(title)}</div>
                        <div>${escapeHtml(message)}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                    `;
                    notifyArea.prepend(wrapper);
                }

                function setSaving(on) {
                    if (saveBtn) saveBtn.disabled = !!on;
                    if (saveSpinner) saveSpinner.classList.toggle('d-none', !on);
                }

                if (form) {
                    form.addEventListener('submit', () => setSaving(true));
                }

                function isImageFile(file) {
                    const okType = ['image/jpeg', 'image/png'].includes(file.type);
                    const okSize = file.size <= 2 * 1024 * 1024;
                    if (!okType) return { ok: false, message: 'Format harus JPEG atau PNG.' };
                    if (!okSize) return { ok: false, message: 'Ukuran maksimal 2MB.' };
                    return { ok: true, message: '' };
                }

                function buildAssetUrl(path) {
                    const raw = String(path || '').trim();
                    if (!raw) return '';
                    if (/^https?:\/\//i.test(raw)) return raw;
                    return @json(url('/')) + '/' + raw.replace(/^\/+/, '');
                }

                function copyText(text) {
                    const raw = String(text || '');
                    if (!raw) return;
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(raw).catch(() => {});
                        return;
                    }
                    const area = document.createElement('textarea');
                    area.value = raw;
                    area.setAttribute('readonly', 'readonly');
                    area.style.position = 'fixed';
                    area.style.left = '-9999px';
                    document.body.appendChild(area);
                    area.select();
                    try { document.execCommand('copy'); } catch (_) {}
                    area.remove();
                }

                function wireImageField(wrapper) {
                    const section = wrapper.dataset.section || '';
                    const key = wrapper.dataset.key || '';

                    const valueEl = wrapper.querySelector('[data-image-value]');
                    const dropzone = wrapper.querySelector('[data-image-dropzone]');
                    const fileInput = wrapper.querySelector('[data-image-file]');
                    const statusEl = wrapper.querySelector('[data-image-status]');
                    const progressWrap = wrapper.querySelector('[data-image-progress]');
                    const progressBar = wrapper.querySelector('[data-image-progress-bar]');
                    const preview = wrapper.querySelector('[data-image-preview]');
                    const empty = wrapper.querySelector('[data-image-empty]');
                    const openBtn = wrapper.querySelector('[data-image-open]');
                    const pathInput = wrapper.querySelector('[data-image-path]');
                    const clearBtn = wrapper.querySelector('[data-image-clear]');
                    const copyBtn = wrapper.querySelector('[data-image-copy]');

                    function setPreview(url) {
                        const has = !!url;
                        if (preview) {
                            preview.src = url || '';
                            preview.classList.toggle('d-none', !has);
                        }
                        if (empty) empty.classList.toggle('d-none', has);
                        if (openBtn) {
                            openBtn.classList.toggle('disabled', !has);
                            openBtn.setAttribute('href', has ? url : '#');
                        }
                    }

                    function setValue(path, previewUrl) {
                        const raw = String(path || '').trim();
                        if (valueEl) valueEl.value = raw;
                        if (pathInput && pathInput.value !== raw) pathInput.value = raw;
                        setPreview(previewUrl || buildAssetUrl(raw));
                    }

                    function setUploading(on) {
                        if (dropzone) dropzone.classList.toggle('opacity-50', !!on);
                        if (dropzone) dropzone.classList.toggle('pe-none', !!on);
                    }

                    function setProgress(percent) {
                        if (!progressWrap || !progressBar) return;
                        progressWrap.classList.remove('d-none');
                        const p = Math.max(0, Math.min(100, Number(percent || 0)));
                        progressBar.style.width = `${p}%`;
                        progressBar.setAttribute('aria-valuenow', String(p));
                    }

                    function resetProgress() {
                        if (!progressWrap || !progressBar) return;
                        progressWrap.classList.add('d-none');
                        progressBar.style.width = '0%';
                        progressBar.setAttribute('aria-valuenow', '0');
                        progressBar.classList.remove('bg-danger', 'bg-success');
                    }

                    function uploadFile(file) {
                        const check = isImageFile(file);
                        if (!check.ok) {
                            pushNotice('warning', 'File ditolak', check.message);
                            return;
                        }

                        const objectUrl = URL.createObjectURL(file);
                        setPreview(objectUrl);

                        setUploading(true);
                        resetProgress();
                        setProgress(1);
                        if (statusEl) statusEl.textContent = `Uploading… ${file.name}`;

                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', uploadUrl, true);
                        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                        xhr.upload.onprogress = (e) => {
                            if (!e.lengthComputable) return;
                            const percent = Math.round((e.loaded / e.total) * 100);
                            setProgress(percent);
                        };

                        xhr.onload = () => {
                            let payload = null;
                            try { payload = JSON.parse(xhr.responseText || '{}'); } catch (_) {}

                            if (xhr.status >= 200 && xhr.status < 300 && payload && payload.data) {
                                if (progressBar) progressBar.classList.add('bg-success');
                                if (statusEl) statusEl.textContent = 'Selesai';
                                setValue(payload.data.path || '', payload.data.url || '');
                            } else {
                                if (progressBar) progressBar.classList.add('bg-danger');
                                if (statusEl) statusEl.textContent = 'Gagal upload';
                                const msg = payload && payload.message ? payload.message : 'Upload gagal diproses.';
                                pushNotice('danger', 'Gagal', msg);
                            }

                            setUploading(false);
                            setTimeout(() => {
                                resetProgress();
                                if (statusEl) statusEl.textContent = '';
                            }, 900);
                            try { URL.revokeObjectURL(objectUrl); } catch (_) {}
                        };

                        xhr.onerror = () => {
                            if (progressBar) progressBar.classList.add('bg-danger');
                            if (statusEl) statusEl.textContent = 'Gagal upload';
                            pushNotice('danger', 'Gagal', 'Koneksi bermasalah saat upload.');
                            setUploading(false);
                        };

                        const form = new FormData();
                        form.set('page', page);
                        form.set('section', section);
                        form.set('key', key);
                        form.set('file', file, file.name);
                        xhr.send(form);
                    }

                    if (dropzone && fileInput) {
                        dropzone.addEventListener('click', () => fileInput.click());
                        dropzone.addEventListener('keydown', (e) => {
                            if (e.key === 'Enter' || e.key === ' ') {
                                e.preventDefault();
                                fileInput.click();
                            }
                        });
                        dropzone.addEventListener('dragover', (e) => {
                            e.preventDefault();
                            dropzone.classList.add('border-primary');
                        });
                        dropzone.addEventListener('dragleave', () => dropzone.classList.remove('border-primary'));
                        dropzone.addEventListener('drop', (e) => {
                            e.preventDefault();
                            dropzone.classList.remove('border-primary');
                            const file = e.dataTransfer?.files?.[0];
                            if (file) uploadFile(file);
                        });
                        fileInput.addEventListener('change', () => {
                            const file = fileInput.files?.[0];
                            if (file) uploadFile(file);
                            fileInput.value = '';
                        });
                    }

                    if (pathInput) {
                        pathInput.addEventListener('input', () => {
                            const raw = String(pathInput.value || '').trim();
                            setValue(raw, buildAssetUrl(raw));
                        });
                    }

                    if (clearBtn) {
                        clearBtn.addEventListener('click', () => {
                            setValue('', '');
                            if (statusEl) statusEl.textContent = '';
                            resetProgress();
                        });
                    }

                    if (copyBtn) {
                        copyBtn.addEventListener('click', () => {
                            const raw = valueEl ? valueEl.value : (pathInput ? pathInput.value : '');
                            copyText(raw);
                            pushNotice('success', 'Tersalin', 'Path gambar berhasil disalin.');
                        });
                    }
                }

                document.querySelectorAll('[data-image-field]').forEach((el) => wireImageField(el));

                (function wirePricingCards() {
                    const currencySymbolByCode = {
                        USD: '$',
                        IDR: 'Rp',
                        EUR: '€',
                    };

                    function getInput(plan, field) {
                        return document.querySelector(`[data-pricing-input][data-plan="${plan}"][data-field="${field}"]`);
                    }

                    function getInputValue(plan, field) {
                        const el = getInput(plan, field);
                        if (!el) return '';
                        if (el.type === 'checkbox') return el.checked ? '1' : '0';
                        return String(el.value || '');
                    }

                    function setText(el, text) {
                        if (!el) return;
                        el.textContent = String(text ?? '');
                    }

                    function normalizeFeatures(raw) {
                        return String(raw || '')
                            .split(/\r\n|\r|\n/g)
                            .map((v) => String(v).trim())
                            .filter((v) => v !== '');
                    }

                    function syncFeaturesStorage(plan) {
                        const storage = document.querySelector(`[data-features-storage][data-plan="${plan}"]`);
                        const inputs = Array.from(document.querySelectorAll(`[data-features-input][data-plan="${plan}"]`));
                        if (!storage) return [];

                        const values = inputs
                            .map((el) => String(el.value || '').trim())
                            .filter((v) => v !== '');

                        storage.value = values.join('\n');
                        return values;
                    }

                    function renumberFeatures(plan) {
                        const rows = Array.from(document.querySelectorAll(`[data-features-list][data-plan="${plan}"] [data-feature-row]`));
                        rows.forEach((row, i) => {
                            const indexEl = row.querySelector('[data-feature-index]');
                            if (indexEl) indexEl.textContent = String(i + 1);
                        });
                    }

                    function updatePlanPreview(plan) {
                        const previewWrap = document.querySelector(`[data-pricing-preview][data-plan="${plan}"]`);
                        if (!previewWrap) return;

                        const isActive = getInputValue(plan, 'active') !== '0';
                        const title = getInputValue(plan, 'title').trim() || 'Nama Paket';
                        const subtitle = getInputValue(plan, 'subtitle').trim() || 'Deskripsi singkat';
                        const price = getInputValue(plan, 'price').trim() || '0';
                        const currencyCode = getInputValue(plan, 'currency').trim() || 'USD';
                        const currencySymbol = currencySymbolByCode[currencyCode] || '$';
                        const ctaText = getInputValue(plan, 'cta_text').trim() || 'BUY TICKET';
                        const ctaUrl = getInputValue(plan, 'cta_url').trim() || '#';
                        const badge = getInputValue(plan, 'badge').trim();

                        setText(previewWrap.querySelector('[data-preview-title]'), title);
                        setText(previewWrap.querySelector('[data-preview-subtitle]'), subtitle);
                        setText(previewWrap.querySelector('[data-preview-price]'), price);
                        setText(previewWrap.querySelector('[data-preview-currency]'), currencySymbol);
                        setText(previewWrap.querySelector('[data-preview-cta-text]'), ctaText);

                        const ctaLink = previewWrap.querySelector('[data-preview-cta-link]');
                        if (ctaLink) ctaLink.setAttribute('href', ctaUrl || '#');

                        const inactiveBadge = previewWrap.querySelector('[data-preview-inactive]');
                        if (inactiveBadge) inactiveBadge.classList.toggle('d-none', isActive);

                        const badgeEl = previewWrap.querySelector('[data-preview-badge]');
                        if (badgeEl) {
                            setText(badgeEl, badge);
                            badgeEl.classList.toggle('d-none', badge === '');
                        }

                        const quickTitle = document.querySelector(`[data-pricing-quick-title][data-plan="${plan}"]`);
                        if (quickTitle) setText(quickTitle, title === 'Nama Paket' ? '' : title);

                        const quickStatus = document.querySelector(`[data-pricing-quick-status][data-plan="${plan}"]`);
                        if (quickStatus) setText(quickStatus, isActive ? 'Aktif' : 'Nonaktif');

                        const activeLabel = document.querySelector(`label.form-check-label[for="pricing-${plan}-active"]`);
                        if (activeLabel) setText(activeLabel, isActive ? 'Aktif' : 'Nonaktif');

                        const features = syncFeaturesStorage(plan);
                        const list = previewWrap.querySelector('[data-preview-features]');
                        if (list) {
                            list.innerHTML = features.map((v) => `<li class="position-relative">${escapeHtml(v)}</li>`).join('');
                        }

                        return { isActive };
                    }

                    function addFeatureRow(plan, value) {
                        const list = document.querySelector(`[data-features-list][data-plan="${plan}"]`);
                        if (!list) return;

                        const row = document.createElement('div');
                        row.className = 'input-group';
                        row.setAttribute('data-feature-row', '');
                        row.innerHTML = `
                            <span class="input-group-text" data-feature-index>0</span>
                            <input type="text" class="form-control" value="${escapeHtml(value ?? '')}" data-features-input data-plan="${escapeHtml(plan)}">
                            <button type="button" class="btn btn-outline-danger" data-features-remove>
                                <i class="la la-trash" aria-hidden="true"></i>
                            </button>
                        `;
                        list.appendChild(row);
                        renumberFeatures(plan);
                    }

                    document.querySelectorAll('[data-features-add]').forEach((btn) => {
                        btn.addEventListener('click', () => {
                            const plan = btn.getAttribute('data-plan') || '';
                            if (!plan) return;
                            addFeatureRow(plan, '');
                            updatePlanPreview(plan);
                        });
                    });

                    document.addEventListener('click', (e) => {
                        const removeBtn = e.target?.closest?.('[data-features-remove]');
                        if (!removeBtn) return;
                        const row = removeBtn.closest('[data-feature-row]');
                        const plan = row?.querySelector?.('[data-features-input]')?.getAttribute?.('data-plan') || '';
                        if (row) row.remove();

                        if (plan) {
                            const remaining = document.querySelectorAll(`[data-features-list][data-plan="${plan}"] [data-feature-row]`);
                            if (remaining.length === 0) addFeatureRow(plan, '');
                            renumberFeatures(plan);
                            updatePlanPreview(plan);
                        }
                    });

                    document.addEventListener('input', (e) => {
                        const input = e.target;
                        if (!input) return;

                        if (input.matches?.('[data-features-input]')) {
                            const plan = input.getAttribute('data-plan') || '';
                            if (plan) updatePlanPreview(plan);
                            return;
                        }

                        if (input.matches?.('[data-pricing-input]')) {
                            const plan = input.getAttribute('data-plan') || '';
                            if (plan && plan !== 'section') updatePlanPreview(plan);
                        }
                    });

                    document.addEventListener('change', (e) => {
                        const input = e.target;
                        if (!input || !input.matches?.('[data-pricing-input]')) return;
                        const plan = input.getAttribute('data-plan') || '';
                        if (plan && plan !== 'section') updatePlanPreview(plan);
                    });

                    ['silver', 'gold', 'premium'].forEach((plan) => {
                        renumberFeatures(plan);
                        updatePlanPreview(plan);
                    });
                })();

                (function wireExtraPricingCards() {
                    const root = document.querySelector('[data-extra-cards]');
                    if (!root) return;

                    const addBtn = root.querySelector('[data-extra-card-add]');
                    const list = root.querySelector('[data-extra-cards-list]');
                    const storage = root.querySelector('[data-extra-cards-storage]');
                    const empty = root.querySelector('[data-extra-cards-empty]');

                    if (!addBtn || !list || !storage) return;

                    const storeUrl = String(list.dataset.storeUrl || '');
                    const updateTpl = String(list.dataset.updateUrlTemplate || '');
                    const destroyTpl = String(list.dataset.destroyUrlTemplate || '');
                    const currencySymbolByCode = {
                        USD: '$',
                        IDR: 'Rp',
                        EUR: '€',
                    };
                    const wrapCycle = ['silver-ticket-details', 'gold-ticket-details', 'premium-ticket-details'];

                    function parseJson(raw, fallback) {
                        try {
                            const v = JSON.parse(String(raw || ''));
                            return Array.isArray(v) ? v : fallback;
                        } catch (_) {
                            return fallback;
                        }
                    }

                    let cards = parseJson(storage.value, []);

                    function syncStorage() {
                        storage.value = JSON.stringify(cards);
                        if (empty) empty.classList.toggle('d-none', cards.length > 0);
                    }

                    function urlFromTemplate(tpl, id) {
                        return tpl.replace('__ID__', encodeURIComponent(String(id || '')));
                    }

                    function setStatus(cardEl, text, isError) {
                        const el = cardEl.querySelector('[data-card-status]');
                        if (!el) return;
                        el.textContent = String(text || '');
                        el.classList.toggle('text-danger', !!isError);
                    }

                    function setError(cardEl, payload) {
                        const box = cardEl.querySelector('[data-card-error]');
                        if (!box) return;

                        const errors = payload && payload.errors ? payload.errors : null;
                        if (!errors || typeof errors !== 'object') {
                            box.classList.add('d-none');
                            box.textContent = '';
                            return;
                        }

                        const lines = [];
                        Object.values(errors).forEach((arr) => {
                            if (Array.isArray(arr)) {
                                arr.forEach((m) => lines.push(String(m)));
                            } else if (arr) {
                                lines.push(String(arr));
                            }
                        });

                        if (lines.length === 0) {
                            box.classList.add('d-none');
                            box.textContent = '';
                            return;
                        }

                        box.classList.remove('d-none');
                        box.innerHTML = `<div class="fw-semibold mb-1">Gagal menyimpan</div><ul class="mb-0 ps-3">${lines
                            .map((v) => `<li>${escapeHtml(v)}</li>`)
                            .join('')}</ul>`;
                    }

                    function getField(cardEl, name) {
                        return cardEl.querySelector(`[data-card-field="${name}"]`);
                    }

                    function normalizeFeaturesText(raw) {
                        return String(raw || '')
                            .split(/\r\n|\r|\n/g)
                            .map((v) => String(v).trim())
                            .filter((v) => v !== '');
                    }

                    function syncExtraFeaturesStorage(cardEl) {
                        const storageEl = getField(cardEl, 'features');
                        const inputs = Array.from(cardEl.querySelectorAll('[data-extra-features-input]'));
                        if (!storageEl) return [];

                        const values = inputs
                            .map((el) => String(el.value || '').trim())
                            .filter((v) => v !== '');

                        storageEl.value = values.join('\n');
                        return values;
                    }

                    function renumberExtraFeatures(cardEl) {
                        const rows = Array.from(cardEl.querySelectorAll('[data-extra-feature-row]'));
                        rows.forEach((row, i) => {
                            const indexEl = row.querySelector('[data-extra-feature-index]');
                            if (indexEl) indexEl.textContent = String(i + 1);
                        });
                    }

                    function addExtraFeatureRow(cardEl, value) {
                        const listEl = cardEl.querySelector('[data-extra-features-list]');
                        if (!listEl) return;

                        const row = document.createElement('div');
                        row.className = 'input-group';
                        row.setAttribute('data-extra-feature-row', '');
                        row.innerHTML = `
                            <span class="input-group-text" data-extra-feature-index>0</span>
                            <input type="text" class="form-control" value="${escapeHtml(value ?? '')}" data-extra-features-input>
                            <button type="button" class="btn btn-outline-danger" data-extra-features-remove>
                                <i class="la la-trash" aria-hidden="true"></i>
                            </button>
                        `;
                        listEl.appendChild(row);
                        renumberExtraFeatures(cardEl);
                    }

                    function getWrapClass(cardEl) {
                        const raw = String(cardEl.dataset.wrapClass || cardEl.getAttribute('data-wrap-class') || '').trim();
                        if (raw) return raw;
                        const idx = Array.from(list.querySelectorAll('[data-extra-card]')).indexOf(cardEl);
                        const normalized = idx < 0 ? 0 : idx;
                        return wrapCycle[(normalized + 3) % wrapCycle.length] || wrapCycle[0];
                    }

                    function updateExtraPreview(cardEl) {
                        const previewWrap = cardEl.querySelector('[data-extra-card-preview]');
                        if (!previewWrap) return;

                        const activeEl = getField(cardEl, 'active');
                        const isActive = activeEl && activeEl.type === 'checkbox' && activeEl.checked;
                        const title = String(getField(cardEl, 'title')?.value || '').trim() || 'Nama Paket';
                        const subtitle = String(getField(cardEl, 'subtitle')?.value || '').trim() || 'Deskripsi singkat';
                        const price = String(getField(cardEl, 'price')?.value || '').trim() || '0';
                        const currencyCode = String(getField(cardEl, 'currency')?.value || 'USD').trim() || 'USD';
                        const currencySymbol = currencySymbolByCode[currencyCode] || '$';
                        const ctaText = String(getField(cardEl, 'button_text')?.value || '').trim() || 'BUY TICKET';
                        const ctaUrl = String(getField(cardEl, 'button_url')?.value || '').trim() || '#';
                        const badge = String(getField(cardEl, 'badge')?.value || '').trim();

                        const labelEl = activeEl?.closest?.('.form-check')?.querySelector?.('.form-check-label');
                        if (labelEl) setText(labelEl, isActive ? 'Aktif' : 'Nonaktif');

                        setText(previewWrap.querySelector('[data-preview-title]'), title);
                        setText(previewWrap.querySelector('[data-preview-subtitle]'), subtitle);
                        setText(previewWrap.querySelector('[data-preview-price]'), price);
                        setText(previewWrap.querySelector('[data-preview-currency]'), currencySymbol);
                        setText(previewWrap.querySelector('[data-preview-cta-text]'), ctaText);

                        const ctaLink = previewWrap.querySelector('[data-preview-cta-link]');
                        if (ctaLink) ctaLink.setAttribute('href', ctaUrl || '#');

                        const inactiveBadge = previewWrap.querySelector('[data-preview-inactive]');
                        if (inactiveBadge) inactiveBadge.classList.toggle('d-none', isActive);

                        const badgeEl = previewWrap.querySelector('[data-preview-badge]');
                        if (badgeEl) {
                            setText(badgeEl, badge);
                            badgeEl.classList.toggle('d-none', badge === '');
                        }

                        const features = syncExtraFeaturesStorage(cardEl);
                        const listEl = previewWrap.querySelector('[data-preview-features]');
                        if (listEl) {
                            listEl.innerHTML = features.map((v) => `<li class="position-relative">${escapeHtml(v)}</li>`).join('');
                        }

                        const wrapClass = getWrapClass(cardEl);
                        cardEl.dataset.wrapClass = wrapClass;
                        const ticketEl = previewWrap.classList.contains('ticket-details') ? previewWrap : null;
                        if (ticketEl) {
                            wrapCycle.forEach((c) => ticketEl.classList.remove(c));
                            ticketEl.classList.add(wrapClass);
                        }
                    }

                    function readCardFromDom(cardEl) {
                        const id = String(cardEl.dataset.cardId || '');
                        const activeEl = getField(cardEl, 'active');
                        const active = activeEl && activeEl.type === 'checkbox' && activeEl.checked ? '1' : '0';

                        syncExtraFeaturesStorage(cardEl);

                        return {
                            id,
                            active,
                            title: String(getField(cardEl, 'title')?.value || ''),
                            subtitle: String(getField(cardEl, 'subtitle')?.value || ''),
                            currency: String(getField(cardEl, 'currency')?.value || 'USD'),
                            price: String(getField(cardEl, 'price')?.value || ''),
                            features: normalizeFeaturesText(getField(cardEl, 'features')?.value || ''),
                            button_text: String(getField(cardEl, 'button_text')?.value || ''),
                            button_url: String(getField(cardEl, 'button_url')?.value || ''),
                            badge: String(getField(cardEl, 'badge')?.value || ''),
                        };
                    }

                    function upsertCard(card) {
                        const id = String(card.id || '');
                        if (!id) return;
                        const idx = cards.findIndex((c) => String(c.id || '') === id);
                        if (idx === -1) cards.push(card);
                        else cards[idx] = { ...cards[idx], ...card };
                        syncStorage();
                    }

                    function removeCard(id) {
                        const key = String(id || '');
                        cards = cards.filter((c) => String(c.id || '') !== key);
                        syncStorage();
                    }

                    function renderCard(card) {
                        const id = String(card.id || '');
                        const cardEl = document.createElement('div');
                        cardEl.className = 'border rounded p-3 bg-white';
                        cardEl.setAttribute('data-extra-card', '');
                        cardEl.setAttribute('data-card-id', id);
                        cardEl.dataset.cardId = id;

                        const checked = String(card.active || '0') !== '0' ? 'checked' : '';
                        const features = Array.isArray(card.features) ? card.features : [];
                        const featureRows = features.length > 0 ? features : [''];
                        const featuresText = featureRows.map((v) => String(v ?? '')).join('\n');
                        const wrapClass =
                            wrapCycle[(Array.from(list.querySelectorAll('[data-extra-card]')).length + 3) % wrapCycle.length] || wrapCycle[0];
                        const currencyCode = String(card.currency || 'USD');
                        const currencySymbol = currencySymbolByCode[currencyCode] || '$';
                        const isActive = String(card.active || '0') !== '0';

                        cardEl.innerHTML = `
                            <div class="row g-4">
                                <div class="col-12 col-lg-7">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                                <div class="fw-semibold text-black">Card</div>
                                                <button type="button" class="btn btn-outline-danger btn-xxs" data-extra-card-delete>
                                                    <i class="la la-trash" aria-hidden="true"></i>
                                                    Hapus
                                                </button>
                                            </div>
                                            <div class="text-muted small mt-1" data-card-status></div>
                                        </div>

                                        <div class="col-12" data-field-item data-hay="pricing card tambahan aktif status aktif nonaktif">
                                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                                <div class="fw-semibold text-black">Status Card</div>
                                                <div class="form-check form-switch m-0">
                                                    <input class="form-check-input" type="checkbox" role="switch" ${checked} data-card-field="active">
                                                    <label class="form-check-label">${isActive ? 'Aktif' : 'Nonaktif'}</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12" data-field-item data-hay="pricing card tambahan judul nama paket title">
                                            <label class="form-label text-black">Judul / Nama paket</label>
                                            <input type="text" class="form-control" value="${escapeHtml(card.title || '')}" data-card-field="title">
                                        </div>
                                        <div class="col-12" data-field-item data-hay="pricing card tambahan deskripsi singkat subtitle">
                                            <label class="form-label text-black">Deskripsi singkat</label>
                                            <input type="text" class="form-control" value="${escapeHtml(card.subtitle || '')}" data-card-field="subtitle">
                                        </div>

                                        <div class="col-12" data-field-item data-hay="pricing card tambahan currency mata uang harga price">
                                            <div class="d-flex flex-column flex-md-row align-items-md-end gap-3">
                                                <div class="w-100 flex-md-shrink-0" style="max-width: 260px;">
                                                    <label class="form-label text-black">Mata uang</label>
                                                    <select class="form-select" data-card-field="currency">
                                                        <option value="USD" ${String(card.currency || 'USD') === 'USD' ? 'selected' : ''}>USD ($)</option>
                                                        <option value="IDR" ${String(card.currency || '') === 'IDR' ? 'selected' : ''}>IDR (Rp)</option>
                                                        <option value="EUR" ${String(card.currency || '') === 'EUR' ? 'selected' : ''}>EUR (€)</option>
                                                    </select>
                                                </div>
                                                <div class="w-100 flex-grow-1">
                                                    <label class="form-label text-black">Harga</label>
                                                    <input type="number" step="0.01" min="0" inputmode="decimal" class="form-control" value="${escapeHtml(
                                                        card.price || ''
                                                    )}" data-card-field="price">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12" data-field-item data-hay="pricing card tambahan fitur features list tambah hapus">
                                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                                <div class="form-label text-black mb-0">Fitur-fitur</div>
                                                <button type="button" class="btn btn-outline-primary btn-xxs" data-extra-features-add>
                                                    <i class="la la-plus" aria-hidden="true"></i>
                                                    Tambah fitur
                                                </button>
                                            </div>
                                            <div class="d-flex flex-column gap-2 mt-2" data-extra-features-list>
                                                ${featureRows
                                                    .map(
                                                        (v) => `
                                                    <div class="input-group" data-extra-feature-row>
                                                        <span class="input-group-text" data-extra-feature-index>0</span>
                                                        <input type="text" class="form-control" value="${escapeHtml(v ?? '')}" data-extra-features-input>
                                                        <button type="button" class="btn btn-outline-danger" data-extra-features-remove>
                                                            <i class="la la-trash" aria-hidden="true"></i>
                                                        </button>
                                                    </div>
                                                `
                                                    )
                                                    .join('')}
                                            </div>
                                            <textarea class="d-none" data-card-field="features">${escapeHtml(featuresText)}</textarea>
                                        </div>

                                        <div class="col-12 col-md-6" data-field-item data-hay="pricing card tambahan tombol cta text">
                                            <label class="form-label text-black">Tombol CTA (teks)</label>
                                            <input type="text" class="form-control" value="${escapeHtml(card.button_text || '')}" data-card-field="button_text">
                                        </div>
                                        <div class="col-12 col-md-6" data-field-item data-hay="pricing card tambahan tombol cta url">
                                            <label class="form-label text-black">Tombol CTA (link)</label>
                                            <input type="url" class="form-control" placeholder="https://" value="${escapeHtml(
                                                card.button_url || ''
                                            )}" data-card-field="button_url">
                                        </div>
                                        <div class="col-12" data-field-item data-hay="pricing card tambahan badge">
                                            <label class="form-label text-black">Badge (opsional)</label>
                                            <input type="text" class="form-control" value="${escapeHtml(card.badge || '')}" data-card-field="badge">
                                        </div>
                                    </div>

                                    <div class="alert alert-danger d-none mt-3 mb-0" role="alert" data-card-error></div>
                                </div>

                                <div class="col-12 col-lg-5">
                                    <div class="fw-semibold text-black mb-2">Preview</div>
                                    <div class="ticket-details ${wrapClass} position-relative" data-extra-card-preview>
                                        <div class="d-flex align-items-center justify-content-between gap-2">
                                            <h3 class="mb-0" data-preview-title>${escapeHtml(String(card.title || '').trim() || 'Nama Paket')}</h3>
                                            <span class="badge bg-secondary ${isActive ? 'd-none' : ''}" data-preview-inactive>Nonaktif</span>
                                        </div>
                                        <p class="mb-1" data-preview-subtitle>${escapeHtml(String(card.subtitle || '').trim() || 'Deskripsi singkat')}</p>
                                        <span>Starting at:</span>
                                        <div class="price">
                                            <small data-preview-currency>${escapeHtml(currencySymbol)}</small><span data-preview-price>${escapeHtml(
                                                String(card.price || '').trim() || '0'
                                            )}</span>
                                        </div>
                                        <ul class="list-unstyled" data-preview-features></ul>
                                        <div class="generic-btn">
                                            <a href="${escapeHtml(String(card.button_url || '').trim() || '#')}" data-preview-cta-link>
                                                <span data-preview-cta-text>${escapeHtml(String(card.button_text || '').trim() || 'BUY TICKET')}</span>
                                                <i class="fas fa-arrow-right"></i>
                                            </a>
                                        </div>
                                        <div class="recomended-box ${String(card.badge || '').trim() ? '' : 'd-none'}" data-preview-badge>${escapeHtml(
                                            String(card.badge || '')
                                        )}</div>
                                    </div>
                                </div>
                            </div>
                        `;

                        cardEl.dataset.wrapClass = wrapClass;
                        cardEl.setAttribute('data-wrap-class', wrapClass);

                        return cardEl;
                    }

                    const saveTimers = new Map();

                    async function saveCard(cardEl) {
                        const id = String(cardEl.dataset.cardId || '');
                        if (!id) return;

                        setError(cardEl, null);
                        setStatus(cardEl, 'Menyimpan…', false);

                        updateExtraPreview(cardEl);
                        const payload = readCardFromDom(cardEl);
                        upsertCard(payload);

                        const url = urlFromTemplate(updateTpl, id);
                        try {
                            const res = await fetch(url, {
                                method: 'PUT',
                                headers: {
                                    'Content-Type': 'application/json',
                                    Accept: 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                body: JSON.stringify(payload),
                            });

                            const json = await res.json().catch(() => ({}));
                            if (!res.ok) {
                                setStatus(cardEl, 'Gagal menyimpan', true);
                                setError(cardEl, json);
                                return;
                            }

                            if (json && json.data) {
                                upsertCard(json.data);
                            }
                            setStatus(cardEl, 'Tersimpan', false);
                        } catch (_) {
                            setStatus(cardEl, 'Gagal menyimpan', true);
                        }
                    }

                    function scheduleSave(cardEl) {
                        const id = String(cardEl.dataset.cardId || '');
                        if (!id) return;

                        const old = saveTimers.get(id);
                        if (old) clearTimeout(old);
                        saveTimers.set(
                            id,
                            setTimeout(() => {
                                saveTimers.delete(id);
                                saveCard(cardEl);
                            }, 450)
                        );
                    }

                    addBtn.addEventListener('click', async () => {
                        if (!storeUrl) return;
                        addBtn.disabled = true;
                        try {
                            const res = await fetch(storeUrl, {
                                method: 'POST',
                                headers: {
                                    Accept: 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                body: JSON.stringify({}),
                            });

                            const json = await res.json().catch(() => ({}));
                            if (!res.ok || !json || !json.data) {
                                pushNotice('danger', 'Gagal', 'Card tidak berhasil ditambahkan.');
                                return;
                            }

                            const card = json.data;
                            const el = renderCard(card);
                            list.appendChild(el);
                            renumberExtraFeatures(el);
                            syncExtraFeaturesStorage(el);
                            updateExtraPreview(el);
                            upsertCard(card);
                            setStatus(el, 'Tersimpan', false);
                            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        } catch (_) {
                            pushNotice('danger', 'Gagal', 'Card tidak berhasil ditambahkan.');
                        } finally {
                            addBtn.disabled = false;
                        }
                    });

                    list.addEventListener('input', (e) => {
                        const cardEl = e.target?.closest?.('[data-extra-card]');
                        if (!cardEl) return;
                        if (e.target?.matches?.('[data-extra-features-input]')) {
                            syncExtraFeaturesStorage(cardEl);
                        }
                        updateExtraPreview(cardEl);
                        scheduleSave(cardEl);
                    });

                    list.addEventListener('change', (e) => {
                        const cardEl = e.target?.closest?.('[data-extra-card]');
                        if (!cardEl) return;
                        updateExtraPreview(cardEl);
                        scheduleSave(cardEl);
                    });

                    list.addEventListener('click', (e) => {
                        const addFeaturesBtn = e.target?.closest?.('[data-extra-features-add]');
                        if (addFeaturesBtn) {
                            const cardEl = addFeaturesBtn.closest('[data-extra-card]');
                            if (!cardEl) return;
                            addExtraFeatureRow(cardEl, '');
                            syncExtraFeaturesStorage(cardEl);
                            updateExtraPreview(cardEl);
                            scheduleSave(cardEl);
                            return;
                        }

                        const removeFeaturesBtn = e.target?.closest?.('[data-extra-features-remove]');
                        if (removeFeaturesBtn) {
                            const cardEl = removeFeaturesBtn.closest('[data-extra-card]');
                            if (!cardEl) return;
                            const row = removeFeaturesBtn.closest('[data-extra-feature-row]');
                            if (row) row.remove();
                            const remaining = cardEl.querySelectorAll('[data-extra-feature-row]');
                            if (remaining.length === 0) addExtraFeatureRow(cardEl, '');
                            renumberExtraFeatures(cardEl);
                            syncExtraFeaturesStorage(cardEl);
                            updateExtraPreview(cardEl);
                            scheduleSave(cardEl);
                        }
                    });

                    list.addEventListener('click', async (e) => {
                        const btn = e.target?.closest?.('[data-extra-card-delete]');
                        if (!btn) return;

                        const cardEl = btn.closest('[data-extra-card]');
                        if (!cardEl) return;

                        const id = String(cardEl.dataset.cardId || '');
                        if (!id) return;

                        if (!confirm('Hapus card ini?')) return;

                        const url = urlFromTemplate(destroyTpl, id);
                        btn.disabled = true;
                        try {
                            const res = await fetch(url, {
                                method: 'DELETE',
                                headers: {
                                    Accept: 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            });

                            if (!res.ok) {
                                pushNotice('danger', 'Gagal', 'Card tidak berhasil dihapus.');
                                btn.disabled = false;
                                return;
                            }

                            removeCard(id);
                            cardEl.remove();
                            pushNotice('success', 'Berhasil', 'Card berhasil dihapus.');
                        } catch (_) {
                            pushNotice('danger', 'Gagal', 'Card tidak berhasil dihapus.');
                            btn.disabled = false;
                        }
                    });

                    syncStorage();
                    Array.from(list.querySelectorAll('[data-extra-card]')).forEach((cardEl) => {
                        renumberExtraFeatures(cardEl);
                        syncExtraFeaturesStorage(cardEl);
                        updateExtraPreview(cardEl);
                    });
                })();

                if (fieldSearch) {
                    const items = Array.from(document.querySelectorAll('[data-field-item]'));
                    const tabPanes = Array.from(document.querySelectorAll('[data-tab-pane]'));
                    const tabButtons = Array.from(document.querySelectorAll('[data-tab-btn]'));
                    const tabsKey = `kontenHalaman.activeTab.${page}`;

                    function showTabBySection(section) {
                        const btn = document.querySelector(`[data-tab-btn][data-section="${section}"]`);
                        if (!btn) return;
                        const tab = new bootstrap.Tab(btn);
                        tab.show();
                    }

                    function getActiveSection() {
                        const activeBtn = document.querySelector('[data-tab-btn].active');
                        return activeBtn ? (activeBtn.getAttribute('data-section') || '') : '';
                    }

                    try {
                        const saved = localStorage.getItem(tabsKey);
                        if (saved) showTabBySection(saved);
                    } catch (_) {}

                    tabButtons.forEach((btn) => {
                        btn.addEventListener('shown.bs.tab', () => {
                            const section = btn.getAttribute('data-section') || '';
                            try {
                                localStorage.setItem(tabsKey, section);
                            } catch (_) {}
                        });
                    });

                    function applyFilter() {
                        const q = String(fieldSearch.value || '').toLowerCase().trim();
                        const matchedBySection = new Map();

                        items.forEach((item) => {
                            const hay = String(item.dataset.hay || '');
                            const show = !q || hay.includes(q);
                            item.classList.toggle('d-none', !show);
                            const section = item.closest('[data-tab-pane]')?.getAttribute('data-tab-section') || '';
                            matchedBySection.set(section, (matchedBySection.get(section) || 0) + (show ? 1 : 0));
                        });

                        tabPanes.forEach((pane) => {
                            const section = pane.getAttribute('data-tab-section') || '';
                            const count = matchedBySection.get(section) || 0;
                            const emptyState = pane.querySelector('[data-empty-state]');
                            if (emptyState) emptyState.classList.toggle('d-none', !q || count > 0);
                        });

                        tabButtons.forEach((btn) => {
                            const section = btn.getAttribute('data-section') || '';
                            const count = matchedBySection.get(section) || 0;
                            btn.closest('li')?.classList.toggle('d-none', q && count === 0);
                            const badge = btn.querySelector('[data-tab-badge]');
                            if (badge) {
                                const initial = badge.getAttribute('data-initial') || String(count);
                                badge.textContent = q ? String(count) : String(initial);
                            }
                        });

                        const activeSection = getActiveSection();
                        const activeVisible = !q || (matchedBySection.get(activeSection) || 0) > 0;
                        if (!activeVisible) {
                            const firstVisible = tabButtons.find((btn) => !(btn.closest('li')?.classList.contains('d-none')));
                            if (firstVisible) {
                                const section = firstVisible.getAttribute('data-section') || '';
                                showTabBySection(section);
                            }
                        }
                    }

                    fieldSearch.addEventListener('input', applyFilter);
                    applyFilter();
                }
            })();
        </script>
    </main>
@endsection
