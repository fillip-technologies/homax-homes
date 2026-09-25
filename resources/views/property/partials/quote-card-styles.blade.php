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
           margin shorthand at equal specificity and was canceling it. */
        @media (min-width: 1024px) {
            .hx-pcard__price {
                margin-top: auto;
            }
        }
    </style>
