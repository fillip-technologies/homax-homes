    <script>
        // "Read more" only appears when the clamp is actually hiding something,
        // so a short description does not get a pointless toggle under it.
        (function () {
            var prose = document.querySelector('[data-pd-prose]');
            var btn = document.querySelector('[data-pd-more]');
            if (!prose || !btn) return;

            function sync() {
                if (!prose.classList.contains('is-clamped')) return;
                btn.hidden = prose.scrollHeight <= prose.clientHeight + 2;
            }

            btn.addEventListener('click', function () {
                var clamped = prose.classList.toggle('is-clamped');
                btn.textContent = clamped ? 'Read more' : 'Read less';
            });

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', sync);
            } else {
                sync();
            }
            // fonts can reflow the copy after load, so measure again
            window.addEventListener('load', sync);
        })();
    </script>
