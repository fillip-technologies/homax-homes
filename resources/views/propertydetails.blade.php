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
            --primary: #5146C7;
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

        /* Shadows */
        body {
            font-family: "DM Sans", sans-serif;
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
            font-family: "Aboreto", cursive;
        }

        .glassmorphism {
            background: rgba(255,
                    255,
                    255,
                    0.8);
            /* Changed to white with opacity */
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 0, 0, 0.1);
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
            box-shadow: 0 15px 40px rgba(81, 70, 199, 0.18);
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
            background-color: rgba(81, 70, 199, 0.10);
            /* primary with alpha */
            transition: background-color 0.3s ease;
        }

        .icon-bg-circle:hover {
            background-color: rgba(81, 70, 199, 0.18);
        }

        .btn-primary {
            background-color: #5146C7;
            /* Changed to brand accent */
            color: #ffffff;
            /* Changed to white */
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #4038A8;
            /* Changed to brand hover */
            box-shadow: 0 0 15px rgba(81, 70, 199, 0.28);
            /* Changed to brand accent with opacity */
        }

        .btn-secondary {
            background-color: transparent;
            border: 1px solid #5146C7;
            /* Changed to brand accent */
            color: #5146C7;
            /* Changed to brand accent */
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: rgba(13,
                    148,
                    136,
                    0.1);
            /* Changed to brand accent with opacity */
            color: #4038A8;
            /* Changed to brand hover */
        }
        /* ==================== PREMIUM PROJECT HERO ==================== */
        .hx-hero {
            --hx-indigo: #5146C7;
            --hx-indigo-dark: #4038A8;
            --hx-navy: #17113B;
            --hx-ink: #111827;
            --hx-muted: #5F6472;
            --hx-lav: #F7F6FF;
            --hx-lav2: #E9E7FF;
            --hx-line: #E7E7F0;

            position: relative;
            isolation: isolate;
            overflow: hidden;
            /* Hero typography is a clean sans throughout; the decorative display
               face is reserved for the sections below. */
            --hx-sans: "DM Sans", "Inter", system-ui, -apple-system, "Segoe UI", sans-serif;
            font-family: var(--hx-sans);
            background: linear-gradient(180deg, #FBFBFF 0%, #F3F2FD 46%, #EEECFA 100%);
        }

        .hx-hero__wash {
            position: absolute;
            inset: 0;
            z-index: 1;
            /* Keeps dark text legible over the photo without putting a panel
               behind any one column. */
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .66) 0%, rgba(250, 250, 255, .60) 50%, rgba(244, 243, 253, .70) 100%),
                radial-gradient(900px 420px at 12% -8%, rgba(81, 70, 199, .10), transparent 62%),
                radial-gradient(760px 420px at 88% 6%, rgba(120, 168, 235, .12), transparent 64%);
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
            border: 34px solid rgba(81, 70, 199, .06);
        }

        .hx-hero__glow--b {
            width: 240px;
            height: 240px;
            right: -110px;
            bottom: 60px;
            border: 28px solid rgba(81, 70, 199, .05);
        }

        .hx-hero__inner {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 1560px;
            margin: 0 auto;
            padding: 30px 28px 26px;
        }

        .hx-hero__grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 22px;
            align-items: start;
        }

        @media (min-width: 1024px) {
            .hx-hero__grid {
                /* left | centre (focal) | right - centre is the widest column */
                grid-template-columns: minmax(0, 0.92fr) minmax(0, 1.34fr) minmax(0, 1fr);
                gap: 22px;
            }
        }

        @media (min-width: 1440px) {
            .hx-hero__grid {
                gap: 26px;
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
            color: #4038A8;
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
            font-family: "Aboreto", var(--hx-sans);
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
            font-family: "Aboreto", var(--hx-sans);
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
            background: linear-gradient(180deg, #5A4FD4 0%, var(--hx-indigo) 100%);
            color: #fff;
            box-shadow: 0 8px 20px rgba(81, 70, 199, .24);
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
            background: linear-gradient(180deg, #4F45C2 0%, var(--hx-indigo-dark) 100%);
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(81, 70, 199, .34);
        }

        .hx-btn--secondary {
            background: #fff;
            color: var(--hx-indigo);
            border-color: #D9D5F5;
        }

        .hx-btn--secondary:hover {
            transform: translateY(-2px);
            border-color: var(--hx-indigo);
            box-shadow: 0 10px 22px rgba(81, 70, 199, .16);
        }

        .hx-btn--outline {
            background: #fff;
            color: var(--hx-indigo);
            border-color: var(--hx-line);
        }

        .hx-btn--outline:hover {
            transform: translateY(-2px);
            border-color: var(--hx-indigo);
            box-shadow: 0 10px 20px rgba(81, 70, 199, .14);
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
            box-shadow: 0 8px 16px rgba(81, 70, 199, .14);
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
            background: rgba(23, 17, 59, .80);
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
            background: rgba(23, 17, 59, .3);
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
            background: rgba(23, 17, 59, .78);
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
            font-family: "Aboreto", var(--hx-sans);
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
            box-shadow: 0 0 0 3px rgba(81, 70, 199, .12);
        }

        .hx-field>i {
            width: 44px;
            flex: 0 0 44px;
            text-align: center;
            color: #9A9AB5;
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
            color: #9A9AB5;
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
            color: #9A9AB5;
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
        #gallery {
            scroll-margin-top: calc(var(--hx-header-h, 68px) + 96px);
        }

        /* ---------- section nav ---------- */
        /* Standalone sticky project nav, styled as a floating capsule so it stays
           obvious while content scrolls underneath it. */
        .hx-navbar {
            --hx-indigo: #5146C7;
            --hx-indigo-dark: #4038A8;
            --hx-muted: #5F6472;
            --hx-line: #E7E7F0;
            font-family: "DM Sans", "Inter", system-ui, -apple-system, "Segoe UI", sans-serif;

            position: sticky;
            top: calc(var(--hx-header-h, 68px) + 14px);
            z-index: 35;
            padding: 0 16px 14px;
            /* transparent rail: only the capsule itself is interactive */
            pointer-events: none;
        }

        .hx-navbar__inner {
            pointer-events: auto;
            display: flex;
            justify-content: center;
            max-width: 1560px;
            margin: 0 auto;
            padding: 0;
        }

        .hx-secnav {
            display: flex;
            gap: 4px;
            max-width: 100%;
            overflow-x: auto;
            scrollbar-width: none;
            padding: 7px;
            border-radius: 999px;
            background: #fff;
            border: 1px solid #DFDBF6;
            box-shadow:
                0 18px 42px rgba(17, 24, 39, .16),
                0 2px 0 rgba(81, 70, 199, .06),
                0 0 0 5px rgba(81, 70, 199, .05);
        }

        .hx-secnav::-webkit-scrollbar {
            display: none;
        }

        .hx-secnav__item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            flex: 0 0 auto;
            padding: 12px 20px;
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
            box-shadow: 0 6px 16px rgba(81, 70, 199, .32);
        }

        .hx-secnav__item:hover,
        .hx-secnav__item.is-active {
            background: var(--hx-indigo);
            color: #fff;
        }

        /* ---------- docked enquiry bar ---------- */
        .hx-dock {
            --hx-indigo: #5146C7;
            --hx-indigo-dark: #4038A8;
            --hx-ink: #111827;
            --hx-muted: #5F6472;
            --hx-line: #E7E7F0;
            --hx-lav: #F7F6FF;
            --hx-lav2: #E9E7FF;
            font-family: "DM Sans", "Inter", system-ui, -apple-system, "Segoe UI", sans-serif;

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

            .hx-navbar {
                padding: 0 14px 12px;
            }

            .hx-stage {
                height: clamp(300px, 50vw, 460px);
            }
        }

        @media (max-width: 639px) {
            .hx-hero__inner {
                padding: 20px 14px 20px;
            }

            .hx-navbar {
                padding: 0 10px 10px;
                top: calc(var(--hx-header-h, 68px) + 10px);
            }

            .hx-secnav {
                padding: 6px;
            }

            .hx-secnav__item {
                padding: 10px 16px;
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

        // Only surface specs that actually hold a value.
        $heroSpecs = [];
        if (filled($property->bedrooms)) {
            $heroSpecs[] = ['icon' => 'fa-house-chimney', 'label' => 'Configuration', 'value' => $property->bedrooms . ' BHK'];
        }
        if (filled($property->property_type)) {
            $heroSpecs[] = ['icon' => 'fa-building', 'label' => 'Property Type', 'value' => $property->property_type];
        }
        if (filled($property->availability)) {
            $heroSpecs[] = ['icon' => 'fa-calendar-check', 'label' => 'Availability', 'value' => $property->availability];
        }
        if (filled($property->rera_id)) {
            $heroSpecs[] = ['icon' => 'fa-shield-halved', 'label' => 'RERA ID', 'value' => $property->rera_id];
        } elseif (filled($property->super_area) && (float) $property->super_area > 1) {
            $heroSpecs[] = ['icon' => 'fa-ruler-combined', 'label' => 'Super Area', 'value' => $property->super_area . ' sq.ft'];
        }

        $heroImage = $featuredImage ? asset($featuredImage->image_path) : asset('assets/images/home.png');
        $heroTotalImages = count($propertyimagesall);
    @endphp

    <section class="hx-hero">
        <div class="hx-hero__photo" style="background-image:url('{{ $heroImage }}')" aria-hidden="true"></div>
        <div class="hx-hero__wash" aria-hidden="true"></div>
        <div class="hx-hero__glow hx-hero__glow--a" aria-hidden="true"></div>
        <div class="hx-hero__glow hx-hero__glow--b" aria-hidden="true"></div>

        <div class="hx-hero__inner">
            <div class="hx-hero__grid">

                {{-- ------------------------- LEFT : project info ------------------------- --}}
                <div class="hx-col hx-col--info">
                    <div class="hx-badges">
                        @if (filled($property->property_status))
                            <span class="hx-chip hx-chip--ok"><span class="hx-dot"></span>{{ $property->property_status }}</span>
                        @endif
                        @if ($property->is_verified)
                            <span class="hx-chip hx-chip--verified"><i class="fa-solid fa-circle-check"></i>Verified Listing</span>
                        @endif
                    </div>

                    @if ($heroEyebrow)
                        <p class="hx-eyebrow"><span class="hx-eyebrow__rule"></span>{{ $heroEyebrow }}</p>
                    @endif

                    <h1 class="hx-title">{{ $property->title }}</h1>

                    @if ($heroLocation || $property->address)
                        <p class="hx-loc">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>{{ $heroLocation ?: $property->address }}</span>
                        </p>
                    @endif

                    @if ($heroBlurb)
                        <p class="hx-blurb">{{ $heroBlurb }}</p>
                    @endif

                    <div id="price" class="hx-card hx-price-card">
                        @if ($priceDisplay)
                            <div class="hx-price-row">
                                <div>
                                    <span class="hx-label">Starting From</span>
                                    <span class="hx-price">{{ $priceUnit }} {{ $priceDisplay }}</span>
                                </div>
                                @if ($property->listing_type)
                                    <span class="hx-chip hx-chip--soft">{{ $property->listing_type }}</span>
                                @endif
                            </div>
                        @endif

                        @if (count($heroSpecs))
                            <div class="hx-specs">
                                @foreach ($heroSpecs as $spec)
                                    <div class="hx-spec">
                                        <i class="fa-solid {{ $spec['icon'] }}"></i>
                                        <span class="hx-spec__v">{{ $spec['value'] }}</span>
                                        <span class="hx-spec__l">{{ $spec['label'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="hx-cta">
                            <a href="#enquiry" class="hx-btn hx-btn--primary">
                                Enquire Now <i class="fa-solid fa-arrow-right hx-btn__arrow"></i>
                            </a>
                            <a href="#enquiry" class="hx-btn hx-btn--secondary">
                                <i class="fa-regular fa-calendar-check"></i> Book a Site Visit
                            </a>
                        </div>

                        <div class="hx-mini-row">
                            @if ($property->brochure)
                                <a href="{{ url($property->brochure) }}" download class="hx-btn hx-btn--mini">
                                    <i class="fa-solid fa-file-arrow-down"></i>Download Brochure
                                </a>
                            @endif
                            <a href="tel:+11234567892" class="hx-btn hx-btn--mini">
                                <i class="fa-solid fa-phone"></i>Call Us
                            </a>
                            <a href="https://wa.me/919999999999" target="_blank" rel="noopener noreferrer"
                                class="hx-btn hx-btn--mini hx-btn--wa">
                                <i class="fa-brands fa-whatsapp"></i>WhatsApp
                            </a>
                        </div>
                    </div>
                </div>

                {{-- ------------------------- CENTER : existing gallery ------------------------- --}}
                <div class="hx-col hx-col--media">
                    <div id="gallery" class="hx-gallery">
                        <div id="bigimage" class="hx-stage" onclick="openModal(currentBigImageSrc)">
                            <img id="bigImageDisplay1" src="{{ $heroImage }}" alt="{{ $property->title }}"
                                class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 ease-in-out" />
                            <img id="bigImageDisplay2" src="" alt=""
                                class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 ease-in-out opacity-0" />

                            @if ($heroTotalImages > 1)
                                <button type="button" onclick="prevBigImage(event)" aria-label="Previous image"
                                    class="hx-nav hx-nav--prev"><i class="fa-solid fa-arrow-left"></i></button>
                                <button type="button" onclick="nextBigImage(event)" aria-label="Next image"
                                    class="hx-nav hx-nav--next"><i class="fa-solid fa-arrow-right"></i></button>
                                <span id="heroImgCounter" class="hx-counter">1 / {{ $heroTotalImages }}</span>
                            @endif

                            @if ($heroTotalImages > 1)
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
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ------------------------- RIGHT : existing enquiry form ------------------------- --}}
                <div class="hx-col hx-col--form">
                    <div id="enquiry" class="hx-card hx-enquiry">
                        <span class="hx-enquiry__eyebrow">LET&rsquo;S TALK<i></i></span>
                        <h2 class="hx-enquiry__title">Interested in this Property?</h2>
                        <p class="hx-enquiry__sub">Get project details, latest offers and site visit assistance from our
                            team.</p>

                        @if (session('success'))
                            <div class="hx-alert">{{ session('success') }}</div>
                        @endif

                        {{-- Same route, method, field names, validation and reCAPTCHA as before. --}}
                        <form action="{{ route('property.inquiry.store', $property->id) }}" method="POST" class="hx-form">
                            @csrf

                            <div class="hx-field">
                                <label for="name" class="sr-only">Full Name</label>
                                <i class="fa-regular fa-user"></i>
                                <input type="text" id="name" name="name" required placeholder="Full Name"
                                    value="{{ old('name') }}" />
                            </div>
                            @error('name')
                                <p class="hx-err">{{ $message }}</p>
                            @enderror

                            <div class="hx-field">
                                <label for="phone" class="sr-only">Mobile Number</label>
                                <i class="fa-solid fa-phone"></i>
                                <input type="tel" id="phone" name="phone" required placeholder="Mobile Number"
                                    value="{{ old('phone') }}" />
                            </div>
                            @error('phone')
                                <p class="hx-err">{{ $message }}</p>
                            @enderror

                            <div class="hx-field">
                                <label for="email" class="sr-only">Email Address</label>
                                <i class="fa-regular fa-envelope"></i>
                                <input type="email" id="email" name="email" placeholder="Email Address"
                                    value="{{ old('email') }}" />
                            </div>
                            @error('email')
                                <p class="hx-err">{{ $message }}</p>
                            @enderror

                            <div class="hx-field hx-field--area">
                                <label for="message" class="sr-only">Message</label>
                                <i class="fa-regular fa-comment-dots"></i>
                                <textarea id="message" name="message" rows="2" placeholder="I&rsquo;m interested in this property...">{{ old('message') }}</textarea>
                            </div>
                            @error('message')
                                <p class="hx-err">{{ $message }}</p>
                            @enderror

                            <label for="terms" class="hx-terms">
                                <input id="terms" name="terms" type="checkbox" required {{ old('terms') ? 'checked' : '' }} />
                                <span>I agree to the <a href="#">terms and conditions</a></span>
                            </label>
                            @error('terms')
                                <p class="hx-err">{{ $message }}</p>
                            @enderror

                            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                            @error('g-recaptcha-response')
                                <p class="hx-err">{{ $message }}</p>
                            @enderror

                            <button type="submit" class="hx-btn hx-btn--primary hx-btn--block">
                                Get Project Details <i class="fa-solid fa-arrow-right hx-btn__arrow"></i>
                            </button>
                        </form>

                        <div class="hx-or"><span>OR</span></div>

                        <div class="hx-contact-row">
                            <a href="tel:+11234567892" class="hx-btn hx-btn--outline">
                                <i class="fa-solid fa-phone"></i>Call Us
                            </a>
                            <a href="https://wa.me/919999999999" target="_blank" rel="noopener noreferrer"
                                class="hx-btn hx-btn--whatsapp">
                                <i class="fa-brands fa-whatsapp"></i>WhatsApp
                            </a>
                        </div>

                        <ul class="hx-trust">
                            <li><i class="fa-solid fa-shield-halved"></i><b>Best Price</b><span>Assurance</span></li>
                            <li><i class="fa-regular fa-calendar-check"></i><b>Free Site</b><span>Visit</span></li>
                            <li><i class="fa-solid fa-headset"></i><b>Expert</b><span>Guidance</span></li>
                        </ul>
                    </div>
                </div>
            </div>


        </div>

        <a href="#overview" class="hx-scroll" aria-label="Scroll to explore">
            <span class="hx-scroll__ring"><i class="fa-solid fa-arrow-down"></i></span>
            <span class="hx-scroll__txt">Scroll to explore</span>
        </a>
    </section>

    {{-- Project section navigation. Its own section (not part of the hero) and
         sticky beneath the site header once the hero scrolls past. --}}
    <div class="hx-navbar">
        <div class="hx-navbar__inner">
                <nav class="hx-secnav" aria-label="Project sections">
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
                    <a href="{{ $property->video_url }}" target="_blank" rel="noopener noreferrer"
                        class="hx-secnav__item"><i class="fa-solid fa-video"></i>Virtual Tour</a>
                @endif
                @if ($property->brochure)
                    <a href="{{ url($property->brochure) }}" download class="hx-secnav__item"><i
                            class="fa-solid fa-download"></i>Brochure</a>
                @endif
                </nav>
        </div>
    </div>

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
                <a href="#enquiry" class="hx-btn hx-btn--primary hx-dock__cta">
                    Enquire Now <i class="fa-solid fa-arrow-right hx-btn__arrow"></i>
                </a>
            </div>
        </div>
    </div>

    <script>
        // The site header is sticky at top:0 and its height can change with the
        // viewport, so measure it and let the project nav park right beneath it.
        (function () {
            var header = document.querySelector('header');
            if (!header) return;

            function syncHeaderHeight() {
                var h = Math.round(header.getBoundingClientRect().height);
                if (h > 0) document.documentElement.style.setProperty('--hx-header-h', h + 'px');
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

        </script>

        <!-- Property Details and Highlights (Restyled) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">
            <!-- Main Details Column -->
            <div class="lg:col-span-3 space-y-10">
                <!-- Brochure Download Section -->
                <div class="mt-6">
                    <a href="{{ $property->brochure ? url($property->brochure) : '#' }}" download
                        class="inline-flex items-center btn-primary text-white px-6 py-3 rounded-lg shadow-md transition-all duration-300"
                        style="background-color: #5146C7; color: #FFFFFF;">
                        <i class="fa-solid fa-file-arrow-down mr-2 text-lg"></i>
                        Download Brochure (PDF)
                    </a>
                </div>

                <section id="overview" class="bg-brand-light p-8 rounded-xl shadow-property animated-element animate-slide-in-left"
                    style="animation-delay: 0.6s">
                    <h3 class="font-display text-2xl font-bold text-textClr-primary flex items-center mb-6">
                        <i class="fa-solid fa-clipboard-list text-brand-primary mr-3"></i>Property Overview
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6 text-textClr-secondary">

                        @if ($property->property_type)
                            <div class="flex items-center space-x-3">
                                <div class="icon-bg-circle rounded-full px-4 py-3">
                                    <i class="fa-solid fa-building text-brand-primary text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm">Property Type</p>
                                    <p class="font-semibold text-textClr-primary text-md">{{ $property->property_type }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if ($property->super_area)
                            <div class="flex items-center space-x-3">
                                <div class="icon-bg-circle rounded-full px-4 py-3">
                                    <i class="fa-solid fa-chart-area text-brand-primary text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm">Super Area</p>
                                    <p class="font-semibold text-textClr-primary text-md">{{ $property->super_area }}
                                        sq.ft.</p>
                                </div>
                            </div>
                        @endif

                        @if ($property->furnishing)
                            <div class="flex items-center space-x-3">
                                <div class="icon-bg-circle rounded-full p-3">
                                    <i class="fa-solid fa-couch text-brand-primary text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm">Furnishing</p>
                                    <p class="font-semibold text-textClr-primary text-md">{{ $property->furnishing }}</p>
                                </div>
                            </div>
                        @endif

                        {{-- @if ($property->preferred_tenants)
                            <div class="flex items-center space-x-3">
                                <div class="icon-bg-circle rounded-full py-3 px-3">
                                    <i class="fa-solid fa-users text-brand-primary text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm">Preferred Tenants</p>
                                    <p class="font-semibold text-textClr-primary text-md">
                                        {{ $property->preferred_tenants }}</p>
                                </div>
                            </div>
                        @endif --}}

                        @if ($property->availability)
                            <div class="flex items-center space-x-3">
                                <div class="icon-bg-circle rounded-full px-4 py-3">
                                    <i class="fa-solid fa-calendar-check text-brand-primary text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm">Availability</p>
                                    <p class="font-semibold text-textClr-primary text-md">{{ $property->availability }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if ($property->year_built)
                            <div class="flex items-center space-x-3">
                                <div class="icon-bg-circle rounded-full py-3 px-4">
                                    <i class="fa-solid fa-calendar-alt text-brand-primary text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm">Year Built</p>
                                    <p class="font-semibold text-textClr-primary text-md">{{ $property->year_built }}</p>
                                </div>
                            </div>
                        @endif
                        @if ($property->rera_id)
                            <div class="flex items-center space-x-3">
                                <div class="icon-bg-circle rounded-full py-3 px-4">
                                  <i class="fa-solid fa-id-badge text-brand-primary text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm">RERA ID</p>
                                    <p class="font-semibold text-textClr-primary text-md">{{ $property->rera_id }}</p>
                                </div>
                            </div>
                        @endif

                    </div>
                </section>


                <!-- Description -->
                <section class="bg-brand-light p-8 rounded-xl shadow-property animated-element animate-slide-in-left"
                    style="animation-delay: 0.7s">
                    <h3 class="font-display text-2xl font-bold text-textClr-primary flex items-center mb-6">
                        <i class="fa-solid fa-align-left text-brand-primary mr-3"></i>Detailed Description
                    </h3>
                    <div class="space-y-4 text-textClr-secondary leading-relaxed">
                        <p>
                            {!! $property->description !!}
                        </p>

                    </div>
                </section>
                <!-- Key Features -->
                <section class="bg-brand-light p-8 rounded-xl shadow-property animated-element animate-slide-in-left"
                    style="animation-delay: 0.7s">
                    <h3 class="font-display text-2xl font-bold text-textClr-primary flex items-center mb-6">
                        <i class="fa-solid fa-list-check text-brand-primary mr-3"></i>Key Features
                    </h3>
                    <ul class="space-y-3 text-textClr-secondary leading-relaxed list-none">
                        {!! $property->keyfeatures !!}
                    </ul>
                </section>


                @php
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
                        'Dishwasher' => 'fa-dishwasher',
                        'Balcony' => 'fa-mountain-sun',
                    ];
                @endphp

                @if (!empty($property->features) || !empty($property->amenities))
                    <section id="amenities" class="bg-brand-light p-8 rounded-xl shadow-property animated-element animate-slide-in-left"
                        style="animation-delay: 0.8s">
                        <h3 class="font-display text-2xl font-bold text-textClr-primary flex items-center mb-8">
                            <i class="fa-solid fa-stars text-brand-primary mr-3"></i>Amenities & Features
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-2 gap-y-3 text-textClr-secondary">
                            @foreach (array_merge($property->features ?? [], $property->amenities ?? []) as $item)
                                @if (!empty($item))
                                    <div class="flex items-center space-x-3 group">
                                        <i
                                            class="fa-solid {{ $iconMap[$item] ?? 'fa-circle-question' }} text-brand-primary text-xl group-hover:animate-pulse"></i>
                                        <span>{{ $item }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </section>
                @endif


                <!-- Location -->
                <section id="location" class="bg-brand-light p-8 rounded-xl shadow-property animated-element animate-slide-in-left"
                    style="animation-delay: 0.9s">
                    <h3 class="font-display text-2xl font-bold text-textClr-primary flex items-center mb-6">
                        <i class="fa-solid fa-map-marker-alt text-brand-primary mr-3"></i>Location & Neighborhood
                    </h3>

                    <div class="rounded-lg overflow-hidden shadow-lg mb-8">
                        @php
                            $mapUrl = null;
                            $defaultCity = $property->city;
                            $apiKey = 'AIzaSyAfS-bCjy7PCM5Z-79SZyaMJNgBByvzN6o'; // Replace with your actual API key

                            if (!empty($property->google_map_link)) {
                                if (strpos($property->google_map_link, 'embed') !== false) {
                                    // Use embed link as-is
                                    $mapUrl = $property->google_map_link;
                                } elseif (
                                    preg_match('/@([\-0-9.]+),([\-0-9.]+)/', $property->google_map_link, $matches)
                                ) {
                                    // Extract lat/lng from standard Google Maps URL
                                    $lat = $matches[1];
                                    $lng = $matches[2];
                                    $mapUrl = "https://www.google.com/maps/embed/v1/view?key={$apiKey}&center={$lat},{$lng}&zoom=14";
                                } else {
                                    // Fallback if URL format is unknown: use it as a search query
                                    $mapUrl =
                                        "https://www.google.com/maps/embed/v1/search?key={$apiKey}&q=" .
                                        urlencode($property->google_map_link);
                                }
                            } elseif (!empty($property->address)) {
                                $mapUrl =
                                    "https://www.google.com/maps/embed/v1/place?key={$apiKey}&q=" .
                                    urlencode($property->address);
                            } else {
                                $mapUrl =
                                    "https://www.google.com/maps/embed/v1/place?key={$apiKey}&q=" .
                                    urlencode($defaultCity);
                            }
                        @endphp

                        @if ($mapUrl)
                            <iframe src="{{ $mapUrl }}" width="100%" height="400" style="border:0"
                                allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                                class="rounded-lg"></iframe>
                        @else
                            <div class="text-center text-gray-500 py-16">
                                <i class="fa-solid fa-map-location-dot fa-2x mb-3"></i>
                                <p>No map location available</p>
                            </div>
                        @endif
                    </div>

                    <!--Notes -->

                    <section class="bg-brand-light p-8 rounded-xl  animated-element animate-slide-in-left"
                        style="animation-delay: 0.7s">
                        <h3 class="font-display text-2xl font-bold text-textClr-primary flex items-center mb-6">
                            <i class="fa-solid fa-list-check text-brand-primary mr-3"></i>Notes
                        </h3>
                        <ul class="space-y-3 text-textClr-secondary leading-relaxed list-none">
                            {!! $property->notes !!}
                        </ul>
                    </section>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                        <!-- Nearby Places -->
                        <div class="bg-brand-dark p-6 rounded-lg">
                            <h4 class="font-semibold text-textClr-primary mb-3">Nearby Places</h4>
                            <ul class="space-y-2 text-textClr-secondary">
                                @if ($property->bazar_distance_km)
                                    <li class="flex items-center">
                                        <i class="fa-solid fa-shopping-cart text-brand-secondary mr-2"></i>
                                        {{ $property->bazar_distance_km }}
                                    </li>
                                @endif
                                @if ($property->hospital_distance_km)
                                    <li class="flex items-center">
                                        <i class="fa-solid fa-hospital text-brand-secondary mr-2"></i>
                                        {{ $property->hospital_distance_km }}
                                    </li>
                                @endif
                                @if ($property->school_distance_km)
                                    <li class="flex items-center">
                                        <i class="fa-solid fa-school text-brand-secondary mr-2"></i>
                                        {{ $property->school_distance_km }}
                                    </li>
                                @endif
                            </ul>
                        </div>

                        <!-- Connectivity -->
                        <div class="bg-brand-dark p-6 rounded-lg">
                            <h4 class="font-semibold text-textClr-primary mb-3">Connectivity</h4>
                            <ul class="space-y-2 text-textClr-secondary">
                                @if ($property->bus_stand_distance_km)
                                    <li class="flex items-center">
                                        <i class="fa-solid fa-bus text-brand-secondary mr-2"></i>
                                        {{ $property->bus_stand_distance_km }}
                                    </li>
                                @endif
                                @if ($property->junction_distance_km)
                                    <li class="flex items-center">
                                        <i class="fa-solid fa-train text-brand-secondary mr-2"></i>
                                        {{ $property->junction_distance_km }}
                                    </li>
                                @endif
                                @if ($property->airport_distance_km)
                                    <li class="flex items-center">
                                        <i class="fa-solid fa-plane text-brand-secondary mr-2"></i>
                                        {{ $property->airport_distance_km }}
                                    </li>
                                @endif
                            </ul>
                        </div>

                    </div>
                </section>

            </div>

            <!-- Sidebar Column (Contact Agent) -->
        </div>
    </div>

    <!-- Modal -->
    <div id="modal" class="fixed inset-0 bg-black bg-opacity-100 flex justify-center items-center hidden z-50 p-4">
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
                <!-- Card 1 -->
                <div
                    class="property-card bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-500 hover:shadow-2xl hover:-translate-y-2">
                    <div class="relative h-60 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1568605114967-8130f3a36994" alt="Luxury Condo"
                            class="w-full h-full object-cover transition-transform duration-700 hover:scale-110" />
                        <div class="absolute top-4 left-4 flex flex-col space-y-2">
                            <span class="bg-primary text-white text-xs font-semibold px-3 py-1 rounded-full animate-pulse">
                                Featured
                            </span>
                            <span class="bg-red-500 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                Hot Deal
                            </span>
                        </div>
                        <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm rounded-full p-2 shadow-md">
                            <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-xl font-bold text-gray-800">Luxury Sea View Condo</h3>
                            <span class="bg-primary/10 text-primary text-xs font-medium px-2.5 py-0.5 rounded">New</span>
                        </div>
                        <p class="text-sm text-gray-500 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Bandra West, Mumbai
                        </p>
                        <div class="flex items-center text-sm text-gray-600 mb-4 space-x-4">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                    </path>
                                </svg>
                                3 BHK
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                2023
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4">
                                    </path>
                                </svg>
                                1800 sqft
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-primary">₹2.75 Cr</span>
                            <a href="{{ route('propertydetails.index') }}"
                                class="text-sm bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-md transition-colors duration-300 flex items-center">
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

                <!-- Card 2 -->
                <div
                    class="property-card bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-500 hover:shadow-2xl hover:-translate-y-2">
                    <div class="relative h-60 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c" alt="Modern Villa"
                            class="w-full h-full object-cover transition-transform duration-700 hover:scale-110" />
                        <div class="absolute top-4 left-4">
                            <span class="bg-blue-600 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                Verified
                            </span>
                        </div>
                        <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm rounded-full p-2 shadow-md">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-xl font-bold text-gray-800">Modern Luxury Villa</h3>
                            <span
                                class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">Premium</span>
                        </div>
                        <p class="text-sm text-gray-500 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Whitefield, Bangalore
                        </p>
                        
                        <div class="flex items-center text-sm text-gray-600 mb-4 space-x-4">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                    </path>
                                </svg>
                                4 BHK
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                2022
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4">
                                    </path>
                                </svg>
                                3200 sqft
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-primary">₹4.2 Cr</span>
                            <a href="{{ route('propertydetails.index') }}"
                                class="text-sm bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-md transition-colors duration-300 flex items-center">
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

                <!-- Card 3 -->
                <div
                    class="property-card bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-500 hover:shadow-2xl hover:-translate-y-2">
                    <div class="relative h-60 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1605276374104-dee2a0ed3cd6" alt="Penthouse"
                            class="w-full h-full object-cover transition-transform duration-700 hover:scale-110" />
                        <div class="absolute top-4 left-4">
                            <span class="bg-purple-600 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                Luxury
                            </span>
                        </div>
                        <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm rounded-full p-2 shadow-md">
                            <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-xl font-bold text-gray-800">Skyline Penthouse</h3>
                            <span
                                class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded">Exclusive</span>
                        </div>
                        <p class="text-sm text-gray-500 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Worli, Mumbai
                        </p>
                        <div class="flex items-center text-sm text-gray-600 mb-4 space-x-4">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                    </path>
                                </svg>
                                5 BHK
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                2021
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4">
                                    </path>
                                </svg>
                                4500 sqft
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-primary">₹8.5 Cr</span>
                            <a href="{{ route('propertydetails.index') }}"
                                class="text-sm bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-md transition-colors duration-300 flex items-center">
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

                <!-- Card 4 -->
                <div
                    class="property-card bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-500 hover:shadow-2xl hover:-translate-y-2">
                    <div class="relative h-60 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1582268611958-ebfd161ef9cf" alt="Family Home"
                            class="w-full h-full object-cover transition-transform duration-700 hover:scale-110" />
                        <div class="absolute top-4 left-4">
                            <span class="bg-green-600 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                Family Home
                            </span>
                        </div>
                        <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm rounded-full p-2 shadow-md">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-xl font-bold text-gray-800">Spacious Family Home</h3>
                            <span
                                class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Popular</span>
                        </div>
                        <p class="text-sm text-gray-500 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Gurgaon, Delhi NCR
                        </p>
                        <div class="flex items-center text-sm text-gray-600 mb-4 space-x-4">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                    </path>
                                </svg>
                                3 BHK
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                2020
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4">
                                    </path>
                                </svg>
                                2100 sqft
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-primary">₹1.85 Cr</span>
                            <a href="{{ route('propertydetails.index') }}"
                                class="text-sm bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-md transition-colors duration-300 flex items-center">
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
            </div>

            <!-- View All Button -->
            <div class="text-center mt-12">
                <button
                    class="bg-white border-2 border-primary text-primary hover:bg-primary hover:text-white px-8 py-3 rounded-full font-semibold transition-all duration-300 transform hover:scale-105 shadow-md">
                    View All Properties
                </button>
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
            }, 3000);
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






