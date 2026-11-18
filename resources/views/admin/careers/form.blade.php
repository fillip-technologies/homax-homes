@extends('admin.layout')

@php $editing = $opening->exists; @endphp

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>{{ $editing ? 'Edit Job Opening' : 'Add Job Opening' }}</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.careers.index') }}">Careers</a></li>
                        <li class="breadcrumb-item active">{{ $editing ? 'Edit' : 'Add' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    @include('admin.partials.alerts')

    <section class="content">
        <div class="container-fluid">
            <form action="{{ $editing ? route('admin.careers.update', $opening) : route('admin.careers.store') }}" method="POST">
                @csrf
                @if ($editing) @method('PUT') @endif

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card card-primary">
                            <div class="card-header"><h3 class="card-title">Role</h3></div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="title">Job title <span class="text-danger">*</span></label>
                                    <input type="text" id="title" name="title" maxlength="150" required
                                        class="form-control @error('title') is-invalid @enderror"
                                        value="{{ old('title', $opening->title) }}">
                                </div>

                                <div class="form-group">
                                    <label for="slug">URL slug</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">/careers/</span></div>
                                        <input type="text" id="slug" name="slug" maxlength="200" class="form-control"
                                            placeholder="auto-generated from the title" value="{{ old('slug', $opening->slug) }}">
                                    </div>
                                    <small class="text-muted">Leave blank to generate it from the title. Changing it changes the opening's link.</small>
                                </div>

                                <div class="form-group">
                                    <label for="summary">Short summary</label>
                                    <textarea id="summary" name="summary" rows="3" maxlength="500" class="form-control"
                                        placeholder="One or two sentences shown on the job card.">{{ old('summary', $opening->summary) }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label for="description">Full description</label>
                                    <textarea id="description" name="description" class="form-control">{{ old('description', $opening->description) }}</textarea>
                                    <small class="text-muted">Responsibilities, requirements, what you offer. Shown on the opening's own page.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header"><h3 class="card-title">Details</h3></div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="employment_type">Job type <span class="text-danger">*</span></label>
                                    <select id="employment_type" name="employment_type" class="form-control">
                                        @foreach (\App\Models\JobOpening::TYPES as $value => $label)
                                            <option value="{{ $value }}" @selected(old('employment_type', $opening->employment_type) === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="department">Department</label>
                                    <input type="text" id="department" name="department" maxlength="100" class="form-control"
                                        placeholder="e.g. Sales" value="{{ old('department', $opening->department) }}">
                                </div>
                                <div class="form-group">
                                    <label for="location">Location</label>
                                    <input type="text" id="location" name="location" maxlength="100" class="form-control"
                                        placeholder="e.g. Mumbai" value="{{ old('location', $opening->location) }}">
                                </div>
                                <div class="form-group">
                                    <label for="experience">Experience</label>
                                    <input type="text" id="experience" name="experience" maxlength="100" class="form-control"
                                        placeholder="e.g. 2-4 years" value="{{ old('experience', $opening->experience) }}">
                                </div>
                                <div class="form-group">
                                    <label for="salary">Salary <small class="text-muted">(optional)</small></label>
                                    <input type="text" id="salary" name="salary" maxlength="100" class="form-control"
                                        placeholder="e.g. 4-6 LPA + incentives" value="{{ old('salary', $opening->salary) }}">
                                </div>
                                <div class="form-group">
                                    <label for="highlights">Highlights</label>
                                    <input type="text" id="highlights" name="highlights" maxlength="255" class="form-control"
                                        placeholder="Flexible Hours, Training Provided" value="{{ old('highlights', $opening->highlights) }}">
                                    <small class="text-muted">Comma separated; shown as tags.</small>
                                </div>
                                <div class="form-group">
                                    <label for="sort_order">Order</label>
                                    <input type="number" id="sort_order" name="sort_order" min="0" max="9999" class="form-control"
                                        placeholder="auto" value="{{ old('sort_order', $opening->exists ? $opening->sort_order : '') }}">
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                                        {{ old('is_active', $opening->is_active) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">Show on website</label>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary btn-block">{{ $editing ? 'Save changes' : 'Add opening' }}</button>
                                <a href="{{ route('admin.careers.index') }}" class="btn btn-link btn-block">Cancel</a>
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
        $('#description').summernote({
            height: 360,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'hr']],
                ['view', ['codeview']]
            ],
            styleTags: ['p', 'h2', 'h3', 'h4']
        });
    });
</script>
@endsection
