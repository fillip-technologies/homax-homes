@extends('layout.layout')

{{-- @section('title', '1 Home Page') --}}
{{-- @section('title') --}}
@section('title', \Illuminate\Support\Str::limit($property->title, 60))
@section('description', \Illuminate\Support\Str::limit(strip_tags($property->description), 155))
@section('keywords', implode(', ', array_slice($property->keywords ?? [], 0, 10)))
@section('author', 'Homax Homes Team')

@section('og_title', \Illuminate\Support\Str::limit($property->title, 60))
@section('og_description', \Illuminate\Support\Str::limit(strip_tags($property->description), 200))
@section('og_image', $featuredImage ? asset($featuredImage->image_path) : asset('assets/images/home.png'))
@section('og_url', url()->current())
@section('og_type', 'property')

@section('twitter_card', 'summary_large_image')
@section('twitter_title', \Illuminate\Support\Str::limit($property->title, 60))
@section('twitter_description', \Illuminate\Support\Str::limit(strip_tags($property->description), 200))
@section('twitter_image', $featuredImage ? asset($featuredImage->image_path) : asset('assets/images/home.png'))
@section('twitter_site', '@HomaxHomes')
@section('twitter_creator', '@HomaxHomes')

@section('canonical', url()->current())
@section('head')

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Elysian Estates - Premium Property Listings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    @include('property.partials.head-styles')


@endsection

{{-- Project section links. These used to live in a floating pill below the
     hero; the site header now renders them in its place (see includes/header),
     so on a project page the header IS the project nav. The header pulls this
     section twice - once for the desktop row, once for the mobile strip. --}}
@section('headerNav')
    <a href="#overview" class="hx-secnav__item is-active"><i class="fa-solid fa-house"></i>Overview</a>
    <a href="#price" class="hx-secnav__item"><i class="fa-solid fa-tag"></i>Price</a>
    @if ($property->floor_plan_image)
        <button type="button" class="hx-secnav__item"
            onclick="openModal('{{ asset($property->floor_plan_image) }}')"><i
                class="fa-regular fa-file-lines"></i>Floor Plan</button>
    @endif
    <a href="#amenities" class="hx-secnav__item"><i class="fa-solid fa-grip"></i>Amenities</a>
    <a href="#gallery" class="hx-secnav__item"><i class="fa-regular fa-image"></i>Gallery</a>
    <a href="#location" class="hx-secnav__item"><i class="fa-solid fa-location-dot"></i>Location</a>
    @if ($property->video_url)
        <a href="#virtual-tour" class="hx-secnav__item"><i class="fa-solid fa-video"></i>Virtual Tour</a>
    @endif
    @if ($property->brochure || ($property->details && $property->details->contains(fn($d) => filled($d->document))))
        <button type="button" class="hx-secnav__item hx-secnav__item--bob" data-hxq-open
            data-hxq-heading="Download Brochure" data-hxq-submit-label="Download Now" data-hxq-intent="brochure"><i
                class="fa-solid fa-download"></i>Brochure</button>
    @endif
@endsection

@section('content')

    {{-- ==================== PREMIUM PROJECT HERO ====================
         Visual redesign only. Every value comes from the existing
         $property / $propertyimagesall / $featuredImage variables. The gallery
         and enquiry form are the SAME elements (identical ids, field names,
         route and validation) — only relocated and restyled. --}}
    @php
        // --- price -------------------------------------------------------------
        // `price` may hold a plain number (404444.00) OR an author-entered range
        // ("50L-70L"). Only format when it is genuinely numeric, otherwise show
        // the stored text as-is so ranges are never mangled into a single number.
        $rawPrice = trim((string) ($property->price ?? ''));
        $priceUnit = $property->price_unit ?: '₹';
        $priceDisplay = null;

        if ($rawPrice !== '') {
            if (is_numeric($rawPrice)) {
                $n = (float) $rawPrice;
                if ($n >= 10000000) {
                    $priceDisplay = rtrim(rtrim(number_format($n / 10000000, 2, '.', ''), '0'), '.') . ' Cr';
                } elseif ($n >= 100000) {
                    $priceDisplay = rtrim(rtrim(number_format($n / 100000, 2, '.', ''), '0'), '.') . ' L';
                } else {
                    $priceDisplay = number_format($n);
                }
            } else {
                // Space out an author-typed range for legibility (display only).
                $priceDisplay = preg_replace('/\s*-\s*/', ' – ', $rawPrice);
            }
        }

        // --- description -------------------------------------------------------
        // Stored as rich HTML. strip_tags alone leaves entities like &nbsp; and
        // &quot; visible as raw text, so decode them and collapse whitespace.
        $heroBlurb = $property->keyfeatures ?: ($property->description ?? '');
        // Replace tags with a space (not nothing) so block elements don't run
        // together as "Description:Property type".
        $heroBlurb = preg_replace('/<[^>]*>/', ' ', $heroBlurb);
        $heroBlurb = html_entity_decode($heroBlurb, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $heroBlurb = preg_replace('/<[^>]*>/', ' ', $heroBlurb);
        $heroBlurb = trim(preg_replace('/\s+/u', ' ', $heroBlurb));
        $heroBlurb = \Illuminate\Support\Str::limit($heroBlurb, 155);

        $heroLocation = collect([$property->city, $property->state])->filter()->implode(', ');
        $heroEyebrow = $property->city ? 'Premium Living in ' . \Illuminate\Support\Str::title($property->city) : null;

        // --- project card ------------------------------------------------------
        // Ribbon across the top of the card. A pre-launch listing outranks the
        // plain status line.
        $heroBanner = $property->pre_launch_property
            ? 'Pre-Launch: Limited Time Only'
            : (filled($property->property_status) ? 'Booking Open: ' . $property->property_status : null);

        // "By <developer>". Uses developer_name column if present, else falls back to owner name.
        $heroBy = $property->developer_name ?: optional($property->owner)->name;
        if ($heroBy && in_array(mb_strtolower(trim($heroBy)), ['admin', 'administrator', 'superadmin'], true)) {
            $heroBy = null;
        }

        // Possession: the date the property is available to move into.
        $possession = null;
        if (filled($property->possession_date)) {
            try {
                $possession = \Carbon\Carbon::parse($property->possession_date)->format('m-Y');
            } catch (\Throwable $e) {
                $possession = null;
            }
        }

        // Bullet list under the highlight box. Only surface what holds a value,
        // except RERA which states "Coming Soon" rather than going quiet - an
        // absent registration number is itself information a buyer wants.
        $heroBullets = [];
        if (filled($property->floors) && (int) $property->floors > 0) {
            $heroBullets[] = 'Total Floor : G+' . (int) $property->floors . ' Storeyed Tower';
        }
        $heroBullets[] = 'RERA Regd No. : ' . (filled($property->rera_id) ? $property->rera_id : 'Coming Soon');
        if ($possession) {
            $heroBullets[] = 'Possession : ' . $possession;
        }

        // "Luxurious 2 BHK Residences" - the configuration line above the price.
        $heroConfig = filled($property->bedrooms) ? 'Luxurious ' . $property->bedrooms . ' BHK Residences' : null;

        // Highlight box. keyfeatures is authored one point per line and is the
        // preferred source - it is the only place a human writes the pitch.
        $heroBenefits = collect(preg_split('/\R/u', (string) ($property->keyfeatures ?? '')))
            ->map(fn($l) => trim(html_entity_decode(strip_tags($l), ENT_QUOTES | ENT_HTML5, 'UTF-8')))
            ->filter()
            ->take(3)
            ->map(fn($l) => \Illuminate\Support\Str::words($l, 7, '…'))
            ->values();

        // A listing saved from the rich-text editor without key features stores
        // "<p><br></p>", which strips to nothing and silently removed the whole
        // banner from the card. Rebuild it from columns that do hold data, so
        // every listing keeps its banner and every line stays factual.
        if ($heroBenefits->isEmpty()) {
            $asList = function ($raw) {
                if (blank($raw)) {
                    return collect();
                }
                $list = is_array($raw) ? $raw : (json_decode((string) $raw, true) ?: preg_split('/\s*,\s*/', (string) $raw));
                return collect($list)
                    ->map(fn($v) => trim((string) $v))
                    ->filter()
                    ->values();
            };

            $where = $property->city ?: ($property->location ?: $property->landmark);
            $fallback = collect();

            if (filled($where)) {
                $fallback->push('Luxury Living in ' . $where);
            }
            if (($feat = $asList($property->features ?? null)->take(3))->isNotEmpty()) {
                $fallback->push($feat->implode(', '));
            }
            if (($amen = $asList($property->amenities ?? null)->take(3))->isNotEmpty()) {
                $fallback->push($amen->implode(', '));
            }
            if ($fallback->isEmpty() && filled($property->category)) {
                $fallback->push($property->category . ' Property');
            }

            $heroBenefits = $fallback->take(3)->values();
        }

        $heroImage = $featuredImage ? asset($featuredImage->image_path) : asset('assets/images/home.png');
        $heroTotalImages = count($propertyimagesall);
    @endphp

    {{-- ==================== PAGE SHELL ====================
         Desktop splits into the main flow (hero, section nav and every detail
         section) plus a right rail carrying the enquiry form. Keeping the rail
         a sibling of the entire flow - not of the hero - is what allows the
         form to stay stuck past the hero. --}}
    <div class="hx-shell">
        <div class="hx-shell__main">
    @include('property.partials.hero-styles')

    <section class="hx-hero">
        {{-- The slider IS the hero backdrop. Same ids and inline handlers the
             gallery script already binds, so zoom, arrows and thumbs keep working. --}}
        <div class="hx-hero__decor">
            <div class="hx-gallery">
                <div id="bigimage" class="hx-stage" onclick="openModal(currentBigImageSrc)">
                    <img id="bigImageDisplay1" src="{{ $heroImage }}" alt="{{ $property->title }}" class="hx-slide" />
                    <img id="bigImageDisplay2" src="" alt="" class="hx-slide hx-slide--idle" />
                </div>
            </div>

            <div class="hx-hero__scrim" aria-hidden="true"></div>

            @if ($heroTotalImages > 1)
                <div class="hx-hero__controls">
                    <button type="button" onclick="prevBigImage(event)" aria-label="Previous image"
                        class="hx-nav hx-nav--prev"><i class="fa-solid fa-arrow-left"></i></button>

                    <div class="hx-thumbs">
                        @foreach ($propertyimagesall->take(5) as $index => $image)
                            <button type="button" class="hx-thumb {{ $index === 0 ? 'is-active' : '' }}"
                                onclick="changeBigImage('{{ asset($image->image_path) }}', {{ $index }}); event.stopPropagation();"
                                aria-label="Show image {{ $index + 1 }}">
                                <img src="{{ asset($image->image_path) }}" alt="" loading="lazy" decoding="async" />
                                @if ($loop->last && $heroTotalImages > 5)
                                    <span class="hx-thumb__more">+{{ $heroTotalImages - 5 }}<small>More</small></span>
                                @endif
                            </button>
                        @endforeach
                    </div>

                    <button type="button" onclick="nextBigImage(event)" aria-label="Next image"
                        class="hx-nav hx-nav--next"><i class="fa-solid fa-arrow-right"></i></button>

                    <span id="heroImgCounter" class="hx-counter">1 / {{ $heroTotalImages }}</span>
                </div>
            @endif
        </div>

        <div class="hx-hero__inner">
            <div class="hx-hero__grid">
                <div class="hx-col hx-col--info">
                    <article class="hx-pcard">
                        @if ($heroBanner)
                            <header class="hx-pcard__banner">{{ $heroBanner }}</header>
                        @endif

                        <div class="hx-pcard__body">
                            <h1 class="hx-pcard__title">{{ $property->title }}</h1>

                            @if ($property->address || $heroLocation)
                                <p class="hx-pcard__at">At {{ $property->address ?: $heroLocation }}</p>
                            @endif

                            @if ($heroBy)
                                <p class="hx-pcard__by">By {{ $heroBy }}</p>
                            @endif

                            @if ($property->is_verified)
                                <p class="hx-pcard__verified"><i class="fa-solid fa-circle-check"></i>Verified Listing</p>
                            @endif

                            @if ($heroBenefits->count())
                                <div class="hx-pcard__perks">
                                    @foreach ($heroBenefits as $benefit)
                                        <span>{{ $benefit }}</span>
                                    @endforeach
                                </div>
                            @endif

                            @if (count($heroBullets))
                                <ul class="hx-pcard__bullets">
                                    @foreach ($heroBullets as $bullet)
                                        <li>{{ $bullet }}</li>
                                    @endforeach
                                </ul>
                            @endif

                            @if ($heroConfig)
                                <p class="hx-pcard__config">{{ $heroConfig }}</p>
                            @endif

                            <div id="price" class="hx-pcard__price">
                                @if ($priceDisplay)
                                    <span class="hx-pcard__price-label">Starting From</span>
                                    <strong class="hx-pcard__price-value">{{ $priceUnit }} {{ $priceDisplay }}*</strong>
                                    <span class="hx-pcard__price-note">Onwards</span>
                                @else
                                    <span class="hx-pcard__price-label">Price on request</span>
                                @endif
                            </div>

                            <button type="button" class="hx-pcard__cta" data-hxq-open
                                data-hxq-heading="Enquire Now">Enquire Now</button>
                        </div>
                    </article>
                </div>
            </div>
        </div>

        <a href="#overview" class="hx-scroll" aria-label="Scroll to explore">
            <span class="hx-scroll__ring"><i class="fa-solid fa-arrow-down"></i></span>
        </a>
    </section>

    {{-- Keeps the existing enquiry form reachable once the hero scrolls away.
         This is not a second form - every control here points at the one form
         above (#enquiry) or the same tel:/WhatsApp links. --}}
    <div id="hxDock" class="hx-dock" aria-hidden="true">
        <div class="hx-dock__inner">
            <div class="hx-dock__meta">
                <b>{{ $property->title }}</b>
                @if ($priceDisplay)
                    <span>{{ $priceUnit }} {{ $priceDisplay }}</span>
                @endif
            </div>
            <div class="hx-dock__actions">
                <a href="tel:+919920685877" class="hx-btn hx-btn--outline hx-dock__icon" aria-label="Call us">
                    <i class="fa-solid fa-phone"></i><span>Call</span>
                </a>
                <a href="https://wa.me/919920685877" target="_blank" rel="noopener noreferrer"
                    class="hx-btn hx-btn--whatsapp hx-dock__icon" aria-label="WhatsApp">
                    <i class="fa-brands fa-whatsapp"></i><span>WhatsApp</span>
                </a>
                <a href="#enquiry" data-hxq-open class="hx-btn hx-btn--primary hx-dock__cta">
                    Enquire Now <i class="fa-solid fa-arrow-right hx-btn__arrow"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Enquiry modal + the gated-brochure handoff. Both live in
         resources/views/components/. --}}
    <x-enquiry-modal :property="$property" :benefits="$heroBenefits" />

    @if (session('brochure_url'))
        @include('property.partials.brochure-download-script')
    @endif

    @include('property.partials.quote-card-styles')

    @include('property.partials.enquiry-modal-script')

    @include('property.partials.sticky-header-script')



    @push('scripts')
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endpush



    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

        @include('property.partials.gallery-script')

    @include('property.partials.content-styles')

        {{-- ==================== PROJECT CONTENT ====================
             Section-per-card layout. Every value still comes from the same
             $property columns the previous markup read - this is a restyle, not
             new data. Sections whose column is empty simply do not render. --}}
        @php
            $pdName = $property->title;

            // Carried over verbatim from the previous markup.
            $iconMap = [
                'Swimming Pool' => 'fa-person-swimming',
                'Gym' => 'fa-dumbbell',
                'Parking' => 'fa-car',
                'Garden' => 'fa-tree',
                'Security' => 'fa-user-shield',
                'Lift' => 'fa-elevator',
                'Power Backup' => 'fa-bolt',
                'WiFi' => 'fa-wifi',
                'Air Conditioning' => 'fa-wind',
                'Heating' => 'fa-temperature-high',
                'TV' => 'fa-tv',
                'Washing Machine' => 'fa-soap',
                'Microwave' => 'fa-fire-burner',
                'Refrigerator' => 'fa-snowflake',
                'Dishwasher' => 'fa-sink',
                'Balcony' => 'fa-mountain-sun',
            ];

            // Map embed, unchanged from the previous Location block.
            $mapUrl = null;
            $apiKey = 'AIzaSyAfS-bCjy7PCM5Z-79SZyaMJNgBByvzN6o';
            if (!empty($property->google_map_link)) {
                if (strpos($property->google_map_link, 'embed') !== false) {
                    $mapUrl = $property->google_map_link;
                } elseif (preg_match('/@([\-0-9.]+),([\-0-9.]+)/', $property->google_map_link, $matches)) {
                    $mapUrl = "https://www.google.com/maps/embed/v1/view?key={$apiKey}&center={$matches[1]},{$matches[2]}&zoom=14";
                } else {
                    $mapUrl = "https://www.google.com/maps/embed/v1/search?key={$apiKey}&q=" . urlencode($property->google_map_link);
                }
            } elseif (!empty($property->address)) {
                $mapUrl = "https://www.google.com/maps/embed/v1/place?key={$apiKey}&q=" . urlencode($property->address);
            } elseif (!empty($property->city)) {
                $mapUrl = "https://www.google.com/maps/embed/v1/place?key={$apiKey}&q=" . urlencode($property->city);
            }

            // Overview tiles. Same fields the old "Property Overview" grid read.
            $pdHighlights = [];
            if (filled($property->category)) {
                $pdHighlights[] = ['fa-building', 'Category', $property->category];
            }
            if ($property->details && $property->details->count() > 0) {
                $configs = $property->details->pluck('unit_type')->filter()->unique();
                if ($configs->isEmpty()) {
                    $configs = $property->details->pluck('bedrooms')->filter()->unique()->map(fn($b) => $b . ' BHK');
                }
                if ($configs->isNotEmpty()) {
                    $pdHighlights[] = ['fa-bed', 'Configuration', $configs->implode(', ')];
                }
                if (filled($property->apartment_per_floor)) {
                    $pdHighlights[] = ['fa-door-open', 'Apt / Floor', $property->apartment_per_floor];
                }
            } elseif (filled($property->bedrooms)) {
                $pdHighlights[] = ['fa-bed', 'Configuration', $property->bedrooms . ' BHK'];
            }
            if (filled($property->furnishing)) {
                $pdHighlights[] = ['fa-couch', 'Furnishing', $property->furnishing];
            }
            if (filled($property->possession_date)) {
                try {
                    $pdHighlights[] = ['fa-calendar-check', 'Possession', \Carbon\Carbon::parse($property->possession_date)->format('m-Y')];
                } catch (\Throwable $e) {
                    // leave possession out of the highlight strip if the date can't be parsed
                }
            }
            if (filled($property->rera_id)) {
                $pdHighlights[] = ['fa-id-badge', 'RERA ID', $property->rera_id];
            }
            if (filled($property->city)) {
                $pdHighlights[] = ['fa-location-dot', 'Location', \Illuminate\Support\Str::title($property->city)];
            }

            // Areas are seeded as 1.00 on several rows, which is a placeholder
            // rather than a real measurement - treat anything <= 1 as absent.
            $pdArea = null;
            foreach (['carpet_area', 'super_area', 'plot_area'] as $col) {
                if (filled($property->$col) && (float) $property->$col > 1) {
                    $pdArea = rtrim(rtrim(number_format((float) $property->$col, 2, '.', ','), '0'), '.') . ' Sq.Ft';
                    break;
                }
            }

            $pdUnitType = filled($property->bedrooms)
                ? $property->bedrooms . ' BHK'
                : 'Unit';

            // "0.5 km" style values get the place name in front; older free-text values
            // (e.g. "Metro Station (0.5 km)") already carry their own name and show as-is.
            // An optional name typed in the admin (place_names) is shown after the label:
            // "Hospital: Apollo Hospital - 2 km".
            $pdPlaceNames = (array) $property->place_names;
            $pdPlaceText = function ($label, $val, $key = null) use ($pdPlaceNames) {
                $name = trim((string) ($pdPlaceNames[$key] ?? ''));
                if ($name !== '') {
                    $label .= ': ' . $name;
                }

                return preg_match('/^\d+(\.\d+)?\s?(m|km)$/i', trim($val)) ? $label . ' - ' . trim($val) : $val;
            };

            $pdNearby = [];
            foreach ([
                ['fa-train-subway', 'Metro Station', $property->bazar_distance_km, 'bazar'],
                ['fa-hospital', 'Hospital', $property->hospital_distance_km, 'hospital'],
                ['fa-school', 'School', $property->school_distance_km, 'school'],
            ] as [$ic, $label, $val, $key]) {
                if (filled($val)) { $pdNearby[] = [$ic, $pdPlaceText($label, $val, $key)]; }
            }

            $pdConnect = [];
            foreach ([
                ['fa-bus', 'Bus Stand', $property->bus_stand_distance_km, 'bus_stand'],
                ['fa-train', 'Railway Junction', $property->junction_distance_km, 'junction'],
                ['fa-plane', 'Airport', $property->airport_distance_km, 'airport'],
            ] as [$ic, $label, $val, $key]) {
                if (filled($val)) { $pdConnect[] = [$ic, $pdPlaceText($label, $val, $key)]; }
            }

            foreach ((array) $property->custom_nearby_places as $place) {
                if (blank($place['label'] ?? null) || blank($place['distance'] ?? null)) { continue; }
                $ic = array_key_exists($place['icon'] ?? '', \App\Models\Property::PLACE_ICONS)
                    ? $place['icon'] : \App\Models\Property::DEFAULT_PLACE_ICON;
                $row = [$ic, $place['label'] . ' - ' . $place['distance']];
                if (($place['group'] ?? '') === 'connectivity') { $pdConnect[] = $row; } else { $pdNearby[] = $row; }
            }
        @endphp

        <div class="pd-stack">

            {{-- ---------- Welcome ---------- --}}
            @if (filled($property->description))
                <section class="pd-card">
                    <h2 class="pd-h">Welcome To {{ $pdName }}</h2>
                    <div class="pd-prose is-clamped" data-pd-prose>{!! $property->description !!}</div>
                    <button type="button" class="pd-readmore" data-pd-more hidden>Read more</button>

                    <button type="button" class="pd-btn" data-hxq-open data-hxq-heading="Download Brochure"
                        data-hxq-submit-label="Download Now" data-hxq-intent="brochure">
                        <i class="fa-solid fa-file-arrow-down"></i>Download Brochure
                    </button>
                </section>
            @endif

            {{-- ---------- Project Highlights ---------- --}}
            @if (count($pdHighlights))
                <section id="overview" class="pd-card">
                    <h2 class="pd-h">Project Highlights</h2>
                    <div class="pd-tiles">
                        @foreach ($pdHighlights as $tile)
                            <div class="pd-tile">
                                <span class="pd-tile__ic"><i class="fa-solid {{ $tile[0] }}"></i></span>
                                <h3 class="pd-tile__t">{{ $tile[1] }}</h3>
                                <p class="pd-tile__d">{{ $tile[2] }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- ---------- Pricing ---------- --}}
            @php
                $detailsCollection = ($property->details && $property->details->count() > 0) ? $property->details : collect();
                $hasAptPerFloor = filled($property->apartment_per_floor);
                $hasSuperAreaCol = $detailsCollection->contains(fn($d) => filled($d->super_area) && (float)$d->super_area > 1);
            @endphp
            <section class="pd-card">
                <h2 class="pd-h">{{ $pdName }} Pricing {{ $pdArea ? 'And Carpet Area' : '' }}</h2>
                <div class="pd-tablewrap">
                    <table class="pd-table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Carpet Area</th>
                                @if ($hasSuperAreaCol)
                                    <th>Super Area</th>
                                @endif
                                @if ($hasAptPerFloor)
                                    <th>Apt / Floor</th>
                                @endif
                                <th>Price</th>
                                <th><span class="sr-only">Breakup</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($detailsCollection->isNotEmpty())
                                @foreach ($detailsCollection as $detail)
                                    @php
                                        $dType = $detail->unit_type ?: ($detail->bedrooms ? $detail->bedrooms . ' BHK' : $pdUnitType);
                                        $dCarpet = filled($detail->carpet_area) && (float)$detail->carpet_area > 1 ? $detail->carpet_area . ' sq.ft' : ($pdArea ?: 'On Request');
                                        $dSuper = filled($detail->super_area) && (float)$detail->super_area > 1 ? $detail->super_area . ' sq.ft' : null;
                                        $dPrice = $detail->price ? $detail->price : ($priceDisplay ? $priceUnit . ' ' . $priceDisplay : 'On Request');
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $dType }}</strong>
                                            @if ($detail->bathrooms || $detail->balconies)
                                                <div class="small text-muted" style="font-size: 11.5px; margin-top: 2px;">
                                                    @if ($detail->bathrooms) {{ $detail->bathrooms }} Baths @endif
                                                    @if ($detail->bathrooms && $detail->balconies) • @endif
                                                    @if ($detail->balconies) {{ $detail->balconies }} Balconies @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $dCarpet }}</td>
                                        @if ($hasSuperAreaCol)
                                            <td>{{ $dSuper ?: 'N/A' }}</td>
                                        @endif
                                        @if ($hasAptPerFloor)
                                            <td>{{ $property->apartment_per_floor }}</td>
                                        @endif
                                        <td class="pd-table__price">
                                            {{ $dPrice }}
                                        </td>
                                        <td>
                                            @if (filled($detail->document))
                                                <button type="button" class="pd-chip" data-hxq-open
                                                    data-hxq-heading="Download Plan & Costing for {{ $dType }}"
                                                    data-hxq-submit-label="Download Now"
                                                    data-hxq-intent="brochure"
                                                    data-hxq-detail-id="{{ $detail->id }}">
                                                    <i class="fa-solid fa-file-arrow-down mr-1"></i> Cost Sheet
                                                </button>
                                            @else
                                                <button type="button" class="pd-chip" data-hxq-open
                                                    data-hxq-heading="Request Price Breakup for {{ $dType }}"
                                                    data-hxq-submit-label="Get Price Breakup"
                                                    data-hxq-detail-id="{{ $detail->id }}">
                                                    Price Breakup
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td>{{ $pdUnitType }}</td>
                                    <td>{{ $pdArea ?: 'On Request' }}</td>
                                    @if ($hasSuperAreaCol)
                                        <td>{{ $property->super_area ? $property->super_area . ' sq.ft' : 'N/A' }}</td>
                                    @endif
                                    @if ($hasAptPerFloor)
                                        <td>{{ $property->apartment_per_floor }}</td>
                                    @endif
                                    <td class="pd-table__price">
                                        {{ $priceDisplay ? $priceUnit . ' ' . $priceDisplay : 'On Request' }}
                                    </td>
                                    <td>
                                        <button type="button" class="pd-chip" data-hxq-open
                                            data-hxq-heading="Request Price Breakup" data-hxq-submit-label="Get Price Breakup">
                                            Price Breakup
                                        </button>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @php
                    $firstDetailWithDoc = $detailsCollection->first(fn($d) => filled($d->document));
                @endphp
                <button type="button" class="pd-btn pd-btn--soft" data-hxq-open
                    data-hxq-heading="Download Costing Details" data-hxq-submit-label="Download Now"
                    data-hxq-intent="brochure"
                    @if ($firstDetailWithDoc) data-hxq-detail-id="{{ $firstDetailWithDoc->id }}" @endif>
                    <i class="fa-solid fa-file-invoice"></i>Download Costing Details
                </button>
            </section>

            {{-- ---------- Floor Plan ---------- --}}
            @if ($property->floor_plan_image)
                <section class="pd-card">
                    <h2 class="pd-h">{{ $pdName }} Floor Plan</h2>
                    <div class="pd-plans">
                        <figure class="pd-plan">
                            <img src="{{ asset($property->floor_plan_image) }}" alt="{{ $pdName }} floor plan"
                                loading="lazy" decoding="async" />
                            <figcaption>Request Unit Plan Layout</figcaption>
                            <button type="button" class="pd-plan__btn" data-hxq-open
                                data-hxq-heading="Request Unit Plan" data-hxq-submit-label="Request Now">
                                <i class="fa-solid fa-download"></i>Unit Plan
                            </button>
                        </figure>

                        <div class="pd-plan pd-plan--ghost">
                            <button type="button" class="pd-plan__zoom"
                                onclick="openModal('{{ asset($property->floor_plan_image) }}')">
                                <i class="fa-solid fa-magnifying-glass-plus"></i>
                                <span>View Full Layout</span>
                            </button>
                        </div>
                    </div>
                </section>
            @endif

            {{-- ---------- Amenities ---------- --}}
            @if (!empty($property->features))
                <section id="amenities" class="pd-card">
                    <div class="pd-h-row">
                        <h2 class="pd-h">Amenities Of {{ $pdName }}</h2>
                        <button type="button" class="pd-btn pd-btn--sm" data-hxq-open
                            data-hxq-heading="Download Amenities" data-hxq-submit-label="Download Now"
                            data-hxq-intent="brochure">
                            <i class="fa-solid fa-download"></i>Download Amenities
                        </button>
                    </div>
                    <div class="pd-amen">
                        @foreach ($property->features as $item)
                            @if (!empty($item))
                                <div class="pd-amen__item">
                                    <i class="fa-solid {{ $iconMap[$item] ?? 'fa-circle-check' }}"></i>
                                    <span>{{ $item }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- ---------- Gallery ----------
                 Holds the #gallery anchor the section nav targets. Tiles reuse
                 openModal(), so the existing full-screen viewer (with its own
                 prev/next over imageSources) does the browsing. --}}
            @if ($propertyimagesall->count())
                @php $pdShots = $propertyimagesall->count(); @endphp
                <section id="gallery" class="pd-card">
                    <div class="pd-h-row">
                        <h2 class="pd-h">Gallery</h2>
                        <span class="pd-count">{{ $pdShots }} {{ \Illuminate\Support\Str::plural('Photo', $pdShots) }}</span>
                    </div>
                    <div class="pd-gal">
                        @foreach ($propertyimagesall as $index => $image)
                            <button type="button"
                                class="pd-gal__item{{ $index === 0 && $pdShots >= 5 ? ' is-lead' : '' }}"
                                onclick="openModal('{{ asset($image->image_path) }}')"
                                aria-label="Open photo {{ $index + 1 }} of {{ $pdShots }}">
                                <img src="{{ asset($image->image_path) }}"
                                    alt="{{ $property->title }} - photo {{ $index + 1 }}"
                                    loading="lazy" decoding="async" />
                                <span class="pd-gal__zoom" aria-hidden="true">
                                    <i class="fa-solid fa-magnifying-glass-plus"></i>
                                </span>
                            </button>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- ---------- Premium Specifications ---------- --}}
            @if (!empty($property->amenities))
                <section class="pd-card">
                    <h2 class="pd-h">Premium Specifications</h2>
                    <div class="pd-specs">
                        @foreach ($property->amenities as $item)
                            @if (!empty($item))
                                <div class="pd-spec">
                                    <span class="pd-spec__ic"><i class="fa-solid {{ $iconMap[$item] ?? 'fa-circle-check' }}"></i></span>
                                    <div>
                                        <h3 class="pd-spec__t">{{ $item }}</h3>
                                        <p class="pd-spec__d">Included with this residence.</p>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- ---------- Key Features ---------- --}}
            @if (filled($property->keyfeatures))
                <section class="pd-card">
                    <h2 class="pd-h">Key Features</h2>
                    <div class="pd-prose pd-prose--list">{!! $property->keyfeatures !!}</div>
                </section>
            @endif

            {{-- ---------- Location ---------- --}}
            <section id="location" class="pd-card">
                <h2 class="pd-h">Location Advantages</h2>

                <div class="pd-loc">
                    <div class="pd-loc__map">
                        @if ($mapUrl)
                            <iframe src="{{ $mapUrl }}" width="100%" height="100%" style="border:0"
                                allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                                title="Map of {{ $pdName }}"></iframe>
                        @else
                            <div class="pd-loc__empty">
                                <i class="fa-solid fa-map-location-dot"></i>
                                <p>No map location available</p>
                            </div>
                        @endif
                    </div>

                    <div class="pd-loc__list">
                        @if (count($pdNearby))
                            <h3 class="pd-loc__h">Nearby Places</h3>
                            <ul>
                                @foreach ($pdNearby as $row)
                                    <li><i class="fa-solid {{ $row[0] }}"></i><span>{{ $row[1] }}</span></li>
                                @endforeach
                            </ul>
                        @endif

                        @if (count($pdConnect))
                            <h3 class="pd-loc__h">Connectivity</h3>
                            <ul>
                                @foreach ($pdConnect as $row)
                                    <li><i class="fa-solid {{ $row[0] }}"></i><span>{{ $row[1] }}</span></li>
                                @endforeach
                            </ul>
                        @endif

                        @if (!count($pdNearby) && !count($pdConnect))
                            <p class="pd-loc__none">Distances for this project have not been added yet.</p>
                        @endif
                    </div>
                </div>
            </section>

            {{-- ---------- Notes ---------- --}}
            @if (filled($property->notes))
                <section class="pd-card">
                    <h2 class="pd-h">Notes</h2>
                    <div class="pd-prose pd-prose--list">{!! $property->notes !!}</div>
                </section>
            @endif

            {{-- ---------- Virtual Site Visit ---------- --}}
            @if ($property->video_url)
                <section id="virtual-tour" class="pd-card">
                    <h2 class="pd-h">Virtual Tour Request</h2>
                    <a class="pd-tour" href="{{ $property->video_url }}" target="_blank" rel="noopener noreferrer"
                        style="background-image:url('{{ $heroImage }}')">
                        <span class="pd-tour__play"><i class="fa-solid fa-play"></i></span>
                        <span class="pd-tour__txt">
                            <strong>Virtual Site Visit</strong>
                            <small>{{ $pdName }}</small>
                        </span>
                    </a>
                </section>
            @endif

        </div>

    @include('property.partials.read-more-script')

    </div>
        </div>{{-- /.hx-shell__main --}}

        {{-- Right rail. This is the SAME enquiry form that used to sit inside
             the hero grid - same ids, field names, route and validation. It
             lives here, as a sibling of the whole main flow rather than of the
             hero, which is what lets position:sticky hold it from the hero all
             the way down to the end of the detail sections. --}}
        <aside class="hx-shell__aside" aria-label="Enquiry">
            <div id="enquiry" class="hxq-sticky">
                <div class="hxq-card">
                    <div class="hxq-strip">
                        <button type="button" class="hxq-strip__item" data-hxq-open
                            @if ($property->brochure || ($property->details && $property->details->contains(fn($d) => filled($d->document)))) data-hxq-heading="Download Price Sheet"
                                data-hxq-submit-label="Download Now" data-hxq-intent="brochure" @endif>
                            <i class="fa-solid fa-file-arrow-down"></i>
                            <span>Download<br>Price Sheet</span>
                        </button>

                        <span class="hxq-strip__sep" aria-hidden="true"></span>

                        <a href="tel:+919920685877" class="hxq-strip__item">
                            <i class="fa-solid fa-phone-volume"></i>
                            <span>+91 99206 85877</span>
                        </a>
                    </div>

                    <div class="hxq-actions">
                        {{-- Gated download: opens the enquiry form, and the
                             controller hands back the PDF once the lead lands. --}}
                        <a href="#enquiry" data-hxq-open data-hxq-heading="Download Brochure"
                            data-hxq-submit-label="Download Now" data-hxq-intent="brochure"
                            class="hxq-action hxq-action--bob"><i class="fa-solid fa-file-arrow-down"></i>Brochure</a>
                        <a href="tel:+919920685877" class="hxq-action"><i class="fa-solid fa-phone"></i>Call</a>
                        <a href="https://wa.me/919920685877" target="_blank" rel="noopener noreferrer"
                            class="hxq-action hxq-action--wa"><i class="fa-brands fa-whatsapp"></i>WhatsApp</a>
                    </div>

                    <x-enquiry-form :property="$property" uid="side" />
                </div>
            </div>
        </aside>
    </div>{{-- /.hx-shell --}}

    <!-- Modal -->
    <div id="modal" class="fixed inset-0 bg-[#8B6508] flex justify-center items-center hidden z-50 p-4">
        <button onclick="closeModal()"
            class="absolute top-6 right-6 text-white text-4xl font-bold hover:text-brand-primary transition-transform z-60">
            <i class="fa-solid fa-times"></i>
        </button>

        <!-- Previous button -->
        <button onclick="prevImage()"
            class="absolute left-6 top-1/2 -translate-y-1/2 text-white text-4xl font-bold hover:text-brand-primary transition-transform">
            <i class="fa-solid fa-chevron-left"></i>
        </button>

        <!-- Next button -->
        <button onclick="nextImage()"
            class="absolute right-6 top-1/2 -translate-y-1/2 text-white text-4xl font-bold hover:text-brand-primary transition-transform">
            <i class="fa-solid fa-chevron-right"></i>
        </button>

        <img id="modalImage" class="w-4/5 h-full object-contain rounded-lg shadow-2xl transition-opacity duration-300" />

        <!-- Image counter -->
        <div class="absolute bottom-6 left-0 right-0 text-center text-white font-medium">
            <span id="currentImageNum">1</span> / <span id="totalImages">7</span>
        </div>
    </div>

    <!-- Similar Properties -->
    <section id="featured-properties" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Heading -->
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                    Similar Properties
                </h2>
                <div class="mx-auto w-24 h-1 bg-gradient-to-r from-primary to-primary-dark rounded-full mb-6"></div>
                <p class="text-gray-500 max-w-3xl mx-auto text-lg">
                    Explore our handpicked selection of premium properties. Each listing is carefully vetted to ensure
                    quality and value for our clients.
                </p>
            </div>

            <!-- Property Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @forelse($similarProperties as $sim)
                    @php
                        $cardImage = $sim->main_image 
                            ? asset($sim->main_image) 
                            : ($sim->featuredImage ? asset($sim->featuredImage->image_path) : 'https://images.unsplash.com/photo-1568605114967-8130f3a36994');
                    @endphp
                    <div
                        class="property-card bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-500 hover:shadow-2xl hover:-translate-y-2 flex flex-col justify-between">
                        <div>
                            <div class="relative h-60 overflow-hidden bg-gray-100">
                                <img src="{{ $cardImage }}" alt="{{ $sim->title }}"
                                    class="w-full h-full object-cover transition-transform duration-700 hover:scale-110" />
                                <div class="absolute top-4 left-4 flex flex-col space-y-2">
                                    @if($sim->project_status)
                                        <span class="bg-[#DAA520] text-white text-xs font-semibold px-3 py-1 rounded-full shadow-sm">
                                            {{ $sim->project_status }}
                                        </span>
                                    @elseif($sim->pre_launch_property)
                                        <span class="bg-[#DAA520] text-white text-xs font-semibold px-3 py-1 rounded-full shadow-sm">
                                            Pre-Launch
                                        </span>
                                    @elseif($sim->is_featured)
                                        <span class="bg-[#000080] text-white text-xs font-semibold px-3 py-1 rounded-full shadow-sm animate-pulse">
                                            Featured
                                        </span>
                                    @elseif($sim->property_status)
                                        <span class="bg-red-500 text-white text-xs font-semibold px-3 py-1 rounded-full shadow-sm">
                                            {{ $sim->property_status }}
                                        </span>
                                    @endif
                                </div>
                                @if($sim->is_verified)
                                    <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm rounded-full p-2 shadow-md">
                                        <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20" title="Verified">
                                            <path fill-rule="evenodd"
                                                d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-xl font-bold text-gray-800 line-clamp-1" title="{{ $sim->title }}">{{ $sim->title }}</h3>
                                    @if($sim->project_status)
                                        <span class="bg-primary/10 text-primary text-xs font-medium px-2.5 py-0.5 rounded shrink-0">{{ $sim->project_status }}</span>
                                    @endif
                                </div>
                                @if($sim->developer_name)
                                    <p class="text-xs text-primary font-medium mb-1">By {{ $sim->developer_name }}</p>
                                @endif
                                <p class="text-sm text-gray-500 mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="truncate">{{ $sim->location ?? $sim->city ?? $sim->address }}</span>
                                </p>
                                <div class="flex items-center text-sm text-gray-600 mb-4 space-x-4">
                                    @if($sim->bedrooms)
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                                </path>
                                            </svg>
                                            {{ $sim->bedrooms }} BHK
                                        </span>
                                    @endif
                                    @if($sim->super_area)
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4">
                                                </path>
                                            </svg>
                                            {{ $sim->super_area }} sqft
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="px-6 pb-6">
                            <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                                <span class="text-2xl font-bold text-[#DAA520]">&#8377;{{ $sim->price }}</span>
                                <a href="{{ route('property.show', $sim->id) }}"
                                    class="text-sm bg-[#000080] hover:bg-[#000066] text-white px-4 py-2 rounded-md transition-colors duration-300 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                             d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                             d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                         </path>
                                     </svg>
                                     View
                                 </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-10 text-gray-500">
                        <p class="text-lg">No similar properties found at this moment.</p>
                    </div>
                @endforelse
            </div>

            <!-- View All Button -->
            <div class="text-center mt-12">
                <a href="{{ route('property.search') }}"
                    class="inline-block bg-white border-2 border-[#000080] text-[#000080] hover:bg-[#000080] hover:text-white px-8 py-3 rounded-full font-semibold transition-all duration-300 transform hover:scale-105 shadow-md">
                    View All Properties
                </a>
            </div>
        </div>
    </section>

    @include('property.partials.reveal-script')


@endsection






