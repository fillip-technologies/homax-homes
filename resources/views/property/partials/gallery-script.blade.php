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
            // sync without altering their behavior.
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
