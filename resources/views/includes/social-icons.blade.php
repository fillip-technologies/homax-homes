{{-- Social links (Facebook, Instagram, YouTube), moved here from the footer.
     Used in the desktop header (followed by the WhatsApp icon, which reuses
     .hx-social__link / .hx-social__wa) and in the mobile drawer. Inline SVG:
     no requests to a third-party icon host. --}}
@once
        <style>
          /* Brand-coloured round social buttons. */
          .hx-social { display: flex; align-items: center; gap: 8px; }
          .hx-social__link { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 9999px; color: #fff; box-shadow: 0 2px 8px rgba(0, 0, 0, .18); transition: transform .25s ease, box-shadow .25s ease, filter .25s ease; }
          .hx-social__link svg { width: 20px; height: 20px; }
          /* Every glyph is drawn in a 24x24 box with the same padding so all four icons read as one size. */
          .hx-social__link:hover, .hx-social__link:focus-visible { transform: translateY(-3px) scale(1.08); box-shadow: 0 6px 14px rgba(0, 0, 0, .25); filter: brightness(1.1); color: #fff; }
          .hx-social__fb { background: #1877F2; }
          /* The "f" glyph runs almost edge to edge of its box, so shrink it for breathing room inside the circle. */
          .hx-social__fb svg { width: 21px; height: 21px; }
          .hx-social__ig { background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285AEB 90%); }
          .hx-social__yt { background: #FF0000; }
          .hx-social__wa { background: #25D366; }
        </style>
@endonce
        <div class="hx-social {{ $class ?? '' }}">
          <a href="https://www.facebook.com/homaxhomes" target="_blank" rel="noopener noreferrer" aria-label="Facebook"
            class="hx-social__link hx-social__fb">
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path d="M13.5 21.5v-8.2h2.77l.41-3.2H13.5V8.05c0-.93.26-1.56 1.59-1.56h1.7V3.62a22.6 22.6 0 00-2.48-.13c-2.45 0-4.13 1.5-4.13 4.24v2.37H7.4v3.2h2.78v8.2h3.32z"/>
            </svg>
          </a>
          <a href="https://www.instagram.com/homaxhomes" target="_blank" rel="noopener noreferrer" aria-label="Instagram"
            class="hx-social__link hx-social__ig">
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path d="M12 2.16c3.2 0 3.58.01 4.85.07 1.17.05 1.8.25 2.23.41.56.22.96.48 1.38.9.42.42.68.82.9 1.38.16.42.36 1.06.41 2.23.06 1.27.07 1.65.07 4.85s-.01 3.58-.07 4.85c-.05 1.17-.25 1.8-.41 2.23-.22.56-.48.96-.9 1.38-.42.42-.82.68-1.38.9-.42.16-1.06.36-2.23.41-1.27.06-1.65.07-4.85.07s-3.58-.01-4.85-.07c-1.17-.05-1.8-.25-2.23-.41-.56-.22-.96-.48-1.38-.9-.42-.42-.68-.82-.9-1.38-.16-.42-.36-1.06-.41-2.23C2.17 15.58 2.16 15.2 2.16 12s.01-3.58.07-4.85c.05-1.17.25-1.8.41-2.23.22-.56.48-.96.9-1.38.42-.42.82-.68 1.38-.9.42-.16 1.06-.36 2.23-.41C8.42 2.17 8.8 2.16 12 2.16zm0 3.68a6.16 6.16 0 100 12.32 6.16 6.16 0 000-12.32zm0 10.16a4 4 0 110-8 4 4 0 010 8zm7.84-10.4a1.44 1.44 0 11-2.88 0 1.44 1.44 0 012.88 0z"/>
            </svg>
          </a>
          <a href="https://www.youtube.com/@Homax-homes" target="_blank" rel="noopener noreferrer" aria-label="YouTube"
            class="hx-social__link hx-social__yt">
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path d="M23.5 6.19a3.02 3.02 0 00-2.12-2.14C19.5 3.55 12 3.55 12 3.55s-7.5 0-9.38.5A3.02 3.02 0 00.5 6.19C0 8.08 0 12 0 12s0 3.92.5 5.81a3.02 3.02 0 002.12 2.14c1.88.5 9.38.5 9.38.5s7.5 0 9.38-.5a3.02 3.02 0 002.12-2.14C24 15.92 24 12 24 12s0-3.92-.5-5.81zM9.55 15.57V8.43L15.82 12l-6.27 3.57z"/>
            </svg>
          </a>
        </div>
