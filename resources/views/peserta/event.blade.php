@extends('peserta.partials.app')

@section('content')

    <style>
        .index3-event-section {
            position: relative;
            background: linear-gradient(135deg, #b80000 0%, #F80000 45%, #ff4d4d 100%);
            min-height: 100vh;
        }

        .index3-event-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(1000px 420px at 15% 20%, rgba(255, 255, 255, 0.20) 0%, rgba(255, 255, 255, 0) 60%),
                radial-gradient(900px 380px at 85% 35%, rgba(0, 0, 0, 0.18) 0%, rgba(0, 0, 0, 0) 60%),
                radial-gradient(650px 280px at 50% 90%, rgba(255, 255, 255, 0.14) 0%, rgba(255, 255, 255, 0) 65%);
            pointer-events: none;
        }

        .index3-event-section > .container {
            position: relative;
            z-index: 1;
        }

        .index3-event-section .generic-title .small-text,
        .index3-event-section .generic-title h2 {
            color: #ffffff;
        }

        .index3-event-section .index3-event-tabs-con .nav-tabs {
            border-bottom: 0;
            gap: 10px;
        }

        .index3-event-section .index3-event-tabs-con .nav-tabs .nav-link {
            border: 0;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.18);
            color: rgba(255, 255, 255, 0.92);
            padding: 10px 16px;
            transition: transform 140ms ease, background-color 140ms ease;
        }

        .index3-event-section .index3-event-tabs-con .nav-tabs .nav-link:hover {
            background: rgba(255, 255, 255, 0.26);
            transform: translateY(-1px);
        }

        .index3-event-section .index3-event-tabs-con .nav-tabs .nav-link.active {
            background: rgba(255, 255, 255, 0.92);
            color: #1a1a1a;
        }

        .index3-event-section .index3-faqs .card {
            border: 0;
            border-radius: 16px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.16);
            margin-bottom: 14px;
        }

        .index3-event-section .index3-faqs .card:hover {
            box-shadow: 0 14px 34px rgba(0, 0, 0, 0.20);
        }

        .index3-event-section .index3-faqs .card-header {
            background: transparent;
            border: 0;
            padding: 0;
        }

        .index3-event-section .index3-faqs .card-header .btn.btn-link {
            width: 100%;
            text-align: left;
            padding: 18px 18px;
            color: #111827;
            text-decoration: none;
        }

        .index3-event-section .index3-faqs .card-header .btn.btn-link:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(248, 0, 0, 0.22);
            border-radius: 12px;
        }

        .index3-event-section .index3-event-detail {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .index3-event-section .index3-event-date-con {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 0 0 auto;
        }

        .index3-event-section .index3-event-date {
            width: 56px;
            height: 56px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            background: linear-gradient(135deg, #F80000 0%, #ff5c5c 100%);
            color: #ffffff;
            font-weight: 800;
            font-size: 22px;
            line-height: 1;
            box-shadow: 0 10px 18px rgba(248, 0, 0, 0.22);
        }

        .index3-event-section .index3-event-month span {
            font-weight: 800;
            color: #111827;
            letter-spacing: 0.02em;
        }

        .index3-event-section .index3-event-month small {
            color: #4b5563;
        }

        .index3-event-section .index3-event-organizer {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1 1 auto;
            justify-content: flex-end;
            min-width: 0;
        }

        .index3-event-section .index3-event-organizer figure img {
            width: 56px;
            height: 56px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid rgba(248, 0, 0, 0.18);
            background: #ffffff;
        }

        .index3-event-section .index3-organizer-detail {
            min-width: 0;
        }

        .index3-event-section .index3-organizer-detail span {
            font-weight: 800;
            color: #111827;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .index3-event-section .index3-organizer-detail small {
            color: #4b5563;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .index3-event-section .index3-faqs .card-body {
            padding: 0 18px 18px;
            color: #374151;
        }

        .index3-event-section .index3-faqs .card-body span {
            color: #111827;
            font-weight: 700;
        }

        .index3-event-section .index3-faq-btn-con p {
            color: rgba(255, 255, 255, 0.92);
        }

        @media (max-width: 767.98px) {
            .index3-event-section .index3-event-detail {
                flex-direction: column;
                align-items: flex-start;
            }

            .index3-event-section .index3-event-organizer {
                justify-content: flex-start;
                width: 100%;
            }

            .index3-event-section .index3-faqs .card-header .btn.btn-link {
                padding: 16px;
            }
        }
    </style>

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

@endsection
