{{-- reCAPTCHA widget; rendered only when the site key is configured (mirrors the server-side check). --}}
@if (config('services.recaptcha.site_key'))
    <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
    @push('scripts')
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endpush
@endif
