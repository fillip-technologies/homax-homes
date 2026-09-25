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
            .hxq-sticky .hxq-card {
                height: var(--hx-panel-h);
                display: flex;
                flex-direction: column;
            }

            /* The project card only has a MINIMUM height. A fixed height with a
               hidden-scrollbar body used to push the Enquire Now button out of
               view whenever the title wrapped or a listing had extra bullets.
               It now grows to fit, so the button is always fully visible. */
            .hx-pcard {
                min-height: var(--hx-panel-h);
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
            .hxq-sticky .hxq-body {
                flex: 1 1 auto;
                min-height: 0;
                overflow-y: auto;
                /* scrollable, but the bar itself is hidden - the panels are a
                   fixed height and a visible track cluttered the gold. */
                scrollbar-width: none;
                -ms-overflow-style: none;
            }

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
                flex: 1 1 auto;
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
            font-size: clamp(19px, 1.5vw, 22px);
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
