@php
    $page = (string) ($page ?? 'home');

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
@endphp

<section id="faq" class="faq-main-section w-100 float-left padding-top padding-bottom position-relative light-bg">
    <div class="container">
        <div class="generic-title text-center">
            <span class="small-text" data-aos="fade-up" data-aos-duration="700">{{ konten($page, 'faq', 'small_text') ?: 'FAQ' }}</span>
            <h2 data-aos="fade-up" data-aos-duration="700">{{ konten($page, 'faq', 'title') ?: 'Frequently Asked Questions' }}</h2>
        </div>
        <div class="faq-inner-section">
            <div id="accordion">
                <div class="card" data-aos="fade-up" data-aos-duration="700">
                    <div class="card-header" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true"
                                aria-controls="collapseOne">
                                {{ $faqQ1 }}
                            </button>
                        </h5>
                    </div>

                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">{!! nl2br(e($faqA1)) !!}</div>
                    </div>
                </div>
                <div class="card" data-aos="fade-up" data-aos-duration="700">
                    <div class="card-header" id="headingTwo">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false"
                                aria-controls="collapseTwo">
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
                            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false"
                                aria-controls="collapseThree">
                                {{ $faqQ3 }}
                            </button>
                        </h5>
                    </div>
                    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                        <div class="card-body">{!! nl2br(e($faqA3)) !!}</div>
                    </div>
                </div>
                <div class="card" data-aos="fade-up" data-aos-duration="700">
                    <div class="card-header" id="headingfour">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapsefour" aria-expanded="false"
                                aria-controls="collapsefour">
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
                            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapsefive" aria-expanded="false"
                                aria-controls="collapsefive">
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
