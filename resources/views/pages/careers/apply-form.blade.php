{{-- Job application form. Pass $job (a JobOpening) on a job page to apply for that role;
     leave it out for a general application. --}}
@php
    $job = $job ?? null;

    $label = 'block text-sm font-medium text-gray-700 mb-1.5';
    $field = fn (string $name) => 'w-full h-12 px-4 rounded-lg border bg-white text-gray-800 placeholder-gray-400 transition focus:outline-none focus:ring-2 '
        . ($errors->has($name)
            ? 'border-red-400 focus:border-red-500 focus:ring-red-200'
            : 'border-gray-300 focus:border-[#000080] focus:ring-[#000080]/20');

    // Errors shown next to a field; anything else (e.g. captcha) goes in the summary box.
    $inline = ['first_name', 'last_name', 'email', 'phone', 'position', 'resume', 'cover_letter', 'agree'];
    $otherErrors = collect($errors->getMessages())->except($inline)->flatten();
@endphp

@if (session('success'))
    <div class="mb-6 flex items-start gap-3 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3" role="status">
        <i class="fas fa-check-circle mt-0.5" aria-hidden="true"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif
@if ($errors->any())
    <div class="mb-6 rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3" role="alert">
        <p class="font-medium">Please fix the highlighted fields and attach your files again.</p>
        @foreach ($otherErrors as $error)
            <p class="text-sm mt-1">{{ $error }}</p>
        @endforeach
    </div>
@endif

<form action="{{ route('careers.apply') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
    @csrf

    @if ($job)
        {{-- The role this application is for: fixed to the job page it was opened from. --}}
        <input type="hidden" name="position" value="{{ $job->id }}">
        <div class="rounded-xl border border-[#000080]/20 bg-[#000080]/5 p-5">
            <div class="flex items-start gap-4">
                <div class="shrink-0 w-11 h-11 rounded-full bg-[#000080] text-white flex items-center justify-center">
                    <i class="fas fa-briefcase" aria-hidden="true"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm text-gray-500">You're applying for</p>
                    <p class="text-xl font-bold text-[#000080] break-words">{{ $job->title }}</p>
                    <p class="text-sm text-gray-600">{{ collect([$job->department, $job->type_label, $job->location])->filter()->join(' · ') }}</p>
                </div>
            </div>
            @error('position')<p class="text-sm text-red-600 mt-3">This opening is no longer accepting applications.</p>@enderror
        </div>
    @endif

    {{-- Your details --}}
    <fieldset>
        <legend class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <span class="w-6 h-6 rounded-full bg-[#000080] text-white text-xs flex items-center justify-center">1</span>
            Your details
        </legend>
        <div class="grid sm:grid-cols-2 gap-x-6 gap-y-5">
            @foreach ([
                ['first_name', 'first-name', 'First name', 'text', true, 100, 'given-name', ''],
                ['last_name', 'last-name', 'Last name', 'text', true, 100, 'family-name', ''],
                ['email', 'email', 'Email address', 'email', true, 255, 'email', 'you@example.com'],
                ['phone', 'phone', 'Phone number', 'tel', false, 50, 'tel', '+91 98765 43210'],
            ] as [$name, $id, $text, $type, $required, $max, $auto, $placeholder])
                <div>
                    <label for="{{ $id }}" class="{{ $label }}">
                        {{ $text }}
                        @if ($required)<span class="text-red-600">*</span>@else<span class="text-gray-400 font-normal">(optional)</span>@endif
                    </label>
                    <input type="{{ $type }}" id="{{ $id }}" name="{{ $name }}" value="{{ old($name) }}" maxlength="{{ $max }}"
                           autocomplete="{{ $auto }}" @if ($placeholder) placeholder="{{ $placeholder }}" @endif
                           @required($required) class="{{ $field($name) }}">
                    @error($name)<p class="text-sm text-red-600 mt-1.5">{{ $message }}</p>@enderror
                </div>
            @endforeach
        </div>
    </fieldset>

    {{-- Documents --}}
    <fieldset>
        <legend class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <span class="w-6 h-6 rounded-full bg-[#000080] text-white text-xs flex items-center justify-center">2</span>
            Documents
        </legend>
        <div class="grid sm:grid-cols-2 gap-x-6 gap-y-5">
            @foreach ([
                ['resume', 'resume', 'Resume / CV', true],
                ['cover_letter', 'cover-letter', 'Cover letter', false],
            ] as [$name, $id, $text, $required])
                <div>
                    <span class="{{ $label }}">
                        {{ $text }}
                        @if ($required)<span class="text-red-600">*</span>@else<span class="text-gray-400 font-normal">(optional)</span>@endif
                    </span>
                    <label for="{{ $id }}" data-file-drop
                           class="relative flex items-center gap-3 rounded-lg border-2 border-dashed px-4 py-4 cursor-pointer transition
                                  {{ $errors->has($name) ? 'border-red-300 bg-red-50/50' : 'border-gray-300 bg-gray-50 hover:border-[#000080]/60 hover:bg-[#000080]/5' }}
                                  focus-within:border-[#000080] focus-within:ring-2 focus-within:ring-[#000080]/20">
                        <span class="shrink-0 w-10 h-10 rounded-full bg-[#000080]/10 text-[#000080] flex items-center justify-center">
                            <i class="fas fa-file-upload" aria-hidden="true" data-file-icon></i>
                        </span>
                        <span class="min-w-0">
                            <span class="block text-sm font-semibold text-[#000080] truncate" data-file-name>Choose a file</span>
                            <span class="block text-xs text-gray-500">PDF or Word, up to 2 MB</span>
                        </span>
                        <input type="file" id="{{ $id }}" name="{{ $name }}" accept=".pdf,.doc,.docx" @required($required)
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    </label>
                    @error($name)<p class="text-sm text-red-600 mt-1.5">{{ $message }}</p>@enderror
                </div>
            @endforeach
        </div>
    </fieldset>

    <div class="space-y-5 pt-2 border-t border-gray-100">
        <div class="pt-5">
            <label for="agree" class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" id="agree" name="agree" value="1" required @checked(old('agree'))
                       class="mt-0.5 h-5 w-5 shrink-0 rounded border-gray-300 accent-[#000080] cursor-pointer">
                <span class="text-sm text-gray-700">I agree to the privacy policy and consent to my data being processed. <span class="text-red-600">*</span></span>
            </label>
            @error('agree')<p class="text-sm text-red-600 mt-1.5 ml-8">{{ $message }}</p>@enderror
        </div>

        @include('includes.recaptcha')

        <button type="submit"
                class="w-full h-12 inline-flex items-center justify-center gap-2 bg-[#000080] hover:bg-[#000066] text-white font-semibold rounded-lg shadow-sm transition duration-300 focus:outline-none focus:ring-4 focus:ring-[#000080]/25">
            <i class="fas fa-paper-plane" aria-hidden="true"></i>
            Submit Application
        </button>
    </div>
</form>

@once
<script>
    (function () {
        // Show the chosen file's name inside its upload box.
        document.querySelectorAll('[data-file-drop]').forEach(function (drop) {
            var input = drop.querySelector('input[type=file]');
            var name = drop.querySelector('[data-file-name]');
            var icon = drop.querySelector('[data-file-icon]');
            input.addEventListener('change', function () {
                var file = input.files && input.files[0];
                name.textContent = file ? file.name : 'Choose a file';
                icon.className = file ? 'fas fa-file-alt' : 'fas fa-file-upload';
                drop.classList.toggle('border-[#000080]', !!file);
                drop.classList.toggle('bg-[#000080]/5', !!file);
            });
        });
    })();
</script>
@endonce
