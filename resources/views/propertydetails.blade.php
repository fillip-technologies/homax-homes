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
    <style>
        :root {
            /* Primary Colors */
            --primary: #8B6508;
            --primary-dark: #6F5106;
            --primary-darker: #2B2000;

            /* Neutral Colors */
            --gray-dark: #717271;
            --gray-light: #b1b2b1;
            --white: #ffffff;

            /* Additional Colors */
            --teal: #38b2ac;
            /* Keeping teal for some elements as accent */
            --red: #e53e3e;
            --yellow: #f6e05e;
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

        /* Shadows */
        body {
            font-family: "Mulish", sans-serif;
            background-color: #f9fafb;
            /* Changed to gray-50 */
            color: #1f2937;
            /* Changed to gray-800 */
            overflow-x: hidden;
        }

        .hero-section {
            background: linear-gradient(rgba(255, 255, 255, 0.8),
                    rgba(255, 255, 255, 0.8)),
                url("assets/hero-background.jpg");
            /* Keep image but adjust overlay */
            background-size: cover;
            background-position: center;
        }

        .font-display {
            font-family: "Mulish", sans-serif;
        }

        .glassmorphism {
            background: rgba(255,
                    255,
                    255,
                    0.8);
            /* Changed to white with opacity */
            backdrop-filter: blur(10px);
            border: 1px solid rgba(139, 101, 8, 0.1);
            /* Changed border color */
        }

        .animated-element {
            opacity: 1;
            /* Initially hidden for JS reveal or CSS animation */
        }

        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(43, 32, 0, 0.18);
            /* shadow-property-hover */
        }

        .gallery-thumb-new {
            transition: transform 0.3s ease, filter 0.3s ease;
        }

        .gallery-thumb-new:hover {
            transform: scale(1.05);
            filter: brightness(1.1);
        }

        .icon-bg-circle {
            background-color: rgba(139, 101, 8, 0.10);
            /* primary with alpha */
            transition: background-color 0.3s ease;
        }

        .icon-bg-circle:hover {
            background-color: rgba(139, 101, 8, 0.18);
        }

        .btn-primary {
            background-color: #8B6508;
            /* Changed to brand accent */
            color: #ffffff;
            /* Changed to white */
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #6F5106;
            /* Changed to brand hover */
            box-shadow: 0 0 15px rgba(43, 32, 0, 0.28);
            /* Changed to brand accent with opacity */
        }

        .btn-secondary {
            background-color: transparent;
            border: 1px solid #8B6508;
            /* Changed to brand accent */
            color: #8B6508;
            /* Changed to brand accent */
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: rgba(13,
                    148,
                    136,
                    0.1);
            /* Changed to brand accent with opacity */
            color: #6F5106;
            /* Changed to brand hover */
        }
        /* ==================== PREMIUM PROJECT HERO ==================== */
        .hx-hero {
            --hx-indigo: #8B6508;
            --hx-indigo-dark: #6F5106;
            --hx-navy: #2B2000;
            --hx-ink: #111827;
            --hx-muted: #5F6472;
            --hx-lav: #FDF9F0;
            --hx-lav2: #F5EEDC;
            --hx-line: #EFE8D8;

            position: relative;
            isolation: isolate;
            /* Hero typography is a clean sans throughout; the decorative display
               face is reserved for the sections below. */
            --hx-sans: "Mulish", "Inter", system-ui, -apple-system, "Segoe UI", sans-serif;
            font-family: var(--hx-sans);
            background: linear-gradient(180deg, #FEFCF6 0%, #FAF5E9 46%, #F7F0E0 100%);
        }

        .hx-hero__decor {
            position: absolute;
            inset: 0;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
        }

        @media (min-width: 1024px) {
            /* The hero now sits in the shell's main column, but its backdrop
               should still read as one full-width band behind the form too.
               Generous negative offsets do that; .hx-shell trims the overhang. */
            .hx-hero__decor {
                left: -40px;
                right: -460px;
            }
        }

        .hx-hero__wash {
            position: absolute;
            inset: 0;
            z-index: 1;
            /* Keeps dark text legible over the photo without putting a panel
               behind any one column. */
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .66) 0%, rgba(253, 250, 244, .60) 50%, rgba(250, 246, 236, .70) 100%),
                radial-gradient(900px 420px at 12% -8%, rgba(139, 101, 8, .10), transparent 62%),
                radial-gradient(760px 420px at 88% 6%, rgba(218, 165, 32, .14), transparent 64%);
        }

        /* The project's own photo as the hero background: blurred so it reads as
           atmosphere, with the veil above it carrying text contrast. */
        .hx-hero__photo {
            position: absolute;
            inset: 0;
            z-index: 0;
            background-size: cover;
            background-position: center;
            filter: blur(30px) saturate(122%);
            opacity: .95;
            transform: scale(1.06);
        }

        .hx-hero__glow {
            position: absolute;
            z-index: 1;
            border-radius: 50%;
            filter: blur(8px);
            pointer-events: none;
        }

        .hx-hero__glow--a {
            width: 300px;
            height: 300px;
            left: -140px;
            top: 90px;
            border: 34px solid rgba(139, 101, 8, .06);
        }

        .hx-hero__glow--b {
            width: 240px;
            height: 240px;
            right: -110px;
            bottom: 60px;
            border: 28px solid rgba(139, 101, 8, .05);
        }

        .hx-hero__inner {
            position: relative;
            z-index: 2;
            width: 100%;
            margin: 0 auto;
            /* The shell supplies the horizontal gutter, so the hero only pads
               vertically. Below 1024px the shell collapses and the hero takes
               its own side padding back (see the max-width rules further down). */
            padding: 30px 0 26px;
        }

        .hx-hero__grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 22px;
            align-items: start;
        }

        @media (min-width: 1024px) {
            .hx-hero__grid {
                /* info card | slider. The third column (the enquiry form) now
                   lives in .hx-shell__aside so it can outlast the hero. */
                grid-template-columns: minmax(0, 0.82fr) minmax(0, 1.18fr);
                gap: 22px;
            }
        }

        @media (min-width: 1440px) {
            .hx-hero__grid {
                gap: 26px;
            }
        }

        @media (min-width: 1024px) {
            /* .max-w-7xl / lg:px-8 predate the shell; the shell owns the gutter
               now, so drop the doubled-up padding and let the column decide. */
            .hx-shell__main > .max-w-7xl {
                max-width: none;
                padding-left: 0;
                padding-right: 0;
            }
        }

        /* ============ page shell: main flow + sticky right rail ============
           The rail must be a sibling of the ENTIRE flow (hero + nav + sections).
           When it sat inside .hx-hero__grid its sticky travel ended with the
           hero, which is why the form used to scroll away. */
        .hx-shell {
            position: relative;
            width: 100%;
            max-width: 1560px;
            margin: 0 auto;
            padding: 0 28px;
            /* clip (not hidden) - hidden would turn this into a scroll container
               and silently kill position:sticky on the rail. */
            overflow-x: clip;
        }

        .hx-shell__main {
            min-width: 0;
        }

        .hx-shell__aside {
            min-width: 0;
        }

        @media (min-width: 1024px) {
            .hx-shell {
                display: grid;
                grid-template-columns: minmax(0, 1fr) minmax(292px, 330px);
                gap: 22px;
                /* Deliberately NOT align-items:start - the rail has to stretch to
                   the full height of the main column, because that stretched box
                   is the distance position:sticky is allowed to travel. */
                align-items: stretch;
            }
        }

        @media (min-width: 1440px) {
            .hx-shell {
                grid-template-columns: minmax(0, 1fr) 340px;
                gap: 26px;
            }
        }

        @media (max-width: 1023px) {
            .hx-shell {
                padding: 0;
            }

            /* Stacked: the rail follows the hero instead of sitting beside it. */
            .hx-shell__aside {
                padding: 0 18px 28px;
            }
        }

        @media (max-width: 639px) {
            .hx-shell__aside {
                padding: 0 14px 24px;
            }
        }

        /* ---------- shared card ---------- */
        .hx-card {
            background: rgba(255, 255, 255, .96);
            border: 1px solid var(--hx-line);
            border-radius: 20px;
            box-shadow: 0 16px 40px rgba(17, 24, 39, .08);
        }

        /* ---------- left column ---------- */
        .hx-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 14px;
        }

        .hx-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 13px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            line-height: 1;
            white-space: nowrap;
            background: rgba(255, 255, 255, .85);
            border: 1px solid var(--hx-line);
        }

        .hx-chip--ok {
            color: #1B7F4B;
        }

        .hx-chip--verified {
            color: #6F5106;
        }

        .hx-chip--soft {
            background: var(--hx-lav);
            color: var(--hx-indigo);
        }

        .hx-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #22A45D;
            box-shadow: 0 0 0 3px rgba(34, 164, 93, .16);
        }

        .hx-eyebrow {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: .2em;
            text-transform: uppercase;
            color: var(--hx-muted);
            margin-bottom: 10px;
        }

        .hx-eyebrow__rule {
            width: 30px;
            height: 2px;
            border-radius: 2px;
            background: var(--hx-indigo);
            flex: 0 0 30px;
        }

        .hx-title {
            font-family: "Mulish", var(--hx-sans);
            font-size: clamp(31px, 2.6vw, 44px);
            font-weight: 400;
            line-height: 1.08;
            letter-spacing: .005em;
            color: var(--hx-ink);
            margin: 0;
        }

        .hx-loc {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
            font-size: 15px;
            color: var(--hx-muted);
        }

        .hx-loc i {
            color: var(--hx-indigo);
        }

        .hx-blurb {
            margin-top: 12px;
            font-size: 14.5px;
            line-height: 1.62;
            color: var(--hx-muted);
        }

        /* ---------- price card ---------- */
        .hx-price-card {
            margin-top: 18px;
            padding: 18px;
        }

        .hx-price-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .hx-label {
            display: block;
            font-size: 12.5px;
            font-weight: 500;
            color: var(--hx-muted);
        }

        .hx-price {
            display: block;
            margin-top: 4px;
            font-family: "Mulish", var(--hx-sans);
            font-size: clamp(27px, 2.1vw, 35px);
            font-weight: 400;
            line-height: 1.08;
            letter-spacing: .01em;
            color: var(--hx-indigo);
        }

        .hx-specs {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 8px;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid var(--hx-line);
        }

        .hx-spec {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 1px;
        }

        .hx-spec i {
            color: var(--hx-indigo);
            font-size: 17px;
            margin-bottom: 5px;
        }

        .hx-spec__v {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--hx-ink);
            line-height: 1.25;
        }

        .hx-spec__l {
            font-size: 10.5px;
            color: var(--hx-muted);
        }

        .hx-cta {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-top: 18px;
        }

        .hx-mini-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        /* ---------- buttons + micro-interactions ---------- */
        .hx-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 13px 16px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            line-height: 1;
            white-space: nowrap;
            cursor: pointer;
            border: 1px solid transparent;
            transition: transform .22s cubic-bezier(.2, .7, .3, 1), box-shadow .22s ease,
                background-color .2s ease, color .2s ease, border-color .2s ease;
        }

        .hx-btn__arrow {
            font-size: 12px;
            transition: transform .22s cubic-bezier(.2, .7, .3, 1);
        }

        .hx-btn:hover .hx-btn__arrow {
            transform: translateX(4px);
        }

        .hx-btn--primary {
            position: relative;
            overflow: hidden;
            background: linear-gradient(180deg, #96700A 0%, var(--hx-indigo) 100%);
            color: #fff;
            box-shadow: 0 8px 20px rgba(43, 32, 0, .24);
        }

        /* Continuous shimmer sweep across the primary CTAs. */
        .hx-btn--primary::after {
            content: "";
            position: absolute;
            top: 0;
            bottom: 0;
            left: -60%;
            width: 45%;
            pointer-events: none;
            background: linear-gradient(100deg, transparent 0%, rgba(255, 255, 255, .38) 50%, transparent 100%);
            transform: skewX(-18deg);
            animation: hxShimmer 3.4s ease-in-out infinite;
        }

        .hx-btn--primary>* {
            position: relative;
            z-index: 1;
        }

        @keyframes hxShimmer {
            0% {
                left: -60%;
            }

            55%,
            100% {
                left: 125%;
            }
        }

        .hx-btn--primary:hover {
            background: linear-gradient(180deg, #7A5A07 0%, var(--hx-indigo-dark) 100%);
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(43, 32, 0, .34);
        }

        .hx-btn--secondary {
            background: #fff;
            color: var(--hx-indigo);
            border-color: #E7DDC3;
        }

        .hx-btn--secondary:hover {
            transform: translateY(-2px);
            border-color: var(--hx-indigo);
            box-shadow: 0 10px 22px rgba(43, 32, 0, .16);
        }

        .hx-btn--outline {
            background: #fff;
            color: var(--hx-indigo);
            border-color: var(--hx-line);
        }

        .hx-btn--outline:hover {
            transform: translateY(-2px);
            border-color: var(--hx-indigo);
            box-shadow: 0 10px 20px rgba(43, 32, 0, .14);
        }

        .hx-btn--whatsapp {
            background: #fff;
            color: #128C4A;
            border-color: #CBEBDA;
        }

        .hx-btn--whatsapp:hover {
            transform: translateY(-2px);
            background: #F1FBF5;
            box-shadow: 0 10px 20px rgba(18, 140, 74, .16);
        }

        .hx-btn--mini {
            padding: 9px 13px;
            font-size: 12.5px;
            border-radius: 10px;
            background: var(--hx-lav);
            color: var(--hx-indigo-dark);
            border-color: var(--hx-line);
        }

        .hx-btn--mini:hover {
            transform: translateY(-2px);
            background: var(--hx-lav2);
            box-shadow: 0 8px 16px rgba(43, 32, 0, .14);
        }

        .hx-btn--mini.hx-btn--wa {
            background: #F1FBF5;
            color: #128C4A;
            border-color: #CBEBDA;
        }

        .hx-btn--block {
            width: 100%;
        }

        .hx-btn i {
            transition: transform .22s ease;
        }

        .hx-btn--outline:hover i,
        .hx-btn--whatsapp:hover i,
        .hx-btn--mini:hover i {
            transform: translateY(-1px);
        }

        /* ---------- gallery ---------- */
        .hx-stage {
            position: relative;
            height: clamp(340px, 44vw, 620px);
            border-radius: 20px;
            overflow: hidden;
            background: var(--hx-lav);
            box-shadow: 0 24px 54px rgba(17, 24, 39, .16);
            cursor: zoom-in;
        }

        .hx-nav {
            position: absolute;
            top: 50%;
            z-index: 20;
            width: 46px;
            height: 46px;
            margin-top: -23px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, .94);
            color: var(--hx-ink);
            box-shadow: 0 8px 20px rgba(17, 24, 39, .2);
            transition: transform .2s ease, background-color .2s ease, color .2s ease;
        }

        .hx-nav:hover {
            transform: scale(1.09);
            background: var(--hx-indigo);
            color: #fff;
        }

        .hx-nav--prev {
            left: 16px;
        }

        .hx-nav--next {
            right: 16px;
        }

        .hx-counter {
            position: absolute;
            top: 16px;
            right: 16px;
            z-index: 20;
            padding: 7px 14px;
            border-radius: 999px;
            background: rgba(43, 32, 0, .80);
            color: #fff;
            font-size: 12.5px;
            font-weight: 600;
            backdrop-filter: blur(3px);
        }

        /* thumbnails float inside the bottom of the stage, as in the reference */
        .hx-thumbs {
            position: absolute;
            left: 14px;
            right: 14px;
            bottom: 14px;
            z-index: 20;
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 8px;
        }

        .hx-thumb {
            position: relative;
            height: 78px;
            border-radius: 12px;
            overflow: hidden;
            border: 2px solid rgba(255, 255, 255, .55);
            background: rgba(43, 32, 0, .3);
            transition: transform .2s ease, border-color .2s ease;
        }

        .hx-thumb:hover {
            transform: translateY(-3px);
            border-color: #fff;
        }

        .hx-thumb.is-active {
            border-color: #fff;
        }

        .hx-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .hx-thumb__more {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: rgba(43, 32, 0, .78);
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            line-height: 1.1;
        }

        .hx-thumb__more small {
            font-size: 10px;
            font-weight: 500;
            opacity: .85;
        }

        /* ---------- enquiry card ---------- */
        .hx-enquiry {
            padding: 22px;
        }

        .hx-enquiry__eyebrow {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .2em;
            color: var(--hx-muted);
        }

        .hx-enquiry__eyebrow i {
            display: block;
            width: 36px;
            height: 2px;
            border-radius: 2px;
            background: var(--hx-indigo);
        }

        .hx-enquiry__title {
            font-family: "Mulish", var(--hx-sans);
            font-size: clamp(21px, 1.55vw, 27px);
            font-weight: 400;
            letter-spacing: .005em;
            line-height: 1.2;
            color: var(--hx-ink);
            margin: 9px 0 6px;
        }

        .hx-enquiry__sub {
            font-size: 13.5px;
            line-height: 1.55;
            color: var(--hx-muted);
            margin-bottom: 16px;
        }

        .hx-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .hx-field {
            position: relative;
            display: flex;
            align-items: center;
            border: 1px solid var(--hx-line);
            border-radius: 12px;
            background: #fff;
            transition: border-color .18s ease, box-shadow .18s ease;
        }

        .hx-field:focus-within {
            border-color: var(--hx-indigo);
            box-shadow: 0 0 0 3px rgba(43, 32, 0, .12);
        }

        .hx-field>i {
            width: 44px;
            flex: 0 0 44px;
            text-align: center;
            color: #ADA189;
            font-size: 14px;
        }

        .hx-field input,
        .hx-field textarea {
            width: 100%;
            border: 0;
            outline: none;
            background: transparent;
            padding: 13px 14px 13px 0;
            font-size: 14px;
            font-family: inherit;
            color: var(--hx-ink);
            resize: none;
        }

        .hx-field input::placeholder,
        .hx-field textarea::placeholder {
            color: #ADA189;
        }

        .hx-field--area {
            align-items: flex-start;
        }

        .hx-field--area>i {
            margin-top: 13px;
        }

        .hx-err {
            margin: -4px 0 0;
            font-size: 12px;
            color: #DC2626;
        }

        .hx-terms {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            font-size: 12.5px;
            color: var(--hx-muted);
            cursor: pointer;
        }

        .hx-terms input {
            margin-top: 2px;
            width: 15px;
            height: 15px;
            flex: 0 0 15px;
            accent-color: var(--hx-indigo);
        }

        .hx-terms a {
            color: var(--hx-indigo);
            text-decoration: underline;
        }

        .hx-alert {
            margin-bottom: 14px;
            padding: 11px 14px;
            border-radius: 12px;
            background: #E7F7EE;
            color: #1B7F4B;
            font-size: 13px;
        }

        .hx-or {
            position: relative;
            text-align: center;
            margin: 16px 0 13px;
        }

        .hx-or::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: var(--hx-line);
        }

        .hx-or span {
            position: relative;
            background: #fff;
            padding: 0 12px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .14em;
            color: #ADA189;
        }

        .hx-contact-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .hx-trust {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 6px;
            margin-top: 18px;
            padding-top: 15px;
            border-top: 1px solid var(--hx-line);
        }

        .hx-trust li {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            font-size: 11.5px;
            line-height: 1.35;
            color: var(--hx-muted);
        }

        .hx-trust li i {
            color: var(--hx-indigo);
            font-size: 15px;
            margin-bottom: 5px;
        }

        .hx-trust li b {
            font-weight: 700;
            color: var(--hx-ink);
        }

        /* Anchor targets clear both sticky bars (site header + project nav). */
        #overview,
        #price,
        #amenities,
        #location,
        #gallery,
        #virtual-tour {
            scroll-margin-top: calc(var(--hx-header-h, 68px) + 96px);
        }

        /* ---------- section nav ---------- */
        /* Standalone sticky project nav, styled as a floating capsule so it stays
           obvious while content scrolls underneath it. */
        /* The project's section links live in the site header now - the desktop
           nav slot, plus this scrollable strip below it on small screens. The
           header owns the stickiness, so this container positions nothing. */
        .hx-hdrnav {
            font-family: "Mulish", "Inter", system-ui, -apple-system, "Segoe UI", sans-serif;
            display: flex;
            gap: 4px;
            overflow-x: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
            padding: 6px 12px 8px;
        }

        .hx-hdrnav::-webkit-scrollbar {
            display: none;
        }

        .hx-secnav__item {
            /* Declared on the item, not a wrapper: these now render in two
               different containers (the header's desktop nav and the mobile
               strip), so they cannot inherit from a single parent any more. */
            --hx-indigo: #8B6508;
            --hx-muted: #5F6472;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            flex: 0 0 auto;
            padding: 10px 16px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 600;
            color: var(--hx-muted);
            white-space: nowrap;
            transition: background-color .2s ease, color .2s ease, transform .2s ease;
        }

        .hx-secnav__item:hover {
            transform: translateY(-1px);
        }

        .hx-secnav__item.is-active {
            box-shadow: 0 6px 16px rgba(43, 32, 0, .32);
        }

        .hx-secnav__item:hover,
        .hx-secnav__item.is-active {
            background: var(--hx-indigo);
            color: #fff;
        }

        /* ---------- docked enquiry bar ---------- */
        .hx-dock {
            --hx-indigo: #8B6508;
            --hx-indigo-dark: #6F5106;
            --hx-ink: #111827;
            --hx-muted: #5F6472;
            --hx-line: #EFE8D8;
            --hx-lav: #FDF9F0;
            --hx-lav2: #F5EEDC;
            font-family: "Mulish", "Inter", system-ui, -apple-system, "Segoe UI", sans-serif;

            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 45;
            padding: 10px 16px calc(10px + env(safe-area-inset-bottom, 0px));
            background: rgba(255, 255, 255, .94);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-top: 1px solid var(--hx-line);
            box-shadow: 0 -8px 26px rgba(17, 24, 39, .10);

            transform: translateY(115%);
            opacity: 0;
            visibility: hidden;
            transition: transform .3s cubic-bezier(.2, .7, .3, 1), opacity .25s ease, visibility .3s;
        }

        .hx-dock.is-on {
            transform: translateY(0);
            opacity: 1;
            visibility: visible;
        }

        .hx-dock__inner {
            max-width: 1560px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .hx-dock__meta {
            min-width: 0;
        }

        .hx-dock__meta b {
            display: block;
            font-size: 14.5px;
            font-weight: 700;
            color: var(--hx-ink);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .hx-dock__meta span {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--hx-indigo);
        }

        .hx-dock__actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 0 0 auto;
        }

        .hx-dock__icon {
            padding: 11px 14px;
        }

        @media (prefers-reduced-motion: reduce) {
            .hx-dock {
                transition: none;
            }
        }

        @media (max-width: 639px) {
            .hx-dock__meta {
                display: none;
            }

            .hx-dock__inner {
                gap: 8px;
            }

            .hx-dock__actions {
                width: 100%;
            }

            .hx-dock__icon span {
                display: none;
            }

            .hx-dock__cta {
                flex: 1 1 auto;
            }
        }

        /* ---------- scroll cue ---------- */
        .hx-scroll {
            position: absolute;
            right: 22px;
            bottom: 20px;
            z-index: 3;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 7px;
            color: var(--hx-muted);
            font-size: 11px;
            letter-spacing: .06em;
            opacity: .85;
            transition: opacity .2s ease;
        }

        .hx-scroll:hover {
            opacity: 1;
        }

        .hx-scroll__ring {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            border: 1px solid var(--hx-line);
            color: var(--hx-indigo);
            box-shadow: 0 6px 16px rgba(17, 24, 39, .10);
            animation: hxBob 2.1s ease-in-out infinite;
        }

        @keyframes hxBob {

            0%,
            100% {
                transform: translateY(0);
                opacity: .78;
            }

            50% {
                transform: translateY(5px);
                opacity: 1;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .hx-scroll__ring,
            .hx-btn--primary::after {
                animation: none;
            }

            .hx-btn,
            .hx-btn__arrow,
            .hx-nav,
            .hx-thumb {
                transition: none;
            }
        }

        /* ---------- responsive ---------- */
        @media (max-width: 1279px) {
            .hx-scroll {
                display: none;
            }
        }

        @media (max-width: 1023px) {
            .hx-hero__inner {
                padding: 24px 18px 22px;
            }

            .hx-stage {
                height: clamp(300px, 50vw, 460px);
            }
        }

        @media (max-width: 639px) {
            .hx-hero__inner {
                padding: 20px 14px 20px;
            }

            .hx-secnav__item {
                padding: 9px 13px;
                font-size: 13px;
            }

            .hx-title {
                font-size: 28px;
            }

            .hx-specs {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 12px;
            }

            .hx-cta {
                grid-template-columns: minmax(0, 1fr);
            }

            .hx-stage {
                height: 300px;
            }

            .hx-thumbs {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }

            .hx-thumbs .hx-thumb:nth-child(5) {
                display: none;
            }

            .hx-thumb {
                height: 54px;
            }

            .hx-nav {
                width: 38px;
                height: 38px;
                margin-top: -19px;
            }

            .hx-enquiry,
            .hx-price-card {
                padding: 16px;
            }

            .hx-trust li {
                font-size: 10.5px;
            }
        }
    </style>


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
                $possession = \Carbon\Carbon::parse($property->possession_date)->format('F Y');
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
    <style>
        /* ============ hero: full-bleed slider + overlaid project card ============
           The slider is the hero backdrop rather than a boxed column, so the photo
           runs edge to edge under both the card and the enquiry rail. Every id and
           handler below is the one the existing gallery script already drives. */
        .hx-hero {
            min-height: clamp(540px, 74vh, 780px);
            display: flex;
            /* top, not center: centering inside a 540-780px hero pushed the
               project card down the page while the enquiry form stayed at the
               top of the rail, so the two never lined up. */
            align-items: flex-start;
        }

        /* .hx-hero__decor keeps its desktop breakout (rule further up), so the photo
           already runs under the rail. It only needed to stop being inert now that
           the slider controls live inside it. */
        .hx-hero__decor {
            pointer-events: none;
        }

        .hx-hero__decor .hx-gallery,
        .hx-hero__decor .hx-stage {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            margin: 0;
            border-radius: 0;
            box-shadow: none;
        }

        .hx-hero__decor .hx-gallery {
            pointer-events: auto;
            cursor: zoom-in;
        }

        .hx-slide {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .55s ease-in-out;
        }

        .hx-slide--idle {
            opacity: 0;
        }

        /* Darkens the photo just enough for white controls, and deepens both edges
           so the card and the enquiry rail keep contrast on any image. */
        .hx-hero__scrim {
            position: absolute;
            inset: 0;
            z-index: 2;
            pointer-events: none;
            background:
                linear-gradient(90deg, rgba(8, 11, 32, .58) 0%, rgba(8, 11, 32, .26) 36%, rgba(8, 11, 32, .10) 58%, rgba(8, 11, 32, .40) 100%),
                linear-gradient(180deg, rgba(8, 11, 32, .26) 0%, transparent 28%, rgba(8, 11, 32, .30) 100%);
        }

        /* One control bar in the free band between the card and the rail. */
        .hx-hero__controls {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 18px;
            z-index: 3;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            padding: 0 16px;
            pointer-events: none;
        }

        .hx-hero__controls > * {
            pointer-events: auto;
        }

        .hx-hero__controls .hx-nav {
            position: static;
            transform: none;
            flex: 0 0 auto;
        }

        .hx-hero__controls .hx-counter {
            position: static;
        }

        .hx-hero__controls .hx-thumbs {
            position: static;
            display: flex;
            gap: 8px;
        }

        /* The inner layer spans the hero, so let clicks fall through to the slider
           and re-arm them only on the card itself. */
        .hx-hero__inner {
            pointer-events: none;
        }

        .hx-hero__inner .hx-pcard,
        .hx-scroll {
            pointer-events: auto;
        }

        .hx-hero__grid {
            display: block;
        }

        .hx-col--info {
            max-width: 336px;
        }

        /* ---- project card and enquiry form share one height ----
           The two boxes sit in different grid containers (.hx-hero__grid vs
           .hx-shell__aside), so no amount of align-items can equalise them.
           One shared custom property is the honest fix: both take the same
           height, and whichever runs long scrolls inside itself instead of
           dragging the other out of alignment. Retune in this one place. */
        @media (min-width: 1024px) {
            :root {
                --hx-panel-h: 600px;
            }

            /* Scoped to .hxq-sticky on purpose: the enquiry MODAL reuses
               .hxq-card, and a fixed height there would crop the popup. */
            .hx-pcard,
            .hxq-sticky .hxq-card {
                height: var(--hx-panel-h);
                display: flex;
                flex-direction: column;
            }

            /* fixed chrome: never absorb the leftover space */
            .hx-pcard__banner,
            .hxq-sticky .hxq-card::before,
            .hxq-sticky .hxq-strip,
            .hxq-sticky .hxq-actions {
                flex: none;
            }

            /* min-height:0 is what actually lets a flex child scroll */
            .hx-pcard__body,
            .hxq-sticky .hxq-body {
                flex: 1 1 auto;
                min-height: 0;
                overflow-y: auto;
                /* scrollable, but the bar itself is hidden - the panels are a
                   fixed height and a visible track cluttered the gold. */
                scrollbar-width: none;
                -ms-overflow-style: none;
            }

            .hx-pcard__body::-webkit-scrollbar,
            .hxq-sticky .hxq-body::-webkit-scrollbar {
                width: 0;
                height: 0;
                display: none;
            }

            /* A listing with no configuration line and few bullets left a gap
               below the buttons inside the fixed height. Anchoring the price
               block to the bottom makes short cards read as deliberate rather
               than unfinished. */
            .hx-pcard__body {
                display: flex;
                flex-direction: column;
            }

            .hx-pcard__price {
                margin-top: auto;
                padding-top: 12px;
            }
        }

        /* ---------- the project card ---------- */
        .hx-pcard {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 26px 64px rgba(6, 10, 30, .34);
            font-family: var(--hx-sans);
        }

        .hx-pcard__banner {
            padding: 9px 14px;
            text-align: center;
            font-size: 12.5px;
            font-weight: 700;
            color: #2B2000;
            letter-spacing: .2px;
            background: linear-gradient(90deg, #E8C55C 0%, #DAA520 46%, #B8860B 100%);
        }

        .hx-pcard__body {
            padding: 14px 16px 16px;
        }

        .hx-pcard__title {
            margin: 0 0 8px;
            text-align: center;
            font-size: clamp(19px, 1.5vw, 24px);
            font-weight: 800;
            line-height: 1.14;
            color: #111827;
            letter-spacing: -.4px;
        }

        .hx-pcard__at {
            margin: 0 0 2px;
            font-size: 12.5px;
            color: #4B5563;
            line-height: 1.4;
        }

        .hx-pcard__by {
            margin: 0;
            font-size: 13px;
            font-weight: 600;
            color: #111827;
        }

        .hx-pcard__verified {
            display: flex;
            align-items: center;
            gap: 6px;
            margin: 6px 0 0;
            font-size: 11.5px;
            font-weight: 600;
            color: #0F9D58;
        }

        /* Spec-sheet bullets rather than the old label/value grid: reads tighter
           in a narrow card and matches the reference layout. */
        .hx-pcard__bullets {
            margin: 11px 0 0;
            padding: 0 0 0 18px;
            list-style: disc;
            display: grid;
            gap: 6px;
        }

        .hx-pcard__bullets li {
            font-size: 14.5px;
            font-weight: 600;
            line-height: 1.45;
            color: #1F2937;
        }

        .hx-pcard__bullets li::marker {
            color: #8B6508;
        }

        .hx-pcard__config {
            margin: 11px 0 0;
            font-size: 14.5px;
            font-weight: 700;
            line-height: 1.35;
            color: #111827;
        }

        /* Gentle entrance for both panels - a soft rise and fade, nothing that
           draws attention to itself. Honoured only when motion is welcome. */
        @keyframes hxPanelPop {
            from {
                opacity: 0;
                transform: translateY(14px) scale(.985);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .hx-pcard,
        .hxq-sticky .hxq-card {
            animation: hxPanelPop .55s cubic-bezier(.22, .9, .3, 1) both;
        }

        /* the rail follows the card in rather than arriving with it */
        .hxq-sticky .hxq-card {
            animation-delay: .12s;
        }

        @media (prefers-reduced-motion: reduce) {

            .hx-pcard,
            .hxq-sticky .hxq-card {
                animation: none;
            }
        }

        .hx-pcard__perks {
            position: relative;
            margin: 11px 0 0;
            padding: 11px 11px;
            background: linear-gradient(140deg, #B8860B 0%, #DAA520 55%, #B8860B 100%);
            border-radius: 10px;
            display: grid;
            gap: 4px;
        }

        .hx-pcard__perks::after {
            content: "";
            position: absolute;
            inset: 6px;
            border: 1px dashed rgba(43, 32, 0, .45);
            border-radius: 3px;
            pointer-events: none;
        }

        .hx-pcard__perks span {
            position: relative;
            z-index: 1;
            padding: 0 6px;
            text-align: center;
            font-size: 12.5px;
            font-weight: 700;
            line-height: 1.3;
            color: #2B2000;
        }

        .hx-pcard__price {
            margin: 12px 0 0;
            text-align: center;
        }

        .hx-pcard__price-label {
            display: block;
            font-size: 13.5px;
            color: #6B7280;
        }

        .hx-pcard__price-value {
            display: block;
            margin-top: 3px;
            /* the headline number in the card - deliberately the largest thing
               here after the project title */
            font-size: clamp(27px, 2.2vw, 34px);
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -.5px;
            color: #111827;
        }

        .hx-pcard__price-note {
            display: block;
            margin-top: 2px;
            font-size: 12.5px;
            color: #6B7280;
        }

        .hx-pcard__cta {
            display: block;
            width: 100%;
            margin: 11px 0 0;
            padding: 11px 14px;
            border: 0;
            border-radius: 4px;
            text-align: center;
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            cursor: pointer;
            background: linear-gradient(135deg, #96700A 0%, #8B6508 48%, #6F5106 100%);
            box-shadow: 0 10px 24px rgba(43, 32, 0, .32);
            transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
        }

        .hx-pcard__cta:hover {
            transform: translateY(-1px);
            filter: brightness(1.05);
            box-shadow: 0 14px 30px rgba(43, 32, 0, .42);
        }

        .hx-pcard__cta:active {
            transform: translateY(0);
        }

        /* Three quick actions, now on the gold form panel where the single
           call-back button used to sit. White pills so they lift off the gold
           instead of dissolving into it. */
        .hxq-actions {
            display: flex;
            gap: 7px;
            margin: 12px 14px 0;
        }

        .hxq-action {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 9px 4px;
            border: 1px solid rgba(43, 32, 0, .14);
            border-radius: 7px;
            background: #fff;
            font-size: 12px;
            font-weight: 700;
            color: #374151;
            text-decoration: none;
            box-shadow: 0 2px 6px rgba(43, 32, 0, .10);
            transition: transform .2s ease, color .2s ease, box-shadow .2s ease;
        }

        .hxq-action i {
            font-size: 13px;
        }

        .hxq-action:hover {
            transform: translateY(-1px);
            color: #8B6508;
            box-shadow: 0 6px 14px rgba(43, 32, 0, .16);
        }

        .hxq-action--wa:hover {
            color: #128C7E;
        }

        /* The project card's highlight banner, repeated inside the enquiry
           popup. Deeper bronze than the card's version on purpose: the popup
           panel is itself gold, so the card's #DAA520 banner would dissolve
           into it. White on #8B6508 measures 5.3:1. */
        .hxq-perks {
            position: relative;
            margin: 12px 14px 0;
            padding: 11px;
            background: linear-gradient(140deg, #7A5A07 0%, #8B6508 55%, #7A5A07 100%);
            border-radius: 10px;
            display: grid;
            gap: 4px;
        }

        .hxq-perks::after {
            content: "";
            position: absolute;
            inset: 6px;
            border: 1px dashed rgba(255, 255, 255, .45);
            border-radius: 3px;
            pointer-events: none;
        }

        .hxq-perks span {
            position: relative;
            z-index: 1;
            padding: 0 6px;
            text-align: center;
            font-size: 12.5px;
            font-weight: 700;
            line-height: 1.3;
            color: #fff;
        }

        /* ---- continuous motion ----
           hxPanelPop fires once on load; these keep running, so the banner and
           the brochure button stay gently alive instead of settling. Kept small
           on purpose - it should register at the edge of vision, not nag. */
        /* NOT hxBob - that name is already taken by the scroll-indicator ring
           further up, and redefining it here would silently hijack it. */
        @keyframes hxBtnBob {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        /* One up-and-down move for everything that should catch the eye: the
           highlight banner (card + popup) and every download CTA. The banner
           runs slower than the buttons so the page drifts out of lockstep on
           its own rather than bouncing as one block. */
        .hx-pcard__perks,
        .hxq-perks {
            animation: hxBtnBob 3.2s ease-in-out infinite;
        }

        /* .pd-btn is all three section CTAs: Download Brochure, Download
           Costing Details and Download Amenities. --bob is the Brochure link in
           the site header, which renders twice (desktop nav + mobile strip). */
        .hxq-action--bob,
        .hx-secnav__item--bob,
        .pd-btn {
            animation: hxBtnBob 2.2s ease-in-out infinite;
        }

        /* :hover sets its own transform, so stand the loop down while pointing
           at it - otherwise the animation wins and the lift never shows. */
        .hxq-action--bob:hover,
        .hx-secnav__item--bob:hover,
        .pd-btn:hover {
            animation-play-state: paused;
        }

        @media (prefers-reduced-motion: reduce) {

            .hx-pcard__perks,
            .hxq-perks,
            .hxq-action--bob,
            .hx-secnav__item--bob,
            .pd-btn {
                animation: none;
            }
        }

        /* ---------- stacked ---------- */
        @media (max-width: 1023px) {
            .hx-hero {
                display: block;
                min-height: 0;
            }

            /* The slider stops being a backdrop and becomes a normal band above the
               card - a photo behind the card just reads as noise at this width. */
            .hx-hero__decor {
                position: relative;
                top: auto;
                bottom: auto;
                left: 0;
                right: 0;
                height: clamp(230px, 54vw, 380px);
            }

            .hx-hero__scrim {
                background: linear-gradient(180deg, rgba(8, 11, 32, .22) 0%, transparent 40%, rgba(8, 11, 32, .30) 100%);
            }

            .hx-col--info {
                max-width: none;
            }
        }
    </style>

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
                <a href="tel:+11234567892" class="hx-btn hx-btn--outline hx-dock__icon" aria-label="Call us">
                    <i class="fa-solid fa-phone"></i><span>Call</span>
                </a>
                <a href="https://wa.me/919999999999" target="_blank" rel="noopener noreferrer"
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
        <script>
            // The lead was just captured, so release the file this visitor asked for.
            (function () {
                var a = document.createElement('a');
                a.href = @json(session('brochure_url'));
                a.setAttribute('download', '');
                document.body.appendChild(a);
                a.click();
                a.remove();
            })();
        </script>
    @endif

    <style>
        /* ============ sticky quote card + enquiry modal ============ */
        .hxq-sticky {
            position: sticky;
            /* park below the sticky site header, measured into --hx-header-h */
            top: calc(var(--hx-header-h, 72px) + 14px);
            /* A form taller than the viewport would otherwise pin with its submit
               button permanently below the fold. Scroll it internally instead;
               overflow on the sticky element itself is safe, only an overflowing
               ANCESTOR would break the stickiness. */
            max-height: calc(100vh - var(--hx-header-h, 72px) - 28px);
            overflow-y: auto;
            overscroll-behavior: contain;
            scrollbar-width: thin;
        }

        /* Breathing room so the card never collides with the last detail section.
           The 30px top matches .hx-hero__inner's padding-top, which is what puts
           the form's top edge on the same line as the project card's. */
        @media (min-width: 1024px) {
            .hx-shell__aside {
                padding-top: 30px;
                padding-bottom: 28px;
            }
        }

        .hxq-card {
            /* Same gold ramp as the project card's banner and perks panel, so
               the two read as one set rather than two unrelated panels. The
               navy CTAs sit on top of this. */
            background: linear-gradient(165deg, #E8C55C 0%, #DAA520 55%, #B8860B 100%);
            border: 1px solid rgba(43, 32, 0, .15);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 22px 54px rgba(43, 32, 0, .34);
            font-family: var(--font-body, "Mulish", system-ui, sans-serif);
        }

        /* Thin lit edge along the top of the card. */
        .hxq-card::before {
            content: "";
            display: block;
            height: 3px;
            background: linear-gradient(90deg, #2B2000 0%, #8B6508 50%, #2B2000 100%);
        }

        .hxq-strip {
            display: flex;
            align-items: stretch;
        }

        .hxq-strip__item {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            padding: 13px 10px;
            background: transparent;
            border: 0;
            cursor: pointer;
            color: #2B2000;
            font-size: 12.5px;
            font-weight: 600;
            line-height: 1.25;
            text-decoration: none;
        }

        .hxq-strip__item:hover {
            background: rgba(43, 32, 0, .12);
        }

        .hxq-strip__item i {
            /* deep bronze rather than mid-gold: this sits ON the gold panel,
               where #8B6508 would only reach 2.35:1 */
            color: #2B2000;
            font-size: 17px;
        }

        .hxq-strip__sep {
            width: 1px;
            background: rgba(43, 32, 0, .2);
        }

        .hxq-body {
            background: #fff;
            margin: 14px;
            border-radius: 14px;
            padding: 20px 18px 22px;
            box-shadow: 0 8px 22px rgba(43, 32, 0, .16);
        }

        .hxq-title {
            margin: 0 0 14px;
            text-align: center;
            /* pinned to the body font so this card stays sentence case
               regardless of what the heading scale sets */
            font-family: var(--font-body, "Mulish", system-ui, sans-serif);
            text-transform: none;
            letter-spacing: normal;
            font-size: 19px;
            font-weight: 700;
            color: #111827;
        }

        /* Short accent rule under the heading instead of a bare line of text. */
        .hxq-title::after {
            content: "";
            display: block;
            width: 44px;
            height: 3px;
            margin: 9px auto 0;
            border-radius: 3px;
            background: linear-gradient(90deg, #DAA520 0%, #B8860B 100%);
        }

        .hxq-form {
            display: grid;
            gap: 11px;
        }

        .hxq-phone {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1.2fr);
            gap: 10px;
        }

        .hxq-form input[type="text"],
        .hxq-form input[type="email"],
        .hxq-form input[type="tel"],
        .hxq-cc {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #F5EEDC;
            border-radius: 10px;
            /* Tinted at rest, clearing to white on focus - gives the fields a
               visible resting state without drawing boxes around everything. */
            background: #FDF9F0;
            color: #111827;
            font-size: 14px;
            font-family: inherit;
            transition: background .18s ease, border-color .18s ease, box-shadow .18s ease;
        }

        .hxq-form input::placeholder {
            color: #ADA189;
        }

        .hxq-form input:focus,
        .hxq-cc:focus {
            outline: none;
            background: #fff;
            border-color: #8B6508;
            box-shadow: 0 0 0 4px rgba(43, 32, 0, .14);
        }

        .hxq-terms {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            font-size: 12px;
            color: #5F6472;
            cursor: pointer;
        }

        .hxq-terms input {
            margin-top: 2px;
            accent-color: #8B6508;
        }

        .hxq-terms a {
            color: #8B6508;
            font-weight: 600;
        }

        .hxq-submit {
            width: 100%;
            padding: 14px;
            border: 0;
            border-radius: 10px;
            background: linear-gradient(135deg, #96700A 0%, #8B6508 48%, #6F5106 100%);
            box-shadow: 0 10px 24px rgba(43, 32, 0, .32);
            color: #fff;
            font-size: 15.5px;
            font-weight: 700;
            font-family: inherit;
            letter-spacing: .2px;
            cursor: pointer;
            transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
        }

        .hxq-submit:hover {
            transform: translateY(-1px);
            filter: brightness(1.05);
            box-shadow: 0 14px 30px rgba(43, 32, 0, .42);
        }

        .hxq-submit:active {
            transform: translateY(0);
        }

        .hxq-err {
            margin: -4px 0 0;
            font-size: 12px;
            color: #DC2626;
        }

        .hxq-alert {
            margin-bottom: 12px;
            padding: 10px 12px;
            border-radius: 8px;
            background: #E7F7EE;
            color: #1B7F4B;
            font-size: 13px;
        }

        /* the reCAPTCHA iframe is a fixed 304px wide; scale it to fit the card */
        .hxq-body .g-recaptcha {
            transform: scale(.86);
            transform-origin: 0 0;
            height: 68px;
        }

        /* ---- modal ---- */
        .hxq-modal {
            position: fixed;
            inset: 0;
            z-index: 1000;
            display: flex;
            justify-content: center;
            padding: 24px 16px;
            overflow-y: auto;
            opacity: 0;
            visibility: hidden;
            transition: opacity .22s ease, visibility .22s ease;
        }

        .hxq-modal.is-open {
            opacity: 1;
            visibility: visible;
        }

        .hxq-modal__backdrop {
            position: fixed;
            inset: 0;
            background: rgba(43, 32, 0, .62);
        }

        .hxq-modal__panel {
            position: relative;
            width: 100%;
            max-width: 396px;
            margin: auto;
            transform: translateY(12px);
            transition: transform .22s ease;
        }

        .hxq-modal.is-open .hxq-modal__panel {
            transform: none;
        }

        .hxq-modal__x {
            position: absolute;
            top: -14px;
            right: -10px;
            z-index: 2;
            width: 34px;
            height: 34px;
            border: 0;
            border-radius: 50%;
            background: #fff;
            color: #111827;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(43, 32, 0, .22);
        }

        /* ============ button shimmer ============
           Reuses @keyframes hxShimmer (declared alongside .hx-btn--primary, which
           already sweeps) so every button shares one timing curve. Filled CTAs
           loop; secondary buttons and chips sweep only on hover, otherwise the
           whole page glitters at once and nothing reads as the primary action. */
        .hx-pcard__cta,
        .hxq-submit,
        .btn-primary,
        .hxq-strip__item,
        .hx-secnav__item,
        .hx-btn--outline,
        .hx-btn--whatsapp,
        .hx-nav {
            position: relative;
            overflow: hidden;
            isolation: isolate;
        }

        /* .hx-hero__controls .hx-nav pins the arrows static for the control bar,
           which outranks the single-class rule above. Match that specificity so
           the sweep has a positioned box to sit inside. */
        .hx-hero__controls .hx-nav {
            position: relative;
        }

        .hx-pcard__cta::after,
        .hxq-submit::after,
        .btn-primary::after,
        .hxq-strip__item::after,
        .hx-secnav__item::after,
        .hx-btn--outline::after,
        .hx-btn--whatsapp::after,
        .hx-nav::after {
            content: "";
            position: absolute;
            top: 0;
            bottom: 0;
            left: -60%;
            width: 45%;
            z-index: 0;
            pointer-events: none;
            transform: skewX(-18deg);
            background: linear-gradient(100deg, transparent 0%, rgba(255, 255, 255, .38) 50%, transparent 100%);
        }

        /* Keep icons above the sweep. Bare label text sits under it on purpose -
           that is what reads as a shine passing over the button. */
        .hx-pcard__cta > *,
        .hxq-submit > *,
        .btn-primary > *,
        .hxq-strip__item > *,
        .hx-secnav__item > *,
        .hx-btn--outline > *,
        .hx-btn--whatsapp > *,
        .hx-nav > * {
            position: relative;
            z-index: 1;
        }

        /* Filled CTAs: continuous loop, staggered so they never flash in unison. */
        .hx-pcard__cta::after,
        .hxq-submit::after,
        .btn-primary::after {
            animation: hxShimmer 3.4s ease-in-out infinite;
        }
        .hxq-submit::after {
            animation-delay: 1.1s;
        }

        .btn-primary::after {
            animation-delay: .8s;
        }

        /* Secondary buttons on a light face need an indigo sheen - white on white
           is invisible. The strip sits on the navy card, so it keeps white. */
        .hx-secnav__item::after,
        .hx-btn--outline::after,
        .hx-btn--whatsapp::after,
        .hx-nav::after {
            background: linear-gradient(100deg, transparent 0%, rgba(139, 101, 8, .22) 50%, transparent 100%);
        }

        /* Secondary buttons and chips: one sweep per hover. */
        .hxq-strip__item:hover::after,
        .hx-secnav__item:hover::after,
        .hx-btn--outline:hover::after,
        .hx-btn--whatsapp:hover::after,
        .hx-nav:hover::after {
            animation: hxShimmer 1.1s ease-out;
        }

        @media (prefers-reduced-motion: reduce) {

            .hx-pcard__cta::after,
            .hxq-submit::after,
            .btn-primary::after,
            .hxq-strip__item:hover::after,
            .hx-secnav__item:hover::after,
            .hx-btn--outline:hover::after,
            .hx-btn--whatsapp:hover::after,
            .hx-nav:hover::after {
                animation: none;
            }
        }

        @media (max-width: 1023px) {
            .hxq-sticky {
                position: static;
                max-height: none;
                overflow-y: visible;
            }
        }

        /* ============ compact panels ============
           --hx-panel-h was a flat 600px. On a laptop whose viewport is ~700px
           tall that is more than the room left under the sticky header, so
           .hxq-sticky overflowed: a thin scrollbar appeared over the gold and
           the submit button sat below the fold. The height now tracks the
           viewport, and both panels are tightened so their content still fits
           inside it. Retune the 540px cap here. */
        @media (min-width: 1024px) {
            :root {
                --hx-panel-h: min(540px, calc(100vh - var(--hx-header-h, 72px) - 48px));
            }
        }

        /* Shorter than this and even the compacted panels cannot fit, so stop
           pinning them: natural height, page scrolls as usual. Better than a
           card the viewer has to scroll inside. */
        @media (min-width: 1024px) and (max-height: 620px) {

            .hx-pcard,
            .hxq-sticky .hxq-card {
                height: auto;
            }

            .hxq-sticky {
                position: static;
                max-height: none;
                overflow-y: visible;
            }
        }

        /* The control bar (prev / thumbnails / next / counter) owns the bottom
           band of the hero, so the scroll hint could not stay at bottom:20px -
           it landed on top of the slide counter in the right corner. Lift it
           clear of that row. Still hidden below 1280px by the rule above. */
        .hx-scroll {
            bottom: 96px;
        }

        /* No visible track on the rail: it no longer overflows, and the thin
           bar sat directly on the gold. */
        .hxq-sticky {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .hxq-sticky::-webkit-scrollbar {
            width: 0;
            height: 0;
            display: none;
        }

        /* ---- right rail, tightened ---- */
        .hxq-strip__item {
            padding: 10px 8px;
            font-size: 12px;
        }

        .hxq-strip__item i {
            font-size: 15px;
        }

        .hxq-actions {
            margin: 9px 12px 0;
            gap: 6px;
        }

        .hxq-action {
            padding: 7px 4px;
            font-size: 11.5px;
        }

        .hxq-body {
            margin: 11px;
            padding: 14px 14px 16px;
        }

        .hxq-title {
            margin: 0 0 10px;
            font-size: 17px;
        }

        .hxq-title::after {
            margin: 7px auto 0;
        }

        .hxq-form {
            gap: 8px;
        }

        .hxq-form input[type="text"],
        .hxq-form input[type="email"],
        .hxq-form input[type="tel"],
        .hxq-cc {
            padding: 9px 12px;
            font-size: 13.5px;
        }

        .hxq-terms {
            font-size: 11.5px;
        }

        .hxq-body .g-recaptcha {
            transform: scale(.78);
            height: 62px;
        }

        .hxq-submit {
            padding: 11px;
            font-size: 14.5px;
        }

        /* ---- left rail: sized up to fill the shared panel height ----
           This card carries far less content than the form opposite it, so at
           the compact sizes it left a block of empty white under the CTA.
           Bigger type fills the same height honestly instead of padding it. */
        .hx-pcard__banner {
            padding: 9px 12px;
            font-size: 13.5px;
        }

        .hx-pcard__body {
            padding: 14px 18px 16px;
        }

        .hx-pcard__title {
            margin: 0 0 8px;
            font-size: clamp(22px, 1.8vw, 27px);
        }

        .hx-pcard__at {
            font-size: 14px;
        }

        .hx-pcard__by {
            font-size: 14px;
        }

        .hx-pcard__verified {
            margin: 8px 0 0;
            font-size: 13px;
        }

        .hx-pcard__perks {
            margin: 12px 0 0;
            padding: 12px;
        }

        .hx-pcard__perks span {
            font-size: 14px;
        }

        .hx-pcard__bullets {
            margin: 13px 0 0;
            gap: 9px;
        }

        .hx-pcard__bullets li {
            font-size: 16px;
        }

        .hx-pcard__config {
            margin: 13px 0 0;
            font-size: 16px;
        }

        .hx-pcard__price-label {
            font-size: 15px;
        }

        .hx-pcard__price-value {
            font-size: clamp(30px, 2.4vw, 36px);
        }

        .hx-pcard__price-note {
            font-size: 14px;
        }

        .hx-pcard__cta {
            margin: 12px 0 0;
            padding: 13px 14px;
            font-size: 16.5px;
        }

        /* Price block soaks up the leftover height so the CTA lands on the
           bottom edge. The panel-height block sets margin-top:auto for this,
           but the base .hx-pcard__price rule further down the file carries a
           margin shorthand at equal specificity and was cancelling it. */
        @media (min-width: 1024px) {
            .hx-pcard__price {
                margin-top: auto;
            }
        }
    </style>

    <script>
        // Enquiry modal: one shared form, opened from any [data-hxq-open] trigger.
        (function () {
            var modal = document.getElementById('hxqModal');
            if (!modal) return;

            var lastFocused = null;

            function openEnquiry(trigger) {
                lastFocused = trigger || document.activeElement;

                // a trigger can relabel the form, e.g. "Instant Call Back"
                var heading = trigger && trigger.getAttribute('data-hxq-heading');
                var title = modal.querySelector('.hxq-title');
                if (title) title.textContent = heading || 'Get The Best Quote';

                // ...and retarget what the submission is FOR, so a brochure
                // trigger comes back with the PDF instead of a plain thank-you.
                var label = trigger && trigger.getAttribute('data-hxq-submit-label');
                var submit = modal.querySelector('[data-hxq-submit]');
                if (submit) submit.textContent = label || 'Get It Now';

                var field = modal.querySelector('[data-hxq-intent-field]');
                if (field) field.value = (trigger && trigger.getAttribute('data-hxq-intent')) || 'enquiry';

                var detailField = modal.querySelector('[data-hxq-detail-field]');
                if (detailField) detailField.value = (trigger && trigger.getAttribute('data-hxq-detail-id')) || '';

                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';

                var first = modal.querySelector('input:not([type=hidden]), select');
                if (first) setTimeout(function () { first.focus(); }, 60);
            }

            function closeEnquiry() {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
                var detailField = modal.querySelector('[data-hxq-detail-field]');
                if (detailField) detailField.value = '';
                if (lastFocused && lastFocused.focus) lastFocused.focus();
            }

            document.addEventListener('click', function (e) {
                var opener = e.target.closest('[data-hxq-open]');
                if (opener) {
                    e.preventDefault();
                    openEnquiry(opener);
                    return;
                }
                if (e.target.closest('[data-hxq-close]')) {
                    e.preventDefault();
                    closeEnquiry();
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && modal.classList.contains('is-open')) closeEnquiry();
            });

            // Validation failed on a submission that came from the modal, so reopen
            // it - otherwise the errors render out of sight in the sidebar copy.
            @if ($errors->any() && old('source') === 'modal')
                openEnquiry(document.querySelector(
                    '[data-hxq-open][data-hxq-intent="{{ old('intent', 'enquiry') }}"]'));
            @endif
        })();
    </script>

    <script>
        // The site header is sticky at top:0 and its height can change with the
        // viewport, so measure it and let the project nav park right beneath it.
        (function () {
            var header = document.querySelector('header');
            if (!header) return;

            function syncHeaderHeight() {
                // The header is only sticky on some routes. When it scrolls away
                // there is nothing to clear, so reserve 0 - otherwise the project
                // nav and the sticky enquiry card float below a phantom gap.
                var pos = window.getComputedStyle(header).position;
                var sticks = (pos === 'sticky' || pos === 'fixed');
                var h = sticks ? Math.round(header.getBoundingClientRect().height) : 0;
                document.documentElement.style.setProperty('--hx-header-h', h + 'px');
            }

            syncHeaderHeight();
            window.addEventListener('load', syncHeaderHeight);

            // Show the docked enquiry bar only while the real form is off-screen.
            var dock = document.getElementById('hxDock');
            var form = document.getElementById('enquiry');
            if (dock && form && window.IntersectionObserver) {
                new IntersectionObserver(function (entries) {
                    var visible = entries[0].isIntersecting;
                    dock.classList.toggle('is-on', !visible);
                    dock.setAttribute('aria-hidden', visible ? 'true' : 'false');
                }, { rootMargin: '-10% 0px -10% 0px' }).observe(form);
            }
            if (window.ResizeObserver) {
                new ResizeObserver(syncHeaderHeight).observe(header);
            } else {
                window.addEventListener('resize', syncHeaderHeight, { passive: true });
            }
        })();
    </script>



    @push('scripts')
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endpush



    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

        <script>
            // Create array of all image paths
            const bigImageSources = [
                @foreach ($propertyimagesall as $image)
                    "{{ asset($image->image_path) }}",
                @endforeach
            ];

            let currentBigImageIndex = 0;
            let currentBigImageSrc = bigImageSources[0] || "{{ asset('assets/images/home.png') }}";
            let showingFirst = true;

            document.addEventListener("DOMContentLoaded", () => {
                updateBigImage(); // Initial load
            });

            function changeBigImage(src, index) {
                currentBigImageIndex = index;
                currentBigImageSrc = src;
                updateBigImage();
            }

            function updateBigImage(direction = "next") {
                const img1 = document.getElementById("bigImageDisplay1");
                const img2 = document.getElementById("bigImageDisplay2");

                if (!img1 || !img2) return;

                const currentImg = showingFirst ? img1 : img2;
                const nextImg = showingFirst ? img2 : img1;

                const newSrc = bigImageSources[currentBigImageIndex] || "{{ asset('assets/images/home.png') }}";

                // Reset z-index and position
                nextImg.src = newSrc;
                nextImg.style.zIndex = 2;
                currentImg.style.zIndex = 1;

                // Start position of next image
                nextImg.style.transform = `translateX(${
            direction === "next" ? "100%" : "-100%"
        })`;
                nextImg.style.opacity = 1;

                // Trigger reflow to apply initial transform
                void nextImg.offsetWidth;

                // Animate in and out
                nextImg.style.transform = "translateX(0%)";
                currentImg.style.transform = `translateX(${
            direction === "next" ? "-100%" : "100%"
        })`;

                // After transition complete
                setTimeout(() => {
                    showingFirst = !showingFirst;
                    currentBigImageSrc = newSrc;
                    currentImg.style.opacity = 0;
                }, 500);
            }

            function nextBigImage(event) {
                if (event) event.stopPropagation();
                currentBigImageIndex =
                    (currentBigImageIndex + 1) % bigImageSources.length;
                updateBigImage("next");
            }

            function prevBigImage(event) {
                if (event) event.stopPropagation();
                currentBigImageIndex =
                    (currentBigImageIndex - 1 + bigImageSources.length) %
                    bigImageSources.length;
                updateBigImage("prev");
            }
        
            // --- hero image counter -------------------------------------------------
            // Additive only: wraps the existing gallery functions so "1 / N" stays in
            // sync without altering their behaviour.
            (function () {
                function sync() {
                    var el = document.getElementById('heroImgCounter');
                    if (!el || typeof bigImageSources === 'undefined') return;
                    el.textContent = (currentBigImageIndex + 1) + ' / ' + bigImageSources.length;
                }
                ['changeBigImage', 'nextBigImage', 'prevBigImage'].forEach(function (fn) {
                    var orig = window[fn];
                    if (typeof orig !== 'function') return;
                    window[fn] = function () {
                        var out = orig.apply(this, arguments);
                        sync();
                        return out;
                    };
                });
                document.addEventListener('DOMContentLoaded', sync);
            })();

            // --- active thumbnail ---------------------------------------------------
            // The thumb rail rendered is-active on the first item and never moved it.
            // Same additive wrapper as the counter above.
            (function () {
                function syncThumbs() {
                    var thumbs = document.querySelectorAll('.hx-thumb');
                    if (!thumbs.length) return;
                    for (var i = 0; i < thumbs.length; i++) {
                        thumbs[i].classList.toggle('is-active', i === currentBigImageIndex);
                    }
                }
                ['changeBigImage', 'nextBigImage', 'prevBigImage'].forEach(function (fn) {
                    var orig = window[fn];
                    if (typeof orig !== 'function') return;
                    window[fn] = function () {
                        var out = orig.apply(this, arguments);
                        syncThumbs();
                        return out;
                    };
                });
                document.addEventListener('DOMContentLoaded', syncThumbs);
            })();

            // --- hero slider autoplay -----------------------------------------------
            // Drives the existing nextBigImage() on a timer rather than reimplementing
            // the transition. Holds still while the pointer is over the stage, while
            // the tab is backgrounded, and while the zoom modal is open. Any manual
            // move restarts the clock so autoplay never yanks the slide out from under
            // someone mid-look.
            (function () {
                var DELAY = 5000;
                var stage = document.getElementById('bigimage');
                if (!stage || typeof bigImageSources === 'undefined' || bigImageSources.length < 2) return;

                var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                if (reduced) return;

                var timer = null;
                var paused = false;

                function modalOpen() {
                    var m = document.getElementById('modal');
                    return !!m && !m.classList.contains('hidden');
                }

                function tick() {
                    if (paused || document.hidden || modalOpen()) return;
                    if (typeof window.nextBigImage === 'function') window.nextBigImage();
                }

                function stop() {
                    if (timer) { clearInterval(timer); timer = null; }
                }

                function start() {
                    stop();
                    timer = setInterval(tick, DELAY);
                }

                // Wrap last, so this sees the counter/thumb-wrapped versions.
                ['changeBigImage', 'nextBigImage', 'prevBigImage'].forEach(function (fn) {
                    var orig = window[fn];
                    if (typeof orig !== 'function') return;
                    window[fn] = function () {
                        var out = orig.apply(this, arguments);
                        if (timer) start();   // reset the clock, only while running
                        return out;
                    };
                });

                stage.addEventListener('mouseenter', function () { paused = true; });
                stage.addEventListener('mouseleave', function () { paused = false; });
                stage.addEventListener('focusin', function () { paused = true; });
                stage.addEventListener('focusout', function () { paused = false; });
                document.addEventListener('visibilitychange', function () {
                    if (document.hidden) { stop(); } else { start(); }
                });

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', start);
                } else {
                    start();
                }
            })();

        </script>

    <style>
        /* ============ project content (below the hero) ============
           Card-per-section layout. Palette is the site's own: #8B6508 indigo,
           #2B2000 navy, #FDF9F0 / #F5EEDC lavender, #EFE8D8 line. */
        .pd-stack {
            --pd-indigo: #8B6508;
            --pd-indigo-dark: #6F5106;
            --pd-navy: #2B2000;
            --pd-ink: #111827;
            --pd-muted: #5F6472;
            --pd-line: #EFE8D8;
            --pd-lav: #FDF9F0;

            display: grid;
            gap: 18px;
            font-family: "Mulish", "Inter", system-ui, -apple-system, "Segoe UI", sans-serif;
            padding-bottom: 8px;
        }

        .pd-card {
            background: #fff;
            border: 1px solid var(--pd-line);
            border-radius: 12px;
            padding: 22px 24px 24px;
            box-shadow: 0 6px 20px rgba(17, 24, 39, .05);
        }

        /* Section headings use the body sans, not the all-caps display face. */
        .pd-h {
            margin: 0 0 16px;
            font-family: inherit;
            text-transform: none;
            letter-spacing: -.2px;
            font-size: clamp(19px, 1.6vw, 23px);
            font-weight: 700;
            color: var(--pd-navy);
        }

        .pd-h-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
        }

        .pd-h-row .pd-h {
            margin-bottom: 0;
        }

        .pd-h-row + * {
            margin-top: 16px;
        }

        /* ---------- prose ---------- */
        .pd-prose {
            color: var(--pd-muted);
            font-size: 15px;
            line-height: 1.75;
        }

        .pd-prose p {
            margin: 0 0 10px;
        }

        .pd-prose.is-clamped {
            display: -webkit-box;
            -webkit-line-clamp: 5;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .pd-prose--list ul,
        .pd-prose--list ol {
            margin: 0;
            padding-left: 18px;
        }

        .pd-prose--list li {
            margin-bottom: 6px;
        }

        .pd-readmore {
            margin-top: 6px;
            padding: 0;
            border: 0;
            background: none;
            color: var(--pd-indigo);
            font: inherit;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
        }

        .pd-readmore:hover {
            text-decoration: underline;
        }

        /* ---------- buttons ---------- */
        .pd-btn {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            margin-top: 16px;
            padding: 11px 20px;
            border: 0;
            border-radius: 8px;
            background: linear-gradient(135deg, #96700A 0%, var(--pd-indigo) 48%, var(--pd-indigo-dark) 100%);
            box-shadow: 0 8px 20px rgba(43, 32, 0, .28);
            color: #fff;
            font: inherit;
            font-size: 14.5px;
            font-weight: 700;
            cursor: pointer;
            transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
        }

        .pd-btn:hover {
            transform: translateY(-1px);
            filter: brightness(1.05);
            box-shadow: 0 12px 26px rgba(43, 32, 0, .38);
        }

        .pd-btn:active {
            transform: translateY(0);
        }

        .pd-btn--sm {
            margin-top: 0;
            padding: 9px 15px;
            font-size: 13px;
        }

        .pd-btn--soft {
            background: var(--pd-lav);
            border: 1px solid #E9DFC4;
            box-shadow: none;
            color: var(--pd-indigo-dark);
        }

        .pd-btn--soft:hover {
            background: #F8F1E1;
            box-shadow: 0 8px 18px rgba(43, 32, 0, .16);
        }

        .pd-chip {
            padding: 6px 13px;
            border: 1px solid #E9DFC4;
            border-radius: 6px;
            background: var(--pd-lav);
            color: var(--pd-indigo-dark);
            font: inherit;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: background .2s ease, border-color .2s ease;
        }

        .pd-chip:hover {
            background: var(--pd-indigo);
            border-color: var(--pd-indigo);
            color: #fff;
        }

        /* ---------- highlight tiles ---------- */
        .pd-tiles {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 14px;
        }

        .pd-tile {
            padding: 18px 14px;
            border: 1px solid var(--pd-line);
            border-radius: 10px;
            text-align: center;
            background: #fff;
            transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
        }

        .pd-tile:hover {
            transform: translateY(-2px);
            border-color: #E9DFC4;
            box-shadow: 0 10px 24px rgba(43, 32, 0, .12);
        }

        .pd-tile__ic {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 46px;
            height: 46px;
            margin-bottom: 10px;
            border-radius: 50%;
            background: var(--pd-lav);
            color: var(--pd-indigo);
            font-size: 19px;
        }

        .pd-tile__t {
            margin: 0 0 3px;
            font-family: inherit;
            text-transform: none;
            letter-spacing: normal;
            font-size: 12px;
            font-weight: 600;
            color: var(--pd-muted);
        }

        .pd-tile__d {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            color: var(--pd-ink);
            line-height: 1.3;
        }

        /* ---------- pricing table ---------- */
        .pd-tablewrap {
            overflow-x: auto;
            border: 1px solid var(--pd-line);
            border-radius: 10px;
        }

        .pd-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14.5px;
            min-width: 440px;
        }

        .pd-table th {
            padding: 12px 16px;
            text-align: left;
            background: var(--pd-lav);
            color: var(--pd-navy);
            font-weight: 700;
            font-size: 13.5px;
            border-bottom: 1px solid var(--pd-line);
        }

        .pd-table td {
            padding: 14px 16px;
            color: var(--pd-ink);
            border-bottom: 1px solid var(--pd-line);
        }

        .pd-table tr:last-child td {
            border-bottom: 0;
        }

        .pd-table__price {
            font-weight: 700;
            color: var(--pd-indigo-dark);
            white-space: nowrap;
        }

        /* ---------- floor plans ---------- */
        .pd-plans {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 14px;
        }

        .pd-plan {
            position: relative;
            margin: 0;
            border: 1px solid var(--pd-line);
            border-radius: 10px;
            overflow: hidden;
            background: var(--pd-lav);
            min-height: 210px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }

        .pd-plan img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* The layout itself is the teaser, not the deliverable - the sharp
               copy comes from the gated request. */
            filter: blur(3px) saturate(.9);
            transform: scale(1.05);
        }

        .pd-plan figcaption {
            position: relative;
            z-index: 1;
            padding: 14px 14px 8px;
            color: #fff;
            font-weight: 700;
            font-size: 15px;
            text-shadow: 0 2px 8px rgba(8, 11, 32, .7);
            background: linear-gradient(180deg, transparent, rgba(8, 11, 32, .55));
        }

        .pd-plan__btn {
            position: relative;
            z-index: 1;
            margin: 0 14px 14px;
            padding: 9px 14px;
            border: 0;
            border-radius: 7px;
            background: rgba(255, 255, 255, .94);
            color: var(--pd-indigo-dark);
            font: inherit;
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .pd-plan__btn:hover {
            background: #fff;
        }

        .pd-plan--ghost {
            justify-content: center;
            align-items: center;
            background: var(--pd-lav);
        }

        .pd-plan__zoom {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            padding: 20px;
            border: 0;
            background: none;
            color: var(--pd-indigo-dark);
            font: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .pd-plan__zoom i {
            font-size: 26px;
        }

        /* ---------- amenities ---------- */
        .pd-amen {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 10px;
        }

        .pd-amen__item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 14px;
            border: 1px solid var(--pd-line);
            border-radius: 9px;
            font-size: 14px;
            color: var(--pd-ink);
            transition: border-color .2s ease, background .2s ease;
        }

        .pd-amen__item:hover {
            border-color: #E9DFC4;
            background: var(--pd-lav);
        }

        .pd-amen__item i {
            color: var(--pd-indigo);
            font-size: 17px;
            width: 20px;
            text-align: center;
        }

        /* ---------- gallery ---------- */
        .pd-count {
            padding: 5px 12px;
            border: 1px solid var(--pd-line);
            border-radius: 999px;
            background: var(--pd-lav);
            font-size: 13px;
            font-weight: 600;
            color: var(--pd-muted);
        }

        .pd-gal {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
        }

        .pd-gal__item {
            position: relative;
            padding: 0;
            border: 0;
            border-radius: 10px;
            overflow: hidden;
            aspect-ratio: 4 / 3;
            background: var(--pd-lav);
            cursor: zoom-in;
        }

        /* First frame leads the grid, but only once there are enough photos
           behind it to fill the space it takes. */
        @media (min-width: 700px) {
            .pd-gal__item.is-lead {
                grid-column: span 2;
                grid-row: span 2;
                aspect-ratio: auto;
            }
        }

        .pd-gal__item img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .4s ease;
        }

        .pd-gal__item:hover img {
            transform: scale(1.06);
        }

        .pd-gal__zoom {
            position: absolute;
            inset: 0;
            display: grid;
            place-content: center;
            background: rgba(8, 11, 32, .40);
            color: #fff;
            font-size: 20px;
            opacity: 0;
            transition: opacity .25s ease;
        }

        .pd-gal__item:hover .pd-gal__zoom,
        .pd-gal__item:focus-visible .pd-gal__zoom {
            opacity: 1;
        }

        /* ---------- specifications ---------- */
        .pd-specs {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 12px;
        }

        .pd-spec {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 15px;
            border: 1px solid var(--pd-line);
            border-radius: 10px;
        }

        .pd-spec__ic {
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 9px;
            background: var(--pd-lav);
            color: var(--pd-indigo);
            font-size: 17px;
        }

        .pd-spec__t {
            margin: 0 0 2px;
            font-family: inherit;
            text-transform: none;
            letter-spacing: normal;
            font-size: 15px;
            font-weight: 700;
            color: var(--pd-ink);
        }

        .pd-spec__d {
            margin: 0;
            font-size: 13px;
            color: var(--pd-muted);
        }

        /* ---------- location ---------- */
        .pd-loc {
            display: grid;
            gap: 16px;
        }

        @media (min-width: 900px) {
            .pd-loc {
                grid-template-columns: minmax(0, 1.15fr) minmax(0, 1fr);
                align-items: stretch;
            }
        }

        .pd-loc__map {
            min-height: 300px;
            border: 1px solid var(--pd-line);
            border-radius: 10px;
            overflow: hidden;
        }

        .pd-loc__map iframe {
            display: block;
            width: 100%;
            height: 100%;
            min-height: 300px;
        }

        .pd-loc__empty {
            display: grid;
            place-content: center;
            gap: 8px;
            height: 100%;
            min-height: 300px;
            color: var(--pd-muted);
            text-align: center;
        }

        .pd-loc__empty i {
            font-size: 26px;
        }

        .pd-loc__list {
            padding: 16px 18px;
            border: 1px solid var(--pd-line);
            border-radius: 10px;
            background: var(--pd-lav);
        }

        .pd-loc__h {
            margin: 0 0 8px;
            font-family: inherit;
            text-transform: none;
            letter-spacing: normal;
            font-size: 14px;
            font-weight: 700;
            color: var(--pd-navy);
        }

        .pd-loc__h:not(:first-child) {
            margin-top: 16px;
        }

        .pd-loc__list ul {
            margin: 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 8px;
        }

        .pd-loc__list li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 14px;
            color: var(--pd-ink);
        }

        .pd-loc__list li i {
            margin-top: 3px;
            color: var(--pd-indigo);
            width: 18px;
            text-align: center;
        }

        .pd-loc__none {
            margin: 0;
            font-size: 14px;
            color: var(--pd-muted);
        }

        /* ---------- virtual tour ---------- */
        .pd-tour {
            position: relative;
            display: flex;
            align-items: center;
            gap: 16px;
            min-height: 220px;
            padding: 22px;
            border-radius: 12px;
            overflow: hidden;
            background-size: cover;
            background-position: center;
            text-decoration: none;
        }

        .pd-tour::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(8, 11, 32, .74) 0%, rgba(8, 11, 32, .40) 60%, rgba(8, 11, 32, .30) 100%);
        }

        .pd-tour__play {
            position: relative;
            z-index: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 58px;
            height: 58px;
            flex: 0 0 auto;
            border-radius: 50%;
            background: rgba(255, 255, 255, .94);
            color: var(--pd-indigo-dark);
            font-size: 20px;
            transition: transform .2s ease;
        }

        .pd-tour:hover .pd-tour__play {
            transform: scale(1.07);
        }

        .pd-tour__txt {
            position: relative;
            z-index: 1;
            display: grid;
            gap: 2px;
            color: #fff;
        }

        .pd-tour__txt strong {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: .3px;
        }

        .pd-tour__txt small {
            font-size: 14px;
            opacity: .85;
        }

        @media (max-width: 639px) {
            .pd-card {
                padding: 18px 16px 20px;
            }

            .pd-tour {
                min-height: 180px;
            }
        }
    </style>

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
                $aptFloorVal = $property->details->pluck('apartment_per_floor')->filter()->unique()->implode(', ');
                if ($aptFloorVal) {
                    $pdHighlights[] = ['fa-door-open', 'Apt / Floor', $aptFloorVal];
                }
            } elseif (filled($property->bedrooms)) {
                $pdHighlights[] = ['fa-bed', 'Configuration', $property->bedrooms . ' BHK'];
            }
            if (filled($property->furnishing)) {
                $pdHighlights[] = ['fa-couch', 'Furnishing', $property->furnishing];
            }
            if (filled($property->possession_date)) {
                try {
                    $pdHighlights[] = ['fa-calendar-check', 'Possession', \Carbon\Carbon::parse($property->possession_date)->format('F Y')];
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

            $pdNearby = [];
            foreach ([
                ['fa-train-subway', $property->bazar_distance_km],
                ['fa-hospital', $property->hospital_distance_km],
                ['fa-school', $property->school_distance_km],
            ] as [$ic, $val]) {
                if (filled($val)) { $pdNearby[] = [$ic, $val]; }
            }

            $pdConnect = [];
            foreach ([
                ['fa-bus', $property->bus_stand_distance_km],
                ['fa-train', $property->junction_distance_km],
                ['fa-plane', $property->airport_distance_km],
            ] as [$ic, $val]) {
                if (filled($val)) { $pdConnect[] = [$ic, $val]; }
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
                $hasAptPerFloor = $detailsCollection->contains(fn($d) => filled($d->apartment_per_floor));
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
                                            <td>{{ $detail->apartment_per_floor ?: 'N/A' }}</td>
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
                                        <td>N/A</td>
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

    <script>
        // "Read more" only appears when the clamp is actually hiding something,
        // so a short description does not get a pointless toggle under it.
        (function () {
            var prose = document.querySelector('[data-pd-prose]');
            var btn = document.querySelector('[data-pd-more]');
            if (!prose || !btn) return;

            function sync() {
                if (!prose.classList.contains('is-clamped')) return;
                btn.hidden = prose.scrollHeight <= prose.clientHeight + 2;
            }

            btn.addEventListener('click', function () {
                var clamped = prose.classList.toggle('is-clamped');
                btn.textContent = clamped ? 'Read more' : 'Read less';
            });

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', sync);
            } else {
                sync();
            }
            // fonts can reflow the copy after load, so measure again
            window.addEventListener('load', sync);
        })();
    </script>

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

                        <a href="tel:+11234567892" class="hxq-strip__item">
                            <i class="fa-solid fa-phone-volume"></i>
                            <span>+1 123 456 7892</span>
                        </a>
                    </div>

                    <div class="hxq-actions">
                        {{-- Gated download: opens the enquiry form, and the
                             controller hands back the PDF once the lead lands. --}}
                        <a href="#enquiry" data-hxq-open data-hxq-heading="Download Brochure"
                            data-hxq-submit-label="Download Now" data-hxq-intent="brochure"
                            class="hxq-action hxq-action--bob"><i class="fa-solid fa-file-arrow-down"></i>Brochure</a>
                        <a href="tel:+11234567892" class="hxq-action"><i class="fa-solid fa-phone"></i>Call</a>
                        <a href="https://wa.me/919999999999" target="_blank" rel="noopener noreferrer"
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

    <script>
        // Basic animation reveal (can be enhanced with Intersection Observer)
        document.addEventListener("DOMContentLoaded", () => {
            const animatedElements = document.querySelectorAll(".animated-element");
            animatedElements.forEach((el) => {
                // For CSS animations, class can be added directly or via Intersection Observer
                // For this example, we'll assume CSS handles the animation start
            });

            // Set total images counter on load
            document.getElementById("totalImages").textContent =
                imageSources.length;
        });

        // Modal script (from original, ensure compatibility)
        // Update the imageSources array to use property images
        const imageSources = [
            @foreach ($propertyimagesall as $image)
                "{{ asset($image->image_path) }}",
            @endforeach
        ];
        let currentIndex = 0;
        let autoSlideInterval = null;

        function openModal(src) {
            currentIndex = imageSources.indexOf(src);
            if (currentIndex === -1) currentIndex = 0; // Default to first if src not found
            updateModalImage();
            document.getElementById("modal").classList.remove("hidden");
            document.body.style.overflow = "hidden";
            startAutoSlide();
        }

        function closeModal() {
            document.getElementById("modal").classList.add("hidden");
            document.body.style.overflow = "auto";
            stopAutoSlide();
        }

        function updateModalImage() {
            const img = document.getElementById("modalImage");

            // Fade effect
            img.style.opacity = "0";

            setTimeout(() => {
                img.src = imageSources[currentIndex];
                document.getElementById("currentImageNum").textContent =
                    currentIndex + 1;
                img.style.opacity = "1";
                // 300ms to match the image's own transition-opacity duration-300.
                // Was 3000, which left the viewer blank for three seconds.
            }, 300);
        }

        function nextImage() {
            currentIndex = (currentIndex + 1) % imageSources.length;
            updateModalImage();
            resetAutoSlideTimer();
        }

        function prevImage() {
            currentIndex =
                (currentIndex - 1 + imageSources.length) % imageSources.length;
            updateModalImage();
            resetAutoSlideTimer();
        }

        function startAutoSlide() {
            // Auto-slide every 2 seconds
            autoSlideInterval = setInterval(nextImage, 2000);
        }

        function stopAutoSlide() {
            if (autoSlideInterval) {
                clearInterval(autoSlideInterval);
                autoSlideInterval = null;
            }
        }

        function resetAutoSlideTimer() {
            stopAutoSlide();
            startAutoSlide();
        }

        // Basic keyboard nav for modal
        document.addEventListener("keydown", function(event) {
            if (!document.getElementById("modal").classList.contains("hidden")) {
                if (event.key === "Escape") {
                    closeModal();
                } else if (event.key === "ArrowRight") {
                    nextImage();
                } else if (event.key === "ArrowLeft") {
                    prevImage();
                }
            }
        });

        // Example trigger function - you can call this on your image clicks
        function showCarousel(startIndex = 0) {
            currentIndex = startIndex;
            openModal(imageSources[startIndex]);
        }
    </script>


@endsection






