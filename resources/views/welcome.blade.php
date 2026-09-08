@extends('layout.layout')

@section('title', 'Homax Homes')
@php
    $primaryColor = $primaryColor ?? '#5146C7'; // fallback
@endphp

@section('head')
    {{-- Warm up the connections for the off-site images used further down the page. --}}
    <link rel="preconnect" href="https://images.unsplash.com" crossorigin />
    <link rel="dns-prefetch" href="https://images.unsplash.com" />
    <link rel="dns-prefetch" href="https://upload.wikimedia.org" />
    <link rel="dns-prefetch" href="https://randomuser.me" />

    {{-- Hero background is the LCP element; start it before the CSS resolves. --}}
    <link rel="preload" as="image" href="{{ asset('assets/hero-section.webp') }}" fetchpriority="high" />
@endsection

@section('content')
    <style>
        :root {
            /* Primary Colors */
            --primary: {{ $primaryColor }};
            --primary-dark: #4038A8;
            --primary-darker: #17113B;

            /* Neutral Colors */
            --gray-dark: #717271;
            --gray-light: #b1b2b1;
            --white: #ffffff;

            /* Additional Colors */
            --teal: #38b2ac;
            /* Keeping teal for some elements as accent */
            --red: #e53e3e;
            --yellow: #f6e05e;
            --purple: #805ad5;
            --blue: #4299e1;
            --green: #48bb78;
        }

        /* Text Colors */
        .text-primary {
            color: var(--primary);
        }

        .text-primary-dark {
            color: var(--primary-dark);
        }

        .text-primary-darker {
            color: var(--primary-darker);
        }

        .text-gray-dark {
            color: var(--gray-dark);
        }

        .text-gray-light {
            color: var(--gray-light);
        }

        /* Background Colors */
        .bg-primary {
            background-color: var(--primary);
        }

        .bg-primary-dark {
            background-color: var(--primary-dark);
        }

        .bg-primary-darker {
            background-color: var(--primary-darker);
        }

        .bg-gray-dark {
            background-color: var(--gray-dark);
        }

        .bg-gray-light {
            background-color: var(--gray-light);
        }

        /* Gradient Backgrounds */
        .bg-gradient-primary {
            background-image: linear-gradient(to right, var(--primary), var(--primary-dark));
        }

        .bg-gradient-primary-dark {
            background-image: linear-gradient(to right, var(--primary-dark), var(--primary-darker));
        }

        /* Border Colors */
        .border-primary {
            border-color: var(--primary);
        }

        .border-primary-dark {
            border-color: var(--primary-dark);
        }

        /* Hover States */
        .hover\:bg-primary:hover {
            background-color: var(--primary);
        }

        .hover\:bg-primary-dark:hover {
            background-color: var(--primary-dark);
        }

        .hover\:text-primary:hover {
            color: var(--primary);
        }

        /* Focus States */
        .focus\:ring-primary:focus {
            --tw-ring-color: var(--primary);
        }

        .homax-hero {
            background-size: cover;
            background-position: center right;
            font-family: "DM Sans", sans-serif;
        }

        .homax-hero-title {
            font-size: 36px;
            line-height: 1.1;
            font-family: "Aboreto", cursive;
            font-weight: 400;
            letter-spacing: 0;
            color: #111827;
            max-width: 760px;
        }

        .homax-hero-title-line {
            display: block;
            white-space: nowrap;
        }

        .homax-script-accent {
            font-family: "Segoe Script", "Brush Script MT", "Lucida Handwriting", cursive;
            font-size: 42px;
            line-height: 0.95;
            font-weight: 400;
            letter-spacing: 0.02em;
            color: #5146C7;
            opacity: 0.78;
            transform: rotate(-8deg);
            text-shadow: 0 1px 10px rgba(255, 255, 255, 0.35);
        }

        .homax-script-accent span {
            display: block;
            padding-left: 24px;
            margin-top: -2px;
        }

        .homax-script-accent::after {
            content: "";
            display: block;
            width: 72px;
            height: 2px;
            margin: 9px 0 0 94px;
            background: #5146C7;
            opacity: 0.75;
            transform: rotate(-5deg);
        }

        .homax-project-marquee {
            overflow-x: auto;
            overflow-y: hidden;
            width: 100%;
            scroll-snap-type: x proximity;
            -webkit-overflow-scrolling: touch;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .homax-project-marquee::-webkit-scrollbar {
            display: none;
        }

        .homax-project-track {
            display: flex;
            gap: 24px;
            width: max-content;
            transform: translate3d(0, 0, 0);
        }

        .homax-project-marquee.is-auto {
            overflow: hidden;
        }

        .homax-project-marquee.is-auto .homax-project-track {
            animation: homaxProjectScroll 46s linear infinite;
        }

        /* Stop compositing the marquee while it is scrolled out of view. */
        .homax-project-marquee.is-paused .homax-project-track {
            animation-play-state: paused;
        }

        @media (prefers-reduced-motion: reduce) {
            .homax-project-marquee.is-auto .homax-project-track {
                animation: none;
            }
        }

        /* On phones/tablets the auto-marquee becomes a normal swipeable carousel.
           With `overflow: hidden` it could not be browsed by touch at all — the
           cards just drifted past and there was no way to reach them. */
        @media (max-width: 1023px), (hover: none) and (pointer: coarse) {
            .homax-project-marquee.is-auto {
                overflow-x: auto;
                overflow-y: hidden;
                scroll-snap-type: x proximity;
                -webkit-overflow-scrolling: touch;
            }

            .homax-project-marquee.is-auto .homax-project-track {
                animation: none;
            }

            /* The duplicated half exists only to make the desktop loop seamless;
               when swiping it just shows every property twice. */
            .marquee-clone {
                display: none !important;
            }

            .homax-project-track {
                gap: 16px;
            }

            .homax-project-type-card {
                width: min(84vw, 320px);
            }
        }

        /* Replaces the per-scroll-tick inline style writes in the rail script. */
        .scroll-left-btn.is-disabled,
        .scroll-right-btn.is-disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .homax-project-track>* {
            flex: 0 0 auto;
            scroll-snap-align: start;
        }

        .homax-project-type-card {
            width: min(78vw, 330px);
            flex: 0 0 auto;
        }

        @keyframes homaxProjectScroll {
            from {
                transform: translate3d(0, 0, 0);
            }

            to {
                transform: translate3d(calc(-50% - 12px), 0, 0);
            }
        }

        /* ---- Geometric section background (grid + corner arc) -------------------
           Pure CSS, no image requests. Sits behind content via ::before/::after,
           pointer-events:none so it never intercepts clicks. The radial mask fades
           the grid out at the edges so it doesn't collide with section borders. */
        .homax-pattern {
            position: relative;
            overflow: hidden;
        }

        .homax-pattern > * {
            position: relative;
            z-index: 2;
        }

        .homax-pattern::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 1;
            pointer-events: none;
            background-image:
                linear-gradient(rgba(81, 70, 199, 0.075) 1px, transparent 1px),
                linear-gradient(90deg, rgba(81, 70, 199, 0.075) 1px, transparent 1px);
            background-size: 40px 40px;
            -webkit-mask-image: radial-gradient(circle at 50% 50%, #000 55%, transparent 100%);
            mask-image: radial-gradient(circle at 50% 50%, #000 55%, transparent 100%);
        }

        .homax-pattern::after {
            content: "";
            position: absolute;
            z-index: 1;
            pointer-events: none;
            right: -90px;
            top: -90px;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            border: 26px solid rgba(81, 70, 199, 0.10);
        }

        /* Alternate placement so consecutive sections don't look copy-pasted. */
        .homax-pattern--left::after {
            right: auto;
            top: auto;
            left: -110px;
            bottom: -110px;
            width: 320px;
            height: 320px;
            border-width: 30px;
        }

        @media (max-width: 767px) {
            .homax-pattern::before {
                background-size: 28px 28px;
            }

            .homax-pattern::after {
                width: 190px;
                height: 190px;
                border-width: 18px;
                right: -70px;
                top: -70px;
            }

            .homax-pattern--left::after {
                left: -80px;
                bottom: -80px;
                right: auto;
                top: auto;
                width: 200px;
                height: 200px;
            }
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .property-scroll-container {
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
        }

        .property-card {
            backface-visibility: hidden;
            contain: layout paint;
        }

        .property-card img {
            backface-visibility: hidden;
            transform: translate3d(0, 0, 0);
        }

        @media (min-width: 768px) {
            .homax-hero-title {
                font-size: 46px;
                line-height: 1.05;
            }
        }

        @media (min-width: 1024px) {
            .homax-hero-title {
                font-size: clamp(50px, 3.75vw, 58px);
            }
        }

        @media (min-width: 1280px) {
            .homax-script-accent {
                font-size: 46px;
            }
        }

        @media (max-width: 767px) {
            .homax-hero {
                min-height: 560px;
                background-position: 68% center;
            }

            .homax-hero .homax-hero-content {
                min-height: 560px;
                width: 100%;
                padding-top: 56px;
                padding-bottom: 72px;
            }

            .homax-hero-title {
                max-width: min(100%, 360px);
                font-size: clamp(32px, 10vw, 38px);
                line-height: 1.12;
            }

            .homax-hero-title-line {
                white-space: normal;
            }

            .homax-hero-title-line:first-child {
                white-space: nowrap;
            }

            .homax-hero p {
                max-width: 330px;
            }

            .homax-hero .hero-actions {
                width: 100%;
                max-width: 330px;
                gap: 12px;
            }

            .homax-hero .hero-actions > * {
                flex: 1 1 100%;
                text-align: center;
            }

            .homax-hero .scroll-indicator {
                display: none;
            }
        }

        @media (max-width: 420px) {
            .homax-hero {
                min-height: 590px;
                background-position: 72% center;
            }

            .homax-hero .homax-hero-content {
                min-height: 590px;
            }

            .homax-hero-title {
                max-width: 310px;
                font-size: 32px;
            }
        }
    </style>

    <body class="bg-white text-gray-700 font-sans overflow-x-hidden">

        <!-- Hero Section -->
        <section class="homax-hero relative min-h-[540px] md:min-h-[500px] lg:min-h-[560px] z-0"
            style="background-image: url('{{ asset('assets/hero-section.webp') }}')">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-gradient-to-r from-white/90 via-white/68 to-white/10"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/10 via-transparent to-transparent"></div>

            <!-- Content -->
            <div class="relative z-10 max-w-[1240px] mx-auto px-5 sm:px-8 lg:px-10">
                <div
                    class="homax-hero-content flex flex-col items-start justify-center min-h-[540px] md:min-h-[500px] lg:min-h-[560px] text-left w-full lg:w-[55%] py-16">
                    <p class="text-[12px] font-medium uppercase text-[#5F6875] mb-3" style="letter-spacing: 4px;">
                        HOMES FOR A BRIGHTER TOMORROW
                    </p>
                    <div class="w-14 h-px bg-[#5146C7] mb-6"></div>
                    <h1 class="homax-hero-title">
                        <span class="homax-hero-title-line">Find a Home</span>
                        <span class="homax-hero-title-line">You'll Be <span class="text-[#5146C7]">Proud Of</span></span>
                    </h1>

                    <p class="mt-5 mb-8 text-[15px] md:text-[17px] leading-[1.5] md:leading-[1.6] font-normal max-w-[560px] text-[#5F6472]">
                        Explore thoughtfully planned homes and real estate projects in prime locations. Better spaces. A
                        brighter future.
                    </p>

                    <!-- Action Buttons -->
                    <div class="hero-actions flex flex-wrap gap-4 justify-start">
                        <button
                            class="bg-[#5146C7] hover:bg-[#4038A8] text-white text-[14px] font-semibold px-[25px] py-[14px] rounded-md transition-colors duration-300 shadow-sm">
                            Explore Projects &rarr;
                        </button>
                        <a href="/contact"
                            class="bg-white/90 hover:bg-white text-[#111827] border border-[#5146C7] text-[14px] font-semibold px-[25px] py-[14px] rounded-md transition-colors duration-300 shadow-sm">
                            Contact Us &rarr;
                        </a>
                    </div>
                </div>
            </div>

            <!-- <div class="text-white hidden xl:block absolute left-[72%] top-[54%] z-10 pointer-events-none">
                Spaces
                <span>for a Better Life</span>
            </div> -->

            <!-- Scroll Down Indicator -->
            <div class="scroll-indicator absolute bottom-10 left-1/2 transform -translate-x-1/2 animate-bounce">
                <a href="#featured-properties" class="text-white hover:text-[#5146C7] transition-colors duration-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                </a>
            </div>
        </section>

        <!-- Search Bar -->
        <div class="bg-[#F7F6FF] py-6 lg:-mt-8 relative z-10">
            <div class="max-w-[1240px] mx-auto px-4 sm:px-6 lg:px-8">
                <form action="{{ route('property.search') }}" method="GET"
                    class="bg-white rounded-xl p-6 w-full mx-auto grid gap-4 grid-cols-1 md:grid-cols-5 border border-[#E7E7F0] transition-shadow duration-300"
                    style="box-shadow: 0 10px 35px rgba(0,0,0,0.10);">

                    <select name="property_type"
                        class="border border-[#E7E7F0] bg-white text-[#5F6472] px-4 py-3 rounded-md w-full md:col-span-1 focus:outline-none focus:ring-2 focus:ring-[#5146C7]">
                        <option class="text-gray-800" value="">Project Type</option>
                        <option class="text-gray-800" value="Residential Flat"
                            {{ request('property_type') == 'Residential Flat' ? 'selected' : '' }}>Residential Flat</option>
                        <option class="text-gray-800" value="Residential Plot"
                            {{ request('property_type') == 'Residential Plot' ? 'selected' : '' }}>Residential Plot</option>
                        <option class="text-gray-800" value="Commercial"
                            {{ request('property_type') == 'Commercial' ? 'selected' : '' }}>
                            Commercial</option>
                        {{-- <option class="text-gray-800" value="Villa"
                            {{ request('property_type') == 'Villa' ? 'selected' : '' }}>Villa</option> --}}
                        <option class="text-gray-800" value="Apartment"
                            {{ request('property_type') == 'Apartment' ? 'selected' : '' }}>Apartment
                        </option>
                        {{-- <option class="text-gray-800" value="Penthouse"
                            {{ request('property_type') == 'Penthouse' ? 'selected' : '' }}>Penthouse
                        </option> --}}
                        <option class="text-gray-800" value="House"
                            {{ request('property_type') == 'House' ? 'selected' : '' }}>House</option>
                        {{-- <option class="text-gray-800" value="Condo"
                            {{ request('property_type') == 'Condo' ? 'selected' : '' }}>Condo</option> --}}
                        {{-- <option class="text-gray-800" value="Townhouse"
                            {{ request('property_type') == 'Townhouse' ? 'selected' : '' }}>Townhouse
                        </option> --}}
                    </select>

                    <input type="text" name="search" placeholder="Search by project name, locality, city"
                        value="{{ request('search') }}"
                        class="border border-[#E7E7F0] bg-white text-[#5F6472] placeholder-[#5F6472] px-4 py-3 rounded-md w-full md:col-span-2 focus:outline-none focus:ring-2 focus:ring-[#5146C7]" />

                    <select name="listing_type"
                        class="border border-[#E7E7F0] bg-white text-[#5F6472] px-4 py-3 rounded-md w-full md:col-span-1 focus:outline-none focus:ring-2 focus:ring-[#5146C7]">
                        <option class="text-gray-800" value="">Availability</option>
                        <option class="text-gray-800" value="For Sale"
                            {{ request('listing_type') == 'For Sale' ? 'selected' : '' }}>For Sale
                        </option>
                        <option class="text-gray-800" value="For Resale"
                            {{ request('listing_type') == 'For Resale' ? 'selected' : '' }}>For
                            Resale</option>
                        {{-- <option class="text-gray-800" value="For Rent"
                            {{ request('listing_type') == 'For Rent' ? 'selected' : '' }}>For Rent
                        </option> --}}
                        {{-- <option class="text-gray-800" value="Lease"
                            {{ request('listing_type') == 'Lease' ? 'selected' : '' }}>Lease</option> --}}
                    </select>
                    <!-- Add this hidden input to maintain other search parameters -->
                    @foreach (request()->except('sort') as $key => $value)
                        @if ($value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <button type="submit"
                        class="bg-[#5146C7] hover:bg-[#4038A8] text-white font-semibold px-4 py-3 rounded-md transition-colors duration-300 shadow-md md:col-span-1 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Search Projects
                    </button>
                </form>
            </div>
        </div>


        <!-- Featured Projects -->
        <section id="featured-properties" class="homax-pattern py-16 md:py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Heading -->
                <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between mb-10 md:mb-12">
                    <div>
                        <h2 class="text-3xl md:text-4xl text-[#111827] mb-1">
                            Featured Projects<br>
                            <span>Chosen Just for You</span>
                        </h2>
                        <p class="text-[#5F6472] max-w-2xl text-base md:text-lg mt-4">
                            Explore our selected real estate projects in prime locations.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('property.search') }}"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-[#F7F6FF] text-[#111827] hover:bg-[#5146C7] hover:text-white transition-colors duration-300"
                            aria-label="View all projects">
                            &rarr;
                        </a>
                    </div>
                </div>
            </div>

            <!-- Property Cards -->
            <div class="homax-project-marquee is-auto">
                <div class="homax-project-track px-4 sm:px-6 lg:px-8">
                    @php
                        $featuredSet = collect($featured_properties);
                        $featuredCount = $featuredSet->count();
                    @endphp
                    @foreach ($featuredSet->concat($featuredSet) as $idx => $property)
                        @php $isClone = $idx >= $featuredCount; @endphp
                        <a href="{{ route('property.show', $property->id) }}"
                            @if ($isClone) aria-hidden="true" tabindex="-1" @endif
                            class="property-card group block w-[82vw] sm:w-[330px] xl:w-[340px] flex-none {{ $isClone ? 'marquee-clone' : '' }} bg-white border border-[#E7E7F0] rounded-[18px] overflow-hidden shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_14px_34px_rgba(17,24,39,0.10)] focus:outline-none focus:ring-2 focus:ring-[#5146C7]">
                            <div class="relative h-[270px] sm:h-[285px] xl:h-[300px] overflow-hidden rounded-b-[26px] bg-[#F7F6FF]">
                                @if ($property->main_image)
                                    <img loading="lazy" decoding="async" src="{{ asset($property->main_image) }}" alt="{{ $property->title }}"
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
                                @else
                                    <div class="w-full h-full bg-[#F7F6FF] flex items-center justify-center">
                                        <span class="text-[#687386] text-sm">No Image Available</span>
                                    </div>
                                @endif

                                <div class="absolute top-3 left-3 flex flex-col space-y-2">
                                    @if ($property->is_featured)
                                        <span
                                            class="bg-[#5146C7] text-white text-[11px] font-semibold px-3 py-1 rounded-md shadow-sm">
                                            Featured
                                        </span>
                                    @endif
                                    @if ($property->property_status)
                                        <span class="bg-white/95 text-[#5146C7] text-[11px] font-semibold px-3 py-1 rounded-md shadow-sm">
                                            {{ $property->property_status }}
                                        </span>
                                    @endif
                                </div>

                                <div class="absolute top-3 right-3 bg-white/95 backdrop-blur-sm rounded-full p-2 shadow-sm">
                                    <svg class="w-5 h-5 text-[#5146C7]" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>

                            <div class="px-5 pb-6">
                                <div class="relative -mt-7 mb-5 w-fit rounded-r-2xl bg-white px-5 py-3 shadow-sm">
                                    <span class="text-[18px] font-bold text-[#5146C7]">
                                        &#8377;{{ $property->price }}
                                        {{-- @if ($property->price_unit)
                                            <span class="text-sm font-normal">{{ $property->price_unit }}</span>
                                        @endif --}}
                                    </span>
                                </div>

                                <div class="flex justify-between items-start gap-3 mb-2">
                                    <h3 class="text-[16px] leading-snug font-bold text-[#111827]"
                                        style="font-family: 'Inter', 'DM Sans', sans-serif;">{{ $property->title }}</h3>
                                    @if ($property->is_verified)
                                        <span
                                            class="shrink-0 bg-[#E9E7FF] text-[#5146C7] text-[11px] font-semibold px-2.5 py-1 rounded-md">Verified</span>
                                    @endif
                                </div>

                                <p class="text-[13px] text-[#687386] mb-5 flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-[#5146C7] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="truncate">{{ $property->city }}, {{ $property->state }}</span>
                                </p>

                                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[12px] text-[#687386]">
                                    @if ($property->bedrooms)
                                        <span class="flex items-center min-w-0">
                                            <svg class="w-4 h-4 mr-1.5 shrink-0 text-[#5146C7]" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                                </path>
                                            </svg>
                                            {{ $property->bedrooms }} BHK
                                        </span>
                                    @endif

                                    @if ($property->super_area)
                                        <span class="flex items-center min-w-0">
                                            <svg class="w-4 h-4 mr-1.5 shrink-0 text-[#5146C7]" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4">
                                                </path>
                                            </svg>
                                            {{ $property->super_area }} sqft
                                        </span>
                                    @endif

                                    @if ($property->year_built)
                                        <span class="flex items-center min-w-0">
                                            <svg class="w-4 h-4 mr-1.5 shrink-0 text-[#5146C7]" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            {{ $property->year_built }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>



        <!-- Projects CTA Section -->
        <section class="py-10 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-[#17113B] rounded-[26px] px-6 py-8 md:px-10 lg:px-12 lg:py-12 overflow-hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-[0.95fr_1.65fr] gap-8 lg:gap-12 items-center">
                        <div>
                            <h2 class="text-white text-3xl md:text-4xl leading-tight mb-5">
                                Find Your Dream<br>
                                Home with Ease<br>
                                Today
                            </h2>
                            <p class="text-white/70 text-sm md:text-base leading-relaxed max-w-sm mb-7">
                                Explore thoughtfully planned homes, compare project details, and make confident decisions.
                            </p>
                            <a href="{{ route('property.search') }}"
                                class="inline-flex items-center bg-[#5146C7] hover:bg-[#4038A8] text-white text-sm font-semibold px-6 py-3 rounded-full transition-colors duration-300">
                                See All Properties
                                <span class="ml-2">&rarr;</span>
                            </a>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            @foreach (collect($featured_properties)->take(3) as $property)
                                <div class="h-32 md:h-40 lg:h-44 rounded-2xl overflow-hidden bg-[#25204F]">
                                    @if ($property->main_image)
                                        <img loading="lazy" decoding="async" src="{{ asset($property->main_image) }}" alt="{{ $property->title }}"
                                            class="w-full h-full object-cover" />
                                    @else
                                        <img loading="lazy" decoding="async" src="{{ asset('assets/hero-section.webp') }}" alt="Homax Homes project"
                                            class="w-full h-full object-cover" />
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>



        <!-- Project Types -->
        <section class="homax-pattern homax-pattern--left py-20 bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-12">
                <!-- Section Heading -->
                <div class="max-w-3xl">
                    <h2 class="text-4xl md:text-5xl text-[#111827] mb-4">
                        Explore Our Projects
                    </h2>
                    <p class="text-[#5F6472] text-lg">
                        Explore different types of real estate projects and home options designed around modern living
                        needs.
                    </p>
                </div>
            </div>

            @php
                $projectTypes = [
                    [
                        'title' => 'Apartments',
                        'image' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=800&q=75',
                        'copy' => 'Modern apartment projects planned for convenient, comfortable everyday living.',
                    ],
                    [
                        'title' => 'Villas',
                        'image' => 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?auto=format&fit=crop&w=800&q=75',
                        'copy' => 'Villa-style homes and low-density living options with a focus on privacy and comfort.',
                    ],
                    [
                        'title' => 'Residential Plot',
                        'image' => 'https://images.unsplash.com/photo-1605276374104-dee2a0ed3cd6?auto=format&fit=crop&w=800&q=75',
                        'copy' => 'Residential plot options for buyers planning a home around their own requirements.',
                    ],
                    [
                        'title' => 'Commercial',
                        'image' => 'https://images.unsplash.com/photo-1605146769289-440113cc3d00?auto=format&fit=crop&w=800&q=75',
                        'copy' => 'Commercial project options suited for offices, retail, and business use.',
                    ],
                ];
            @endphp

            <div class="homax-project-marquee">
                <div class="homax-project-track px-4">
                    @foreach (array_merge($projectTypes, $projectTypes) as $ptIdx => $type)
                        @php $isClone = $ptIdx >= count($projectTypes); @endphp
                        <div
                            @if ($isClone) aria-hidden="true" @endif
                            class="homax-project-type-card group relative h-[330px] md:h-[360px] rounded-[18px] overflow-hidden bg-[#17113B] shadow-sm {{ $isClone ? 'marquee-clone' : '' }}">
                            <img loading="lazy" decoding="async" src="{{ $type['image'] }}" alt="{{ $type['title'] }}"
                                class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
                            <div class="absolute inset-0 bg-gradient-to-t from-[#17113B]/88 via-[#17113B]/35 to-transparent"></div>
                            <div class="absolute top-4 right-4 flex h-10 w-10 items-center justify-center rounded-full bg-white/95 text-[#5146C7] shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3">
                                    </path>
                                </svg>
                            </div>
                            <div class="absolute inset-x-0 bottom-0 p-6">
                                <h3 class="text-2xl font-bold text-white mb-3">{{ $type['title'] }}</h3>
                                <p class="text-white/75 text-sm leading-relaxed mb-5">{{ $type['copy'] }}</p>
                                <span
                                    class="inline-flex items-center rounded-full bg-white px-4 py-2 text-xs font-semibold text-[#111827]">
                                    View Projects
                                    <span class="ml-2 flex h-6 w-6 items-center justify-center rounded-full bg-[#5146C7] text-white">&rarr;</span>
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>



        <!-- Stats Section -->
        <section class="relative overflow-hidden bg-[#F7F6FF] lg:min-h-[560px]">
            <div aria-hidden="true" class="pointer-events-none absolute -right-28 -top-20 h-80 w-80 rounded-full bg-[#E9E7FF]/75"></div>
            <div class="absolute bottom-[-170px] right-[18%] h-[360px] w-[520px] rounded-[50%] bg-[#E9E7FF]/55"></div>

            <div class="relative lg:absolute lg:inset-y-0 lg:left-0 lg:w-[50%] min-h-[300px] md:min-h-[430px] lg:min-h-full overflow-hidden rounded-br-[90px] lg:rounded-r-[42%]">
                <img loading="lazy" decoding="async" src="{{ asset('assets/stat-img.webp') }}" alt="Homax Homes lifestyle"
                    class="absolute inset-0 h-full w-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-transparent to-[#F7F6FF]/20"></div>
            </div>

            <div class="relative z-10 max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 py-14 md:py-18 lg:py-[72px]">
                <div class="lg:ml-[54%] lg:max-w-[620px]">
                    <p class="text-[12px] font-medium uppercase tracking-[4px] text-[#687386] mb-4">
                        BUILDING BRIGHTER FUTURES
                    </p>
                    <div class="w-20 h-0.5 bg-[#5146C7] mb-7"></div>

                    <h2 class="text-[32px] md:text-[38px] lg:text-[48px] leading-[1.12] text-[#111827] mb-6">
                        More Than a Home,<br>
                        It's Where <span class="text-[#5146C7]">Life Happens.</span>
                    </h2>

                    <p class="text-[15px] md:text-[17px] leading-[1.6] text-[#5F6472] max-w-[560px] mb-10">
                        Helping families discover spaces where comfort, connection and better living come together.
                    </p>

                    <div class="grid grid-cols-1 min-[460px]:grid-cols-2 gap-5">
                        <!-- Stat 1 -->
                        <div class="stat-item bg-white border border-[#E7E7F0] rounded-[16px] px-5 py-5 min-h-[104px] shadow-[0_10px_28px_rgba(17,24,39,0.07)]">
                            <div class="flex items-center gap-5">
                                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-[#E9E7FF] text-[#5146C7]">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h4m4 0h4a1 1 0 001-1V10m-9 11v-6h4v6">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-[30px] md:text-[34px] font-bold leading-none text-[#5146C7]">1,250+</div>
                                    <div class="mt-2 text-[15px] font-semibold text-[#263548]">Project Options</div>
                                </div>
                            </div>
                        </div>

                        <!-- Stat 2 -->
                        <div class="stat-item bg-white border border-[#E7E7F0] rounded-[16px] px-5 py-5 min-h-[104px] shadow-[0_10px_28px_rgba(17,24,39,0.07)]">
                            <div class="flex items-center gap-5">
                                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-[#E9E7FF] text-[#5146C7]">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m8-4a3 3 0 100-6 3 3 0 000 6zM9 10a3 3 0 100-6 3 3 0 000 6z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-[30px] md:text-[34px] font-bold leading-none text-[#5146C7]">950+</div>
                                    <div class="mt-2 text-[15px] font-semibold text-[#263548]">Customer Enquiries</div>
                                </div>
                            </div>
                        </div>

                        <!-- Stat 3 -->
                        <div class="stat-item bg-white border border-[#E7E7F0] rounded-[16px] px-5 py-5 min-h-[104px] shadow-[0_10px_28px_rgba(17,24,39,0.07)]">
                            <div class="flex items-center gap-5">
                                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-[#E9E7FF] text-[#5146C7]">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 21s7-4.5 7-11a7 7 0 10-14 0c0 6.5 7 11 7 11z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10.5h.01"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-[30px] md:text-[34px] font-bold leading-none text-[#5146C7]">15+</div>
                                    <div class="mt-2 text-[15px] font-semibold text-[#263548]">Market Presence</div>
                                </div>
                            </div>
                        </div>

                        <!-- Stat 4 -->
                        <div class="stat-item bg-white border border-[#E7E7F0] rounded-[16px] px-5 py-5 min-h-[104px] shadow-[0_10px_28px_rgba(17,24,39,0.07)]">
                            <div class="flex items-center gap-5">
                                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-[#E9E7FF] text-[#5146C7]">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18 10a6 6 0 10-12 0v4a2 2 0 002 2h1v-5H7v-1a5 5 0 0110 0v1h-2v5h1a2 2 0 002-2v-4z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-[30px] md:text-[34px] font-bold leading-none text-[#5146C7]">15+</div>
                                    <div class="mt-2 text-[15px] font-semibold text-[#263548]">Support Team</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>



        <!-- Scrollable Project List -->
        <section class="py-20 bg-gradient-to-br from-white via-[#F7F6FF] to-white overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-[0.9fr_1.7fr] gap-8 lg:gap-10 items-center">
                    <div>
                        <p class="text-xs font-bold tracking-[0.24em] uppercase text-[#5146C7] mb-4">New Arrivals</p>
                        <h2 class="text-4xl md:text-5xl leading-tight text-[#111827] mb-6">
                            Latest Projects<br>
                            Ready to<br>
                            Explore.
                        </h2>
                        <p class="text-[#5F6472] text-sm md:text-base leading-relaxed max-w-sm mb-7">
                            Swipe through recently added projects with updated homes, locations, and key details in one
                            quick view.
                        </p>
                        <div class="flex items-center gap-3">
                            <button
                                class="scroll-left-btn inline-flex h-10 w-10 items-center justify-center rounded-full bg-white text-[#111827] shadow-sm hover:bg-[#5146C7] hover:text-white transition-colors duration-300"
                                aria-label="Previous latest projects">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                            <button
                                class="scroll-right-btn inline-flex h-10 w-10 items-center justify-center rounded-full bg-[#5146C7] text-white shadow-sm hover:bg-[#4038A8] transition-colors duration-300"
                                aria-label="Next latest projects">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="relative min-w-0">
                        <div class="property-scroll-container overflow-x-auto pb-8 -mx-4 px-4 scrollbar-hide snap-x snap-proximity">
                            <div class="property-scroll-wrapper flex gap-6" style="min-width: max-content;">
                                @foreach ($newlisted_properties as $property)
                                    <a href="{{ route('property.show', $property->id) }}"
                                        class="property-card group relative flex-shrink-0 w-[82vw] sm:w-[330px] md:w-[350px] h-[450px] snap-start rounded-[22px] overflow-hidden bg-[#17113B] shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_18px_42px_rgba(17,24,39,0.18)] focus:outline-none focus:ring-2 focus:ring-[#5146C7]">
                                        @if ($property->main_image)
                                            <img loading="lazy" decoding="async" src="{{ asset($property->main_image) }}" alt="{{ $property->title }}"
                                                class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                        @else
                                            <div class="absolute inset-0 bg-[#E9E7FF] flex items-center justify-center">
                                                <span class="text-[#687386] text-sm">No Image Available</span>
                                            </div>
                                        @endif

                                        <div class="absolute inset-0 bg-gradient-to-t from-[#17113B]/96 via-[#17113B]/35 to-transparent"></div>

                                        <div class="absolute top-4 left-4 flex flex-col gap-2">
                                            <span
                                                class="bg-white text-[#5146C7] text-[11px] font-bold uppercase tracking-[0.12em] px-3 py-1 rounded-md shadow-sm">
                                                New Arrival
                                            </span>
                                            @if ($property->is_featured)
                                                <span
                                                    class="bg-[#5146C7] text-white text-[11px] font-semibold px-3 py-1 rounded-md shadow-sm">
                                                    Featured
                                                </span>
                                            @endif
                                            @if ($property->property_status)
                                                <span
                                                    class="bg-white/95 text-[#5146C7] text-[11px] font-semibold px-3 py-1 rounded-md shadow-sm">
                                                    {{ $property->property_status }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="absolute top-4 right-4 flex h-10 w-10 items-center justify-center rounded-full bg-white/95 text-[#5146C7] shadow-sm">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </div>

                                        <div class="absolute inset-x-0 bottom-0 p-6">
                                            <div class="mb-4 inline-flex rounded-2xl bg-white px-5 py-3 shadow-sm">
                                                <span class="text-[17px] font-bold text-[#5146C7]">
                                                    &#8377;{{ $property->price }}
                                                </span>
                                            </div>
                                            <h3 class="text-2xl font-bold text-white mb-2"
                                                style="font-family: 'Inter', 'DM Sans', sans-serif;">{{ $property->title }}</h3>
                                            <p class="text-white/80 text-sm mb-4 flex items-center">
                                                <svg class="w-4 h-4 mr-1.5 shrink-0 text-white/80" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                    </path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                <span class="truncate">{{ $property->city }}, {{ $property->state }}</span>
                                            </p>
                                            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-white/75 mb-5">
                                                @if ($property->bedrooms)
                                                    <span>{{ $property->bedrooms }} BHK</span>
                                                @endif
                                                @if ($property->super_area)
                                                    <span>{{ $property->super_area }} sqft</span>
                                                @endif
                                            </div>
                                            <span class="inline-flex items-center text-sm font-semibold text-white">
                                                Tap to view
                                                <span class="ml-2 flex h-7 w-7 items-center justify-center rounded-full bg-[#5146C7] text-white">&rarr;</span>
                                            </span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- How It Works -->
        <section class="py-20 bg-gradient-to-br from-gray-50 to-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                    Your Journey to a <span class="text-primary">New Home</span>
                </h2>
                <div class="mx-auto w-24 h-1.5 bg-gradient-to-r from-primary to-primary-dark rounded-full mb-8"></div>
                <div class="max-w-3xl mx-auto">
                    <p class="text-lg text-gray-600 mb-16">
                        Move from project discovery to a confident home decision with a simple, guided process designed
                        around clear information and practical support.
                    </p>
                </div>

                <div class="relative">
                    <!-- Progress line -->
                    <div
                        class="hidden md:block absolute top-16 left-1/2 transform -translate-x-1/2 h-1.5 bg-gray-200 w-3/4 rounded-full overflow-hidden">
                        <div class="progress-line absolute top-0 left-0 h-full bg-gradient-to-r from-primary to-primary-dark rounded-full"
                            style="width: 0%"></div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12 relative z-10">
                        <!-- Step 1 -->
                        <div class="step-item group">
                            <div class="relative">
                                <div
                                    class="absolute -inset-2 bg-primary/10 rounded-full opacity-0 group-hover:opacity-100 blur-md transition-all duration-300">
                                </div>
                                <div
                                    class="relative bg-white p-6 rounded-full shadow-lg transition-all duration-500 group-hover:-translate-y-2 group-hover:shadow-xl">
                                    <div
                                        class="w-16 h-16 mx-auto flex items-center justify-center bg-primary/10 rounded-full text-primary mb-6">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div
                                    class="step-number absolute top-0 left-0 md:right-0 bg-primary text-white font-bold rounded-full w-8 h-8 flex items-center justify-center shadow-md transform translate-x-1/2 -translate-y-1/2">
                                    1
                                </div>
                            </div>
                            <h3
                                class="text-xl font-bold text-gray-800 mt-6 mb-3 group-hover:text-primary transition-colors duration-300">
                                Explore Projects</h3>
                            <p class="text-gray-600">
                                Browse available projects based on your location, budget, and living preferences.
                            </p>
                        </div>

                        <!-- Step 2 -->
                        <div class="step-item group">
                            <div class="relative">
                                <div
                                    class="absolute -inset-2 bg-primary/10 rounded-full opacity-0 group-hover:opacity-100 blur-md transition-all duration-300">
                                </div>
                                <div
                                    class="relative bg-white p-6 rounded-full shadow-lg transition-all duration-500 group-hover:-translate-y-2 group-hover:shadow-xl">
                                    <div
                                        class="w-16 h-16 mx-auto flex items-center justify-center bg-primary/10 rounded-full text-primary mb-6">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <!-- User icon -->
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 14c4.418 0 8 1.79 8 4v2H4v-2c0-2.21 3.582-4 8-4z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 12a4 4 0 100-8 4 4 0 000 8z" />
                                            <!-- Speech bubble -->
                                        </svg>
                                    </div>
                                </div>
                                <div
                                    class="step-number absolute top-0 left-0 md:right-0 bg-primary text-white font-bold rounded-full w-8 h-8 flex items-center justify-center shadow-md transform translate-x-1/2 -translate-y-1/2">
                                    2
                                </div>
                            </div>
                            <h3
                                class="text-xl font-bold text-gray-800 mt-6 mb-3 group-hover:text-primary transition-colors duration-300">
                                <Main></Main>Get Project Details
                            </h3>
                            <p class="text-gray-600">
                                Review project information and connect with our team for the details you need.
                            </p>
                        </div>

                        <!-- Step 3 -->
                        <div class="step-item group">
                            <div class="relative">
                                <div
                                    class="absolute -inset-2 bg-primary/10 rounded-full opacity-0 group-hover:opacity-100 blur-md transition-all duration-300">
                                </div>
                                <div
                                    class="relative bg-white p-6 rounded-full shadow-lg transition-all duration-500 group-hover:-translate-y-2 group-hover:shadow-xl">
                                    <div
                                        class="w-16 h-16 mx-auto flex items-center justify-center bg-primary/10 rounded-full text-primary mb-6">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div
                                    class="step-number absolute top-0 left-0 md:right-0 bg-primary text-white font-bold rounded-full w-8 h-8 flex items-center justify-center shadow-md transform translate-x-1/2 -translate-y-1/2">
                                    3
                                </div>
                            </div>
                            <h3
                                class="text-xl font-bold text-gray-800 mt-6 mb-3 group-hover:text-primary transition-colors duration-300">
                                Schedule a Visit</h3>
                            <p class="text-gray-600">
                                Plan a site visit at a convenient time and experience the project in person.
                            </p>
                        </div>

                        <!-- Step 4 -->
                        <div class="step-item group">
                            <div class="relative">
                                <div
                                    class="absolute -inset-2 bg-primary/10 rounded-full opacity-0 group-hover:opacity-100 blur-md transition-all duration-300">
                                </div>
                                <div
                                    class="relative bg-white p-6 rounded-full shadow-lg transition-all duration-500 group-hover:-translate-y-2 group-hover:shadow-xl">
                                    <div
                                        class="w-16 h-16 mx-auto flex items-center justify-center bg-primary/10 rounded-full text-primary mb-6">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                                <div
                                    class="step-number absolute top-0 left-0 md:right-0 bg-primary text-white font-bold rounded-full w-8 h-8 flex items-center justify-center shadow-md transform translate-x-1/2 -translate-y-1/2">
                                    4
                                </div>
                            </div>
                            <h3
                                class="text-xl font-bold text-gray-800 mt-6 mb-3 group-hover:text-primary transition-colors duration-300">
                                Find Your Home</h3>
                            <p class="text-gray-600">
                                Choose the home that fits your needs and move ahead with clear next steps.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- CTA Button -->
                <div class="mt-16">
                    <a href="{{ route('property.search') }}"
                        class="cta-button relative overflow-hidden bg-primary hover:bg-[#4038A8]   text-white px-8 py-4 rounded-full font-semibold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 group">
                        <span class="relative z-10">Explore Projects</span>
                    </a>
                </div>
            </div>
        </section>
        <!-- About Homax Homes Section -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <!-- Image Column -->
                    <div class="relative">
                        <div class="relative rounded-2xl overflow-hidden shadow-xl">
                            <img loading="lazy" decoding="async" width="800" height="600" src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&h=600&q=75" alt="About Homax Homes"
                                class="w-full h-auto object-cover transition-transform duration-700 hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-primary/30 to-transparent"></div>
                        </div>

                        <!-- Stats overlay -->
                        <div class="absolute -bottom-8 right-0 lg:-right-8 bg-white rounded-xl shadow-lg p-4 sm:p-6 w-[88%] sm:w-3/4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-primary">1K+</div>
                                    <div class="text-sm text-gray-600">Project Options</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-primary">25+</div>
                                    <div class="text-sm text-gray-600">Locations</div>
                                </div>
                                {{-- <div class="text-center">
              <div class="text-3xl font-bold text-primary">15+</div>
              <div class="text-sm text-gray-600">Years</div>
            </div> --}}
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-primary">98%</div>
                                    <div class="text-sm text-gray-600">Interest</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Column -->
                    <div>
                        <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-6">
                            About <span class="text-primary">Homax Homes</span>
                        </h2>
                        <div class="w-24 h-1.5 bg-gradient-to-r from-primary to-primary-dark rounded-full mb-8"></div>

                        <p class="text-lg text-gray-600 mb-6">
                            Homax Homes is a real estate company focused on thoughtfully planned homes and projects that
                            support better living. Our approach is centered on clear project information, practical
                            guidance, and quality spaces for homebuyers.
                        </p>

                        <div class="space-y-4 mb-8">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mt-1">
                                    <div class="w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <p class="ml-3 text-gray-600">
                                    <span class="font-semibold">Project Information:</span> Clear details to help you
                                    understand each home and project
                                </p>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0 mt-1">
                                    <div class="w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <p class="ml-3 text-gray-600">
                                    <span class="font-semibold">Helpful Guidance:</span> Support for comparing projects,
                                    layouts, and living needs
                                </p>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0 mt-1">
                                    <div class="w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <p class="ml-3 text-gray-600">
                                    <span class="font-semibold">Thoughtful Support:</span> From project discovery to site
                                    visits, our team helps you move forward with clarity
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-4">
                            <button
                                class="bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-full font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg">
                                Learn More
                            </button>
                            <button
                                class="bg-white border-2 border-primary text-primary hover:bg-primary hover:text-white px-6 py-3 rounded-full font-semibold transition-all duration-300 transform hover:scale-105 shadow-md">
                                Explore Projects
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Blog Section -->
        <section id="blog" class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Heading -->
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                        Latest <span class="text-primary">Blog</span> Posts
                    </h2>
                    <div class="mx-auto w-24 h-1 bg-gradient-to-r from-primary to-primary-dark rounded-full mb-6"></div>
                    <p class="text-gray-500 max-w-3xl mx-auto text-lg">
                        Stay updated with real estate insights, home planning ideas, and project-focused guidance.
                    </p>
                </div>

                <!-- Blog Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Blog Post 1 -->
                    <div
                        class="bg-white rounded-xl shadow-md overflow-hidden transition-all duration-500 hover:shadow-xl hover:-translate-y-2">
                        <div class="relative h-60 overflow-hidden">
                            <img loading="lazy" decoding="async" src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=75"
                                alt="Real Estate Trends"
                                class="w-full h-full object-cover transition-transform duration-700 hover:scale-110">
                            <div class="absolute top-4 left-4">
                                <span class="bg-primary text-white text-xs font-semibold px-3 py-1 rounded-full">
                                    Trends
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center text-sm text-gray-500 mb-3">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                June 15, 2023
                            </div>
                            <h3
                                class="text-xl font-bold text-gray-800 mb-3 hover:text-primary transition-colors duration-300">
                                <a href="#">Top 5 Real Estate Trends to Watch in 2023</a>
                            </h3>
                            <p class="text-gray-600 mb-4">
                                Discover the emerging trends that are shaping the real estate market this year and how they
                                might affect your investments.
                            </p>
                            <a href="#"
                                class="text-primary hover:text-primary-dark font-medium flex items-center transition-colors duration-300">
                                Read More
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Blog Post 2 -->
                    <div
                        class="bg-white rounded-xl shadow-md overflow-hidden transition-all duration-500 hover:shadow-xl hover:-translate-y-2">
                        <div class="relative h-60 overflow-hidden">
                            <img loading="lazy" decoding="async" src="https://images.unsplash.com/photo-1605276374104-dee2a0ed3cd6?auto=format&fit=crop&w=800&q=75" alt="Home Buying Tips"
                                class="w-full h-full object-cover transition-transform duration-700 hover:scale-110">
                            <div class="absolute top-4 left-4">
                                <span class="bg-blue-600 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                    Tips
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center text-sm text-gray-500 mb-3">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                May 28, 2023
                            </div>
                            <h3
                                class="text-xl font-bold text-gray-800 mb-3 hover:text-primary transition-colors duration-300">
                                <a href="#">10 Essential Tips for First-Time Home Buyers</a>
                            </h3>
                            <p class="text-gray-600 mb-4">
                                Navigating the home buying process can be overwhelming. Here are 10 crucial tips to help
                                first-time buyers make smart decisions.
                            </p>
                            <a href="#"
                                class="text-primary hover:text-primary-dark font-medium flex items-center transition-colors duration-300">
                                Read More
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Blog Post 3 -->
                    <div
                        class="bg-white rounded-xl shadow-md overflow-hidden transition-all duration-500 hover:shadow-xl hover:-translate-y-2">
                        <div class="relative h-60 overflow-hidden">
                            <img loading="lazy" decoding="async" src="https://images.unsplash.com/photo-1605146769289-440113cc3d00?auto=format&fit=crop&w=800&q=75" alt="Investment Guide"
                                class="w-full h-full object-cover transition-transform duration-700 hover:scale-110">
                            <div class="absolute top-4 left-4">
                                <span class="bg-green-600 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                    Investment
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center text-sm text-gray-500 mb-3">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                April 12, 2023
                            </div>
                            <h3
                                class="text-xl font-bold text-gray-800 mb-3 hover:text-primary transition-colors duration-300">
                                <a href="#">The Ultimate Guide to Real Estate Investment in 2023</a>
                            </h3>
                            <p class="text-gray-600 mb-4">
                                Learn how to maximize your returns with our comprehensive guide to real estate investment
                                strategies for the current market.
                            </p>
                            <a href="#"
                                class="text-primary hover:text-primary-dark font-medium flex items-center transition-colors duration-300">
                                Read More
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- View All Button -->
                <div class="text-center mt-12">
                    <a href="#"
                        class="inline-block bg-primary hover:bg-primary-dark text-white px-8 py-3 rounded-full font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg">
                        View All Blog Posts
                    </a>
                </div>
            </div>
        </section>
        <!-- Featured Developers -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-800">Featured Developers</h2>
                    <div class="mx-auto w-24 h-1 bg-gradient-to-r from-primary to-primary-dark rounded-full my-4"></div>
                    <p class="text-gray-500 max-w-3xl mx-auto">Explore real estate developers and project partners
                    </p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8">
                    <!-- Developer 1 - DLF -->
                    <div
                        class="flex items-center justify-center p-6 bg-gray-50 rounded-xl hover:shadow-md transition-shadow duration-300">
                        <img loading="lazy" decoding="async" src="https://upload.wikimedia.org/wikipedia/commons/a/aa/DLF_logo.svg" alt="DLF"
                            class="h-10 object-contain">
                    </div>

                    <!-- Developer 2 - Godrej Properties -->
                    <div
                        class="flex items-center justify-center p-6 bg-gray-50 rounded-xl hover:shadow-md transition-shadow duration-300">
                        <img loading="lazy" decoding="async" src="https://mma.prnewswire.com/media/1308693/GPL_Logo.jpg?p=facebook"
                            alt="Godrej Properties" class="h-10 object-contain">
                    </div>

                    <!-- Developer 3 - Prestige Group -->
                    <div
                        class="flex items-center justify-center p-6 bg-gray-50 rounded-xl hover:shadow-md transition-shadow duration-300">
                        <img loading="lazy" decoding="async" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRfgG71a-0xes17v2rqjuWV5fsG4JgBiykGnw&s"
                            alt="Prestige Group" class="h-10 object-contain">
                    </div>

                    <!-- Developer 4 - Sobha Limited -->
                    <div
                        class="flex items-center justify-center p-6 bg-gray-50 rounded-xl hover:shadow-md transition-shadow duration-300">
                        <img loading="lazy" decoding="async" src="https://upload.wikimedia.org/wikipedia/en/5/59/Sobha_Ltd_Logo.jpg" alt="Sobha Limited"
                            class="h-10 object-contain">
                    </div>

                    <!-- Developer 5 - Lodha -->
                    <div
                        class="flex items-center justify-center p-6 bg-gray-50 rounded-xl hover:shadow-md transition-shadow duration-300">
                        <img loading="lazy" decoding="async" src="https://upload.wikimedia.org/wikipedia/commons/e/ed/Lodha---New-LOgo.png"
                            alt="Lodha Group" class="h-10 object-contain">
                    </div>

                    <!-- Developer 6 - Brigade Group -->
                    <div
                        class="flex items-center justify-center p-6 bg-gray-50 rounded-xl hover:shadow-md transition-shadow duration-300">
                        <img loading="lazy" decoding="async" src="https://upload.wikimedia.org/wikipedia/en/c/c9/Brigade_Group_Official_Logo.jpeg"
                            alt="Brigade Group" class="h-10 object-contain">
                    </div>
                </div>
            </div>
        </section>







        <!-- Newsletter Section -->
        <!--<section class="py-16 bg-gradient-to-r from-primary to-primary-dark text-white">-->
        <!--  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">-->
        <!--    <h2 class="text-3xl font-bold mb-4">Stay Updated With New Projects</h2>-->
        <!--    <p class="text-lg mb-8 max-w-2xl mx-auto">Subscribe to our newsletter and get the latest project updates directly to your inbox</p>-->

        <!--    <div class="flex flex-col sm:flex-row gap-4 max-w-xl mx-auto">-->
        <!--      <input-->
        <!--        type="email"-->
        <!--        placeholder="Enter your email"-->
        <!--        class="flex-grow px-4 py-3 rounded-md text-gray-800 focus:outline-none focus:ring-2 focus:ring-primary"-->
        <!--      >-->
        <!--      <button class="bg-white text-primary font-semibold px-6 py-3 rounded-md hover:bg-gray-100 transition-colors duration-300">-->
        <!--        Subscribe-->
        <!--      </button>-->
        <!--    </div>-->

        <!--    <p class="text-sm mt-4 opacity-80">We respect your privacy. Unsubscribe at any time.</p>-->
        <!--  </div>-->
        <!--</section>-->

        <!-- Call to Action -->
        <section class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-2">
                        <div class="p-12">
                            <h2 class="text-3xl font-bold text-gray-800 mb-4">Ready to Find Your New Home?</h2>
                            <p class="text-gray-600 mb-8">Explore our projects and get in touch with our team for more
                                details.</p>
                            <div class="flex flex-col sm:flex-row gap-4">
                                <button
                                    class="bg-primary hover:bg-primary-dark text-white px-8 py-3 rounded-full font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg">
                                    Explore Projects
                                </button>
                                <button
                                    class="bg-white border-2 border-primary text-primary hover:bg-primary hover:text-white px-8 py-3 rounded-full font-semibold transition-all duration-300 transform hover:scale-105 shadow-md">
                                    Contact Us
                                </button>
                            </div>
                        </div>
                        <div class="hidden lg:block relative">
                            <img loading="lazy" decoding="async" src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?auto=format&fit=crop&w=800&q=75"
                                alt="Real Estate Agent" class="absolute inset-0 w-full h-full object-cover">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials -->
        <section class="homax-pattern py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                        What Our Clients Say
                    </h2>
                    <div class="mx-auto w-24 h-1 bg-gradient-to-r from-primary to-primary-dark rounded-full mb-6"></div>
                    <p class="text-gray-500 max-w-3xl mx-auto text-lg">
                        A few sample experiences from homebuyers exploring real estate options.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Testimonial 1 -->
                    <div class="bg-gray-50 rounded-xl p-8 shadow-md hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-center mb-6">
                            <img loading="lazy" decoding="async" src="https://randomuser.me/api/portraits/women/43.jpg" alt="Sample homebuyer"
                                class="w-12 h-12 rounded-full mr-4">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-800">Sample Homebuyer</h4>
                                <p class="text-sm text-gray-500">Project Enquiry</p>
                            </div>
                        </div>
                        <div class="text-gray-600 mb-4">
                            "The project information was easy to review, and the team helped me understand the available
                            home options clearly."
                        </div>
                        <div class="flex items-center">
                            <div class="flex text-yellow-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                    </path>
                                </svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                    </path>
                                </svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                    </path>
                                </svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                    </path>
                                </svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                    </path>
                                </svg>
                            </div>
                            <span class="text-sm text-gray-500 ml-2">Sample feedback</span>
                        </div>
                    </div>

                    <!-- Testimonial 2 -->
                    <div class="bg-gray-50 rounded-xl p-8 shadow-md hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-center mb-6">
                            <img loading="lazy" decoding="async" src="https://randomuser.me/api/portraits/men/32.jpg" alt="First-time buyer"
                                class="w-12 h-12 rounded-full mr-4">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-800">First-Time Buyer</h4>
                                <p class="text-sm text-gray-500">Home Search</p>
                            </div>
                        </div>
                        <div class="text-gray-600 mb-4">
                            "As a first-time home buyer, I appreciated having clear details and practical guidance before
                            planning a visit."
                        </div>
                        <div class="flex items-center">
                            <div class="flex text-yellow-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                    </path>
                                </svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                    </path>
                                </svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                    </path>
                                </svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                    </path>
                                </svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                    </path>
                                </svg>
                            </div>
                            <span class="text-sm text-gray-500 ml-2">Sample feedback</span>
                        </div>
                    </div>

                    <!-- Testimonial 3 -->
                    <div class="bg-gray-50 rounded-xl p-8 shadow-md hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-center mb-6">
                            <img loading="lazy" decoding="async" src="https://randomuser.me/api/portraits/women/68.jpg" alt="Project visitor"
                                class="w-12 h-12 rounded-full mr-4">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-800">Project Visitor</h4>
                                <p class="text-sm text-gray-500">Site Visit</p>
                            </div>
                        </div>
                        <div class="text-gray-600 mb-4">
                            "The process helped me compare locations, layouts, and next steps without feeling rushed."
                        </div>
                        <div class="flex items-center">
                            <div class="flex text-yellow-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                    </path>
                                </svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                    </path>
                                </svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                    </path>
                                </svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                    </path>
                                </svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                    </path>
                                </svg>
                            </div>
                            <span class="text-sm text-gray-500 ml-2">Sample feedback</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Scroll Script -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const scrollContainer = document.querySelector('.property-scroll-container');
                const scrollWrapper = document.querySelector('.property-scroll-wrapper');
                const scrollLeftBtn = document.querySelector('.scroll-left-btn');
                const scrollRightBtn = document.querySelector('.scroll-right-btn');

                if (!scrollContainer || !scrollWrapper || !scrollLeftBtn || !scrollRightBtn) return;

                // Cache layout metrics so the scroll handler never forces a synchronous reflow.
                let maxScroll = 0;
                let step = 300;
                const measure = () => {
                    maxScroll = scrollWrapper.scrollWidth - scrollContainer.clientWidth;
                    // Step by one real card (cards are 82vw on phones, 330-350px on desktop)
                    // so the rail lands on a card edge instead of a fixed 300px.
                    const card = scrollWrapper.firstElementChild;
                    if (card) {
                        const gap = parseFloat(getComputedStyle(scrollWrapper).columnGap) || 24;
                        step = card.getBoundingClientRect().width + gap;
                    }
                };

                scrollLeftBtn.addEventListener('click', () => {
                    scrollContainer.scrollBy({ left: -step, behavior: 'smooth' });
                });

                scrollRightBtn.addEventListener('click', () => {
                    scrollContainer.scrollBy({ left: step, behavior: 'smooth' });
                });
                measure();
                if (window.ResizeObserver) {
                    new ResizeObserver(measure).observe(scrollContainer);
                } else {
                    window.addEventListener('resize', measure, { passive: true });
                }

                // Toggle a class instead of writing inline styles on every scroll tick.
                let atStart = null;
                let atEnd = null;
                let ticking = false;

                const update = () => {
                    ticking = false;
                    const nextAtStart = scrollContainer.scrollLeft <= 0;
                    const nextAtEnd = scrollContainer.scrollLeft >= maxScroll - 1;

                    if (nextAtStart !== atStart) {
                        atStart = nextAtStart;
                        scrollLeftBtn.classList.toggle('is-disabled', atStart);
                    }
                    if (nextAtEnd !== atEnd) {
                        atEnd = nextAtEnd;
                        scrollRightBtn.classList.toggle('is-disabled', atEnd);
                    }
                };

                update();

                scrollContainer.addEventListener('scroll', () => {
                    if (ticking) return;
                    ticking = true;
                    requestAnimationFrame(update);
                }, { passive: true });
            });

            // Pause the auto-scrolling marquee whenever it is off-screen or the tab is
            // hidden, so it stops eating compositor frames while you scroll past it.
            document.addEventListener('DOMContentLoaded', function() {
                const marquees = document.querySelectorAll('.homax-project-marquee.is-auto');
                if (!marquees.length || !window.IntersectionObserver) return;

                const observer = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        entry.target.classList.toggle('is-paused', !entry.isIntersecting);
                    });
                }, { rootMargin: '100px' });

                marquees.forEach((el) => {
                    el.classList.add('is-paused');
                    observer.observe(el);
                });

                document.addEventListener('visibilitychange', () => {
                    if (!document.hidden) return;
                    marquees.forEach((el) => el.classList.add('is-paused'));
                });
            });
        </script>
    </body>
@endsection



