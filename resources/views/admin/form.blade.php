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
                        <li class="breadcrumb-item active">General Form</li>
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
                            <h3 class="card-title">Quick Example</h3>
                        </div>

                        <form method="POST" action="#" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Email address</label>
                                    <input type="email" class="form-control" id="exampleInputEmail1" name="email" placeholder="Enter email">
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputPassword1">Password</label>
                                    <input type="password" class="form-control" id="exampleInputPassword1" name="password" placeholder="Password">
                                </div>

                                <div class="form-group">
                                    <label for="image">Upload Image</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="image" name="image" accept="image/*" onchange="previewImage(event)">
                                            <label class="custom-file-label" for="image">Choose file</label>
                                        </div>
                                    </div>
                                    <div id="image-preview-container" class="mt-2" style="display: none; position: relative;">
                                        <img id="image-preview" src="#" alt="Preview" style="max-width: 150px; border: 1px solid #ddd; border-radius: 4px;">
                                        <button type="button" onclick="removeImage()" style="position: absolute; top: -10px; right: -10px; background: red; color: white; border: none; border-radius: 50%; width: 25px; height: 25px;">&times;</button>
                                    </div>
                                    @error('image')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="exampleCheck1" name="check_me">
                                    <label class="form-check-label" for="exampleCheck1">Check me out</label>
                                </div>

                                <div class="form-group">
                                    <label for="similar_properties">Similar Properties</label>
                                    <select class="form-control select2" id="similar_properties" name="similar_properties[]" multiple="multiple" data-placeholder="Search and select similar properties" style="width: 100%;">
                                        <option value="1">Beautiful 3 BHK Apartment</option>
                                        <option value="2">Luxury Villa with Pool</option>
                                        <option value="3">Modern Penthouse Downtown</option>
                                        <option value="4">Spacious Family House</option>
                                        <option value="5">Commercial Office Space</option>
                                    </select>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
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


