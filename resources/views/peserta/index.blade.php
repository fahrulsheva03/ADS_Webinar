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
                                <span class="d-block position-relative">{{ konten('home', 'banner', 'slide_1_date') }}</span>
                                <h1>{!! nl2br(e(konten('home', 'banner', 'slide_1_title'))) !!}</h1>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-map-marker-alt"></i> {{ konten('home', 'banner', 'slide_1_location') }}</li>
                                </ul>
                                <div class="generic-btn">
                                    <a href="{{ konten('home', 'banner', 'slide_1_button_url') }}">{{ konten('home', 'banner', 'slide_1_button_text') }} <i class="fas fa-arrow-right"></i></a>
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
                        <a href="{{ konten('home', 'journey', 'button_url') }}">{{ konten('home', 'journey', 'button_text') }} <i class="fas fa-arrow-right"></i></a>
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
                            <iframe width="560" height="315"
                                src="{{ konten('home', 'journey', 'video_url') }}"
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
                                <span class="d-block client-status">{{ konten('home', 'journey', 'counter_1_label') }}</span>
                            </div>
                        </li>
                        <li class="position-relative">
                            <div class="count d-inline-block">{{ konten('home', 'journey', 'counter_2_value') }}</div>
                            <div class="plus-details">
                                <div class="plus d-inline-block"><span>+</span></div>
                                <span class="d-block client-status">{{ konten('home', 'journey', 'counter_2_label') }}</span>
                            </div>
                        </li>
                        <li class="position-relative">
                            <div class="count d-inline-block">{{ konten('home', 'journey', 'counter_3_value') }}</div>
                            <div class="plus-details">
                                <div class="plus d-inline-block"><span>+</span></div>
                                <span class="d-block client-status">{{ konten('home', 'journey', 'counter_3_label') }}</span>
                            </div>
                        </li>
                        <li class="position-relative">
                            <div class="counter-box position-relative">
                                <div class="2k-con">
                                    <div class="count d-inline-block">{{ konten('home', 'journey', 'counter_4_value') }}</div>
                                    <small class="d-inline-block">{{ konten('home', 'journey', 'counter_4_suffix') }}</small>
                                </div>
                                <div class="plus-details">
                                    <div class="plus d-inline-block"><span>+</span></div>
                                    <span class="d-block client-status">{{ konten('home', 'journey', 'counter_4_label') }}</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div class="digital-text-con">
                        <h3 data-aos="fade-up" data-aos-duration="700">{!! nl2br(e(konten('home', 'journey', 'digital_title'))) !!} <span class="d-inline-block">{{ konten('home', 'journey', 'digital_badge') }}</span></h3>
                        <div class="generic-btn" data-aos="fade-up" data-aos-duration="700">
                            <a href="{{ konten('home', 'journey', 'digital_button_url') }}">{{ konten('home', 'journey', 'digital_button_text') }} <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- JOURNEY SECTION END -->
    {{--
    <!-- EVENT SECTION START -->
    <section class="index3-event-section w-100 float-left padding-top padding-bottom">
        <div class="container">
            <div class="generic-title">
                <span class="small-text" data-aos="fade-up" data-aos-duration="700">SCHEDULE OF EVENT</span>
                <h2 class="mb-0" data-aos="fade-up" data-aos-duration="700">List of Events Planned in This <br> Conference</h2>
            </div>
            <div class="index3-event-tabs-con">
                <ul class="nav nav-tabs" id="myTab" role="tablist" data-aos="fade-up" data-aos-duration="700">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="home-tab" data-toggle="tab" data-target="#home"
                            type="button" role="tab" aria-controls="home" aria-selected="true">DAY 1</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-tab" data-toggle="tab" data-target="#profile" type="button"
                            role="tab" aria-controls="profile" aria-selected="false">DAY 2</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="contact-tab" data-toggle="tab" data-target="#contact" type="button"
                            role="tab" aria-controls="contact" aria-selected="false">DAY 3</button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <div id="accordion" class="index3-faqs">
                            <div class="card" data-aos="fade-up" data-aos-duration="700">
                                <div class="card-header" id="headingOne">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link collapsed" data-toggle="collapse"
                                            data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            <div class="index3-event-detail">
                                                <div class="index3-event-date-con">
                                                    <div class="index3-event-date">
                                                        25
                                                    </div>
                                                    <div class="index3-event-month text-left">
                                                        <span class="d-block">NOVEMBER</span>
                                                        <small class="d-block">08:00 AM-08.45 AM</small>
                                                    </div>
                                                </div>
                                                <div class="index3-event-organizer">
                                                    <figure class="mb-0">
                                                        <img src="assets/images/index3-organizer-img1.png"
                                                            alt="index3-organizer-img1">
                                                    </figure>
                                                    <div class="index3-organizer-detail">
                                                        <span class="d-block">Creativitye Technology</span>
                                                        <small class="d-block"><span>By:</span> Tim Cook - CEO of
                                                            Apple</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </button>
                                    </h6>
                                </div>

                                <div id="collapseOne" class="collapse" aria-labelledby="headingOne"
                                    data-parent="#accordion">
                                    <div class="card-body">
                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                            tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse
                                            ultrices gravida. </p>
                                        <span class="d-block"><i class="fas fa-map-marker-alt"></i> 21 KING STREET, 1175
                                            AUSTRALIA</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card" data-aos="fade-up" data-aos-duration="700">
                                <div class="card-header" id="headingTwo">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link collapsed" data-toggle="collapse"
                                            data-target="#collapseTwo" aria-expanded="false"
                                            aria-controls="collapseTwo">
                                            <div class="index3-event-detail">
                                                <div class="index3-event-date-con">
                                                    <div class="index3-event-date">
                                                        26
                                                    </div>
                                                    <div class="index3-event-month text-left">
                                                        <span class="d-block">NOVEMBER</span>
                                                        <small class="d-block">09:00 AM-10.00 AM</small>
                                                    </div>
                                                </div>
                                                <div class="index3-event-organizer">
                                                    <figure class="mb-0">
                                                        <img src="assets/images/index3-organizer-img2.png"
                                                            alt="index3-organizer-img2">
                                                    </figure>
                                                    <div class="index3-organizer-detail">
                                                        <span class="d-block">Driverless Cities</span>
                                                        <small class="d-block"><span>By:</span> Andy Jassy - CEO of
                                                            Amazon</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </button>
                                    </h6>
                                </div>
                                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo"
                                    data-parent="#accordion">
                                    <div class="card-body">
                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                            tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse
                                            ultrices gravida. </p>
                                        <span class="d-block"><i class="fas fa-map-marker-alt"></i> 21 KING STREET, 1175
                                            AUSTRALIA</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card" data-aos="fade-up" data-aos-duration="700">
                                <div class="card-header" id="headingThree">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link collapsed" data-toggle="collapse"
                                            data-target="#collapseThree" aria-expanded="false"
                                            aria-controls="collapseThree">
                                            <div class="index3-event-detail">
                                                <div class="index3-event-date-con">
                                                    <div class="index3-event-date">
                                                        27
                                                    </div>
                                                    <div class="index3-event-month text-left">
                                                        <span class="d-block">NOVEMBER</span>
                                                        <small class="d-block">10:00 AM-11.15 AM</small>
                                                    </div>
                                                </div>
                                                <div class="index3-event-organizer">
                                                    <figure class="mb-0">
                                                        <img src="assets/images/index3-organizer-img3.png"
                                                            alt="index3-organizer-img3">
                                                    </figure>
                                                    <div class="index3-organizer-detail">
                                                        <span class="d-block">Bringing al to Life</span>
                                                        <small class="d-block"><span>By:</span> Satya Nadella - CEO of
                                                            Microsoft</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </button>
                                    </h6>
                                </div>
                                <div id="collapseThree" class="collapse" aria-labelledby="headingThree"
                                    data-parent="#accordion">
                                    <div class="card-body">
                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                            tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse
                                            ultrices gravida. </p>
                                        <span class="d-block"><i class="fas fa-map-marker-alt"></i> 21 KING STREET, 1175
                                            AUSTRALIA</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card" data-aos="fade-up" data-aos-duration="700">
                                <div class="card-header" id="headingfour">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link collapsed" data-toggle="collapse"
                                            data-target="#collapsefour" aria-expanded="false"
                                            aria-controls="collapsefour">
                                            <div class="index3-event-detail">
                                                <div class="index3-event-date-con">
                                                    <div class="index3-event-date">
                                                        28
                                                    </div>
                                                    <div class="index3-event-month text-left">
                                                        <span class="d-block">NOVEMBER</span>
                                                        <small class="d-block">11:00 AM-12.00 AM</small>
                                                    </div>
                                                </div>
                                                <div class="index3-event-organizer">
                                                    <figure class="mb-0">
                                                        <img src="assets/images/index3-organizer-img4.png"
                                                            alt="index3-organizer-img4">
                                                    </figure>
                                                    <div class="index3-organizer-detail">
                                                        <span class="d-block">Creativitye Technology</span>
                                                        <small class="d-block"><span>By:</span> Neal Mohan CEO of
                                                            YouTube</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </button>
                                    </h6>
                                </div>
                                <div id="collapsefour" class="collapse" aria-labelledby="headingfour"
                                    data-parent="#accordion">
                                    <div class="card-body">
                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                            tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse
                                            ultrices gravida. </p>
                                        <span class="d-block"><i class="fas fa-map-marker-alt"></i> 21 KING STREET, 1175
                                            AUSTRALIA</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="index3-faq-btn-con text-center">
                            <p data-aos="fade-up" data-aos-duration="700">This is a Detailed List Event of Conference for Digital Technology 2024.</p>
                            <div class="generic-btn" data-aos="fade-up" data-aos-duration="700">
                                <a href="contact.html">DOWNLOAD SCHEDULE <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <div id="accordion" class="index3-faqs">
                            <div class="card" data-aos="fade-up" data-aos-duration="700">
                                <div class="card-header" id="headingsix">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link collapsed" data-toggle="collapse"
                                            data-target="#collapsesix" aria-expanded="false"
                                            aria-controls="collapsesix">
                                            <div class="index3-event-detail">
                                                <div class="index3-event-date-con">
                                                    <div class="index3-event-date">
                                                        27
                                                    </div>
                                                    <div class="index3-event-month text-left">
                                                        <span class="d-block">NOVEMBER</span>
                                                        <small class="d-block">10:00 AM-11.15 AM</small>
                                                    </div>
                                                </div>
                                                <div class="index3-event-organizer">
                                                    <figure class="mb-0">
                                                        <img src="assets/images/index3-organizer-img3.png"
                                                            alt="index3-organizer-img3">
                                                    </figure>
                                                    <div class="index3-organizer-detail">
                                                        <span class="d-block">Bringing al to Life</span>
                                                        <small class="d-block"><span>By:</span> Satya Nadella - CEO of
                                                            Microsoft</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </button>
                                    </h6>
                                </div>
                                <div id="collapsesix" class="collapse" aria-labelledby="headingsix"
                                    data-parent="#accordion">
                                    <div class="card-body">
                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                            tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse
                                            ultrices gravida. </p>
                                        <span class="d-block"><i class="fas fa-map-marker-alt"></i> 21 KING STREET, 1175
                                            AUSTRALIA</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card" data-aos="fade-up" data-aos-duration="700">
                                <div class="card-header" id="headingseven">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link collapsed" data-toggle="collapse"
                                            data-target="#collapseseven" aria-expanded="false"
                                            aria-controls="collapseseven">
                                            <div class="index3-event-detail">
                                                <div class="index3-event-date-con">
                                                    <div class="index3-event-date">
                                                        28
                                                    </div>
                                                    <div class="index3-event-month text-left">
                                                        <span class="d-block">NOVEMBER</span>
                                                        <small class="d-block">11:00 AM-12.00 AM</small>
                                                    </div>
                                                </div>
                                                <div class="index3-event-organizer">
                                                    <figure class="mb-0">
                                                        <img src="assets/images/index3-organizer-img4.png"
                                                            alt="index3-organizer-img4">
                                                    </figure>
                                                    <div class="index3-organizer-detail">
                                                        <span class="d-block">Creativitye Technology</span>
                                                        <small class="d-block"><span>By:</span> Neal Mohan CEO of
                                                            YouTube</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </button>
                                    </h6>
                                </div>
                                <div id="collapseseven" class="collapse" aria-labelledby="headingseven"
                                    data-parent="#accordion">
                                    <div class="card-body">
                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                            tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse
                                            ultrices gravida. </p>
                                        <span class="d-block"><i class="fas fa-map-marker-alt"></i> 21 KING STREET, 1175
                                            AUSTRALIA</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card" data-aos="fade-up" data-aos-duration="700">
                                <div class="card-header" id="headingeight">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link collapsed" data-toggle="collapse"
                                            data-target="#collapseeight" aria-expanded="true"
                                            aria-controls="collapseeight">
                                            <div class="index3-event-detail">
                                                <div class="index3-event-date-con">
                                                    <div class="index3-event-date">
                                                        25
                                                    </div>
                                                    <div class="index3-event-month text-left">
                                                        <span class="d-block">NOVEMBER</span>
                                                        <small class="d-block">08:00 AM-08.45 AM</small>
                                                    </div>
                                                </div>
                                                <div class="index3-event-organizer">
                                                    <figure class="mb-0">
                                                        <img src="assets/images/index3-organizer-img1.png"
                                                            alt="index3-organizer-img1">
                                                    </figure>
                                                    <div class="index3-organizer-detail">
                                                        <span class="d-block">Creativitye Technology</span>
                                                        <small class="d-block"><span>By:</span> Tim Cook - CEO of
                                                            Apple</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </button>
                                    </h6>
                                </div>

                                <div id="collapseeight" class="collapse" aria-labelledby="headingeight"
                                    data-parent="#accordion">
                                    <div class="card-body">
                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                            tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse
                                            ultrices gravida. </p>
                                        <span class="d-block"><i class="fas fa-map-marker-alt"></i> 21 KING STREET, 1175
                                            AUSTRALIA</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card" data-aos="fade-up" data-aos-duration="700">
                                <div class="card-header" id="headingnine">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link collapsed" data-toggle="collapse"
                                            data-target="#collapsenine" aria-expanded="false"
                                            aria-controls="collapsenine">
                                            <div class="index3-event-detail">
                                                <div class="index3-event-date-con">
                                                    <div class="index3-event-date">
                                                        26
                                                    </div>
                                                    <div class="index3-event-month text-left">
                                                        <span class="d-block">NOVEMBER</span>
                                                        <small class="d-block">09:00 AM-10.00 AM</small>
                                                    </div>
                                                </div>
                                                <div class="index3-event-organizer">
                                                    <figure class="mb-0">
                                                        <img src="assets/images/index3-organizer-img2.png"
                                                            alt="index3-organizer-img2">
                                                    </figure>
                                                    <div class="index3-organizer-detail">
                                                        <span class="d-block">Driverless Cities</span>
                                                        <small class="d-block"><span>By:</span> Andy Jassy - CEO of
                                                            Amazon</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </button>
                                    </h6>
                                </div>
                                <div id="collapsenine" class="collapse" aria-labelledby="headingnine"
                                    data-parent="#accordion">
                                    <div class="card-body">
                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                            tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse
                                            ultrices gravida. </p>
                                        <span class="d-block"><i class="fas fa-map-marker-alt"></i> 21 KING STREET, 1175
                                            AUSTRALIA</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="index3-faq-btn-con text-center">
                            <p data-aos="fade-up" data-aos-duration="700">This is a Detailed List Event of Conference for Digital Technology 2024.</p>
                            <div class="generic-btn" data-aos="fade-up" data-aos-duration="700">
                                <a href="contact.html">DOWNLOAD SCHEDULE <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                        <div id="accordion" class="index3-faqs">
                            <div class="card" data-aos="fade-up" data-aos-duration="700">
                                <div class="card-header" id="headingten">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link collapsed" data-toggle="collapse"
                                            data-target="#collapseten" aria-expanded="true" aria-controls="collapseten">
                                            <div class="index3-event-detail">
                                                <div class="index3-event-date-con">
                                                    <div class="index3-event-date">
                                                        25
                                                    </div>
                                                    <div class="index3-event-month text-left">
                                                        <span class="d-block">NOVEMBER</span>
                                                        <small class="d-block">08:00 AM-08.45 AM</small>
                                                    </div>
                                                </div>
                                                <div class="index3-event-organizer">
                                                    <figure class="mb-0">
                                                        <img src="assets/images/index3-organizer-img1.png"
                                                            alt="index3-organizer-img1">
                                                    </figure>
                                                    <div class="index3-organizer-detail">
                                                        <span class="d-block">Creativitye Technology</span>
                                                        <small class="d-block"><span>By:</span> Tim Cook - CEO of
                                                            Apple</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </button>
                                    </h6>
                                </div>

                                <div id="collapseten" class="collapse" aria-labelledby="headingten"
                                    data-parent="#accordion">
                                    <div class="card-body">
                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                            tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse
                                            ultrices gravida. </p>
                                        <span class="d-block"><i class="fas fa-map-marker-alt"></i> 21 KING STREET, 1175
                                            AUSTRALIA</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card" data-aos="fade-up" data-aos-duration="700">
                                <div class="card-header" id="headingeleven">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link collapsed" data-toggle="collapse"
                                            data-target="#collapseeleven" aria-expanded="false"
                                            aria-controls="collapseeleven">
                                            <div class="index3-event-detail">
                                                <div class="index3-event-date-con">
                                                    <div class="index3-event-date">
                                                        26
                                                    </div>
                                                    <div class="index3-event-month text-left">
                                                        <span class="d-block">NOVEMBER</span>
                                                        <small class="d-block">09:00 AM-10.00 AM</small>
                                                    </div>
                                                </div>
                                                <div class="index3-event-organizer">
                                                    <figure class="mb-0">
                                                        <img src="assets/images/index3-organizer-img2.png"
                                                            alt="index3-organizer-img2">
                                                    </figure>
                                                    <div class="index3-organizer-detail">
                                                        <span class="d-block">Driverless Cities</span>
                                                        <small class="d-block"><span>By:</span> Andy Jassy - CEO of
                                                            Amazon</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </button>
                                    </h6>
                                </div>
                                <div id="collapseeleven" class="collapse" aria-labelledby="headingeleven"
                                    data-parent="#accordion">
                                    <div class="card-body">
                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                            tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse
                                            ultrices gravida. </p>
                                        <span class="d-block"><i class="fas fa-map-marker-alt"></i> 21 KING STREET, 1175
                                            AUSTRALIA</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card" data-aos="fade-up" data-aos-duration="700">
                                <div class="card-header" id="headingtwelve">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link collapsed" data-toggle="collapse"
                                            data-target="#collapsetwelve" aria-expanded="false"
                                            aria-controls="collapsetwelve">
                                            <div class="index3-event-detail">
                                                <div class="index3-event-date-con">
                                                    <div class="index3-event-date">
                                                        27
                                                    </div>
                                                    <div class="index3-event-month text-left">
                                                        <span class="d-block">NOVEMBER</span>
                                                        <small class="d-block">10:00 AM-11.15 AM</small>
                                                    </div>
                                                </div>
                                                <div class="index3-event-organizer">
                                                    <figure class="mb-0">
                                                        <img src="assets/images/index3-organizer-img3.png"
                                                            alt="index3-organizer-img3">
                                                    </figure>
                                                    <div class="index3-organizer-detail">
                                                        <span class="d-block">Bringing al to Life</span>
                                                        <small class="d-block"><span>By:</span> Satya Nadella - CEO of
                                                            Microsoft</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </button>
                                    </h6>
                                </div>
                                <div id="collapsetwelve" class="collapse" aria-labelledby="headingtwelve"
                                    data-parent="#accordion">
                                    <div class="card-body">
                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                            tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse
                                            ultrices gravida. </p>
                                        <span class="d-block"><i class="fas fa-map-marker-alt"></i> 21 KING STREET, 1175
                                            AUSTRALIA</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card" data-aos="fade-up" data-aos-duration="700">
                                <div class="card-header" id="headingthirteen">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link collapsed" data-toggle="collapse"
                                            data-target="#collapsethirteen" aria-expanded="false"
                                            aria-controls="collapsethirteen">
                                            <div class="index3-event-detail">
                                                <div class="index3-event-date-con">
                                                    <div class="index3-event-date">
                                                        28
                                                    </div>
                                                    <div class="index3-event-month text-left">
                                                        <span class="d-block">NOVEMBER</span>
                                                        <small class="d-block">11:00 AM-12.00 AM</small>
                                                    </div>
                                                </div>
                                                <div class="index3-event-organizer">
                                                    <figure class="mb-0">
                                                        <img src="assets/images/index3-organizer-img4.png"
                                                            alt="index3-organizer-img4">
                                                    </figure>
                                                    <div class="index3-organizer-detail">
                                                        <span class="d-block">Creativitye Technology</span>
                                                        <small class="d-block"><span>By:</span> Neal Mohan CEO of
                                                            YouTube</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </button>
                                    </h6>
                                </div>
                                <div id="collapsethirteen" class="collapse" aria-labelledby="headingthirteen"
                                    data-parent="#accordion">
                                    <div class="card-body">
                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                            tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse
                                            ultrices gravida. </p>
                                        <span class="d-block"><i class="fas fa-map-marker-alt"></i> 21 KING STREET, 1175
                                            AUSTRALIA</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="index3-faq-btn-con text-center">
                            <p data-aos="fade-up" data-aos-duration="700">This is a Detailed List Event of Conference for Digital Technology 2024.</p>
                            <div class="generic-btn" data-aos="fade-up" data-aos-duration="700">
                                <a href="contact.html">DOWNLOAD SCHEDULE <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- EVENT SECTION END -->
    <!-- INDEX3 EVENT INFO ZONE SECTION START -->
    <section class="index3-event-info-zone w-100 float-left padding-top padding-bottom">
        <div class="container">
            <div class="generic-title2 text-center">
                <span class="small-text" data-aos="fade-up" data-aos-duration="700">EVENT INFO ZONE</span>
                <h2 data-aos="fade-up" data-aos-duration="700">How to Attend ConfX 2024</h2>
            </div>
            <div class="index3-event-info-inner-con">
                <div class="index3-event-zone" data-aos="fade-up" data-aos-duration="700">
                    <figure>
                        <img src="assets/images/event-zone-img1.png" alt="event-zone-img1">
                    </figure>
                    <h3>Venue</h3>
                    <p>Find a simulcast location
                        near you and experience
                        an inclusive leadership is
                        avont to homo.</p>
                </div>
                <div class="index3-event-zone" data-aos="fade-up" data-aos-duration="700">
                    <figure>
                        <img src="assets/images/event-zone-img2.png" alt="event-zone-img2">
                    </figure>
                    <h3>Transport</h3>
                    <p>Land transport covers all land-based transport the systems that a provide for movament</p>
                </div>
                <div class="index3-event-zone" data-aos="fade-up" data-aos-duration="700">
                    <figure>
                        <img src="assets/images/event-zone-img3.png" alt="event-zone-img3">
                    </figure>
                    <h3>Hotel</h3>
                    <p>Enjoy is the enjoyment of
                        the pleasure particularly best leisure activities fun experianco afton.</p>
                </div>
            </div>
        </div>
    </section>
    <!-- INDEX3 EVENT INFO ZONE SECTION END -->
    <!-- INDEX3 SPEAKERS SECTION START -->
    <section class="index3-speakers-section w-100 float-left padding-top padding-bottom">
        <div class="container">
            <div class="generic-title2 text-center">
                <span class="small-text" data-aos="fade-up" data-aos-duration="700">WORLD BEST SPEAKERS</span>
                <h2 data-aos="fade-up" data-aos-duration="700">Meet Our Amazing Speakers</h2>
            </div>
            <div class="index3-speaker-outer-con" data-aos="fade-up" data-aos-duration="700">
                <div id="owl-carouselfive" class="owl-carousel owl-theme">
                    <div class="item">
                        <div class="index3-speaker-box position-relative">
                            <figure class="mb-0">
                                <img src="assets/images/index3-speaker-img1.png" alt="index3-speaker-img1">
                            </figure>
                            <div class="index3-speaker-detail-con text-center">
                                <ul>
                                    <li class="d-inline-block"><a href="https://www.facebook.com/login/"><i class="fab fa-facebook-f"></i></a></li>
                                    <li class="d-inline-block"><a href="https://www.linkedin.com/login"><i class="fab fa-linkedin-in"></i></a></li>
                                    <li class="d-inline-block"><a href="https://www.youtube.com/"><i class="fab fa-youtube"></i></a></li>
                                    <li class="d-inline-block"><a href="https://www.instagram.com/accounts/login/?next=https%3A%2F%2Fwww.instagram.com%2Faccounts%2Fonetap%2F%3Fnext%3D%252F%26__coig_login%3D1"><i class="fab fa-instagram"></i></a></li>
                                </ul>
                                <h5>Declan Heyes</h5>
                                <span class="d-block">CMP - ConfX</span>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="index3-speaker-box position-relative">
                            <figure class="mb-0">
                                <img src="assets/images/index3-speaker-img2.png" alt="index3-speaker-img2">
                            </figure>
                            <div class="index3-speaker-detail-con text-center">
                                <ul>
                                    <li class="d-inline-block"><a href="https://www.facebook.com/login/"><i class="fab fa-facebook-f"></i></a></li>
                                    <li class="d-inline-block"><a href="https://www.linkedin.com/login"><i class="fab fa-linkedin-in"></i></a></li>
                                    <li class="d-inline-block"><a href="https://www.youtube.com/"><i class="fab fa-youtube"></i></a></li>
                                    <li class="d-inline-block"><a href="https://www.instagram.com/accounts/login/?next=https%3A%2F%2Fwww.instagram.com%2Faccounts%2Fonetap%2F%3Fnext%3D%252F%26__coig_login%3D1"><i class="fab fa-instagram"></i></a></li>
                                </ul>
                                <h5>Stella Hindley</h5>
                                <span class="d-block">CCEP - ConfX</span>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="index3-speaker-box position-relative">
                            <figure class="mb-0">
                                <img src="assets/images/index3-speaker-img3.png" alt="index3-speaker-img3">
                            </figure>
                            <div class="index3-speaker-detail-con text-center">
                                <ul>
                                    <li class="d-inline-block"><a href="https://www.facebook.com/login/"><i class="fab fa-facebook-f"></i></a></li>
                                    <li class="d-inline-block"><a href="https://www.linkedin.com/login"><i class="fab fa-linkedin-in"></i></a></li>
                                    <li class="d-inline-block"><a href="https://www.youtube.com/"><i class="fab fa-youtube"></i></a></li>
                                    <li class="d-inline-block"><a href="https://www.instagram.com/accounts/login/?next=https%3A%2F%2Fwww.instagram.com%2Faccounts%2Fonetap%2F%3Fnext%3D%252F%26__coig_login%3D1"><i class="fab fa-instagram"></i></a></li>
                                </ul>
                                <h5>Jackson Allardyce</h5>
                                <span class="d-block">CDME - ConfX</span>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="index3-speaker-box position-relative">
                            <figure class="mb-0">
                                <img src="assets/images/index3-speaker-img1.png" alt="index3-speaker-img1">
                            </figure>
                            <div class="index3-speaker-detail-con text-center">
                                <ul>
                                    <li class="d-inline-block"><a href="https://www.facebook.com/login/"><i class="fab fa-facebook-f"></i></a></li>
                                    <li class="d-inline-block"><a href="https://www.linkedin.com/login"><i class="fab fa-linkedin-in"></i></a></li>
                                    <li class="d-inline-block"><a href="https://www.youtube.com/"><i class="fab fa-youtube"></i></a></li>
                                    <li class="d-inline-block"><a href="https://www.instagram.com/accounts/login/?next=https%3A%2F%2Fwww.instagram.com%2Faccounts%2Fonetap%2F%3Fnext%3D%252F%26__coig_login%3D1"><i class="fab fa-instagram"></i></a></li>
                                </ul>
                                <h5>Declan Heyes</h5>
                                <span class="d-block">CMP - ConfX</span>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="index3-speaker-box position-relative">
                            <figure class="mb-0">
                                <img src="assets/images/index3-speaker-img2.png" alt="index3-speaker-img2">
                            </figure>
                            <div class="index3-speaker-detail-con text-center">
                                <ul>
                                    <li class="d-inline-block"><a href="https://www.facebook.com/login/"><i class="fab fa-facebook-f"></i></a></li>
                                    <li class="d-inline-block"><a href="https://www.linkedin.com/login"><i class="fab fa-linkedin-in"></i></a></li>
                                    <li class="d-inline-block"><a href="https://www.youtube.com/"><i class="fab fa-youtube"></i></a></li>
                                    <li class="d-inline-block"><a href="https://www.instagram.com/accounts/login/?next=https%3A%2F%2Fwww.instagram.com%2Faccounts%2Fonetap%2F%3Fnext%3D%252F%26__coig_login%3D1"><i class="fab fa-instagram"></i></a></li>
                                </ul>
                                <h5>Stella Hindley</h5>
                                <span class="d-block">CCEP - ConfX</span>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="index3-speaker-box position-relative">
                            <figure class="mb-0">
                                <img src="assets/images/index3-speaker-img3.png" alt="index3-speaker-img3">
                            </figure>
                            <div class="index3-speaker-detail-con text-center">
                                <ul>
                                    <li class="d-inline-block"><a href="https://www.facebook.com/login/"><i class="fab fa-facebook-f"></i></a></li>
                                    <li class="d-inline-block"><a href="https://www.linkedin.com/login"><i class="fab fa-linkedin-in"></i></a></li>
                                    <li class="d-inline-block"><a href="https://www.youtube.com/"><i class="fab fa-youtube"></i></a></li>
                                    <li class="d-inline-block"><a href="https://www.instagram.com/accounts/login/?next=https%3A%2F%2Fwww.instagram.com%2Faccounts%2Fonetap%2F%3Fnext%3D%252F%26__coig_login%3D1"><i class="fab fa-instagram"></i></a></li>
                                </ul>
                                <h5>Jackson Allardyce</h5>
                                <span class="d-block">CDME - ConfX</span>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="index3-speaker-box position-relative">
                            <figure class="mb-0">
                                <img src="assets/images/index3-speaker-img1.png" alt="index3-speaker-img1">
                            </figure>
                            <div class="index3-speaker-detail-con text-center">
                                <ul>
                                    <li class="d-inline-block"><a href="https://www.facebook.com/login/"><i class="fab fa-facebook-f"></i></a></li>
                                    <li class="d-inline-block"><a href="https://www.linkedin.com/login"><i class="fab fa-linkedin-in"></i></a></li>
                                    <li class="d-inline-block"><a href="https://www.youtube.com/"><i class="fab fa-youtube"></i></a></li>
                                    <li class="d-inline-block"><a href="https://www.instagram.com/accounts/login/?next=https%3A%2F%2Fwww.instagram.com%2Faccounts%2Fonetap%2F%3Fnext%3D%252F%26__coig_login%3D1"><i class="fab fa-instagram"></i></a></li>
                                </ul>
                                <h5>Declan Heyes</h5>
                                <span class="d-block">CMP - ConfX</span>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="index3-speaker-box position-relative">
                            <figure class="mb-0">
                                <img src="assets/images/index3-speaker-img2.png" alt="index3-speaker-img2">
                            </figure>
                            <div class="index3-speaker-detail-con text-center">
                                <ul>
                                    <li class="d-inline-block"><a href="https://www.facebook.com/login/"><i class="fab fa-facebook-f"></i></a></li>
                                    <li class="d-inline-block"><a href="https://www.linkedin.com/login"><i class="fab fa-linkedin-in"></i></a></li>
                                    <li class="d-inline-block"><a href="https://www.youtube.com/"><i class="fab fa-youtube"></i></a></li>
                                    <li class="d-inline-block"><a href="https://www.instagram.com/accounts/login/?next=https%3A%2F%2Fwww.instagram.com%2Faccounts%2Fonetap%2F%3Fnext%3D%252F%26__coig_login%3D1"><i class="fab fa-instagram"></i></a></li>
                                </ul>
                                <h5>Stella Hindley</h5>
                                <span class="d-block">CCEP - ConfX</span>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="index3-speaker-box position-relative">
                            <figure class="mb-0">
                                <img src="assets/images/index3-speaker-img3.png" alt="index3-speaker-img3">
                            </figure>
                            <div class="index3-speaker-detail-con text-center">
                                <ul>
                                    <li class="d-inline-block"><a href="https://www.facebook.com/login/"><i class="fab fa-facebook-f"></i></a></li>
                                    <li class="d-inline-block"><a href="https://www.linkedin.com/login"><i class="fab fa-linkedin-in"></i></a></li>
                                    <li class="d-inline-block"><a href="https://www.youtube.com/"><i class="fab fa-youtube"></i></a></li>
                                    <li class="d-inline-block"><a href="https://www.instagram.com/accounts/login/?next=https%3A%2F%2Fwww.instagram.com%2Faccounts%2Fonetap%2F%3Fnext%3D%252F%26__coig_login%3D1"><i class="fab fa-instagram"></i></a></li>
                                </ul>
                                <h5>Jackson Allardyce</h5>
                                <span class="d-block">CDME - ConfX</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- INDEX3 SPEAKERS SECTION END -->
    <!-- INDEX3 PRICING PLANS SECTION START -->
    <section class="index3-pricing-plans-section w-100 float-left padding-top padding-bottom">
        <div class="container">
            <div class="generic-title2 text-center">
                <span class="small-text" data-aos="fade-up" data-aos-duration="700">TICKET PRICING</span>
                <h2 data-aos="fade-up" data-aos-duration="700">We Have Several Options <br> for Tickets</h2>
            </div>
            <div class="index3-plan-inner-con">
                <div class="ticket-details silver-ticket-details" data-aos="fade-up" data-aos-duration="700">
                    <h3>Silver</h3>
                    <p>For individuals</p>
                    <span>Starting at:</span>
                    <div class="price"><small>$</small>29</div>
                    <ul class="list-unstyled">
                        <li class="position-relative">Full Access the Conference</li>
                        <li class="position-relative">Music, Launch and Snack</li>
                        <li class="position-relative">Meet Event Speaker</li>
                    </ul>
                    <div class="generic-btn">
                        <a href="shop.html">BUY TICKET <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="ticket-details gold-ticket-details" data-aos="fade-up" data-aos-duration="700">
                    <h3>Gold</h3>
                    <p>For individuals</p>
                    <span>Starting at:</span>
                    <div class="price"><small>$</small>45</div>
                    <ul class="list-unstyled">
                        <li class="position-relative">Full Access the Conference</li>
                        <li class="position-relative">Music, Launch and Snack</li>
                        <li class="position-relative">Meet Event Speaker</li>
                    </ul>
                    <div class="generic-btn">
                        <a href="shop.html">BUY TICKET <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="recomended-box">
                        RECOMMENDED
                    </div>
                </div>
                <div class="ticket-details premium-ticket-details" data-aos="fade-up" data-aos-duration="700">
                    <h3>Premium</h3>
                    <p>For individuals</p>
                    <span>Starting at:</span>
                    <div class="price"><small>$</small>59</div>
                    <ul class="list-unstyled">
                        <li class="position-relative">Full Access the Conference</li>
                        <li class="position-relative">Music, Launch and Snack</li>
                        <li class="position-relative">Meet Event Speaker</li>
                    </ul>
                    <div class="generic-btn">
                        <a href="shop.html">BUY TICKET <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="index3-plan-btn text-center">
                <p data-aos="fade-up" data-aos-duration="700">This is a Detailed List Event of Conference for Digital Technology 2024.</p>
                <div class="generic-btn" data-aos="fade-up" data-aos-duration="700">
                    <a href="pricing.html">GET VIP PASS <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </section>
    <!-- INDEX3 PRICING PLANS SECTION END -->
    <!-- INDEX3 REGISTRATION SECTION START -->
    <section class="index3-registration-section w-100 float-left">
        <div class="container">
            <div class="index3-registration-inner-con">
                <div class="index3-registration-left-con">
                    <h2 data-aos="fade-up" data-aos-duration="700">Join the Biggest <br>
                        Conf-2024 of The Year</h2>
                    <p data-aos="fade-up" data-aos-duration="700">Hear Highlights From Our Sponsors, or Get a Lite or Core Subscription
                        to Watch the Full Main Stage Event on Demand.</p>
                    <div class="generic-btn" data-aos="fade-up" data-aos-duration="700">
                        <a href="contact.html">REGISTER NOW <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="index3-registration-right-con" data-aos="fade-up" data-aos-duration="700">
                    <figure class="mb-0">
                        <img src="assets/images/index3-registration-right-img.png" alt="index3-registration-right-img">
                    </figure>
                </div>
            </div>
        </div>
    </section>
    <!-- INDEX3 REGISTRATION SECTION END -->
  
    <!-- INDEX3 BLOG SECTION START -->
    <section class="blog-main-section index3-blog-section w-100 float-left padding-top position-relative">
        <div class="container">
            <div class="generic-title2 text-center">
                <span class="small-text" data-aos="fade-up" data-aos-duration="700">SKILLS &amp; EXPERIENCE</span>
                <h2 class="mb-0" data-aos="fade-up" data-aos-duration="700">Recent News Articles</h2>
            </div>
            <div class="blogs-inner-con">
                <div class="blog-box position-relative" data-aos="fade-up" data-aos-duration="700">
                    <div class="blog-img position-relative">
                        <span class="d-inline-block">Illustration, Art</span>
                        <figure class="mb-0">
                            <img src="assets/images/blog-img1.jpg" alt="blog-img1">
                        </figure>
                    </div>
                    <div class="blog-text">
                        <span class="d-block">Nov 11, 2020</span>
                        <h6 class="position-relative"><a href="single-blog.html">Lorem ipsum dolor sita consectetur adip.</a></h6>
                    </div>
                </div>
                <div class="blog-box position-relative" data-aos="fade-up" data-aos-duration="700">
                    <div class="blog-img position-relative">
                        <span class="d-inline-block">Vintage, Design</span>
                        <figure class="mb-0">
                            <img src="assets/images/blog-img2.jpg" alt="blog-img3">
                        </figure>
                    </div>
                    <div class="blog-text">
                        <span class="d-block">Nov 18, 2020</span>
                        <h6 class="position-relative"><a href="single-blog.html">Dolor sit amet, consec adipiscing elit, sed.</a></h6>
                    </div>
                </div>
                <div class="blog-box position-relative" data-aos="fade-up" data-aos-duration="700">
                    <div class="blog-img position-relative">
                        <span class="d-inline-block">Questions, Answers</span>
                        <figure class="mb-0">
                            <img src="assets/images/blog-img3.jpg" alt="blog-img3">
                        </figure>
                    </div>
                    <div class="blog-text">
                        <span class="d-block">Nov 25, 2020</span>
                        <h6 class="position-relative"><a href="single-blog.html">Sit amet, consecteturs elit, sed.</a></h6>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- INDEX3 BLOG SECTION END -->
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
    --}}

@endsection
