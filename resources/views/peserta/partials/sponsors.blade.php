@php
    $page = (string) ($page ?? 'home');
    $wrapperClass = (string) ($wrapperClass ?? 'sponsers-main-section w-100 float-left');

    $fallbackLogos = [
        'assets/images/sponsers-logo1.png',
        'assets/images/sponsers-logo2.png',
        'assets/images/sponsers-logo3.png',
        'assets/images/sponsers-logo4.png',
        'assets/images/sponsers-logo5.png',
    ];

    $logos = collect($fallbackLogos)->map(function ($fallback, $idx) use ($page) {
        $key = 'logo_'.($idx + 1);
        $val = trim((string) konten($page, 'sponsors', $key));
        if ($val === '') {
            $val = $fallback;
        }

        if (str_starts_with($val, 'http://') || str_starts_with($val, 'https://')) {
            return $val;
        }

        return asset($val);
    });
@endphp

<div class="{{ $wrapperClass }}">
    <div class="container">
        <div class="sponsers-companies">
            <ul class="list-unstyled mb-0" data-aos="fade-up" data-aos-duration="700">
                @foreach ($logos as $i => $logoUrl)
                    <li>
                        <figure class="mb-0">
                            <img src="{{ $logoUrl }}" alt="Sponsor {{ $i + 1 }}">
                        </figure>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
