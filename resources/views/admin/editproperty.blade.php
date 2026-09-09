@extends('admin.layout')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper" style="background-color: #ffffff;">
        <!-- Content Header (Page header) -->
        <section class="content-header" style="background-color: #4038A8; color: #ffffff;">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Edit Property</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#" style="color: #b1b2b1;">Dashboard</a></li>
                            <li class="breadcrumb-item active" style="color: #ffffff;">Edit Property</li>
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

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <!-- general form elements -->
                        <div class="card card-primary" style="border-color: #5146C7;">
                            <div class="card-header mt-2" style="background-color: #4038A8; color: #ffffff;">
                                <h3 class="card-title">Property Information</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form method="POST" action="{{ route('admin.properties.update', $property->id) }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Basic Information -->
                                        <div class="col-md-6">
                                            <div class="card card-secondary" style="border-color: #b1b2b1;">
                                                <div class="card-header" style="background-color: #717271; color: #ffffff;">
                                                    <h3 class="card-title">Basic Information</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="title">Property Title*</label>
                                                        <input value="{{ old('title', $property->title) }}" type="text"
                                                            class="form-control" id="title" name="title"
                                                            placeholder="e.g. Beautiful 3 BHK Apartment" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="slug">Slug*</label>
                                                        <input value="{{ old('slug', $property->slug) }}" type="text"
                                                            class="form-control" id="slug" name="slug"
                                                            placeholder="e.g. beautiful-3bhk-apartment" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="description">Description*</label>
                                                        <textarea class="form-control text-editor" id="description" name="description" rows="3"
                                                            placeholder="Detailed description of the property" required>{{ old('description', $property->description) }}</textarea>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="property_type">Property Type*</label>
                                                                <select class="form-control" id="property_type"
                                                                    name="property_type" required>
                                                                    <option value="">Select Type</option>
                                                                    <option value="Residential Plot"
                                                                        {{ old('property_type', $property->property_type) == 'Residential Plot' ? 'selected' : '' }}>
                                                                        Residential Plot</option>
                                                                    <option value="Residential Flat"
                                                                        {{ old('property_type', $property->property_type) == 'Residential Flat' ? 'selected' : '' }}>
                                                                        Residential Flat</option>
                                                                    <option value="Commercial"
                                                                        {{ old('property_type', $property->property_type) == 'Commercial' ? 'selected' : '' }}>
                                                                        Commercial</option>
                                                                    <option value="Villa"
                                                                        {{ old('property_type', $property->property_type) == 'Villa' ? 'selected' : '' }}>
                                                                        Villa</option>
                                                                    <option value="Apartment"
                                                                        {{ old('property_type', $property->property_type) == 'Apartment' ? 'selected' : '' }}>
                                                                        Apartment</option>
                                                                    <option value="Penthouse"
                                                                        {{ old('property_type', $property->property_type) == 'Penthouse' ? 'selected' : '' }}>
                                                                        Penthouse</option>
                                                                    <option value="House"
                                                                        {{ old('property_type', $property->property_type) == 'House' ? 'selected' : '' }}>
                                                                        House</option>
                                                                    <option value="Condo"
                                                                        {{ old('property_type', $property->property_type) == 'Condo' ? 'selected' : '' }}>
                                                                        Condo</option>
                                                                    <option value="Townhouse"
                                                                        {{ old('property_type', $property->property_type) == 'Townhouse' ? 'selected' : '' }}>
                                                                        Townhouse</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="listing_type">Listing Type*</label>
                                                                <select class="form-control" id="listing_type"
                                                                    name="listing_type" required>
                                                                    <option value="">Select Type</option>
                                                                    <option value="For Sale"
                                                                        {{ old('listing_type', $property->listing_type) == 'For Sale' ? 'selected' : '' }}>
                                                                        For Sale</option>
                                                                    <option value="For Resale"
                                                                        {{ old('listing_type', $property->listing_type) == 'For Resale' ? 'selected' : '' }}>
                                                                        For Resale</option>
                                                                    <option value="For Rent"
                                                                        {{ old('listing_type', $property->listing_type) == 'For Rent' ? 'selected' : '' }}>
                                                                        For Rent</option>
                                                                    <option value="Lease"
                                                                        {{ old('listing_type', $property->listing_type) == 'Lease' ? 'selected' : '' }}>
                                                                        Lease</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="price">Price*</label>
                                                                <div class="input-group">
                                                                    <input type="number" class="form-control"
                                                                        id="price" name="price"
                                                                        placeholder="e.g. 500000"
                                                                        value="{{ old('price', $property->price) }}"
                                                                        required>
                                                                    <div class="input-group-append">
                                                                        <select class="form-control" id="price_unit"
                                                                            name="price_unit"
                                                                            style="background-color: #5146C7; color: #ffffff;">
                                                                            <option value="₹"
                                                                                {{ old('price_unit', $property->price_unit) == '₹' ? 'selected' : '' }}>
                                                                                ₹</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="security_deposit">Security Deposit</label>
                                                                <input type="number" class="form-control"
                                                                    id="security_deposit" name="security_deposit"
                                                                    placeholder="e.g. 50000"
                                                                    value="{{ old('security_deposit', $property->security_deposit) }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Location Details -->
                                        <div class="col-md-6">
                                            <div class="card card-secondary" style="border-color: #b1b2b1;">
                                                <div class="card-header"
                                                    style="background-color: #717271; color: #ffffff;">
                                                    <h3 class="card-title">Location Details</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="address">Address*</label>
                                                        <input type="text" class="form-control" id="address"
                                                            name="address" placeholder="Full address"
                                                            value="{{ old('address', $property->address) }}" required>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="city">City*</label>
                                                                <input type="text" class="form-control" id="city"
                                                                    name="city" placeholder="City"
                                                                    value="{{ old('city', $property->city) }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="state">State*</label>
                                                                <input type="text" class="form-control" id="state"
                                                                    name="state" placeholder="State"
                                                                    value="{{ old('state', $property->state) }}" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="zip_code">ZIP Code</label>
                                                                <input type="text" class="form-control" id="zip_code"
                                                                    name="zip_code" placeholder="ZIP/Pincode"
                                                                    value="{{ old('zip_code', $property->zip_code) }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="country">Country</label>
                                                                <input type="text" class="form-control" id="country"
                                                                    name="country" value="India" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="latitude">Latitude</label>
                                                                <input type="text" class="form-control" id="latitude"
                                                                    name="latitude" placeholder="e.g. 28.6139"
                                                                    value="{{ old('latitude', $property->latitude) }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="longitude">Longitude</label>
                                                                <input type="text" class="form-control" id="longitude"
                                                                    name="longitude" placeholder="e.g. 77.2090"
                                                                    value="{{ old('longitude', $property->longitude) }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="google_map_link">Google Map Link</label>
                                                        <input type="url" class="form-control" id="google_map_link"
                                                            name="google_map_link"
                                                            placeholder="https://maps.google.com/..."
                                                            value="{{ old('google_map_link', $property->google_map_link) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Property Details -->
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="card card-secondary" style="border-color: #b1b2b1;">
                                                <div class="card-header"
                                                    style="background-color: #717271; color: #ffffff;">
                                                    <h3 class="card-title">Property Details</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="bedrooms">Bedrooms</label>
                                                                <input type="number" class="form-control" id="bedrooms"
                                                                    name="bedrooms" placeholder="0"
                                                                    value="{{ old('bedrooms', $property->bedrooms) }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="bathrooms">Bathrooms</label>
                                                                <input type="number" class="form-control" id="bathrooms"
                                                                    name="bathrooms" placeholder="0"
                                                                    value="{{ old('bathrooms', $property->bathrooms) }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="balconies">Balconies</label>
                                                                <input type="number" class="form-control" id="balconies"
                                                                    name="balconies" placeholder="0"
                                                                    value="{{ old('balconies', $property->balconies) }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="floors">Total Floors</label>
                                                                <input type="number" class="form-control" id="floors"
                                                                    name="floors" placeholder="e.g. 10"
                                                                    value="{{ old('floors', $property->floors) }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="floor_number">Floor Number</label>
                                                                <input type="number" class="form-control"
                                                                    id="floor_number" name="floor_number"
                                                                    placeholder="e.g. 5"
                                                                    value="{{ old('floor_number', $property->floor_number) }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="super_area">Super Area (sq.ft)</label>
                                                                <input type="number" step="0.01" class="form-control"
                                                                    id="super_area" name="super_area"
                                                                    placeholder="e.g. 1200"
                                                                    value="{{ old('super_area', $property->super_area) }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="carpet_area">Carpet Area (sq.ft)</label>
                                                                <input type="number" step="0.01" class="form-control"
                                                                    id="carpet_area" name="carpet_area"
                                                                    placeholder="e.g. 1000"
                                                                    value="{{ old('carpet_area', $property->carpet_area) }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="plot_area">Plot Area (sq.ft)</label>
                                                                <input type="number" step="0.01" class="form-control"
                                                                    id="plot_area" name="plot_area"
                                                                    placeholder="e.g. 2400"
                                                                    value="{{ old('plot_area', $property->plot_area) }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="year_built">Year Built</label>
                                                                <input type="number" class="form-control"
                                                                    id="year_built" name="year_built"
                                                                    placeholder="e.g. 2015"
                                                                    value="{{ old('year_built', $property->year_built) }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="age_of_property">Age of Property</label>
                                                                <input type="number" class="form-control"
                                                                    id="age_of_property" name="age_of_property"
                                                                    placeholder="e.g. 5"
                                                                    value="{{ old('age_of_property', $property->age_of_property) }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Furnishing & Features -->
                                        <div class="col-md-6">
                                            <div class="card card-secondary" style="border-color: #b1b2b1;">
                                                <div class="card-header"
                                                    style="background-color: #717271; color: #ffffff;">
                                                    <h3 class="card-title">Furnishing & Features</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="furnishing">Furnishing</label>
                                                        <select class="form-control" id="furnishing" name="furnishing">
                                                            <option value="">Select Furnishing</option>
                                                            <option value="Fully Furnished"
                                                                {{ old('furnishing', $property->furnishing) == 'Fully Furnished' ? 'selected' : '' }}>
                                                                Fully Furnished</option>
                                                            <option value="Semi Furnished"
                                                                {{ old('furnishing', $property->furnishing) == 'Semi Furnished' ? 'selected' : '' }}>
                                                                Semi Furnished</option>
                                                            <option value="Unfurnished"
                                                                {{ old('furnishing', $property->furnishing) == 'Unfurnished' ? 'selected' : '' }}>
                                                                Unfurnished</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Features</label>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                @php
                                                                    $features = old('features', $property->features ?? []);
                                                                    $features = is_array($features) ? $features : [];
                                                                @endphp
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="features[]" value="Swimming Pool"
                                                                        {{ in_array('Swimming Pool', $features) ? 'checked' : '' }}
                                                                        style="accent-color: #5146C7;">
                                                                    <label class="form-check-label">Swimming Pool</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="features[]" value="Gym"
                                                                        {{ in_array('Gym', $features) ? 'checked' : '' }}
                                                                        style="accent-color: #5146C7;">
                                                                    <label class="form-check-label">Gym</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="features[]" value="Parking"
                                                                        {{ in_array('Parking', $features) ? 'checked' : '' }}
                                                                        style="accent-color: #5146C7;">
                                                                    <label class="form-check-label">Parking</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="features[]" value="Garden"
                                                                        {{ in_array('Garden', $features) ? 'checked' : '' }}
                                                                        style="accent-color: #5146C7;">
                                                                    <label class="form-check-label">Garden</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="features[]" value="Security"
                                                                        {{ in_array('Security', $features) ? 'checked' : '' }}
                                                                        style="accent-color: #5146C7;">
                                                                    <label class="form-check-label">Security</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="features[]" value="Lift"
                                                                        {{ in_array('Lift', $features) ? 'checked' : '' }}
                                                                        style="accent-color: #5146C7;">
                                                                    <label class="form-check-label">Lift</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="features[]" value="Power Backup"
                                                                        {{ in_array('Power Backup', $features) ? 'checked' : '' }}
                                                                        style="accent-color: #5146C7;">
                                                                    <label class="form-check-label">Power Backup</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="features[]" value="WiFi"
                                                                        {{ in_array('WiFi', $features) ? 'checked' : '' }}
                                                                        style="accent-color: #5146C7;">
                                                                    <label class="form-check-label">WiFi</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Amenities</label>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                @php
                                                                    $amenities = old('amenities', $property->amenities ?? []);
                                                                    $amenities = is_array($amenities) ? $amenities : [];
                                                                @endphp
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="amenities[]" value="Air Conditioning"
                                                                        {{ in_array('Air Conditioning', $amenities) ? 'checked' : '' }}
                                                                        style="accent-color: #5146C7;">
                                                                    <label class="form-check-label">Air
                                                                        Conditioning</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="amenities[]" value="Heating"
                                                                        {{ in_array('Heating', $amenities) ? 'checked' : '' }}
                                                                        style="accent-color: #5146C7;">
                                                                    <label class="form-check-label">Heating</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="amenities[]" value="TV"
                                                                        {{ in_array('TV', $amenities) ? 'checked' : '' }}
                                                                        style="accent-color: #5146C7;">
                                                                    <label class="form-check-label">TV</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="amenities[]" value="Washing Machine"
                                                                        {{ in_array('Washing Machine', $amenities) ? 'checked' : '' }}
                                                                        style="accent-color: #5146C7;">
                                                                    <label class="form-check-label">Washing Machine</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="amenities[]" value="Microwave"
                                                                        {{ in_array('Microwave', $amenities) ? 'checked' : '' }}
                                                                        style="accent-color: #5146C7;">
                                                                    <label class="form-check-label">Microwave</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="amenities[]" value="Refrigerator"
                                                                        {{ in_array('Refrigerator', $amenities) ? 'checked' : '' }}
                                                                        style="accent-color: #5146C7;">
                                                                    <label class="form-check-label">Refrigerator</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="amenities[]" value="Dishwasher"
                                                                        {{ in_array('Dishwasher', $amenities) ? 'checked' : '' }}
                                                                        style="accent-color: #5146C7;">
                                                                    <label class="form-check-label">Dishwasher</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="amenities[]" value="Balcony"
                                                                        {{ in_array('Balcony', $amenities) ? 'checked' : '' }}
                                                                        style="accent-color: #5146C7;">
                                                                    <label class="form-check-label">Balcony</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Availability & Media -->
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="card card-secondary" style="border-color: #b1b2b1;">
                                                <div class="card-header"
                                                    style="background-color: #717271; color: #ffffff;">
                                                    <h3 class="card-title">Availability & Status</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="availability">Availability*</label>
                                                        <select class="form-control" id="availability"
                                                            name="availability" required>
                                                            <option value="Immediate"
                                                                {{ old('availability', $property->availability) == 'Immediate' ? 'selected' : '' }}>
                                                                Immediate</option>
                                                            <option value="After Date"
                                                                {{ old('availability', $property->availability) == 'After Date' ? 'selected' : '' }}>
                                                                After Date</option>
                                                            <option value="Negotiable"
                                                                {{ old('availability', $property->availability) == 'Negotiable' ? 'selected' : '' }}>
                                                                Negotiable</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group" id="available_from_group">
                                                        <label for="available_from">Available From</label>
                                                        <input type="date" class="form-control" id="available_from"
                                                            name="available_from"
                                                            value="{{ old('available_from', $property->available_from) }}">
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="preferred_tenants">Preferred Tenants</label>
                                                        <select class="form-control" id="preferred_tenants"
                                                            name="preferred_tenants">
                                                            <option value="">Anyone</option>
                                                            <option value="Family"
                                                                {{ old('preferred_tenants', $property->preferred_tenants) == 'Family' ? 'selected' : '' }}>
                                                                Family</option>
                                                            <option value="Professionals"
                                                                {{ old('preferred_tenants', $property->preferred_tenants) == 'Professionals' ? 'selected' : '' }}>
                                                                Professionals</option>
                                                            <option value="Students"
                                                                {{ old('preferred_tenants', $property->preferred_tenants) == 'Students' ? 'selected' : '' }}>
                                                                Students</option>
                                                            <option value="Company"
                                                                {{ old('preferred_tenants', $property->preferred_tenants) == 'Company' ? 'selected' : '' }}>
                                                                Company</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="property_status">Property Status</label>
                                                        <select class="form-control" id="property_status"
                                                            name="property_status">
                                                            <option value="Available"
                                                                {{ old('property_status', $property->property_status) == 'Available' ? 'selected' : '' }}>
                                                                Available</option>
                                                            <option value="Rented"
                                                                {{ old('property_status', $property->property_status) == 'Rented' ? 'selected' : '' }}>
                                                                Rented</option>
                                                            <option value="Sold"
                                                                {{ old('property_status', $property->property_status) == 'Sold' ? 'selected' : '' }}>
                                                                Sold</option>
                                                            <option value="Under Maintenance"
                                                                {{ old('property_status', $property->property_status) == 'Under Maintenance' ? 'selected' : '' }}>
                                                                Under Maintenance</option>
                                                        </select>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <div class="custom-control custom-checkbox">
                                                                    <input class="custom-control-input" type="checkbox"
                                                                        id="is_featured" name="is_featured"
                                                                        value="1"
                                                                        {{ old('is_featured', $property->is_featured) ? 'checked' : '' }}
                                                                        style="accent-color: #5146C7;">
                                                                    <label for="is_featured"
                                                                        class="custom-control-label">Featured
                                                                        Property</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <div class="custom-control custom-checkbox">
                                                                    <input class="custom-control-input" type="checkbox"
                                                                        id="is_verified" name="is_verified"
                                                                        value="1"
                                                                        {{ old('is_verified', $property->is_verified) ? 'checked' : '' }}
                                                                        style="accent-color: #5146C7;">
                                                                    <label for="is_verified"
                                                                        class="custom-control-label">Verified
                                                                        Property</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Media & Documents -->
                                        <div class="col-md-6">
                                            <div class="card card-secondary" style="border-color: #b1b2b1;">
                                                <div class="card-header"
                                                    style="background-color: #717271; color: #ffffff;">
                                                    <h3 class="card-title">Media & Documents</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="image">Main Image</label>
                                                        <div class="input-group">
                                                            <div class="custom-file">
                                                                <input type="file" class="custom-file-input"
                                                                    id="image" name="main_image" accept="image/*"
                                                                    onchange="previewImage(event)">
                                                                <label class="custom-file-label" for="image">Choose
                                                                    file</label>
                                                            </div>
                                                        </div>
                                                        @if ($property->main_image)
                                                            <div class="mt-2">
                                                                <img src="{{ asset($property->main_image) }}"
                                                                    width="100" class="img-thumbnail">
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="property_images">Additional Images</label>
                                                        <div class="input-group">
                                                            <div class="custom-file">
                                                                <input type="file" class="custom-file-input"
                                                                    id="property_images" name="property_images[]"
                                                                    accept="image/*" multiple
                                                                    onchange="previewAdditionalImages(event)">
                                                                <label class="custom-file-label"
                                                                    for="property_images">Choose files</label>
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">You can select multiple images</small>

                                                        <!-- Existing Images -->
                                                        @if ($property->images->count() > 0)
                                                            <div class="row mt-2">
                                                                @foreach ($property->images as $image)
                                                                    <div class="col-md-3 mb-2 position-relative">
                                                                        <img src="{{ asset($image->image_path) }}"
                                                                            class="img-thumbnail" width="100">
                                                                        <a href="{{ route('admin.properties.deleteImage', $image->id) }}"
                                                                            class="btn btn-danger btn-sm position-absolute"
                                                                            style="top: 0; right: 0;"
                                                                            onclick="return confirm('Are you sure?')">×</a>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="video_url">Video URL</label>
                                                        <input type="url" class="form-control" id="video_url"
                                                            name="video_url" placeholder="YouTube/Vimeo link"
                                                            value="{{ old('video_url', $property->video_url) }}">
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="floor_plan_image">Floor Plan Image</label>
                                                        <div class="input-group">
                                                            <div class="custom-file">
                                                                <input type="file" class="custom-file-input"
                                                                    id="floor_plan_image" name="floor_plan_image"
                                                                    accept="image/*" onchange="previewFloorPlan(event)">
                                                                <label class="custom-file-label"
                                                                    for="floor_plan_image">Choose file</label>
                                                            </div>
                                                        </div>
                                                        @if ($property->floor_plan_image)
                                                            <div class="mt-2">
                                                                <img src="{{ asset($property->floor_plan_image) }}"
                                                                    width="100" class="img-thumbnail">
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="brochure">Brochure (PDF)</label>
                                                        <div class="input-group">
                                                            <div class="custom-file">
                                                                <input type="file" class="custom-file-input"
                                                                    id="brochure" name="brochure" accept=".pdf">
                                                                <label class="custom-file-label" for="brochure">Choose
                                                                    file</label>
                                                            </div>
                                                        </div>
                                                        @if ($property->brochure)
                                                            <div class="mt-2">
                                                                <a href="{{ asset($property->brochure) }}"
                                                                    target="_blank" class="btn btn-sm btn-info">View
                                                                    Brochure</a>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Nearby Places & Connectivity -->
                                    <h4 class="text-muted border-bottom pb-2 mb-3">Nearby Amenities & Connectivity</h4>
                                    <div class="row mt-3">
                                        <!-- Nearby Places -->
                                        <div class="col-md-6">
                                            <div class="card card-secondary" style="border-color: #b1b2b1;">
                                                <div class="card-header"
                                                    style="background-color: #717271; color: #ffffff;">
                                                    <h3 class="card-title"><i class="fas fa-map-marker-alt"></i> Nearby
                                                        Places</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="bazar"><i class="fas fa-store"></i> Bazar</label>
                                                        <input type="text" class="form-control" id="bazar"
                                                            name="bazar_distance_km"
                                                            placeholder="e.g. Main Market (0.5 km)"
                                                            value="{{ old('bazar_distance_km', $property->bazar_distance_km) }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="hospital"><i class="fas fa-hospital"></i>
                                                            Hospital</label>
                                                        <input type="text" class="form-control" id="hospital"
                                                            name="hospital_distance_km"
                                                            placeholder="e.g. City Hospital (1.2 km)"
                                                            value="{{ old('hospital_distance_km', $property->hospital_distance_km) }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="school"><i class="fas fa-school"></i> School</label>
                                                        <input type="text" class="form-control" id="school"
                                                            name="school_distance_km"
                                                            placeholder="e.g. Sunrise Public School (1.5 km)"
                                                            value="{{ old('school_distance_km', $property->school_distance_km) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Connectivity -->
                                        <div class="col-md-6">
                                            <div class="card card-secondary" style="border-color: #b1b2b1;">
                                                <div class="card-header"
                                                    style="background-color: #717271; color: #ffffff;">
                                                    <h3 class="card-title"><i class="fas fa-route"></i> Connectivity</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="bus_stand"><i class="fas fa-bus"></i> Bus
                                                            Stand</label>
                                                        <input type="text" class="form-control" id="bus_stand"
                                                            name="bus_stand_distance_km"
                                                            placeholder="e.g. Gandhi Maidan Bus Stand (0.8 km)"
                                                            value="{{ old('bus_stand_distance_km', $property->bus_stand_distance_km) }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="junction"><i class="fas fa-train"></i> Railway
                                                            Junction</label>
                                                        <input type="text" class="form-control" id="junction"
                                                            name="junction_distance_km"
                                                            placeholder="e.g. Patna Junction (1.2 km)"
                                                            value="{{ old('junction_distance_km', $property->junction_distance_km) }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="airport"><i class="fas fa-plane"></i> Airport</label>
                                                        <input type="text" class="form-control" id="airport"
                                                            name="airport_distance_km"
                                                            placeholder="e.g. Patna Airport (5 km)"
                                                            value="{{ old('airport_distance_km', $property->airport_distance_km) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Additional Information -->
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <div class="card card-secondary" style="border-color: #b1b2b1;">
                                                    <div class="card-header"
                                                        style="background-color: #717271; color: #ffffff;">
                                                        <h3 class="card-title">Additional Information</h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="form-group">
                                                            <label for="similar_properties">Similar Properties</label>
                                                            <select class="form-control select2" id="similar_properties"
                                                                name="similar_properties[]" multiple="multiple"
                                                                data-placeholder="Search and select similar properties"
                                                                style="width: 100%;">
                                                                {{-- @foreach ($property as $prop)
                                                                    <option value="{{ $prop->id }}"
                                                                        {{ in_array($prop->id, old('similar_properties', $property->similarProperties->pluck('id')->toArray() ?? [])) ? 'selected' : '' }}>
                                                                        {{ $prop->title }} ({{ $prop->property_id }})
                                                                    </option>
                                                                @endforeach --}}
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="keyfeatures">Key Features</label>
                                                            <textarea class="form-control text-editor" id="keyfeatures" name="keyfeatures" rows="3"
                                                                placeholder="List key features of the property">{{ old('keyfeatures', $property->keyfeatures) }}</textarea>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="notes">Notes</label>
                                                            <textarea class="form-control text-editor" id="notes" name="notes" rows="3"
                                                                placeholder="Any additional notes">{{ old('notes', $property->notes) }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->

                                    <div class="card-footer" style="background-color: #f8f9fa;">
                                        <button type="submit" class="btn btn-primary"
                                            style="background-color: #5146C7; border-color: #5146C7;">Update
                                            Property</button>
                                        <a href="{{ route('admin.properties.list') }}" class="btn btn-secondary"
                                            style="background-color: #717271; border-color: #717271;">Cancel</a>
                                    </div>
                            </form>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div>
        </section>
    </div>

@section('scripts')
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Summernote -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                placeholder: 'Search and select similar properties',
                allowClear: true
            });

            // Initialize Summernote
            $('.text-editor').summernote({
                height: 150,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });

            // Show/hide available from date based on availability selection
            $('#availability').change(function() {
                if ($(this).val() === 'After Date') {
                    $('#available_from_group').show();
                } else {
                    $('#available_from_group').hide();
                }
            }).trigger('change');

            // Auto-generate slug from title
            $('#title').on('input', function() {
                const title = $(this).val();
                const slug = title.toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/[\s_-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                $('#slug').val(slug);
            });

            // Image preview functions
            function previewImage(event) {
                const file = event.target.files[0];
                const previewContainer = $('#image-preview-container');
                const previewImage = $('#image-preview');

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.attr('src', e.target.result);
                        previewContainer.show();
                    }
                    reader.readAsDataURL(file);
                }
            }

            function previewAdditionalImages(event) {
                const files = event.target.files;
                const previewContainer = $('#additional_images_preview');
                previewContainer.empty();

                if (files) {
                    for (let i = 0; i < files.length; i++) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const div = $('<div class="col-md-3 mb-2"></div>');
                            div.html(`
                                <div style="position: relative;">
                                    <img src="${e.target.result}" alt="Preview" style="max-width: 100%; height: 100px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;">
                                    <button type="button" onclick="removeAdditionalImage(${i})" style="position: absolute; top: -10px; right: -10px; background: red; color: white; border: none; border-radius: 50%; width: 25px; height: 25px;">×</button>
                                </div>
                            `);
                            previewContainer.append(div);
                        }
                        reader.readAsDataURL(files[i]);
                    }
                }
            }

            function previewFloorPlan(event) {
                const file = event.target.files[0];
                const previewContainer = $('#floor_plan_preview');
                const previewImage = $('#floor_plan_preview_img');

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.attr('src', e.target.result);
                        previewContainer.show();
                    }
                    reader.readAsDataURL(file);
                }
            }

            // Make functions available globally
            window.previewImage = previewImage;
            window.previewAdditionalImages = previewAdditionalImages;
            window.previewFloorPlan = previewFloorPlan;

            // Remove image functions
            window.removeImage = function() {
                $('#image').val('');
                $('#image-preview').attr('src', '#');
                $('#image-preview-container').hide();
            }

            window.removeAdditionalImage = function(index) {
                const input = document.getElementById('property_images');
                const files = Array.from(input.files);
                files.splice(index, 1);

                const dataTransfer = new DataTransfer();
                files.forEach(file => dataTransfer.items.add(file));
                input.files = dataTransfer.files;

                const event = new Event('change');
                input.dispatchEvent(event);
            }

            window.removeFloorPlan = function() {
                $('#floor_plan_image').val('');
                $('#floor_plan_preview_img').attr('src', '#');
                $('#floor_plan_preview').hide();
            }
        });
    </script>
@endsection
@endsection


