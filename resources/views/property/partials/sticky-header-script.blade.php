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
