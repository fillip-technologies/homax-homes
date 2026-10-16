@extends('admin.layout')

@php
    $editing = $post->exists;
    // Show a past/future date back in the datetime-local format.
    $publishedAt = old('published_at', $post->published_at?->format('Y-m-d\TH:i'));
    $currentStatus = old('status', $post->status === 'draft' ? 'draft' : ($editing ? 'published' : 'draft'));
@endphp

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>{{ $editing ? 'Edit Post' : 'Add New Post' }}</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.blog.posts.index') }}">Blog Posts</a></li>
                        <li class="breadcrumb-item active">{{ $editing ? 'Edit' : 'Add' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    @include('admin.partials.alerts')

    <section class="content">
        <div class="container-fluid">
            <form action="{{ $editing ? route('admin.blog.posts.update', $post) : route('admin.blog.posts.store') }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @if ($editing) @method('PUT') @endif

                <div class="row">
                    <!-- Main column -->
                    <div class="col-lg-8">
                        <div class="card card-primary">
                            <div class="card-header"><h3 class="card-title">Article</h3></div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="title">Title <span class="text-danger">*</span></label>
                                    <input type="text" id="title" name="title" maxlength="255" required
                                        class="form-control @error('title') is-invalid @enderror"
                                        value="{{ old('title', $post->title) }}">
                                </div>

                                <div class="form-group">
                                    <label for="slug">URL slug</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">/blog/</span></div>
                                        <input type="text" id="slug" name="slug" maxlength="200"
                                            class="form-control @error('slug') is-invalid @enderror"
                                            placeholder="auto-generated from the title"
                                            value="{{ old('slug', $post->slug) }}">
                                    </div>
                                    <small class="text-muted">Leave blank to generate it from the title. Changing it changes the post's link.</small>
                                </div>

                                <div class="form-group">
                                    <label for="excerpt">Excerpt <span class="text-danger">*</span></label>
                                    <textarea id="excerpt" name="excerpt" rows="3" maxlength="500" required
                                        class="form-control @error('excerpt') is-invalid @enderror">{{ old('excerpt', $post->excerpt) }}</textarea>
                                    <small class="text-muted">Shown on cards and under the title. Max 500 characters.</small>
                                </div>

                                <div class="form-group">
                                    <label for="body">Body <span class="text-danger">*</span></label>
                                    <textarea id="body" name="body" class="@error('body') is-invalid @enderror">{{ old('body', $post->body) }}</textarea>
                                    @error('body') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- SEO fields hidden for now. Remove the comment markers to bring them back; the
                             controller still accepts meta_title / meta_description, and the public page
                             falls back to the title and excerpt.
                        <div class="card card-outline card-secondary">
                            <div class="card-header"><h3 class="card-title">SEO</h3></div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="meta_title">Meta title</label>
                                    <input type="text" id="meta_title" name="meta_title" maxlength="70" class="form-control"
                                        value="{{ old('meta_title', $post->meta_title) }}" placeholder="Defaults to the post title">
                                </div>
                                <div class="form-group mb-0">
                                    <label for="meta_description">Meta description</label>
                                    <textarea id="meta_description" name="meta_description" rows="2" maxlength="160" class="form-control"
                                        placeholder="Defaults to the excerpt">{{ old('meta_description', $post->meta_description) }}</textarea>
                                </div>
                            </div>
                        </div>
                        --}}
                    </div>

                    <!-- Side column -->
                    <div class="col-lg-4">
                        <div class="card card-primary">
                            <div class="card-header"><h3 class="card-title">Publish</h3></div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select id="status" name="status" class="form-control">
                                        <option value="draft" @selected($currentStatus === 'draft')>Draft (hidden)</option>
                                        <option value="published" @selected($currentStatus === 'published')>Published</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="published_at">Publish date &amp; time</label>
                                    <input type="datetime-local" id="published_at" name="published_at"
                                        class="form-control @error('published_at') is-invalid @enderror" value="{{ $publishedAt }}">
                                    <small class="text-muted">Blank means now. A future date schedules the post to go live automatically.</small>
                                </div>
                                <div class="custom-control custom-switch mb-3">
                                    <input type="checkbox" class="custom-control-input" id="is_featured" name="is_featured" value="1"
                                        @checked(old('is_featured', $post->is_featured))>
                                    <label class="custom-control-label" for="is_featured">Featured post</label>
                                </div>
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-save mr-1"></i> {{ $editing ? 'Update Post' : 'Save Post' }}
                                </button>
                                @if ($editing)
                                    <a href="{{ $post->url }}" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-block">
                                        {{ $post->isVisible() ? 'View on site' : 'Preview' }}
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="card card-outline card-primary">
                            <div class="card-header"><h3 class="card-title">Details</h3></div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="blog_category_id">Category</label>
                                    <select id="blog_category_id" name="blog_category_id" class="form-control">
                                        <option value="">Uncategorised</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                @selected((string) old('blog_category_id', $post->blog_category_id) === (string) $category->id)>
                                                {{ $category->name }}{{ $category->is_active ? '' : ' (inactive)' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small><a href="{{ route('admin.blog.categories.index') }}">Manage categories</a></small>
                                </div>
                                <div class="form-group mb-0">
                                    <label for="author_name">Author <span class="text-danger">*</span></label>
                                    <input type="text" id="author_name" name="author_name" maxlength="100" required class="form-control"
                                        value="{{ old('author_name', $post->author_name) }}">
                                </div>
                            </div>
                        </div>

                        <div class="card card-outline card-primary">
                            <div class="card-header"><h3 class="card-title">Featured image</h3></div>
                            <div class="card-body">
                                @if ($post->image_url)
                                    <img id="image-preview" src="{{ $post->image_url }}" alt="" class="img-fluid rounded mb-2">
                                @else
                                    <img id="image-preview" src="" alt="" class="img-fluid rounded mb-2 d-none">
                                @endif
                                <div class="form-group">
                                    <input type="file" id="featured_image" name="featured_image" accept=".jpg,.jpeg,.png,.webp"
                                        class="form-control-file @error('featured_image') is-invalid @enderror">
                                    <small class="text-muted">JPG, PNG or WebP, up to 2 MB. Wide images (16:9) look best.</small>
                                </div>
                                <div class="form-group">
                                    <label for="featured_image_alt">Alt text</label>
                                    <input type="text" id="featured_image_alt" name="featured_image_alt" maxlength="255" class="form-control"
                                        value="{{ old('featured_image_alt', $post->featured_image_alt) }}"
                                        placeholder="Describe the image">
                                </div>
                                @if ($post->featured_image)
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="remove_image" name="remove_image" value="1">
                                        <label class="custom-control-label" for="remove_image">Remove current image</label>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
    $(function () {
        var uploadUrl = @json(route('admin.blog.posts.upload'));
        var token = @json(csrf_token());

        // Inline images go to the server instead of being embedded as base64 in the body.
        $('#body').summernote({
            height: 840,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture', 'hr']],
                ['view', ['codeview']]
            ],
            styleTags: ['p', 'blockquote', 'h2', 'h3', 'h4'],
            callbacks: {
                onImageUpload: function (files) {
                    var editor = $(this);
                    Array.prototype.forEach.call(files, function (file) {
                        var data = new FormData();
                        data.append('image', file);
                        data.append('_token', token);
                        $.ajax({
                            url: uploadUrl, method: 'POST', data: data, processData: false, contentType: false,
                            success: function (res) { editor.summernote('insertImage', res.url); },
                            error: function (xhr) {
                                var msg = xhr.responseJSON && xhr.responseJSON.errors
                                    ? Object.values(xhr.responseJSON.errors)[0][0]
                                    : 'Image upload failed.';
                                alert(msg);
                            }
                        });
                    });
                }
            }
        });

        $('#featured_image').on('change', function () {
            var file = this.files && this.files[0];
            if (!file) return;
            var preview = document.getElementById('image-preview');
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('d-none');
        });
    });
</script>
@endsection
