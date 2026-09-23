{{--
    Enquiry modal. Wraps the SAME form component the hero rail renders, with a
    different $uid so the ids stay unique.

    Opened by every [data-hxq-open] trigger - Enquire, Instant Call Back, the
    dock CTA, and every "Download Brochure" button. A trigger can retitle the
    form and relabel its submit via data-hxq-heading / data-hxq-submit-label,
    and set data-hxq-intent="brochure" so the controller returns the PDF.

    Hidden with opacity/visibility rather than display:none so the reCAPTCHA
    widget inside still gets a layout box and renders at full size.
--}}
{{-- $benefits is the SAME collection the project card renders, handed down
     rather than re-parsed here, so the banner cannot drift between the two
     (the card falls back to derived lines when keyfeatures is blank). --}}
@props(['property', 'benefits' => null])

@php
    $modalBenefits = collect($benefits ?? []);
@endphp

<div id="hxqModal" class="hxq-modal" role="dialog" aria-modal="true" aria-label="Property enquiry" aria-hidden="true">
    <div class="hxq-modal__backdrop" data-hxq-close></div>

    <div class="hxq-modal__panel">
        <button type="button" class="hxq-modal__x" data-hxq-close aria-label="Close enquiry form">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="hxq-card">
            <div class="hxq-strip">
                @if ($property->brochure || ($property->details && $property->details->contains(fn($d) => filled($d->document))))
                    {{-- Inside the modal already, so this retargets the open form
                         at the brochure rather than navigating away. --}}
                    <button type="button" class="hxq-strip__item" data-hxq-open
                        data-hxq-heading="Download Price Sheet" data-hxq-submit-label="Download Now"
                        data-hxq-intent="brochure">
                        <i class="fa-solid fa-file-arrow-down"></i>
                        <span>Download<br>Price Sheet</span>
                    </button>
                    <span class="hxq-strip__sep" aria-hidden="true"></span>
                @endif
                <a href="tel:+11234567892" class="hxq-strip__item">
                    <i class="fa-solid fa-phone-volume"></i>
                    <span>+1 123 456 7892</span>
                </a>
            </div>

            @if ($modalBenefits->count())
                <div class="hxq-perks">
                    @foreach ($modalBenefits as $benefit)
                        <span>{{ $benefit }}</span>
                    @endforeach
                </div>
            @endif

            <x-enquiry-form :property="$property" uid="modal" />
        </div>
    </div>
</div>
