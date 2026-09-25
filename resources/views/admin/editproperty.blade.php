@extends('admin.layout')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper" style="background-color: #ffffff;">
        <!-- Content Header (Page header) -->
        <section class="content-header" style="background-color: #000066; color: #ffffff;">
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
                        <div class="card card-primary" style="border-color: #000080;">
                            <div class="card-header mt-2" style="background-color: #000066; color: #ffffff;">
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
                                                        <label for="title">Project Name*</label>
                                                        <input value="{{ old('title', $property->title) }}" type="text"
                                                            class="form-control" id="title" name="title"
                                                            placeholder="e.g. Beautiful 3 BHK Apartment" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="developer_name">Developer / Builder Name</label>
                                                        <input value="{{ old('developer_name', $property->developer_name) }}" type="text"
                                                            class="form-control" id="developer_name" name="developer_name"
                                                            placeholder="e.g. Godrej Properties, DLF">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="slug">Slug*</label>
                                                        <input value="{{ old('slug', $property->slug) }}" type="text"
                                                            class="form-control" id="slug" name="slug"
                                                            placeholder="e.g. beautiful-3bhk-apartment" readonly required>
                                                        <small class="text-muted">The slug can't be changed after creation.</small>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="description">Description*</label>
                                                        <textarea class="form-control text-editor" id="description" name="description" rows="3"
                                                            placeholder="Detailed description of the property" required>{{ old('description', $property->description) }}</textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="category">Category*</label>
                                                        <select class="form-control" id="category"
                                                            name="category" required>
                                                            <option value="Residential"
                                                                {{ old('category', $property->category ?? 'Residential') == 'Residential' ? 'selected' : '' }}>
                                                                Residential</option>
                                                            <option value="Commercial"
                                                                {{ old('category', $property->category) == 'Commercial' ? 'selected' : '' }}>
                                                                Commercial</option>
                                                        </select>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="price">Price Starting*</label>
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control"
                                                                        id="price" name="price"
                                                                        placeholder="e.g. 50L-70L"
                                                                        value="{{ old('price', $property->price) }}"
                                                                        required>
                                                                    <div class="input-group-append">
                                                                        <select class="form-control" id="price_unit"
                                                                            name="price_unit"
                                                                            style="background-color: #000080; color: #ffffff;">
                                                                            <option value="₹"
                                                                                {{ old('price_unit', $property->price_unit) == '₹' ? 'selected' : '' }}>
                                                                                ₹</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <small id="price_in_words" class="form-text mt-1 font-weight-bold" style="color: #000080 !important; display: none;"><i class="fas fa-info-circle mr-1"></i><span id="price_in_words_text"></span></small>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="rera_id">RERA ID</label>
                                                                <input type="text" class="form-control"
                                                                    id="rera_id" name="rera_id"
                                                                    placeholder="e.g. PRM/KA/RERA/1251/..."
                                                                    value="{{ old('rera_id', $property->rera_id) }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
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
                                                        <small id="location_autofill_status" class="form-text mt-1 font-weight-bold" style="display: none;"></small>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="location">Locality / Area</label>
                                                                <input type="text" class="form-control" id="location"
                                                                    name="location" placeholder="e.g. Sector 5, Kharghar"
                                                                    value="{{ old('location', $property->location) }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="landmark">Landmark</label>
                                                                <input type="text" class="form-control" id="landmark"
                                                                    name="landmark" placeholder="e.g. Near City Mall"
                                                                    value="{{ old('landmark', $property->landmark) }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="city">City*</label>
                                                                <input type="text" class="form-control" id="city" name="city"
                                                                    list="city_datalist" placeholder="City"
                                                                    value="{{ old('city', $property->city) }}" required>
                                                                <datalist id="city_datalist">
                                                                    <option value="Mumbai">
                                                                    <option value="Navi Mumbai">
                                                                    <option value="Thane">
                                                                    <option value="Panvel">
                                                                </datalist>
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
                                                                <small id="zip_autofill_status" class="form-text mt-1 font-weight-bold" style="display: none;"></small>
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
                                        <div class="col-md-12">
                                            <div class="card card-secondary" style="border-color: #b1b2b1;">
                                                <div class="card-header d-flex justify-content-between align-items-center"
                                                    style="background-color: #717271; color: #ffffff;">
                                                    <h3 class="card-title mb-0"><i class="fas fa-layer-group mr-1"></i> Property Details / Configurations</h3>
                                                    <button type="button" class="btn btn-sm btn-light ml-auto font-weight-bold" id="add_detail_btn" style="color: #333;">
                                                        <i class="fas fa-plus text-success mr-1"></i> Add Details
                                                    </button>
                                                </div>
                                                <div class="card-body">
                                                    <p class="text-muted small mb-3">Add or edit configurations/units (e.g. 1 BHK, 2 BHK, 3 BHK, Penthouse) for this property.</p>
                                                    <div class="form-row mb-3">
                                                        <div class="col-md-3">
                                                            <label class="small font-weight-bold" for="apartment_per_floor">Apartments Per Floor</label>
                                                            <input type="text" class="form-control form-control-sm" id="apartment_per_floor" name="apartment_per_floor" placeholder="e.g. 4" value="{{ old('apartment_per_floor', $property->apartment_per_floor) }}">
                                                            <small class="text-muted">Applies to all configurations below.</small>
                                                        </div>
                                                    </div>
                                                    <datalist id="unit_type_datalist">
                                                        @include('admin.partials.unit-type-options')
                                                    </datalist>
                                                    <datalist id="count_datalist">
                                                        @include('admin.partials.count-options')
                                                    </datalist>
                                                    <div id="property_details_container">
                                                        @php
                                                            $detailsList = ($property->details && $property->details->count() > 0) ? $property->details : collect([null]);
                                                        @endphp
                                                        @foreach ($detailsList as $index => $detail)
                                                            <div class="property-detail-item border rounded p-3 mb-3" style="background-color: #fcfcfc; border-color: #dcdcdc !important;">
                                                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                                                    <h6 class="mb-0 font-weight-bold text-dark detail-item-title">
                                                                        <i class="fas fa-home mr-1 text-secondary"></i> Configuration #{{ $index + 1 }}
                                                                    </h6>
                                                                    <button type="button" class="btn btn-sm btn-outline-danger remove-detail-btn font-weight-bold" style="{{ count($detailsList) > 1 ? '' : 'display: none;' }}">
                                                                        <i class="fas fa-trash-alt mr-1"></i> Remove
                                                                    </button>
                                                                </div>
                                                                <div class="row">
                                                                    @php
                                                                        $detailUnitType = old("property_details.$index.unit_type", $detail ? $detail->unit_type : '');
                                                                        $detailBedrooms = old("property_details.$index.bedrooms", $detail ? $detail->bedrooms : $property->bedrooms);
                                                                        $detailBathrooms = old("property_details.$index.bathrooms", $detail ? $detail->bathrooms : $property->bathrooms);
                                                                        $detailBalconies = old("property_details.$index.balconies", $detail ? $detail->balconies : $property->balconies);
                                                                    @endphp
                                                                    <div class="col-md-3">
                                                                        <div class="form-group mb-2">
                                                                            <label class="small font-weight-bold">Unit Type</label>
                                                                            <input type="text" class="form-control form-control-sm" list="unit_type_datalist" name="property_details[{{ $index }}][unit_type]"
                                                                                placeholder="e.g. 2 BHK, 3 BHK"
                                                                                value="{{ $detailUnitType }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="form-group mb-2">
                                                                            <label class="small font-weight-bold">Bedrooms</label>
                                                                            <input type="number" min="0" list="count_datalist" class="form-control form-control-sm" name="property_details[{{ $index }}][bedrooms]"
                                                                                placeholder="0"
                                                                                value="{{ $detailBedrooms }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="form-group mb-2">
                                                                            <label class="small font-weight-bold">Bathrooms</label>
                                                                            <input type="number" min="0" list="count_datalist" class="form-control form-control-sm" name="property_details[{{ $index }}][bathrooms]"
                                                                                placeholder="0"
                                                                                value="{{ $detailBathrooms }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="form-group mb-2">
                                                                            <label class="small font-weight-bold">Balconies</label>
                                                                            <input type="number" min="0" list="count_datalist" class="form-control form-control-sm" name="property_details[{{ $index }}][balconies]"
                                                                                placeholder="0"
                                                                                value="{{ $detailBalconies }}">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-3">
                                                                        <div class="form-group mb-2">
                                                                            <label class="small font-weight-bold">Carpet Area (sq.ft)</label>
                                                                            <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="property_details[{{ $index }}][carpet_area]"
                                                                                placeholder="e.g. 850"
                                                                                value="{{ old("property_details.$index.carpet_area", $detail ? $detail->carpet_area : $property->carpet_area) }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="form-group mb-2">
                                                                            <label class="small font-weight-bold">Super Built up Area (sq.ft)</label>
                                                                            <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="property_details[{{ $index }}][super_area]"
                                                                                placeholder="e.g. 1100"
                                                                                value="{{ old("property_details.$index.super_area", $detail ? $detail->super_area : $property->super_area) }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="form-group mb-2">
                                                                            <label class="small font-weight-bold">Price</label>
                                                                            <input type="text" class="form-control form-control-sm" name="property_details[{{ $index }}][price]"
                                                                                placeholder="e.g. 75 Lakh or 1.25 Cr"
                                                                                value="{{ old("property_details.$index.price", $detail ? $detail->price : '') }}">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group mb-0">
                                                                            <label class="small font-weight-bold"><i class="fas fa-file-pdf mr-1 text-danger"></i> Configuration Document / Costing Sheet (PDF/DOCX)</label>
                                                                            @if ($detail && $detail->document)
                                                                                <div class="mb-1 d-flex align-items-center">
                                                                                    <a href="{{ asset($detail->document) }}" target="_blank" class="btn btn-xs btn-outline-info mr-2">
                                                                                        <i class="fas fa-file-alt mr-1"></i> View Current Document
                                                                                    </a>
                                                                                    <span class="badge badge-light text-muted">{{ basename($detail->document) }}</span>
                                                                                    <input type="hidden" name="property_details[{{ $index }}][existing_document]" value="{{ $detail->document }}">
                                                                                </div>
                                                                            @endif
                                                                            <input type="file" class="form-control-file form-control-sm" name="property_details[{{ $index }}][document]" accept=".pdf,.doc,.docx">
                                                                            <small class="text-muted">Optional: Upload unit floor plan, costing sheet, or unit brochure (PDF, DOCX max 10MB)</small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Furnishing & Features -->
                                    <div class="row mt-3">
                                        <div class="col-md-12">
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
                                                        <label>Amenities</label>
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
                                                                        style="accent-color: #000080;">
                                                                    <label class="form-check-label">Swimming Pool</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="features[]" value="Gym"
                                                                        {{ in_array('Gym', $features) ? 'checked' : '' }}
                                                                        style="accent-color: #000080;">
                                                                    <label class="form-check-label">Gym</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="features[]" value="Parking"
                                                                        {{ in_array('Parking', $features) ? 'checked' : '' }}
                                                                        style="accent-color: #000080;">
                                                                    <label class="form-check-label">Parking</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="features[]" value="Garden"
                                                                        {{ in_array('Garden', $features) ? 'checked' : '' }}
                                                                        style="accent-color: #000080;">
                                                                    <label class="form-check-label">Garden</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="features[]" value="Security"
                                                                        {{ in_array('Security', $features) ? 'checked' : '' }}
                                                                        style="accent-color: #000080;">
                                                                    <label class="form-check-label">Security</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="features[]" value="Lift"
                                                                        {{ in_array('Lift', $features) ? 'checked' : '' }}
                                                                        style="accent-color: #000080;">
                                                                    <label class="form-check-label">Lift</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="features[]" value="Power Backup"
                                                                        {{ in_array('Power Backup', $features) ? 'checked' : '' }}
                                                                        style="accent-color: #000080;">
                                                                    <label class="form-check-label">Power Backup</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="features[]" value="WiFi"
                                                                        {{ in_array('WiFi', $features) ? 'checked' : '' }}
                                                                        style="accent-color: #000080;">
                                                                    <label class="form-check-label">WiFi</label>
                                                                </div>
                                                            </div>
                                                        
                                                        @foreach (['Kids’ Pool', 'Jacuzzi', 'Clubhouse', 'Banquet Hall', 'Indoor Games Room', 'Outdoor Games Area', 'Senior Citizen Lounge', 'Café / Coffee Shop', 'Gymnasium', 'Meditation Room', 'Guest Room', 'Jogging Track', 'Steam & Sauna & Spa', 'Landscaped Gardens', 'Gazebo', 'Badminton Court', 'Multipurpose Sport Court', 'Kids Play Area', 'Goods Lift', 'Intercom Facility', '24x7 Water Supply', 'Rain Water Harvesting', 'CCTV Security', 'Aqua Gym', 'Spa & Massage', 'Yoga / Meditation Area', 'Vastu Compliant', 'Amphitheatre', 'Squash Court', 'Home Theatre', 'Library', 'Bar / Lounge', 'Entrance Gateway', 'Basketball Court', 'Tennis Court', 'Table Tennis', 'Senior Citizen Park', 'Shopping / Retail Boulevard', 'Community Hall', 'Party Area', 'Sewage Treatment Plant', 'Earthquake Resistant', 'Fire Safety'] as $extraAmenity)
                                                            <div class="col-md-6">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" name="features[]"
                                                                        value="{{ $extraAmenity }}"
                                                                        {{ in_array($extraAmenity, $features) ? 'checked' : '' }} style="accent-color: #000080;">
                                                                    <label class="form-check-label">{{ $extraAmenity }}</label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                        @php
                                                            $fixedFeatures = array_merge(['Swimming Pool', 'Gym', 'Parking', 'Garden', 'Security', 'Lift', 'Power Backup', 'WiFi'], ['Kids’ Pool', 'Jacuzzi', 'Clubhouse', 'Banquet Hall', 'Indoor Games Room', 'Outdoor Games Area', 'Senior Citizen Lounge', 'Café / Coffee Shop', 'Gymnasium', 'Meditation Room', 'Guest Room', 'Jogging Track', 'Steam & Sauna & Spa', 'Landscaped Gardens', 'Gazebo', 'Badminton Court', 'Multipurpose Sport Court', 'Kids Play Area', 'Goods Lift', 'Intercom Facility', '24x7 Water Supply', 'Rain Water Harvesting', 'CCTV Security', 'Aqua Gym', 'Spa & Massage', 'Yoga / Meditation Area', 'Vastu Compliant', 'Amphitheatre', 'Squash Court', 'Home Theatre', 'Library', 'Bar / Lounge', 'Entrance Gateway', 'Basketball Court', 'Tennis Court', 'Table Tennis', 'Senior Citizen Park', 'Shopping / Retail Boulevard', 'Community Hall', 'Party Area', 'Sewage Treatment Plant', 'Earthquake Resistant', 'Fire Safety']);
                                                            $featuresOther = old('features_other', implode(', ', array_diff($features, $fixedFeatures)));
                                                        @endphp
                                                        <input type="text" class="form-control mt-2" name="features_other"
                                                            placeholder="Add more amenities, separated by commas (e.g. Sauna, Clubhouse)"
                                                            value="{{ $featuresOther }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Premium Specifications</label>
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
                                                                        style="accent-color: #000080;">
                                                                    <label class="form-check-label">Air
                                                                        Conditioning</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="amenities[]" value="Heating"
                                                                        {{ in_array('Heating', $amenities) ? 'checked' : '' }}
                                                                        style="accent-color: #000080;">
                                                                    <label class="form-check-label">Heating</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="amenities[]" value="TV"
                                                                        {{ in_array('TV', $amenities) ? 'checked' : '' }}
                                                                        style="accent-color: #000080;">
                                                                    <label class="form-check-label">TV</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="amenities[]" value="Washing Machine"
                                                                        {{ in_array('Washing Machine', $amenities) ? 'checked' : '' }}
                                                                        style="accent-color: #000080;">
                                                                    <label class="form-check-label">Washing Machine</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="amenities[]" value="Microwave"
                                                                        {{ in_array('Microwave', $amenities) ? 'checked' : '' }}
                                                                        style="accent-color: #000080;">
                                                                    <label class="form-check-label">Microwave</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="amenities[]" value="Refrigerator"
                                                                        {{ in_array('Refrigerator', $amenities) ? 'checked' : '' }}
                                                                        style="accent-color: #000080;">
                                                                    <label class="form-check-label">Refrigerator</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="amenities[]" value="Dishwasher"
                                                                        {{ in_array('Dishwasher', $amenities) ? 'checked' : '' }}
                                                                        style="accent-color: #000080;">
                                                                    <label class="form-check-label">Dishwasher</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="amenities[]" value="Balcony"
                                                                        {{ in_array('Balcony', $amenities) ? 'checked' : '' }}
                                                                        style="accent-color: #000080;">
                                                                    <label class="form-check-label">Balcony</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @php
                                                            $fixedAmenities = ['Air Conditioning', 'Heating', 'TV', 'Washing Machine', 'Microwave', 'Refrigerator', 'Dishwasher', 'Balcony'];
                                                            $amenitiesOther = old('amenities_other', implode(', ', array_diff($amenities, $fixedAmenities)));
                                                        @endphp
                                                        <input type="text" class="form-control mt-2" name="amenities_other"
                                                            placeholder="Add more specifications, separated by commas (e.g. Modular Kitchen, Smart Locks)"
                                                            value="{{ $amenitiesOther }}">
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
                                                        <label for="possession_date">Possession Date</label>
                                                        <input type="month" class="form-control" id="possession_date"
                                                            name="possession_date"
                                                            value="{{ old('possession_date', optional($property->possession_date)->format('Y-m')) }}">
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
                                                    <div class="form-group">
                                                        <label for="project_status">Project Status / Stage</label>
                                                        <select class="form-control" id="project_status"
                                                            name="project_status">
                                                            <option value="">None / Standard Listing</option>
                                                            <option value="Upcoming"
                                                                {{ old('project_status', $property->project_status) == 'Upcoming' ? 'selected' : '' }}>
                                                                Upcoming</option>
                                                            <option value="Pre-Launch"
                                                                {{ old('project_status', $property->project_status ?? ($property->pre_launch_property ? 'Pre-Launch' : '')) == 'Pre-Launch' ? 'selected' : '' }}>
                                                                Pre-Launch</option>
                                                            <option value="Early Possession"
                                                                {{ old('project_status', $property->project_status) == 'Early Possession' ? 'selected' : '' }}>
                                                                Early Possession</option>
                                                            <option value="Ready to move"
                                                                {{ old('project_status', $property->project_status) == 'Ready to move' ? 'selected' : '' }}>
                                                                Ready to move</option>
                                                        </select>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <div class="custom-control custom-checkbox">
                                                                    <input class="custom-control-input" type="checkbox"
                                                                        id="is_featured" name="is_featured"
                                                                        value="1"
                                                                        {{ old('is_featured', $property->is_featured) ? 'checked' : '' }}
                                                                        style="accent-color: #000080;">
                                                                    <label for="is_featured"
                                                                        class="custom-control-label">Featured
                                                                        Property</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <div class="custom-control custom-checkbox">
                                                                    <input class="custom-control-input" type="checkbox"
                                                                        id="is_verified" name="is_verified"
                                                                        value="1"
                                                                        {{ old('is_verified', $property->is_verified) ? 'checked' : '' }}
                                                                        style="accent-color: #000080;">
                                                                    <label for="is_verified"
                                                                        class="custom-control-label">Verified
                                                                        Property</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <div class="custom-control custom-checkbox">
                                                                    <input class="custom-control-input" type="checkbox"
                                                                        id="pre_launch_property" name="pre_launch_property"
                                                                        value="1"
                                                                        {{ old('pre_launch_property', $property->pre_launch_property) ? 'checked' : '' }}
                                                                        style="accent-color: #5146C7;">
                                                                    <label for="pre_launch_property"
                                                                        class="custom-control-label">Pre-Launch
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
                                                        <small class="form-text text-muted mb-2">
                                                            <strong>Shown on:</strong> home page &amp; listing cards (cropped to fit).<br>
                                                            <strong>Best size:</strong> 1200 × 900 px (4:3 landscape), min 800 × 600.<br>
                                                            <strong>Format:</strong> JPG or WebP (PNG only if needed), max 5 MB.<br>
                                                            Keep the main subject in the centre &mdash; the edges get cropped on different card shapes.
                                                        </small>
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
                                                        <small class="form-text text-muted">
                                                            <strong>Shown on:</strong> property page hero slider, thumbnails &amp; photo gallery.<br>
                                                            <strong>Best size:</strong> 1920 × 1080 px (16:9 landscape), min 1200 × 800.<br>
                                                            <strong>Format:</strong> JPG or WebP, max 5 MB each. Avoid GIF and portrait photos.<br>
                                                            The first image is the big hero photo. Keep subjects centred &mdash; the gallery crops to 4:3.
                                                            You can select multiple images.
                                                        </small>

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
                                                        <small class="form-text text-muted mb-2">
                                                            <strong>Best size:</strong> 1200 × 900 px (4:3 landscape), min 800 × 600.<br>
                                                            <strong>Format:</strong> JPG, PNG or WebP, max 5 MB. Use a clean, high-contrast plan with no large empty margins.
                                                        </small>
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
                                    <h4 class="text-muted border-bottom pb-2 mb-3" style="padding-left: 0.75ch;">Nearby & Connectivity</h4>
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
                                                        <label for="bazar"><i class="fas fa-subway"></i> Metro Station</label>
                                                        @include('admin.partials.distance-input', ['id' => 'bazar', 'name' => 'bazar_distance_km', 'value' => old('bazar_distance_km', $property->bazar_distance_km)])
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="hospital"><i class="fas fa-hospital"></i>
                                                            Hospital</label>
                                                        @include('admin.partials.distance-input', ['id' => 'hospital', 'name' => 'hospital_distance_km', 'value' => old('hospital_distance_km', $property->hospital_distance_km)])
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="school"><i class="fas fa-school"></i> School</label>
                                                        @include('admin.partials.distance-input', ['id' => 'school', 'name' => 'school_distance_km', 'value' => old('school_distance_km', $property->school_distance_km)])
                                                    </div>
                                                    @include('admin.partials.custom-places', ['group' => 'nearby', 'places' => old('custom_places', $property->custom_nearby_places ?? [])])
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
                                                        @include('admin.partials.distance-input', ['id' => 'bus_stand', 'name' => 'bus_stand_distance_km', 'value' => old('bus_stand_distance_km', $property->bus_stand_distance_km)])
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="junction"><i class="fas fa-train"></i> Railway
                                                            Junction</label>
                                                        @include('admin.partials.distance-input', ['id' => 'junction', 'name' => 'junction_distance_km', 'value' => old('junction_distance_km', $property->junction_distance_km)])
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="airport"><i class="fas fa-plane"></i> Airport</label>
                                                        @include('admin.partials.distance-input', ['id' => 'airport', 'name' => 'airport_distance_km', 'value' => old('airport_distance_km', $property->airport_distance_km)])
                                                    </div>
                                                    @include('admin.partials.custom-places', ['group' => 'connectivity', 'places' => old('custom_places', $property->custom_nearby_places ?? [])])
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
                                            style="background-color: #000080; border-color: #000080;">Update
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
@endsection

@section('extraJs')
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

            // Real-time price in words helper
            function convertNumberToIndianWords(num) {
                num = Math.floor(Number(num));
                if (isNaN(num) || num <= 0) return "";

                const a = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten",
                    "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"];
                const b = ["", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];

                function twoDigits(n) {
                    if (n < 20) return a[n];
                    return b[Math.floor(n / 10)] + (n % 10 !== 0 ? " " + a[n % 10] : "");
                }

                function threeDigits(n) {
                    let str = "";
                    if (Math.floor(n / 100) > 0) {
                        str += a[Math.floor(n / 100)] + " Hundred ";
                    }
                    if (n % 100 > 0) {
                        str += twoDigits(n % 100);
                    }
                    return str.trim();
                }

                let crore = Math.floor(num / 10000000);
                num %= 10000000;
                let lakh = Math.floor(num / 100000);
                num %= 100000;
                let thousand = Math.floor(num / 1000);
                num %= 1000;
                let hundred = num;

                let parts = [];
                if (crore > 0) parts.push(convertNumberToIndianWords(crore) + " Crore");
                if (lakh > 0) parts.push(twoDigits(lakh) + " Lakh");
                if (thousand > 0) parts.push(twoDigits(thousand) + " Thousand");
                if (hundred > 0) parts.push(threeDigits(hundred));

                return parts.join(" ").trim();
            }

            function formatShortDenomination(num) {
                num = Number(num);
                if (isNaN(num) || num <= 0) return "";
                if (num >= 10000000) {
                    let cr = num / 10000000;
                    return (Number.isInteger(cr) ? cr : cr.toFixed(2).replace(/\.?0+$/, "")) + " Crore";
                }
                if (num >= 100000) {
                    let lk = num / 100000;
                    return (Number.isInteger(lk) ? lk : lk.toFixed(2).replace(/\.?0+$/, "")) + " Lakh";
                }
                if (num >= 100) {
                    if (num >= 1000) {
                        let th = num / 1000;
                        return (Number.isInteger(th) ? th : th.toFixed(2).replace(/\.?0+$/, "")) + " Thousand";
                    }
                    let hd = num / 100;
                    return (Number.isInteger(hd) ? hd : hd.toFixed(2).replace(/\.?0+$/, "")) + " Hundred";
                }
                return num.toString();
            }

            function getPriceHelperText(inputVal) {
                if (!inputVal || !inputVal.trim()) return "";
                let str = inputVal.trim();

                let rangeMatch = str.match(/^([0-9.,]+)\s*(?:-|–|to)\s*([0-9.,]+)$/i);
                if (rangeMatch) {
                    let n1 = parseFloat(rangeMatch[1].replace(/,/g, ""));
                    let n2 = parseFloat(rangeMatch[2].replace(/,/g, ""));
                    if (!isNaN(n1) && !isNaN(n2) && n1 > 0 && n2 > 0) {
                        return formatShortDenomination(n1) + " - " + formatShortDenomination(n2);
                    }
                }

                let clean = str.replace(/,/g, "").trim();
                let num = parseFloat(clean);
                if (!isNaN(num) && num > 0) {
                    let s = formatShortDenomination(num);
                    let w = convertNumberToIndianWords(num);
                    if (w && w.toLowerCase() !== s.toLowerCase()) {
                        return s + " (" + w + ")";
                    }
                    return s;
                }
                return "";
            }

            function updatePriceHelper() {
                const priceInput = document.getElementById('price');
                const helperEl = document.getElementById('price_in_words');
                const helperText = document.getElementById('price_in_words_text');
                if (!priceInput || !helperEl || !helperText) return;

                const text = getPriceHelperText(priceInput.value);
                if (text) {
                    helperText.textContent = text;
                    helperEl.style.display = 'block';
                } else {
                    helperText.textContent = '';
                    helperEl.style.display = 'none';
                }
            }

            $('#price').on('input keyup change', updatePriceHelper);
            updatePriceHelper();

            // Location Auto-fill using Postal PIN Code API (https://api.postalpincode.in)
            (function initLocationAutoFill() {
                const addressInput = document.getElementById('address');
                const cityInput = document.getElementById('city');
                const stateInput = document.getElementById('state');
                const zipInput = document.getElementById('zip_code');
                const addressStatus = document.getElementById('location_autofill_status');
                const zipStatus = document.getElementById('zip_autofill_status');

                if (!addressInput || !cityInput || !stateInput || !zipInput) return;

                let addressDebounceTimer = null;
                let lastProcessedAddress = addressInput.value.trim();
                let lastProcessedZip = zipInput.value.trim();

                function setStatus(targetEl, message, type, autoHideMs) {
                    if (!targetEl) return;
                    let icon = '';
                    let color = '#6c757d';

                    if (type === 'loading') {
                        icon = '<i class="fas fa-spinner fa-spin mr-1"></i>';
                        color = '#007bff';
                    } else if (type === 'success') {
                        icon = '<i class="fas fa-check-circle mr-1"></i>';
                        color = '#28a745';
                    } else if (type === 'error') {
                        icon = '<i class="fas fa-exclamation-circle mr-1"></i>';
                        color = '#e0a800';
                    }

                    targetEl.style.color = color;
                    targetEl.innerHTML = icon + message;
                    targetEl.style.display = 'block';

                    if (autoHideMs && autoHideMs > 0) {
                        setTimeout(function() {
                            if (targetEl.innerHTML === icon + message) {
                                targetEl.style.display = 'none';
                            }
                        }, autoHideMs);
                    }
                }

                async function fetchFromPincodeApi(pin, source) {
                    const statusEl = (source === 'address') ? addressStatus : zipStatus;
                    setStatus(statusEl, `Fetching location for PIN ${pin}...`, 'loading');

                    try {
                        const response = await fetch(`https://api.postalpincode.in/pincode/${pin}`);
                        if (!response.ok) throw new Error('Network response error');
                        const data = await response.json();

                        if (data && data[0] && data[0].Status === 'Success' && data[0].PostOffice && data[0].PostOffice.length > 0) {
                            const po = data[0].PostOffice[0];
                            const district = po.District || '';
                            const state = po.State || '';

                            if (district) cityInput.value = district;
                            if (state) stateInput.value = state;
                            if (source === 'address') {
                                zipInput.value = pin;
                                lastProcessedZip = pin;
                            }

                            setStatus(statusEl, `Location auto-filled: ${district}, ${state}`, 'success', 5000);
                            return true;
                        } else {
                            setStatus(statusEl, `No location found for PIN ${pin}`, 'error', 4000);
                            return false;
                        }
                    } catch (e) {
                        setStatus(statusEl, 'Could not fetch location from PIN API', 'error', 4000);
                        return false;
                    }
                }

                async function fetchFromPostOfficeApi(queryTerm, fullAddress) {
                    if (!queryTerm || queryTerm.length < 3) return false;
                    setStatus(addressStatus, `Looking up location for "${queryTerm}"...`, 'loading');

                    try {
                        const response = await fetch(`https://api.postalpincode.in/postoffice/${encodeURIComponent(queryTerm)}`);
                        if (!response.ok) throw new Error('Network response error');
                        const data = await response.json();

                        if (data && data[0] && data[0].Status === 'Success' && data[0].PostOffice && data[0].PostOffice.length > 0) {
                            const list = data[0].PostOffice;
                            const addrLower = (fullAddress || '').toLowerCase();

                            // Prefer matching post office whose District or State is mentioned in the full address
                            let matched = list.find(po => {
                                const dist = (po.District || '').toLowerCase();
                                const st = (po.State || '').toLowerCase();
                                return (dist && addrLower.includes(dist)) || (st && addrLower.includes(st));
                            });

                            if (!matched) {
                                matched = list.find(po => po.Name.toLowerCase() === queryTerm.toLowerCase()) || list[0];
                            }

                            const district = matched.District || '';
                            const state = matched.State || '';
                            const pin = matched.Pincode || '';

                            if (district) cityInput.value = district;
                            if (state) stateInput.value = state;
                            if (pin && !zipInput.value.trim()) {
                                zipInput.value = pin;
                                lastProcessedZip = pin;
                            }

                            setStatus(addressStatus, `Location auto-filled: ${district}, ${state} (PIN: ${pin})`, 'success', 5000);
                            return true;
                        }
                        return false;
                    } catch (e) {
                        return false;
                    }
                }

                async function handleAddressCheck() {
                    const val = addressInput.value.trim();
                    if (!val || val === lastProcessedAddress) return;
                    lastProcessedAddress = val;

                    // 1. Try 6-digit Indian PIN code inside address (first priority)
                    const pinMatch = val.match(/\b([1-9][0-9]{5})\b/);
                    if (pinMatch) {
                        await fetchFromPincodeApi(pinMatch[1], 'address');
                        return;
                    }

                    // 2. If no PIN code, parse address segments from right to left (locality/city candidates)
                    const noiseWords = /^(india|floor|flat|road|street|plot|house|near|opp|opposite|behind|block|sector|lane|nagar|colony|phase|apartment|apartments|society|tower|building|bldg)$/i;
                    const segments = val.split(/[,;\n\r]+/).map(s => s.trim()).filter(Boolean);

                    for (let i = segments.length - 1; i >= 0; i--) {
                        let token = segments[i].replace(/[^a-zA-Z\s]/g, '').trim();
                        if (token.length >= 3 && !noiseWords.test(token)) {
                            const ok = await fetchFromPostOfficeApi(token, val);
                            if (ok) return;
                        }
                    }

                    // 3. If address search didn't resolve and zip input has 6 digits, try zip
                    const currentZip = zipInput.value.trim();
                    if (/^[1-9][0-9]{5}$/.test(currentZip) && (!cityInput.value.trim() || !stateInput.value.trim())) {
                        await fetchFromPincodeApi(currentZip, 'zip');
                    }
                }

                async function handleZipCheck() {
                    const pin = zipInput.value.trim();
                    if (/^[1-9][0-9]{5}$/.test(pin)) {
                        if (pin === lastProcessedZip && cityInput.value.trim() && stateInput.value.trim()) return;
                        lastProcessedZip = pin;
                        await fetchFromPincodeApi(pin, 'zip');
                    }
                }

                // Address field listeners
                addressInput.addEventListener('input', function() {
                    clearTimeout(addressDebounceTimer);
                    addressDebounceTimer = setTimeout(handleAddressCheck, 750);
                });
                addressInput.addEventListener('blur', function() {
                    clearTimeout(addressDebounceTimer);
                    handleAddressCheck();
                });
                addressInput.addEventListener('change', function() {
                    clearTimeout(addressDebounceTimer);
                    handleAddressCheck();
                });

                // Zip code field listeners
                zipInput.addEventListener('input', function() {
                    const pin = zipInput.value.trim();
                    if (pin.length === 6) {
                        handleZipCheck();
                    }
                });
                zipInput.addEventListener('blur', handleZipCheck);
                zipInput.addEventListener('change', handleZipCheck);
            })();

            // Dynamic Property Details Repeater
            function updatePropertyDetailIndexes() {
                const items = $('#property_details_container .property-detail-item');
                items.each(function(index) {
                    $(this).find('.detail-item-title').html('<i class="fas fa-home mr-1 text-secondary"></i> Configuration #' + (index + 1));
                    $(this).find('input').each(function() {
                        const name = $(this).attr('name');
                        if (name) {
                            const newName = name.replace(/property_details\[\d+\]/, 'property_details[' + index + ']');
                            $(this).attr('name', newName);
                        }
                    });
                    if (items.length > 1) {
                        $(this).find('.remove-detail-btn').show();
                    } else {
                        $(this).find('.remove-detail-btn').hide();
                    }
                });
            }

            $('#add_detail_btn').on('click', function(e) {
                e.preventDefault();
                const nextIdx = $('#property_details_container .property-detail-item').length;
                const html = `
                    <div class="property-detail-item border rounded p-3 mb-3" style="background-color: #fcfcfc; border-color: #dcdcdc !important;">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <h6 class="mb-0 font-weight-bold text-dark detail-item-title">
                                <i class="fas fa-home mr-1 text-secondary"></i> Configuration #${nextIdx + 1}
                            </h6>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-detail-btn font-weight-bold">
                                <i class="fas fa-trash-alt mr-1"></i> Remove
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold">Unit Type</label>
                                    <input type="text" class="form-control form-control-sm" list="unit_type_datalist" name="property_details[${nextIdx}][unit_type]" placeholder="e.g. 2 BHK, 3 BHK">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold">Bedrooms</label>
                                    <input type="number" min="0" list="count_datalist" class="form-control form-control-sm" name="property_details[${nextIdx}][bedrooms]" placeholder="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold">Bathrooms</label>
                                    <input type="number" min="0" list="count_datalist" class="form-control form-control-sm" name="property_details[${nextIdx}][bathrooms]" placeholder="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold">Balconies</label>
                                    <input type="number" min="0" list="count_datalist" class="form-control form-control-sm" name="property_details[${nextIdx}][balconies]" placeholder="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold">Carpet Area (sq.ft)</label>
                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="property_details[${nextIdx}][carpet_area]" placeholder="e.g. 850">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold">Super Built up Area (sq.ft)</label>
                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="property_details[${nextIdx}][super_area]" placeholder="e.g. 1100">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold">Price</label>
                                    <input type="text" class="form-control form-control-sm" name="property_details[${nextIdx}][price]" placeholder="e.g. 75 Lakh or 1.25 Cr">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="small font-weight-bold"><i class="fas fa-file-pdf mr-1 text-danger"></i> Configuration Document / Costing Sheet (PDF/DOCX)</label>
                                    <input type="file" class="form-control-file form-control-sm" name="property_details[${nextIdx}][document]" accept=".pdf,.doc,.docx">
                                    <small class="text-muted">Optional: Upload unit floor plan, costing sheet, or unit brochure (PDF, DOCX max 10MB)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('#property_details_container').append(html);
                updatePropertyDetailIndexes();
            });

            $(document).on('click', '.remove-detail-btn', function(e) {
                e.preventDefault();
                $(this).closest('.property-detail-item').remove();
                updatePropertyDetailIndexes();
            });

            updatePropertyDetailIndexes();
        });
    </script>
@endsection


