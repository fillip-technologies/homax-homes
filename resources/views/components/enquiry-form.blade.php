{{--
    Shared enquiry form.

    Rendered more than once per property page (sticky rail + modal), so every id
    is namespaced with $uid to keep ids unique and <label for> associations
    correct. Moved here from includes/property-enquiry-form.blade.php.

    Required by PropertyInquiryController@store: name, phone, terms, recaptcha.
    email is optional; country_code is prepended to phone server-side.

    `intent` tells the controller what the visitor was actually after. "brochure"
    makes the controller hand back the PDF once the lead is captured.
--}}
@props([
    'property',
    'uid' => 'side',
    'heading' => 'Get The Best Quote',
    'submitLabel' => 'Get It Now',
    'intent' => 'enquiry',
])

<div class="hxq-body">
    <h3 class="hxq-title">{{ $heading }}</h3>

    @if (session('success'))
        <div class="hxq-alert">{{ session('success') }}</div>
    @endif

    <form action="{{ route('property.inquiry.store', $property->id) }}" method="POST" class="hxq-form">
        @csrf
        <input type="hidden" name="source" value="{{ $uid }}">
        {{-- Retargeted by the modal when a "Download Brochure" trigger opens it.
             Attribute is deliberately NOT data-hxq-intent: the trigger buttons
             carry that, and a querySelector inside the modal would match those. --}}
        <input type="hidden" name="intent" value="{{ old('intent', $intent) }}" data-hxq-intent-field>
        <input type="hidden" name="detail_id" value="{{ old('detail_id') }}" data-hxq-detail-field>

        <div class="hxq-field">
            <label for="name-{{ $uid }}" class="sr-only">Name</label>
            <input type="text" id="name-{{ $uid }}" name="name" required placeholder="Name"
                autocomplete="name" value="{{ old('name') }}" />
        </div>
        @error('name')
            <p class="hxq-err">{{ $message }}</p>
        @enderror

        <div class="hxq-field">
            <label for="email-{{ $uid }}" class="sr-only">Email Address (Optional)</label>
            <input type="email" id="email-{{ $uid }}" name="email" placeholder="Email Address(Optional)"
                autocomplete="email" value="{{ old('email') }}" />
        </div>
        @error('email')
            <p class="hxq-err">{{ $message }}</p>
        @enderror

        <div class="hxq-phone">
            <label for="cc-{{ $uid }}" class="sr-only">Country code</label>
            <select id="cc-{{ $uid }}" name="country_code" class="hxq-cc">
                @php
                    $dialCodes = [
                        '+91' => 'India (+91)',
                        '+971' => 'UAE (+971)',
                        '+44' => 'UK (+44)',
                        '+1' => 'USA (+1)',
                        '+61' => 'Australia (+61)',
                        '+65' => 'Singapore (+65)',
                    ];
                @endphp
                @foreach ($dialCodes as $code => $labelText)
                    <option value="{{ $code }}" @selected(old('country_code', '+91') === $code)>{{ $labelText }}</option>
                @endforeach
            </select>

            <label for="phone-{{ $uid }}" class="sr-only">Phone number</label>
            <input type="tel" id="phone-{{ $uid }}" name="phone" required placeholder="Phone number"
                inputmode="numeric" autocomplete="tel" value="{{ old('phone') }}" />
        </div>
        @error('phone')
            <p class="hxq-err">{{ $message }}</p>
        @enderror

        <label for="terms-{{ $uid }}" class="hxq-terms">
            <input id="terms-{{ $uid }}" name="terms" type="checkbox" required {{ old('terms') ? 'checked' : '' }} />
            <span>I agree to the <a href="#">terms and conditions</a></span>
        </label>
        @error('terms')
            <p class="hxq-err">{{ $message }}</p>
        @enderror

        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
        @error('g-recaptcha-response')
            <p class="hxq-err">{{ $message }}</p>
        @enderror

        <button type="submit" class="hxq-submit" data-hxq-submit>{{ $submitLabel }}</button>
    </form>
</div>
