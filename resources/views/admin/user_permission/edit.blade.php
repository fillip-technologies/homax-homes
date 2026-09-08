@extends('admin.layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>User Permission</h1>
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

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">User Permission</h3>
                        </div>
                             <div class="card-body">
                         <form action="{{ route('user_permission.update', $user->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <input type="hidden" name="user_id" value="{{ $user->id }}">

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="all_property" id="all_property" value="1"
                                        {{ old('all_property', $permission->all_property ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="all_property">All Property</label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="featured_image" id="featured_image" value="1"
                                        {{ old('featured_image', $permission->featured_image ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="featured_image">Featured Image</label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="add_now" id="add_now" value="1"
                                        {{ old('add_now', $permission->add_now ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="add_now">Add Now</label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="property_image" id="property_image" value="1"
                                        {{ old('property_image', $permission->property_image ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="property_image">Property Image</label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="our_team" id="our_team" value="1"
                                        {{ old('our_team', $permission->our_team ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="our_team">Our Team</label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="blog" id="blog" value="1"
                                        {{ old('blog', $permission->blog ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="blog">Blog</label>
                                </div>

                                <button type="submit" class="btn btn-success mt-3">Save Permissions</button>
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


