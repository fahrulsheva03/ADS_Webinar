@extends('peserta.partials.app')

@section('content')
    <!-- BANNER SECTION START -->
    <section class="sub-banner-main-section contact-banner w-100 float-left">
        <div class="container">
            <div class="sub-banner-inner-con">
                <h1 data-aos="fade-up" data-aos-duration="700">CONTACT US</h1>
                <p data-aos="fade-up" data-aos-duration="700">Inspiring Talks, Meet the Best Product People Around the World,
                    <br>
                    and Party Together After the Event!
                </p>
                <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-duration="700">
                    <ol class="breadcrumb d-inline-block mb-0">
                        <li class="breadcrumb-item d-inline-block"><a href="index.html">HOME</a></li>
                        <li class="breadcrumb-item active d-inline-block" aria-current="page">CONTACT</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>
    <!-- BANNER SECTION END -->
    <!-- CONTACT INFORMATION SECTION START -->
    <section class="conatct-information-section w-100 float-left padding-top padding-bottom">
        <div class="container">
            <div class="generic-title text-center">
                <span class="small-text" data-aos="fade-up" data-aos-duration="700">CONTACT INFORMATION</span>
                <h2 data-aos="fade-up" data-aos-duration="700">Get In Touch With Us</h2>
            </div>
            <div class="contact-info-inner-con">
                <div class="contact-box" data-aos="fade-up" data-aos-duration="700">
                    <figure>
                        <img src="assets/images/location-icon.png" alt="location-icon">
                    </figure>
                    <h6>Location</h6>
                    <p>121 King Street, Melbourne
                        Victoria 3000 Australia</p>
                    <a href="contact.html">Get Directions</a>
                </div>
                <div class="contact-box" data-aos="fade-up" data-aos-duration="700">
                    <figure>
                        <img src="assets/images/mobile-icon.png" alt="mobile-icon">
                    </figure>
                    <h6>Phone</h6>
                    <ul class="list-unstyled">
                        <li><a href="tel:+61383766284">(+61 3 8376 6284)</a></li>
                        <li><a href="tel:+80023456789">(+800 2345 6789)</a></li>
                    </ul>
                    <a href="tel:+61383766284">Call Now</a>
                </div>
                <div class="contact-box" data-aos="fade-up" data-aos-duration="700">
                    <figure>
                        <img src="assets/images/mail-icon.png" alt="mail-icon">
                    </figure>
                    <h6>Email</h6>
                    <ul class="list-unstyled">
                        <li><a href="mailto:contact@confX.com">contact@confX.com</a></li>
                        <li><a href="mailto:sales@confX.com">sales@confX.com</a></li>
                    </ul>
                    <a href="mailto:contact@confX.com">Email Now</a>
                </div>
            </div>
        </div>
    </section>
    <!-- CONTACT INFORMATION SECTION END -->
    <!-- CONTACT FORM SECTION START -->
    <section class="contact-form-section w-100 float-left padding-top light-bg">
        <div class="container">
            <div class="generic-title text-center">
                <span class="text-center small-text" data-aos="fade-up" data-aos-duration="700">GET IN TOUCH WITH US</span>
                <h2 data-aos="fade-up" data-aos-duration="700">Send Us a Message</h2>
            </div>
            <form class="form-box" method="post" id="contactpage">
                <ul class="list-unstyled" data-aos="fade-up" data-aos-duration="700">
                    <li>
                        <input type="text" name="fname" id="fname" placeholder="Name">
                    </li>
                    <li>
                        <input type="tel" name="phone" id="phone" placeholder="Phone">
                    </li>
                    <li>
                        <input type="email" placeholder="Email" name="email" id="email">
                    </li>
                    <li>
                        <input type="text" placeholder="Website URL">
                    </li>
                    <li>
                        <textarea placeholder="Message" name="subject" id="subject"></textarea>
                    </li>
                </ul>
                <div class="submit-btn generic-btn">
                    <button type="submit" id="submit">SEND MESSAGE <i class="fas fa-arrow-right"></i></button>
                </div>
            </form>
        </div>
    </section>
    <!-- CONTACT FORM SECTION END -->
    <!-- MAP SECTION START -->
    <div class="responsive-map w-100 float-left">
        <div class="container">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.8387096759334!2d144.9532000767644!3d-37.817246734238644!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad65d4dd5a05d97%3A0x3e64f855a564844d!2s121%20King%20St%2C%20Melbourne%20VIC%203000%2C%20Australia!5e0!3m2!1sen!2s!4v1692879195247!5m2!1sen!2s"
                allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
    <!-- MAP SECTION END -->
    <!-- SPONSERS SECTION START -->
    <div class="sponsers-main-section about-sponsers w-100 float-left">
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
