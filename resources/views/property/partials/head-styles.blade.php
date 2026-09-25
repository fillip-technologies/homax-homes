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
