@php
    $page = (string) ($page ?? 'home');

    $faqItemsRaw = konten($page, 'faq', 'items');
    $faqItems = [];
    if (is_string($faqItemsRaw) && trim($faqItemsRaw) !== '') {
        $decoded = json_decode($faqItemsRaw, true);
        if (! is_array($decoded)) {
            $decoded = json_decode(html_entity_decode($faqItemsRaw, ENT_QUOTES | ENT_HTML5), true);
        }
        $faqItems = is_array($decoded) ? $decoded : [];
    }

    $faqItems = collect($faqItems)
        ->filter(fn ($v) => is_array($v) && trim((string) ($v['question'] ?? '')) !== '' && trim((string) ($v['answer'] ?? '')) !== '')
        ->map(function (array $v) {
            $v['id'] = (string) ($v['id'] ?? '');
            $v['question'] = (string) ($v['question'] ?? '');
            $v['answer'] = (string) ($v['answer'] ?? '');
            $v['order'] = (int) ($v['order'] ?? 0);
            $v['active'] = (string) ($v['active'] ?? '1');
            return $v;
        })
        ->filter(fn (array $v) => $v['active'] !== '0')
        ->sortBy('order')
        ->values()
        ->all();

    $faqQ1 = konten($page, 'faq', 'q1') ?: 'What is the design process for branding?';
    $faqA1 =
        konten($page, 'faq', 'a1') ?:
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodLorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
    $faqQ2 = konten($page, 'faq', 'q2') ?: 'How much does logo design services cost?';
    $faqA2 =
        konten($page, 'faq', 'a2') ?:
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodLorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
    $faqQ3 = konten($page, 'faq', 'q3') ?: 'What is the process for a website redesign?';
    $faqA3 =
        konten($page, 'faq', 'a3') ?:
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodLorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
    $faqQ4 = konten($page, 'faq', 'q4') ?: 'What is a content strategy?';
    $faqA4 =
        konten($page, 'faq', 'a4') ?:
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodLorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
    $faqQ5 = konten($page, 'faq', 'q5') ?: 'How much does website design cost?';
    $faqA5 =
        konten($page, 'faq', 'a5') ?:
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodLorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

    $legacy = collect([
        ['id' => 'legacy_1', 'question' => $faqQ1, 'answer' => $faqA1, 'order' => 1, 'active' => '1'],
        ['id' => 'legacy_2', 'question' => $faqQ2, 'answer' => $faqA2, 'order' => 2, 'active' => '1'],
        ['id' => 'legacy_3', 'question' => $faqQ3, 'answer' => $faqA3, 'order' => 3, 'active' => '1'],
        ['id' => 'legacy_4', 'question' => $faqQ4, 'answer' => $faqA4, 'order' => 4, 'active' => '1'],
        ['id' => 'legacy_5', 'question' => $faqQ5, 'answer' => $faqA5, 'order' => 5, 'active' => '1'],
    ])
        ->filter(fn (array $v) => trim((string) ($v['question'] ?? '')) !== '' && trim((string) ($v['answer'] ?? '')) !== '')
        ->values()
        ->all();

    $offset = count($legacy);
    $dynamic = collect($faqItems)
        ->map(fn (array $v) => $v + ['order' => $offset + (int) ($v['order'] ?? 0)])
        ->values()
        ->all();

    $allFaqItems = collect($legacy)
        ->concat($dynamic)
        ->sortBy('order')
        ->values()
        ->all();
@endphp

<section id="faq" class="faq-main-section w-100 float-left padding-top padding-bottom position-relative light-bg">
    <div class="container">
        <div class="generic-title text-center">
            <span class="small-text" data-aos="fade-up" data-aos-duration="700">{{ konten($page, 'faq', 'small_text') ?: 'FAQ' }}</span>
            <h2 data-aos="fade-up" data-aos-duration="700">{{ konten($page, 'faq', 'title') ?: 'Frequently Asked Questions' }}</h2>
        </div>
        <div class="faq-inner-section">
            <div id="accordion">
                @foreach ($allFaqItems as $i => $item)
                    @php
                        $headingId = "headingFaq{$i}";
                        $collapseId = "collapseFaq{$i}";
                        $isFirst = $i === 0;
                    @endphp
                    <div class="card" data-aos="fade-up" data-aos-duration="700">
                        <div class="card-header" id="{{ $headingId }}">
                            <h5 class="mb-0">
                                <button class="btn btn-link @if (!$isFirst) collapsed @endif" data-toggle="collapse"
                                    data-target="#{{ $collapseId }}" aria-expanded="{{ $isFirst ? 'true' : 'false' }}"
                                    aria-controls="{{ $collapseId }}">
                                    {{ $item['question'] }}
                                </button>
                            </h5>
                        </div>
                        <div id="{{ $collapseId }}" class="collapse @if ($isFirst) show @endif"
                            aria-labelledby="{{ $headingId }}" data-parent="#accordion">
                            <div class="card-body">{!! nl2br(e($item['answer'])) !!}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
