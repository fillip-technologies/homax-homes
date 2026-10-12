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
            /* .pd-stack is a single-column grid, so every card shares one track.
               Without this, the pricing table's min-width (below) grows that
               track - and every OTHER card with it - wider than the viewport,
               forcing the whole page to scroll horizontally on mobile even
               though .pd-tablewrap already scrolls the table internally. */
            min-width: 0;
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

        /* Rich text from the admin editor (Summernote): lists, links, media. */
        .pd-prose ul,
        .pd-prose ol {
            margin: 0 0 10px;
            padding-left: 22px;
        }

        .pd-prose ul { list-style: disc; }
        .pd-prose ol { list-style: decimal; }

        .pd-prose li {
            list-style: inherit;
            margin-bottom: 6px;
        }

        .pd-prose a { color: var(--pd-indigo); text-decoration: underline; }
        .pd-prose strong, .pd-prose b { color: var(--pd-ink); font-weight: 700; }
        .pd-prose img, .pd-prose video, .pd-prose iframe { max-width: 100%; height: auto; border-radius: 8px; }
        .pd-prose iframe { aspect-ratio: 16 / 9; width: 100%; }
        .pd-prose table { max-width: 100%; display: block; overflow-x: auto; }
        .pd-prose { overflow-wrap: anywhere; }

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
            -webkit-overflow-scrolling: touch;
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
                padding: 16px 14px 18px;
                border-radius: 10px;
            }

            .pd-h {
                font-size: clamp(17px, 5vw, 20px);
                margin-bottom: 12px;
            }

            .pd-h-row {
                gap: 10px;
            }

            .pd-h-row .pd-btn {
                width: 100%;
                justify-content: center;
            }

            /* 2-column highlights grid instead of single column stack */
            .pd-tiles {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
            }

            .pd-tile {
                padding: 12px 8px;
            }

            .pd-tile__ic {
                width: 36px;
                height: 36px;
                font-size: 15px;
                margin-bottom: 6px;
            }

            .pd-tile__t {
                font-size: 11px;
            }

            .pd-tile__d {
                font-size: 13px;
                line-height: 1.25;
            }

            /* The "View Full Layout" ghost panel exists to sit BESIDE the floor
               plan photo on desktop. Stacked on mobile it just doubles the
               section's height with an empty box, which reads as broken and
               makes scrolling past this section feel stuck - shrink it to a
               slim strip instead of matching the photo's full height. */
            .pd-plan--ghost {
                min-height: 0;
            }

            .pd-plan__zoom {
                flex-direction: row;
                padding: 14px;
                font-size: 13.5px;
            }

            .pd-plan__zoom i {
                font-size: 17px;
            }

            /* Table mobile compactness */
            .pd-table th {
                padding: 10px 10px;
                font-size: 12.5px;
            }

            .pd-table td {
                padding: 10px 10px;
                font-size: 13px;
            }

            .pd-chip {
                padding: 5px 9px;
                font-size: 11.5px;
            }

            .pd-table__hint {
                display: flex;
                align-items: center;
                gap: 6px;
                font-size: 11.5px;
                color: var(--pd-muted);
                margin-bottom: 8px;
            }

            /* 2-column amenities grid */
            .pd-amen {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 8px;
            }

            .pd-amen__item {
                padding: 9px 10px;
                font-size: 12.5px;
                gap: 8px;
            }

            .pd-amen__item i {
                font-size: 15px;
                width: 16px;
            }

            /* 2-column photo gallery grid */
            .pd-gal {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 8px;
            }

            .pd-gal__item {
                border-radius: 8px;
                aspect-ratio: 4 / 3;
            }

            /* Location map & places */
            .pd-loc__map,
            .pd-loc__map iframe {
                min-height: 230px;
            }

            .pd-loc__list {
                padding: 14px 12px;
            }

            .pd-loc__list li {
                font-size: 13px;
            }

            /* Virtual tour */
            .pd-tour {
                min-height: 170px;
                padding: 16px;
            }

            .pd-tour__play {
                width: 48px;
                height: 48px;
                font-size: 18px;
            }

            .pd-tour__txt strong {
                font-size: 17px;
            }

            .pd-tour__txt small {
                font-size: 13px;
            }

            /* General buttons */
            .pd-btn {
                padding: 10px 16px;
                font-size: 13.5px;
                min-height: 42px;
            }
        }
    </style>
