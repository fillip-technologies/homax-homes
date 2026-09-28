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
        // True while the modal is showing an image outside imageSources (the
        // floor plan). Without this, a src not found in the gallery silently
        // fell back to index 0 and auto-slid through unrelated property
        // photos every 2s instead of showing the floor plan the user tapped.
        let standaloneImage = false;

        function openModal(src) {
            currentIndex = imageSources.indexOf(src);
            standaloneImage = currentIndex === -1;

            document.getElementById("modal").classList.remove("hidden");
            document.body.style.overflow = "hidden";

            // A single standalone image (the floor plan) has nothing to page
            // through, so hide the prev/next arrows instead of leaving
            // controls that tap-and-do-nothing.
            document.querySelectorAll('[aria-label="Previous image"], [aria-label="Next image"]')
                .forEach(btn => { btn.style.display = standaloneImage ? "none" : ""; });

            if (standaloneImage) {
                showModalImage(src, 1, 1);
            } else {
                document.getElementById("totalImages").textContent = imageSources.length;
                updateModalImage();
                startAutoSlide();
            }
        }

        function closeModal() {
            document.getElementById("modal").classList.add("hidden");
            document.body.style.overflow = "auto";
            stopAutoSlide();
            standaloneImage = false;
        }

        function showModalImage(src, num, total) {
            const img = document.getElementById("modalImage");
            img.style.opacity = "0";
            setTimeout(() => {
                img.src = src;
                document.getElementById("currentImageNum").textContent = num;
                document.getElementById("totalImages").textContent = total;
                img.style.opacity = "1";
            }, 300);
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
                // 300ms to match the image's own transition-opacity duration-300.
                // Was 3000, which left the viewer blank for three seconds.
            }, 300);
        }

        function nextImage() {
            if (standaloneImage) return;
            currentIndex = (currentIndex + 1) % imageSources.length;
            updateModalImage();
            resetAutoSlideTimer();
        }

        function prevImage() {
            if (standaloneImage) return;
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
