    <script>
        // Enquiry modal: one shared form, opened from any [data-hxq-open] trigger.
        (function () {
            var modal = document.getElementById('hxqModal');
            if (!modal) return;

            var lastFocused = null;

            function openEnquiry(trigger) {
                lastFocused = trigger || document.activeElement;

                // a trigger can relabel the form, e.g. "Instant Call Back"
                var heading = trigger && trigger.getAttribute('data-hxq-heading');
                var title = modal.querySelector('.hxq-title');
                if (title) title.textContent = heading || 'Get The Best Quote';

                // ...and retarget what the submission is FOR, so a brochure
                // trigger comes back with the PDF instead of a plain thank-you.
                var label = trigger && trigger.getAttribute('data-hxq-submit-label');
                var submit = modal.querySelector('[data-hxq-submit]');
                if (submit) submit.textContent = label || 'Get It Now';

                var field = modal.querySelector('[data-hxq-intent-field]');
                if (field) field.value = (trigger && trigger.getAttribute('data-hxq-intent')) || 'inquiry';

                var detailField = modal.querySelector('[data-hxq-detail-field]');
                if (detailField) detailField.value = (trigger && trigger.getAttribute('data-hxq-detail-id')) || '';

                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';

                var first = modal.querySelector('input:not([type=hidden]), select');
                if (first) setTimeout(function () { first.focus(); }, 60);
            }

            function closeEnquiry() {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
                var detailField = modal.querySelector('[data-hxq-detail-field]');
                if (detailField) detailField.value = '';
                if (lastFocused && lastFocused.focus) lastFocused.focus();
            }

            document.addEventListener('click', function (e) {
                var opener = e.target.closest('[data-hxq-open]');
                if (opener) {
                    e.preventDefault();
                    openEnquiry(opener);
                    return;
                }
                if (e.target.closest('[data-hxq-close]')) {
                    e.preventDefault();
                    closeEnquiry();
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && modal.classList.contains('is-open')) closeEnquiry();
            });

            // Validation failed on a submission that came from the modal, so reopen
            // it - otherwise the errors render out of sight in the sidebar copy.
            @if ($errors->any() && old('source') === 'modal')
                openEnquiry(document.querySelector(
                    '[data-hxq-open][data-hxq-intent="{{ old('intent', 'inquiry') }}"]') ||
                    document.querySelector('[data-hxq-open]'));
            @endif
        })();
    </script>
