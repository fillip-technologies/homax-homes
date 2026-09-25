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
        </div>
    </section>
</div>
@endsection
