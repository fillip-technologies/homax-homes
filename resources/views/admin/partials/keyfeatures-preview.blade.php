{{-- Live preview of the "Key Features" card exactly as the public property page shows it. --}}
@php
    $pvField = $field ?? 'keyfeatures';
    $pvHeading = $heading ?? 'Key Features';
@endphp
<div class="form-group kf-preview-wrap">
    <label>Preview (as shown on property page)</label>
    <section class="pd-card kf-preview">
        <h2 class="pd-h" data-kf-heading="{{ $pvField }}">{{ $pvHeading }}</h2>
        <div class="pd-prose pd-prose--list" id="{{ $pvField }}-preview"></div>
    </section>
</div>

@once
<style>
    .kf-preview {
        --pd-muted: #5F6472;
        --pd-line: #EFE8D8;
        --pd-navy: #2B2000;
        font-family: "Mulish", "Inter", system-ui, -apple-system, "Segoe UI", sans-serif;
        background: #fff;
        border: 1px solid var(--pd-line);
        border-radius: 12px;
        padding: 22px 24px 24px;
        box-shadow: 0 6px 20px rgba(17, 24, 39, .05);
    }

    .kf-preview .pd-h {
        margin: 0 0 16px;
        font-size: 21px;
        font-weight: 700;
        letter-spacing: -.2px;
        color: var(--pd-navy);
    }

    .kf-preview .pd-prose {
        color: var(--pd-muted);
        font-size: 15px;
        line-height: 1.75;
    }

    .kf-preview .pd-prose p { margin: 0 0 10px; }
    .kf-preview .pd-prose ul,
    .kf-preview .pd-prose ol { margin: 0 0 10px; padding-left: 22px; }
    .kf-preview .pd-prose a { color: #8B6508; text-decoration: underline; }
    .kf-preview .pd-prose strong, .kf-preview .pd-prose b { color: #111827; font-weight: 700; }
    .kf-preview .pd-prose img, .kf-preview .pd-prose video, .kf-preview .pd-prose iframe { max-width: 100%; height: auto; border-radius: 8px; }
    .kf-preview .pd-prose iframe { aspect-ratio: 16 / 9; width: 100%; }
    .kf-preview .pd-prose { overflow-wrap: anywhere; }
    .kf-preview .pd-prose ul { list-style: disc; }
    .kf-preview .pd-prose ol { list-style: decimal; }
    .kf-preview .pd-prose li { list-style: inherit; margin-bottom: 6px; }
    .kf-preview .kf-empty { color: #9ca3af; font-style: italic; }
</style>

<script>
    window.kfPreview = function (field, opts) {
        opts = opts || {};
        var $ = window.jQuery;
        var $field = $('#' + field);
        var $out = $('#' + field + '-preview');
        function render() {
            var html = $field.data('summernote') ? $field.summernote('code') : $field.val();
            var hasText = $('<div>').html(html || '').text().trim() !== '';
            if (hasText) {
                $out.html(html);
            } else {
                $out.html('<p class="kf-empty">Nothing to show yet — this card is hidden on the property page when empty.</p>');
            }
            if (opts.onRender) opts.onRender();
        }
        $field.on('summernote.change summernote.keyup input', render);
        render();
    };
</script>
@endonce

<script>
    window.addEventListener('load', function () {
        if (!window.jQuery) return;
        var opts = {};
        @if ($pvField === 'description')
        var $h = window.jQuery('[data-kf-heading="description"]');
        var setHeading = function () {
            var t = window.jQuery('#title').val();
            $h.text('Welcome To ' + (t && t.trim() ? t.trim() : 'Your Property'));
        };
        window.jQuery('#title').on('input', setHeading);
        opts.onRender = setHeading;
        @endif
        window.kfPreview('{{ $pvField }}', opts);
    });
</script>
