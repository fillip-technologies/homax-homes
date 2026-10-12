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
        let standaloneImage = false;

        function openModal(src) {
            currentIndex = imageSources.indexOf(src);
            standaloneImage = currentIndex === -1;

            const modal = document.getElementById("modal");
            if (!modal) return;
            modal.classList.remove("hidden");
            document.body.style.overflow = "hidden";

            // A single standalone image (the floor plan) has nothing to page
            // through, so hide the prev/next arrows instead of leaving
            // controls that tap-and-do-nothing.
            document.querySelectorAll('[aria-label="Previous image"], [aria-label="Next image"]')
                .forEach(btn => { btn.style.display = standaloneImage ? "none" : ""; });

            const img1 = document.getElementById("modalImage1");
            const img2 = document.getElementById("modalImage2");
            if (!img1 || !img2) return;


            img1.src = src;
            img1.style.transition = "none";
            img1.style.transform = "translateX(0%)";
            img1.style.opacity = "1";
            img1.style.zIndex = "2";

            img2.removeAttribute("src");
            img2.style.transition = "none";
            img2.style.transform = "translateX(100%)";
            img2.style.opacity = "0";
            img2.style.zIndex = "1";

            if (standaloneImage) {
                document.getElementById("currentImageNum").textContent = "1";
                document.getElementById("totalImages").textContent = "1";
            } else {
                document.getElementById("currentImageNum").textContent = currentIndex + 1;
                document.getElementById("totalImages").textContent = imageSources.length;
            }
        }

        function closeModal() {
            const modal = document.getElementById("modal");
            if (modal) modal.classList.add("hidden");
            document.body.style.overflow = "auto";
            standaloneImage = false;
        }

        function transitionModalImage(direction) {
            if (standaloneImage || imageSources.length <= 1) return;

            if (direction === "next") {
                currentIndex = (currentIndex + 1) % imageSources.length;
            } else {
                currentIndex = (currentIndex - 1 + imageSources.length) % imageSources.length;
            }

            // Instant swap, no slide or fade.
            document.getElementById("modalImage1").src = imageSources[currentIndex];
            document.getElementById("currentImageNum").textContent = currentIndex + 1;
        }

        function nextImage() {
            transitionModalImage("next");
        }

        function prevImage() {
            transitionModalImage("prev");
        }

        // Basic keyboard nav for modal
        document.addEventListener("keydown", function(event) {
            const modal = document.getElementById("modal");
            if (modal && !modal.classList.contains("hidden")) {
                if (event.key === "Escape") {
                    closeModal();
                } else if (event.key === "ArrowRight") {
                    nextImage();
                } else if (event.key === "ArrowLeft") {
                    prevImage();
                }
            }
        });

        // Touch swipe and backdrop click handling
        document.addEventListener("DOMContentLoaded", () => {
            const stage = document.getElementById("modalStage");
            if (stage) {
                let startX = 0;
                stage.addEventListener("touchstart", (e) => {
                    startX = e.changedTouches[0].screenX;
                }, { passive: true });
                stage.addEventListener("touchend", (e) => {
                    const diff = e.changedTouches[0].screenX - startX;
                    if (Math.abs(diff) > 40) {
                        if (diff < 0) nextImage();
                        else prevImage();
                    }
                }, { passive: true });
            }

            const modal = document.getElementById("modal");
            if (modal) {
                modal.addEventListener("click", (e) => {
                    if (e.target === modal) closeModal();
                });
            }
        });

        // Example trigger function - you can call this on your image clicks
        function showCarousel(startIndex = 0) {
            currentIndex = startIndex;
            openModal(imageSources[startIndex]);
        }
    </script>
