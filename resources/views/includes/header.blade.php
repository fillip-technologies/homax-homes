{{-- Sticky everywhere. On a project page this header carries the project's
     section links (see the @yield below), so it has to stay on screen - that
     is the job the floating pill nav used to do. --}}
<header class="bg-white shadow-md sticky top-0 z-50 backdrop-blur-sm bg-white/90">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-3">
            <!-- Logo with animation -->
            <div
                class="text-3xl font-extrabold text-[#DAA520] cursor-pointer transform hover:scale-105 transition duration-300">
                <a href="/" class="flex items-center gap-3" aria-label="Homax Homes">
                    <span
                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#DAA520] text-white text-xl font-bold leading-none">H</span>
                    <span class="flex flex-col leading-none">
                        <span class="text-[22px] font-extrabold tracking-wide text-[#111827]">HOMAX</span>
                        <span class="text-[12px] font-semibold tracking-[0.28em] text-[#DAA520]">HOMES</span>
                    </span>
                </a>
            </div>

            <!-- Navigation Links (Desktop) -->
            <nav class="hidden lg:flex items-center gap-1">
                @php
                $navItems = [
                'Home' => ['url' => '/', 'dropdown' => null],
                'Projects' => [
                'url' => '#',
                'dropdown' => [
                'Residential Project' => route('property.search', [
                'search' => '',
                'property_type' => 'Residential Flat',
                ]),
                'Commercial Project' => route('property.search', [
                'search' => '',
                'property_type' => 'Commercial',
                ]),
                ],
                ],
                'Buy' => [
                'url' => '#',
                'dropdown' => [
                'New Properties' => route('property.search', [
                'search' => '',
                'property_type' => '',
                'sort' => 'newest',
                ]),
                'Resale Properties' => route('property.search', [
                'search' => '',
                'property_type' => '',
                'listing_type' => 'For Resale',
                ]),
                ],
                ],


                'Blog' => [
                'url' => '/#blog',
                'dropdown' => null,
                ],
                'Careers' => [
                'url' => '/careers',
                'dropdown' => [
                'Join Us' => '/join-us',
                'Associates Us' => '/associates-us',
                ],
                ],
                'About' => [
                'url' => '#',
                'dropdown' => [
                'About Us' => '/about-us',
                'Our Team' => '/our-team',
                'Leadership' => '/leadership',

                // 'Testimonials' => '/testimonials',
                ],
                ],
                'Contact' => ['url' => '/contact', 'dropdown' => null],
                ];
                @endphp

                @if (request()->routeIs('property.show'))
                    {{-- A project page swaps the site menu for that project's own
                         section links, which the page supplies via
                         @section('headerNav'). $navItems is still built above
                         because the mobile drawer further down renders it. --}}
                    @yield('headerNav')
                @else
                @foreach ($navItems as $label => $item)
                <div class="relative group">
                    @if ($item['dropdown'])
                    {{-- Was an <a> nested inside a <button> (invalid, and href="#"
                                 jumped the page on click). Render one element, not two. --}}
                    @php $hasUrl = $item['url'] && $item['url'] !== '#'; @endphp
                    <{{ $hasUrl ? 'a' : 'button' }}
                        @if ($hasUrl) href="{{ $item['url'] }}" @else type="button" aria-haspopup="true" aria-expanded="false" @endif
                        class="flex items-center px-4 py-3 text-[#111827] font-medium hover:text-[#DAA520] transition-colors duration-300">
                        {{ $label }}
                        <svg class="w-4 h-4 ml-1 transform group-hover:rotate-180 transition-transform duration-300"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </{{ $hasUrl ? 'a' : 'button' }}>
                    @else
                    <a href="{{ $item['url'] }}"
                        class="flex items-center px-4 py-3 text-[#111827] font-medium hover:text-[#DAA520] transition-colors duration-300">
                        {{ $label }}
                    </a>
                    @endif

                    @if ($item['dropdown'])
                    <div
                        class="absolute left-0 mt-1 w-56 origin-top-right scale-95 opacity-0 invisible group-hover:scale-100 group-hover:opacity-100 group-hover:visible transition-all duration-200 transform-gpu">
                        <div class="bg-white rounded-lg shadow-xl py-2 ring-1 ring-[#000080]/5">
                            @foreach ($item['dropdown'] as $dropdownLabel => $dropdownUrl)
                            <a href="{{ $dropdownUrl }}"
                                class="block px-4 py-2 text-gray-700 hover:bg-[#E6E9FF] hover:text-[#DAA520] transition-colors duration-200">{{ $dropdownLabel }}</a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
                @endif
            </nav>

            <!-- Action Buttons (Desktop) -->
            <div class="hidden lg:flex items-center gap-3">

                <!-- WhatsApp Button -->
                <a href="https://wa.me/919999999999" target="_blank" rel="noopener noreferrer"
                    class="relative px-6 py-2.5 bg-green-500 text-white font-medium rounded-full shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105 group overflow-hidden">
                    <span class="relative z-10">WhatsApp Us</span>
                </a>

            </div>


            <!-- Mobile Menu Button -->
            <div class="lg:hidden flex items-center">
                <button id="mobile-menu-button" class="text-[#111827] focus:outline-none" aria-label="Open mobile menu"
                    aria-controls="mobile-menu" aria-expanded="false">
                    <svg id="mobile-menu-icon" class="w-6 h-6 transition-transform duration-300" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path id="mobile-menu-icon-path" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    @if (request()->routeIs('property.show'))
        {{-- Below lg the desktop nav is hidden, so the project's sections would
             disappear with it. Give them their own scrollable row instead - the
             pill nav used to scroll horizontally here for the same reason. --}}
        <div class="lg:hidden border-t border-[#EFE8D8] bg-white/95">
            <div class="hx-hdrnav" aria-label="Project sections">
                @yield('headerNav')
            </div>
        </div>
    @endif

</header>

{{-- The drawer must live OUTSIDE <header>: the header has backdrop-blur, and a
     backdrop-filter creates a containing block for position:fixed descendants, so
     the off-canvas drawer was anchored to the header and stretched the page to
     725px wide at a 390px viewport. --}}
<div id="mobile-menu-overlay"
    class="fixed inset-0 z-40 hidden bg-[#000030]/45 opacity-0 transition-opacity duration-300 lg:hidden"></div>

<!-- Mobile Drawer Menu -->
<div id="mobile-menu"
    class="fixed right-0 top-0 z-50 flex h-screen w-[86vw] max-w-[360px] translate-x-full flex-col bg-white shadow-2xl transition-transform duration-300 ease-out lg:hidden">
    <div class="flex shrink-0 items-center justify-between border-b border-[#E7E7F0] px-5 py-4">
        <a href="/" class="flex items-center gap-3" aria-label="Homax Homes">
            <span
                class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#DAA520] text-white text-xl font-bold leading-none">H</span>
            <span class="flex flex-col leading-none">
                <span class="text-[20px] font-extrabold tracking-wide text-[#111827]">HOMAX</span>
                <span class="text-[11px] font-semibold tracking-[0.28em] text-[#DAA520]">HOMES</span>
            </span>
        </a>
        <button id="mobile-menu-close" class="text-[#111827] focus:outline-none" aria-label="Close mobile menu">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    <div class="flex-1 overflow-y-auto overscroll-contain px-5 py-4">
        <nav class="flex flex-col space-y-1">
            @foreach ($navItems as $label => $item)
            <div class="mobile-nav-item">
                @if ($item['dropdown'])
                {{-- An <a> nested inside a <button> is invalid and made the label
                                 navigate instead of expanding. Split into link + toggle. --}}
                <div class="flex items-center justify-between w-full">
                    @if ($item['url'] && $item['url'] !== '#')
                    <a href="{{ $item['url'] }}"
                        class="flex-1 py-3 px-2 text-[#111827] font-medium hover:text-[#DAA520] transition-colors duration-200">{{ $label }}</a>
                    <button type="button" aria-expanded="false"
                        aria-label="Toggle {{ $label }} submenu"
                        class="mobile-dropdown-toggle flex h-11 w-11 shrink-0 items-center justify-center rounded-lg text-[#111827] hover:text-[#DAA520] hover:bg-[#F2F4FF] transition-colors duration-200">
                        <svg class="w-4 h-4 transform transition-transform duration-200" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    @else
                    <button type="button" aria-expanded="false"
                        class="mobile-dropdown-toggle flex flex-1 items-center justify-between py-3 px-2 text-[#111827] font-medium hover:text-[#DAA520] transition-colors duration-200">
                        <span>{{ $label }}</span>
                        <svg class="w-4 h-4 transform transition-transform duration-200" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    @endif
                </div>
                @else
                <a href="{{ $item['url'] }}"
                    class="flex items-center justify-between w-full py-3 px-2 text-[#111827] font-medium hover:text-[#DAA520] transition-colors duration-200">
                    {{ $label }}
                </a>
                @endif

                @if ($item['dropdown'])
                <div class="mobile-dropdown hidden pl-4">
                    @foreach ($item['dropdown'] as $dropdownLabel => $dropdownUrl)
                    <a href="{{ $dropdownUrl }}"
                        class="block py-2 px-2 text-[#5F6472] hover:text-[#DAA520] transition-colors duration-200">{{ $dropdownLabel }}</a>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach

            <div class="pt-4 mt-2 border-t border-gray-100">
                <a href="https://wa.me/919999999999" target="_blank" rel="noopener noreferrer"
                    class="block w-full px-4 py-2.5 mt-2 text-center bg-green-500 text-white font-medium rounded-lg hover:bg-green-600 transition-colors duration-200">
                    <span class="relative z-10">WhatsApp Us</span>
                </a>


            </div>
        </nav>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileMenuClose = document.getElementById('mobile-menu-close');
        const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
        const mobileMenuIconPath = document.getElementById('mobile-menu-icon-path');

        function openMobileMenu() {
            mobileMenu.classList.remove('translate-x-full');
            mobileMenuOverlay.classList.remove('hidden');
            requestAnimationFrame(() => {
                mobileMenuOverlay.classList.remove('opacity-0');
                mobileMenuOverlay.classList.add('opacity-100');
            });
            document.body.classList.add('overflow-hidden');
            mobileMenuButton.setAttribute('aria-expanded', 'true');
            mobileMenuIconPath.setAttribute('d', 'M6 18L18 6M6 6l12 12');
        }

        function closeMobileMenu() {
            mobileMenu.classList.add('translate-x-full');
            mobileMenuOverlay.classList.remove('opacity-100');
            mobileMenuOverlay.classList.add('opacity-0');
            document.body.classList.remove('overflow-hidden');
            mobileMenuButton.setAttribute('aria-expanded', 'false');
            mobileMenuIconPath.setAttribute('d', 'M4 6h16M4 12h16M4 18h16');

            window.setTimeout(() => {
                if (mobileMenu.classList.contains('translate-x-full')) {
                    mobileMenuOverlay.classList.add('hidden');
                }
            }, 300);

            document.querySelectorAll('.mobile-dropdown').forEach(dropdown => {
                dropdown.classList.add('hidden');
            });
            document.querySelectorAll('.mobile-nav-item svg').forEach(icon => {
                icon.classList.remove('rotate-180');
            });
        }

        // Toggle mobile menu
        mobileMenuButton.addEventListener('click', function() {
            if (mobileMenu.classList.contains('translate-x-full')) {
                openMobileMenu();
            } else {
                closeMobileMenu();
            }
        });

        mobileMenuClose.addEventListener('click', closeMobileMenu);
        mobileMenuOverlay.addEventListener('click', closeMobileMenu);

        // Mobile dropdown functionality
        const mobileNavItems = document.querySelectorAll('.mobile-nav-item');
        mobileNavItems.forEach(item => {
            const button = item.querySelector('.mobile-dropdown-toggle');
            const dropdown = item.querySelector('.mobile-dropdown');
            if (!button || !dropdown) return;

            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const willOpen = dropdown.classList.contains('hidden');

                // Accordion: close the others first.
                mobileNavItems.forEach(other => {
                    if (other === item) return;
                    const d = other.querySelector('.mobile-dropdown');
                    const b = other.querySelector('.mobile-dropdown-toggle');
                    if (d && !d.classList.contains('hidden')) {
                        d.classList.add('hidden');
                        if (b) {
                            b.setAttribute('aria-expanded', 'false');
                            const i = b.querySelector('svg');
                            if (i) i.classList.remove('rotate-180');
                        }
                    }
                });

                dropdown.classList.toggle('hidden', !willOpen);
                button.setAttribute('aria-expanded', String(willOpen));
                const icon = button.querySelector('svg');
                if (icon) icon.classList.toggle('rotate-180', willOpen);
            });
        });

        // Following a link should dismiss the drawer.
        mobileMenu.querySelectorAll('a[href]').forEach(link => {
            link.addEventListener('click', () => {
                if (!mobileMenu.classList.contains('translate-x-full')) closeMobileMenu();
            });
        });

        // Escape closes it.
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !mobileMenu.classList.contains('translate-x-full')) {
                closeMobileMenu();
                mobileMenuButton.focus();
            }
        });

        // Returning to the desktop breakpoint must not leave the drawer state stuck on.
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024 && !mobileMenu.classList.contains('translate-x-full')) {
                closeMobileMenu();
            }
        }, {
            passive: true
        });

    });
</script>

<style>
    /* 100vh is measured behind the mobile URL bar; dvh tracks the visible area. */
    @supports (height: 100dvh) {
        #mobile-menu {
            height: 100dvh;
        }
    }

    /* Comfortable tap targets in the drawer (WCAG ~44px). */
    #mobile-menu a,
    #mobile-menu .mobile-dropdown-toggle {
        min-height: 44px;
    }

    /* Smooth transitions for dropdowns */
    .group:hover .group-hover\:scale-100 {
        transform: scale(1);
    }

    .group:hover .group-hover\:opacity-100 {
        opacity: 1;
    }

    .group:hover .group-hover\:visible {
        visibility: visible;
    }

    /* Mobile menu animation */
    #mobile-menu {
        transform-origin: top;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.1);
    }

    /* Button hover effects */
    .hover-underline-animation::after {
        content: '';
        position: absolute;
        width: 100%;
        transform: scaleX(0);
        height: 2px;
        bottom: 0;
        left: 0;
        background-color: #DAA520;
        transform-origin: bottom right;
        transition: transform 0.25s ease-out;
    }

    .hover-underline-animation:hover::after {
        transform: scaleX(1);
        transform-origin: bottom left;
    }
</style>
