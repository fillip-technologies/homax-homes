@extends('admin.layout')

@section('extraCss')
<style>
    /* Collapsible sections: a solid, clearly clickable title bar. */
    .ss-section { border: 1px solid #000080; }
    .ss-toggle { display: flex; align-items: center; justify-content: space-between; background: #000080; color: #fff; cursor: pointer; padding: 14px 20px; transition: background-color .2s ease; }
    .ss-toggle .card-title { color: #fff; font-weight: 600; font-size: 1.1rem; margin: 0; }
    .ss-toggle:hover, .ss-toggle:focus { background: #DAA520; outline: none; }
    .ss-toggle[aria-expanded="true"] { background: #DAA520; }
    .ss-toggle::after { display: none; } /* AdminLTE's clearfix pseudo-element would count as a flex item */
    .ss-chevron { margin-left: auto; transition: transform .25s ease; }
    .ss-toggle[aria-expanded="true"] .ss-chevron { transform: rotate(180deg); }
</style>
@endsection

@section('content')
@php
    // The hero image section is always open; the others stay shut unless one was just saved
    // or holds a validation error.
    $openSections = ['hero'];
    if (session('open')) {
        $openSections[] = session('open');
    }
    foreach ($errors->keys() as $errorKey) {
        if (str_starts_with($errorKey, 'about')) { $openSections[] = 'about'; }
        elseif (str_starts_with($errorKey, 'footer')) { $openSections[] = 'footer'; }
        elseif ($errorKey !== 'hero_image') { $openSections[] = 'testimonials'; }
    }
@endphp
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Site Settings</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card ss-section">
                <div class="card-header ss-toggle" role="button" tabindex="0" data-toggle="collapse" data-target="#sec-hero"
                     aria-expanded="{{ in_array('hero', $openSections) ? 'true' : 'false' }}" aria-controls="sec-hero">
                    <h3 class="card-title">Landing Page Hero Image</h3>
                    <i class="fas fa-chevron-down ss-chevron" aria-hidden="true"></i>
                </div>
                <div id="sec-hero" class="collapse {{ in_array('hero', $openSections) ? 'show' : '' }}">
                <div class="card-body">
                    <p class="text-muted">
                        Recommended: <strong>1920 x 820 px</strong> (wide landscape), JPG, PNG or WebP, under 5 MB.
                        Larger images are resized to 1920 px wide and converted to WebP automatically.
                        Keep the subject away from the left side, where the text overlay sits.
                    </p>

                    <img src="{{ $heroUrl }}" alt="Current hero image" class="img-fluid mb-3" style="max-height:280px;">
                    <p><small>{{ $isCustom ? 'Custom image in use.' : 'Default image in use.' }}</small></p>

                    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <input type="file" name="hero_image" accept=".jpg,.jpeg,.png,.webp" class="form-control-file" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </form>

                    @if ($isCustom)
                        <form method="POST" action="{{ route('admin.settings.reset') }}" class="mt-3"
                              onsubmit="return confirm('Reset to the default image?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">Reset to default</button>
                        </form>
                    @endif
                </div>
                </div>
            </div>

            <div class="card ss-section">
                <div class="card-header ss-toggle" role="button" tabindex="0" data-toggle="collapse" data-target="#sec-about"
                     aria-expanded="{{ in_array('about', $openSections) ? 'true' : 'false' }}" aria-controls="sec-about">
                    <h3 class="card-title">Home Page &ldquo;About Homax Homes&rdquo; Section</h3>
                    <i class="fas fa-chevron-down ss-chevron" aria-hidden="true"></i>
                </div>
                <div id="sec-about" class="collapse {{ in_array('about', $openSections) ? 'show' : '' }}">
                <div class="card-body">
                    <p class="text-muted">
                        The text next to the photo in the About section of the home page. Leave a bullet point or a number
                        completely empty to hide it.
                    </p>
                    <form method="POST" action="{{ route('admin.about.update') }}">
                        @csrf
                        <div class="form-group">
                            <label for="about_intro">Intro paragraph</label>
                            <textarea id="about_intro" name="about[intro]" rows="4" maxlength="600" class="form-control" required>{{ old('about.intro', $about['intro']) }}</textarea>
                        </div>

                        <h5 class="mt-4">Bullet points</h5>
                        @foreach ($about['points'] as $i => $point)
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Point {{ $i + 1 }} title <small class="text-muted">(bold)</small></label>
                                    <input type="text" name="about[points][{{ $i }}][title]" maxlength="60" class="form-control" value="{{ old("about.points.$i.title", $point['title']) }}">
                                </div>
                                <div class="form-group col-md-8">
                                    <label>Point {{ $i + 1 }} text</label>
                                    <input type="text" name="about[points][{{ $i }}][text]" maxlength="200" class="form-control" value="{{ old("about.points.$i.text", $point['text']) }}">
                                </div>
                            </div>
                        @endforeach

                        <h5 class="mt-4">Numbers on the photo</h5>
                        @foreach ($about['stats'] as $i => $stat)
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>Number {{ $i + 1 }} <small class="text-muted">(e.g. 1K+)</small></label>
                                    <input type="text" name="about[stats][{{ $i }}][value]" maxlength="12" class="form-control" value="{{ old("about.stats.$i.value", $stat['value']) }}">
                                </div>
                                <div class="form-group col-md-5">
                                    <label>Label {{ $i + 1 }}</label>
                                    <input type="text" name="about[stats][{{ $i }}][label]" maxlength="40" class="form-control" value="{{ old("about.stats.$i.label", $stat['label']) }}">
                                </div>
                            </div>
                        @endforeach

                        <button type="submit" class="btn btn-primary">Save about section</button>
                    </form>

                    <form method="POST" action="{{ route('admin.about.reset') }}" class="mt-3"
                          onsubmit="return confirm('Reset the About section to the default text?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">Reset to default text</button>
                    </form>
                </div>
                </div>
            </div>

            <div class="card ss-section">
                <div class="card-header ss-toggle" role="button" tabindex="0" data-toggle="collapse" data-target="#sec-footer"
                     aria-expanded="{{ in_array('footer', $openSections) ? 'true' : 'false' }}" aria-controls="sec-footer">
                    <h3 class="card-title">Footer &ldquo;About&rdquo; Text &amp; Notices</h3>
                    <i class="fas fa-chevron-down ss-chevron" aria-hidden="true"></i>
                </div>
                <div id="sec-footer" class="collapse {{ in_array('footer', $openSections) ? 'show' : '' }}">
                <div class="card-body">
                    <p class="text-muted">
                        The text block at the very bottom of every page (above the copyright line). Leave a paragraph or a notice
                        completely empty to hide it. Paragraphs are split evenly into two columns.
                    </p>
                    <form method="POST" action="{{ route('admin.footer.update') }}">
                        @csrf
                        <div class="form-group">
                            <label for="footer_heading">Heading</label>
                            <input type="text" id="footer_heading" name="footer[heading]" maxlength="120" class="form-control" value="{{ old('footer.heading', $footerAbout['heading']) }}">
                        </div>

                        <h5 class="mt-4">Paragraphs</h5>
                        @foreach ($footerAbout['paragraphs'] as $i => $paragraph)
                            <div class="form-group">
                                <label>Paragraph {{ $i + 1 }}</label>
                                <textarea name="footer[paragraphs][{{ $i }}]" rows="3" maxlength="600" class="form-control">{{ old("footer.paragraphs.$i", $paragraph) }}</textarea>
                            </div>
                        @endforeach

                        <h5 class="mt-4">Notices <small class="text-muted">(small print under the line)</small></h5>
                        @foreach ($footerAbout['notices'] as $i => $notice)
                            <div class="form-group">
                                <label>Notice {{ $i + 1 }} title</label>
                                <input type="text" name="footer[notices][{{ $i }}][title]" maxlength="60" class="form-control mb-2" value="{{ old("footer.notices.$i.title", $notice['title']) }}">
                                <textarea name="footer[notices][{{ $i }}][text]" rows="3" maxlength="800" class="form-control" placeholder="Notice {{ $i + 1 }} text">{{ old("footer.notices.$i.text", $notice['text']) }}</textarea>
                            </div>
                        @endforeach

                        <button type="submit" class="btn btn-primary">Save footer text</button>
                    </form>

                    <form method="POST" action="{{ route('admin.footer.reset') }}" class="mt-3"
                          onsubmit="return confirm('Reset the footer text to the default?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">Reset to default text</button>
                    </form>
                </div>
                </div>
            </div>

            <div class="card ss-section">
                <div class="card-header ss-toggle" role="button" tabindex="0" data-toggle="collapse" data-target="#sec-testimonials"
                     aria-expanded="{{ in_array('testimonials', $openSections) ? 'true' : 'false' }}" aria-controls="sec-testimonials">
                    <h3 class="card-title">Home Page &ldquo;What Our Clients Say&rdquo; Cards</h3>
                    <i class="fas fa-chevron-down ss-chevron" aria-hidden="true"></i>
                </div>
                <div id="sec-testimonials" class="collapse {{ in_array('testimonials', $openSections) ? 'show' : '' }}">
                <div class="card-body">
                    <p class="text-muted">
                        These cards appear in the testimonials section of the home page, in the order shown.
                        Untick &ldquo;Show on website&rdquo; to hide one without deleting it. If no card is shown, the whole section is hidden.
                    </p>

                    @forelse ($testimonials as $t)
                        <div class="border rounded p-3 mb-3 {{ $t->is_active ? '' : 'bg-light' }}">
                            <div class="d-flex align-items-center">
                                @if ($t->photo_url)
                                    <img src="{{ $t->photo_url }}" alt="" class="rounded-circle mr-3" style="width:44px;height:44px;object-fit:cover;">
                                @else
                                    <span class="rounded-circle mr-3 bg-secondary text-white d-inline-flex align-items-center justify-content-center" style="width:44px;height:44px;">{{ $t->initial }}</span>
                                @endif
                                <div class="flex-grow-1">
                                    <strong>{{ $t->name }}</strong>
                                    @unless ($t->is_active) <span class="badge badge-secondary ml-1">Hidden</span> @endunless
                                    <div class="text-muted small">{{ \Illuminate\Support\Str::limit($t->quote, 120) }}</div>
                                </div>
                                <button class="btn btn-sm btn-outline-primary mr-2" type="button" data-toggle="collapse" data-target="#edit-t-{{ $t->id }}">Edit</button>
                                <form method="POST" action="{{ route('admin.testimonials.destroy', $t) }}" onsubmit="return confirm('Delete this testimonial?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                            <div class="collapse mt-3" id="edit-t-{{ $t->id }}">
                                <form method="POST" action="{{ route('admin.testimonials.update', $t) }}" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Name</label>
                                        <input type="text" name="name" class="form-control" maxlength="100" required value="{{ $t ? $t->name : '' }}">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Subtitle <small class="text-muted">(e.g. Project Enquiry)</small></label>
                                        <input type="text" name="subtitle" class="form-control" maxlength="100" value="{{ $t ? $t->subtitle : '' }}">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Small text next to stars <small class="text-muted">(optional)</small></label>
                                        <input type="text" name="caption" class="form-control" maxlength="100" value="{{ $t ? $t->caption : '' }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Feedback</label>
                                    <textarea name="quote" class="form-control" rows="3" maxlength="600" required>{{ $t ? $t->quote : '' }}</textarea>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-2">
                                        <label>Rating</label>
                                        <select name="rating" class="form-control">
                                            @for ($r = 5; $r >= 1; $r--)
                                                <option value="{{ $r }}" {{ ($t ? $t->rating : 5) == $r ? 'selected' : '' }}>{{ $r }} {{ $r === 1 ? 'star' : 'stars' }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Order</label>
                                        <input type="number" name="sort_order" min="0" max="9999" class="form-control" value="{{ $t ? $t->sort_order : '' }}" placeholder="auto">
                                    </div>
                                    <div class="form-group col-md-5">
                                        <label>Photo <small class="text-muted">(optional, JPG/PNG/WebP, under 2 MB)</small></label>
                                        <input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp" class="form-control-file">
                                    </div>
                                    <div class="form-group col-md-3 pt-4">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="active_{{ $t ? $t->id : 'new' }}" name="is_active" value="1" {{ !$t || $t->is_active ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="active_{{ $t ? $t->id : 'new' }}">Show on website</label>
                                        </div>
                                        @if ($t && $t->photo)
                                            <div class="custom-control custom-checkbox mt-1">
                                                <input type="checkbox" class="custom-control-input" id="rmphoto_{{ $t->id }}" name="remove_photo" value="1">
                                                <label class="custom-control-label" for="rmphoto_{{ $t->id }}">Remove photo</label>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">No testimonials yet.</p>
                    @endforelse

                    <hr>
                    <h5>Add a testimonial</h5>
                    @php $t = null; @endphp
                    <form method="POST" action="{{ route('admin.testimonials.store') }}" enctype="multipart/form-data">
                        @csrf
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Name</label>
                                        <input type="text" name="name" class="form-control" maxlength="100" required value="{{ $t ? $t->name : '' }}">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Subtitle <small class="text-muted">(e.g. Project Enquiry)</small></label>
                                        <input type="text" name="subtitle" class="form-control" maxlength="100" value="{{ $t ? $t->subtitle : '' }}">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Small text next to stars <small class="text-muted">(optional)</small></label>
                                        <input type="text" name="caption" class="form-control" maxlength="100" value="{{ $t ? $t->caption : '' }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Feedback</label>
                                    <textarea name="quote" class="form-control" rows="3" maxlength="600" required>{{ $t ? $t->quote : '' }}</textarea>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-2">
                                        <label>Rating</label>
                                        <select name="rating" class="form-control">
                                            @for ($r = 5; $r >= 1; $r--)
                                                <option value="{{ $r }}" {{ ($t ? $t->rating : 5) == $r ? 'selected' : '' }}>{{ $r }} {{ $r === 1 ? 'star' : 'stars' }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Order</label>
                                        <input type="number" name="sort_order" min="0" max="9999" class="form-control" value="{{ $t ? $t->sort_order : '' }}" placeholder="auto">
                                    </div>
                                    <div class="form-group col-md-5">
                                        <label>Photo <small class="text-muted">(optional, JPG/PNG/WebP, under 2 MB)</small></label>
                                        <input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp" class="form-control-file">
                                    </div>
                                    <div class="form-group col-md-3 pt-4">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="active_{{ $t ? $t->id : 'new' }}" name="is_active" value="1" {{ !$t || $t->is_active ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="active_{{ $t ? $t->id : 'new' }}">Show on website</label>
                                        </div>
                                        @if ($t && $t->photo)
                                            <div class="custom-control custom-checkbox mt-1">
                                                <input type="checkbox" class="custom-control-input" id="rmphoto_{{ $t->id }}" name="remove_photo" value="1">
                                                <label class="custom-control-label" for="rmphoto_{{ $t->id }}">Remove photo</label>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                        <button type="submit" class="btn btn-success">Add testimonial</button>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('extraJs')
<script>
    document.querySelectorAll('.ss-toggle').forEach(function (el) {
        el.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); el.click(); }
        });
    });
</script>
@endsection
