@php
    $speakers = $speakers ?? collect();
@endphp

<section id="speakers" class="speakers-main-section w-100 float-left padding-top padding-bottom">
    <div class="container-fluid">
        <div class="generic-title text-center">
            <span class="small-text" data-aos="fade-up" data-aos-duration="700">WORLD BEST SPEAKERS</span>
            <h2 data-aos="fade-up" data-aos-duration="700">Meet Our Amazing Speakers</h2>
            <p data-aos="fade-up" data-aos-duration="700">8+ Inspiring Talks, Meet the Best Product People Around the
                World, and <br> Party Together After the
                Event!</p>
        </div>
        <div class="speakers-inner-sec" data-aos="fade-up" data-aos-duration="700">
            @forelse ($speakers as $speaker)
                <div class="speaker-box position-relative">
                    <a href="{{ $speaker->linkedin_url ?: '#' }}" @if ($speaker->linkedin_url) target="_blank" rel="noopener" @endif>
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <figure class="mb-0">
                        <img src="{{ $speaker->foto_url ?: asset('assets/images/speakers-img1.jpg') }}" alt="{{ $speaker->nama }}">
                    </figure>
                    <div class="speaker-status">
                        <button class="showBtn{{ $loop->iteration }}">{{ $speaker->nama }} <i class="fas fa-angle-up"></i></button>
                        <div class="staus-con data{{ $loop->iteration }}">
                            <span>{{ $speaker->jabatan }} - {{ $speaker->perusahaan }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center mb-0">Belum ada speaker.</p>
            @endforelse
        </div>
    </div>
</section>
