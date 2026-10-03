@extends('admin.layout')

@section('content')
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

            <div class="card">
                <div class="card-header"><h3 class="card-title">Landing Page Hero Image</h3></div>
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

            <div class="card">
                <div class="card-header"><h3 class="card-title">Home Page &ldquo;What Our Clients Say&rdquo; Cards</h3></div>
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
    </section>
</div>
@endsection
