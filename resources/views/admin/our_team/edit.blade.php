@extends('admin.layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Form</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Edit  Form</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Our Team</h3>
                        </div>
                             <div class="card-body">
                         <form action="{{ route('our_team.update', $our_team->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Employee Name --}}
        <div class="mb-3">
            <label class="form-label">Employee Name</label>
            <input type="text" class="form-control" name="employee_name" value="{{ old('employee_name', $our_team->employee_name) }}" required>
        </div>

        {{-- Designation --}}
        <div class="mb-3">
            <label class="form-label">Designation</label>
            <input type="text" class="form-control" name="designation" value="{{ old('designation', $our_team->designation) }}" required>
        </div>

        {{-- User ID --}}
        <div class="mb-3">
            <label class="form-label">User ID</label>
            <input type="text" class="form-control" name="user_id" value="{{ old('user_id', $our_team->user_id) }}" required>
        </div>

        {{-- Password --}}
        <div class="mb-3">
            <label class="form-label">Password <small>(Leave blank to keep current password)</small></label>
            <input type="password" class="form-control" name="password">
        </div>

        {{-- Joining Date --}}
        <div class="mb-3">
            <label class="form-label">Joining Date</label>
            <input type="date" class="form-control" name="joining_date" value="{{ old('joining_date', $our_team->joining_date) }}">
        </div>

        {{-- Image --}}
        <div class="mb-3">
            <label class="form-label">Employee Image</label>
            <input type="file" class="form-control" name="employee_image" accept="image/*">
            @if ($our_team->employee_image)
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $our_team->employee_image) }}" alt="Employee Image" style="max-width: 150px;">
                </div>
            @endif
        </div>

        {{-- Facebook --}}
        <div class="mb-3">
            <label class="form-label">Facebook URL</label>
            <input type="url" class="form-control" name="fb_id" value="{{ old('fb_id', $our_team->fb_id) }}">
        </div>

        {{-- Twitter --}}
        <div class="mb-3">
            <label class="form-label">Twitter URL</label>
            <input type="url" class="form-control" name="twitter" value="{{ old('twitter', $our_team->twitter) }}">
        </div>

        {{-- LinkedIn --}}
        <div class="mb-3">
            <label class="form-label">LinkedIn URL</label>
            <input type="url" class="form-control" name="linkedin" value="{{ old('linkedin', $our_team->linkedin) }}">
        </div>

        {{-- Instagram --}}
        <div class="mb-3">
            <label class="form-label">Instagram URL</label>
            <input type="url" class="form-control" name="instagram" value="{{ old('instagram', $our_team->instagram) }}">
        </div>

        {{-- Status --}}
        <div class="form-check mb-3">
            <input type="hidden" name="status" value="0">
            <input type="checkbox" class="form-check-input" id="status" name="status" value="1"
                {{ old('status', $our_team->status) ? 'checked' : '' }}>
            <label class="form-check-label" for="status">Active</label>
        </div>

        <button type="submit" class="btn btn-success">Update Team Member</button>
    </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('styles')
    <!-- Select2 and Bootstrap 4 theme -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #b1b2b1;
            min-height: 38px;
            padding: 5px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #5146C7;
            border-color: #5146C7;
            color: white;
            padding: 0 5px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: white;
            margin-right: 5px;
        }

        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #5146C7;
            box-shadow: 0 0 0 0.2rem rgba(211, 53, 147, 0.25);
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #5146C7;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #f8f9fa;
            color: #495057;
        }
    </style>
@endsection

@section('scripts')
    <!-- jQuery (required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- bs-custom-file-input for Bootstrap 4 file input label -->
    <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>

    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap',
                placeholder: 'Search and select similar properties',
                allowClear: true,
                width: '100%'
            });

            bsCustomFileInput.init();
        });

        function previewImage(event) {
            const file = event.target.files[0];
            const previewContainer = document.getElementById('image-preview-container');
            const previewImage = document.getElementById('image-preview');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.style.display = 'inline-block';
                }
                reader.readAsDataURL(file);
            }
        }

        function removeImage() {
            const input = document.getElementById('image');
            const previewContainer = document.getElementById('image-preview-container');
            const previewImage = document.getElementById('image-preview');

            input.value = '';
            previewImage.src = '#';
            previewContainer.style.display = 'none';
        }
    </script>
@endsection


