@extends('admin.layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>General Form</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Create Form</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

      @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> Please check the form for errors.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">User Permission</h3>
                        </div>
                             <div class="card-body">
                         <form action="{{ route('our_team.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Employee Name --}}
        <div class="mb-3">
            <label for="employee_name" class="form-label">Employee Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="employee_name" name="employee_name" required value="{{ old('employee_name') }}">
        </div>

        {{-- Designation --}}
        <div class="mb-3">
            <label for="designation" class="form-label">Designation <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="designation" name="designation" required value="{{ old('designation') }}">
        </div>

        {{-- User ID --}}
        <div class="mb-3">
            <label for="user_id" class="form-label">User ID <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="user_id" name="user_id" required value="{{ old('user_id') }}">
        </div>

        {{-- Password --}}
        <div class="mb-3">
            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        {{-- Joining Date --}}
        <div class="mb-3">
            <label for="joining_date" class="form-label">Joining Date</label>
            <input type="date" class="form-control" id="joining_date" name="joining_date" value="{{ old('joining_date') }}">
        </div>

        {{-- Profile Image --}}
        <div class="mb-3">
            <label for="employee_image" class="form-label">Profile Image</label>
            <input type="file" class="form-control" id="employee_image" name="employee_image" accept="image/*">
        </div>

        {{-- Social Media Links --}}
        <div class="mb-3">
            <label for="fb_id" class="form-label">Facebook URL</label>
            <input type="url" class="form-control" id="fb_id" name="fb_id" value="{{ old('fb_id') }}">
        </div>

        <div class="mb-3">
            <label for="twitter" class="form-label">Twitter URL</label>
            <input type="url" class="form-control" id="twitter" name="twitter" value="{{ old('twitter') }}">
        </div>

        <div class="mb-3">
            <label for="linkedin" class="form-label">LinkedIn URL</label>
            <input type="url" class="form-control" id="linkedin" name="linkedin" value="{{ old('linkedin') }}">
        </div>

        <div class="mb-3">
            <label for="instagram" class="form-label">Instagram URL</label>
            <input type="url" class="form-control" id="instagram" name="instagram" value="{{ old('instagram') }}">
        </div>

        {{-- Status --}}
        <div class="form-check mb-3">
            <input type="hidden" name="status" value="0">
            <input type="checkbox" class="form-check-input" id="status" name="status" value="1" {{ old('status', true) ? 'checked' : '' }}>
            <label class="form-check-label" for="status">Active</label>
        </div>

        <button type="submit" class="btn btn-primary">Add Team Member</button>
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


