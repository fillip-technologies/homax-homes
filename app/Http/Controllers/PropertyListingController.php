<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Support\Facades\Auth;

class PropertyListingController extends Controller
{
    // ... other methods ...


    public function indexwelcome()
    {
        $newlisted_properties = Property::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->take(10) // Limit to 10 newly listed properties
            ->get();
        $featured_properties = Property::where('is_featured', true)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->take(4) // Limit to 4 featured properties
            ->get();

        return view('welcome', compact('featured_properties', 'newlisted_properties'));
    }
    public function index()
    {
        $properties = Property::all();

        return view('admin.propertylisting', compact('properties'));
    }
    public function indexfetured()
    {
        $properties = Property::where('is_featured', true)->get();
$title = 'Featured Properties'; // Set a title for the view
        return view('admin.listofproperties ', compact('properties', 'title'));
    }
    public function search(Request $request)
    {
        $query = Property::query()->where('is_active', true);

        // Search by property type
        if ($request->filled('property_type')) {
            $query->where('property_type', $request->property_type);
        }

        // Search by listing type
        if ($request->filled('listing_type')) {
            $query->where('listing_type', $request->listing_type);
        }

        // General search (title, city, address)
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('city', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%")
                    ->orWhere('address', 'like', "%{$searchTerm}%");
            });
        }
        // Apply sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'area_asc':
                $query->orderBy('super_area', 'asc');
                break;
            case 'area_desc':
                $query->orderBy('super_area', 'desc');
                break;
            case 'bedrooms_asc':
                $query->orderBy('bedrooms', 'asc');
                break;
            case 'bedrooms_desc':
                $query->orderBy('bedrooms', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Get paginated results
        $properties = $query->paginate(5);

        return view('search-results', [
            'properties' => $properties,
            'searchParams' => $request->all()
        ]);
    }

    public function list()
    {
        $properties = Property::all();
        $title = 'All Properties'; // Set a title for the view
        //  dd($properties); // Debugging line to check properties data
        return view('admin.listofproperties', compact('properties', 'title'));
    }
    public function edit($id)
    {
        $property = Property::findOrFail($id);
       $properties = Property::with('similarProperties')->findOrFail($id);
        return view('admin.editproperty', compact('property', 'properties')); // Make sure you have this blade
    }

    public function toggleStatus($id)
    {
        $property = Property::findOrFail($id);

        // Toggle the value
        $property->is_active = $property->is_active == 1 ? 0 : 1;

        // Save and check if it succeeds
        if ($property->save()) {
            return back()->with('success', 'Property status updated successfully.');
        } else {
            return back()->with('error', 'Failed to update property status.');
        }
    }


    public function destroy($id)
    {
        $property = Property::findOrFail($id);

        // This will automatically delete all related images
        $property->images()->delete();

        // Now delete the property
        $property->delete();

        return back()->with('success', 'Property deleted successfully.');
    }


public function update(Request $request, $id)
{
    $property = Property::findOrFail($id);

    // Merge slug with request data
    $request->merge([
        'slug' => Str::slug($request->slug)
    ]);

    // Validate the request
    $validatedData = $this->validateRequest($request, $property->id);

    // Begin database transaction
    DB::beginTransaction();

    try {
        // Handle main image upload if new one is provided
        $mainImagePath = $property->main_image;
        if ($request->hasFile('main_image')) {
            $mainImagePath = $this->handleFileUpload($request->file('main_image'), 'properties/main_images');
        }

        // Handle floor plan image upload if new one is provided
        $floorPlanPath = $property->floor_plan_image;
        if ($request->hasFile('floor_plan_image')) {
            $floorPlanPath = $this->handleFileUpload($request->file('floor_plan_image'), 'properties/floor_plans');
        }

        // Handle brochure upload if new one is provided
        $brochurePath = $property->brochure;
        if ($request->hasFile('brochure')) {
            $brochurePath = $this->handleFileUpload($request->file('brochure'), 'properties/brochures');
        }

        // Update the property
        $property->update([
            // Basic Information
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'slug' => $validatedData['slug'],
            'property_type' => $validatedData['property_type'],
            'listing_type' => $validatedData['listing_type'],
            'price' => $validatedData['price'],
            'price_unit' => $validatedData['price_unit'] ?? '₹',
            'security_deposit' => $validatedData['security_deposit'] ?? null,

            // Location Details
            'address' => $validatedData['address'],
            'city' => $validatedData['city'],
            'state' => $validatedData['state'],
            'zip_code' => $validatedData['zip_code'] ?? null,
            'latitude' => $validatedData['latitude'] ?? null,
            'longitude' => $validatedData['longitude'] ?? null,
            'google_map_link' => $validatedData['google_map_link'] ?? null,

            // Property Details
            'bedrooms' => $validatedData['bedrooms'] ?? null,
            'bathrooms' => $validatedData['bathrooms'] ?? null,
            'balconies' => $validatedData['balconies'] ?? null,
            'floors' => $validatedData['floors'] ?? null,
            'floor_number' => $validatedData['floor_number'] ?? null,
            'super_area' => $validatedData['super_area'] ?? null,
            'carpet_area' => $validatedData['carpet_area'] ?? null,
            'plot_area' => $validatedData['plot_area'] ?? null,
            'year_built' => $validatedData['year_built'] ?? null,
            'age_of_property' => $validatedData['age_of_property'] ?? null,

            // Furnishing
            'furnishing' => $validatedData['furnishing'] ?? null,

            // Features & Amenities
            'features' => json_encode($validatedData['features'] ?? []),
            'amenities' => json_encode($validatedData['amenities'] ?? []),

            // Availability
            'availability' => $validatedData['availability'],
            'available_from' => $validatedData['available_from'] ?? null,
            'preferred_tenants' => $validatedData['preferred_tenants'] ?? null,

            // Media
            'main_image' => $mainImagePath,
            'video_url' => $validatedData['video_url'] ?? null,
            'floor_plan_image' => $floorPlanPath,
            'brochure' => $brochurePath,

            // Additional Info
            'is_featured' => $request->has('is_featured'),
            'is_verified' => $request->has('is_verified'),
            'property_status' => $validatedData['property_status'] ?? 'Available',
            'notes' => $validatedData['notes'] ?? null,
            'keyfeatures' => $validatedData['keyfeatures'] ?? null,

            // Nearby locations
            'bazar_distance_km' => $validatedData['bazar_distance_km'] ?? null,
            'hospital_distance_km' => $validatedData['hospital_distance_km'] ?? null,
            'school_distance_km' => $validatedData['school_distance_km'] ?? null,
            'bus_stand_distance_km' => $validatedData['bus_stand_distance_km'] ?? null,
            'junction_distance_km' => $validatedData['junction_distance_km'] ?? null,
            'airport_distance_km' => $validatedData['airport_distance_km'] ?? null,
        ]);

        // Handle additional images if provided
        if ($request->hasFile('property_images')) {
            $this->handleAdditionalImages($request->file('property_images'), $property->id);
        }

        // Handle similar properties
        DB::table('similar_properties')->where('property_id', $property->id)->delete();
        if (!empty($validatedData['similar_properties'])) {
            $this->handleSimilarProperties($validatedData['similar_properties'], $property->id);
        }

        // Commit the transaction
        DB::commit();

        return redirect()->route('admin.properties.list')
            ->with('success', 'Property updated successfully!');
    } catch (\Exception $e) {
        // Rollback the transaction on error
        DB::rollBack();

        return back()->withInput()
            ->with('error', 'Error updating property: ' . $e->getMessage());
    }
}

// Add this method to handle image deletion
public function deleteImage($id)
{
    $image = PropertyImage::findOrFail($id);

    // Delete the file from storage
    if (file_exists(public_path($image->image_path))) {
        unlink(public_path($image->image_path));
    }

    // Delete the record from database
    $image->delete();

    return back()->with('success', 'Image deleted successfully');
}

// Update the validateRequest method to handle updates

    /**
     * Store a newly created property in storage.
     */
    public function store(Request $request)
    {
        // Debugging line to check request data
        // dd($request->all());


        $validatedData = $this->validateRequest($request);

        // Begin database transaction
        // DB::beginTransaction();
        //  dd($validatedData);
        try {
            // Handle main image upload
            $mainImagePath = $this->handleFileUpload($request->file('main_image'), 'properties/main_images');

            // Handle floor plan image upload
            $floorPlanPath = $this->handleFileUpload($request->file('floor_plan_image'), 'properties/floor_plans');

            // Handle brochure upload
            $brochurePath = $this->handleFileUpload($request->file('brochure'), 'properties/brochures');

            // Create the property
            $property = Property::create([
                // Basic Information
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'slug' => $validatedData['slug'],
                'rera_id' => $validatedData['rera_id'],
                'property_type' => $validatedData['property_type'],
                'listing_type' => $validatedData['listing_type'],
                'price' => $validatedData['price'],
                'price_unit' => $validatedData['price_unit'] ?? '₹',
                'security_deposit' => $validatedData['security_deposit'] ?? null,
                'property_id' => $this->generatePropertyId(),

                // Location Details
                'address' => $validatedData['address'],
                'city' => $validatedData['city'],
                'state' => $validatedData['state'],
                'zip_code' => $validatedData['zip_code'] ?? null,
                'latitude' => $validatedData['latitude'] ?? null,
                'longitude' => $validatedData['longitude'] ?? null,
                'google_map_link' => $validatedData['google_map_link'] ?? null,

                // Property Details
                'bedrooms' => $validatedData['bedrooms'] ?? null,
                'bathrooms' => $validatedData['bathrooms'] ?? null,
                'balconies' => $validatedData['balconies'] ?? null,
                'floors' => $validatedData['floors'] ?? null,
                'floor_number' => $validatedData['floor_number'] ?? null,
                'super_area' => $validatedData['super_area'] ?? null,
                'carpet_area' => $validatedData['carpet_area'] ?? null,
                'plot_area' => $validatedData['plot_area'] ?? null,
                'year_built' => $validatedData['year_built'] ?? null,
                'age_of_property' => $validatedData['age_of_property'] ?? null,

                // Furnishing
                'furnishing' => $validatedData['furnishing'] ?? null,
                'furnishing_details' => $validatedData['furnishing_details'] ?? null,

                // Features & Amenities
                'features' => $validatedData['features'] ?? null,
                'amenities' => $validatedData['amenities'] ?? null,

                // Availability
                'availability' => $validatedData['availability'],
                'available_from' => $validatedData['available_from'] ?? null,
                'preferred_tenants' => $validatedData['preferred_tenants'] ?? null,

                // Media
                'main_image' => $mainImagePath,
                'video_url' => $validatedData['video_url'] ?? null,
                'floor_plan_image' => $floorPlanPath,
                'brochure' => $brochurePath,

                // Additional Info
                'is_featured' => $request->has('is_featured'),
                'is_verified' => $request->has('is_verified'),
                'property_status' => $validatedData['property_status'] ?? 'Available',
                'notes' => $validatedData['notes'] ?? null,
                'keyfeatures' => $validatedData['keyfeatures'] ?? null,

                // ... existing fields ...
                'bazar_distance_km' => $validatedData['bazar_distance_km'] ?? null,
                'hospital_distance_km' => $validatedData['hospital_distance_km'] ?? null,
                'school_distance_km' => $validatedData['school_distance_km'] ?? null,
                'bus_stand_distance_km' => $validatedData['bus_stand_distance_km'] ?? null,
                'junction_distance_km' => $validatedData['junction_distance_km'] ?? null,
                'airport_distance_km' => $validatedData['airport_distance_km'] ?? null,

                // Ownership - assuming you'll use auth later
                'user_id' => Auth::guard('admin')->user()->id  ?? 1, // Default to 1 if no auth
            ]);

            // Handle additional images
            if ($request->hasFile('property_images')) {
                $this->handleAdditionalImages($request->file('property_images'), $property->id);
            }

            // Handle similar properties
            if (!empty($validatedData['similar_properties'])) {
                $this->handleSimilarProperties($validatedData['similar_properties'], $property->id);
            }

            // Commit the transaction
            DB::commit();

            return redirect()->route('admin.properties.list')
                ->with('success', 'Property created successfully!');
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Error creating property: ' . $e->getMessage());
        }
    }

    /**
     * Validate the request data.
     */
    protected function validateRequest(Request $request)
    {
        return $request->validate([
            // Basic Information
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'slug' => 'nullable|string|unique:full_property_schema,slug',
            'property_type' => 'required|in:Apartment,Villa,Residential Plot,Commercial,Penthouse,House,Condo,Townhouse',
            'listing_type' => 'required|in:For Rent,For Sale,Lease',
            'price' => 'nullable|string|max:200',
            'price_unit' => 'nullable|string',
            'rera_id' => 'nullable|string',
            'security_deposit' => 'nullable|numeric|min:0',

            // Location Details
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'google_map_link' => 'nullable|url',

            // Property Details
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'balconies' => 'nullable|integer|min:0',
            'floors' => 'nullable|integer|min:0',
            'floor_number' => 'nullable|integer|min:0',
            'super_area' => 'nullable|numeric|min:0',
            'carpet_area' => 'nullable|numeric|min:0',
            'plot_area' => 'nullable|numeric|min:0',
            'year_built' => 'nullable|integer|min:1800|max:' . date('Y'),
            'age_of_property' => 'nullable|integer|min:0',

            // Furnishing
            'furnishing' => 'nullable|in:Fully Furnished,Semi Furnished,Unfurnished',

            // Features & Amenities
            'features' => 'nullable|array',
            'features.*' => 'string',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string',

            // Availability
            'availability' => 'required|in:Immediate,After Date,Negotiable',
            'available_from' => 'nullable|required_if:availability,After Date|date|after_or_equal:today',
            'preferred_tenants' => 'nullable|in:Family,Professionals,Students,Company,Anyone',

            // Media
            'main_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'property_images' => 'nullable|array',
            'property_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            'video_url' => 'nullable|url',
            'floor_plan_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'brochure' => 'nullable|file|mimes:pdf|max:10240', // 1MB max for brochure

            // Additional Info
            'is_featured' => 'nullable|boolean',
            'is_verified' => 'nullable|boolean',
            'property_status' => 'nullable|in:Available,Rented,Sold,Under Maintenance',
            'notes' => 'nullable|string',
            'keyfeatures' => 'nullable|string',
            'similar_properties' => 'nullable|array',
            'similar_properties.*' => 'nullable|exists:full_property_schema,id',
            // Nearby Locations
            'bazar_distance_km' => 'nullable|string|max:100',
            'hospital_distance_km' => 'nullable|string|max:100',
            'school_distance_km' => 'nullable|string|max:100',
            'bus_stand_distance_km' => 'nullable|string|max:100',
            'junction_distance_km' => 'nullable|string|max:100',
            'airport_distance_km' => 'nullable|string|max:100',


        ]);
    }

    /**
     * Handle file upload.
     */
    protected function handleFileUpload($file, $directory)
    {
        if (!$file) {
            return null;
        }

        // Create directory if it doesn't exist
        $publicPath = public_path($directory);
        if (!file_exists($publicPath)) {
            mkdir($publicPath, 0755, true);
        }

        $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();
        $file->move($publicPath, $fileName);

        return $directory . '/' . $fileName;
    }

    /**
     * Handle additional images upload.
     */
    protected function handleAdditionalImages($files, $propertyId)
    {
        $directory = 'properties/additional_images';
        $publicPath = public_path($directory);

        if (!file_exists($publicPath)) {
            mkdir($publicPath, 0755, true);
        }

        foreach ($files as $file) {
            $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $file->move($publicPath, $fileName);
            $path = $directory . '/' . $fileName;

            PropertyImage::create([
                'property_id' => $propertyId,
                'image_path' => $path,
                'is_featured' => false,
                'order' => 0,
            ]);
        }
    }

    /**
     * Handle similar properties relationship.
     */
    protected function handleSimilarProperties($similarPropertyIds, $propertyId)
    {
        foreach ($similarPropertyIds as $similarId) {
            DB::table('similar_properties')->insert([
                'property_id' => $propertyId,
                'similar_property_id' => $similarId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Generate a unique property ID.
     */
    protected function generatePropertyId()
    {
        $prefix = 'PROP';
        $random = Str::upper(Str::random(6));
        $timestamp = now()->format('Ymd');

        return "{$prefix}-{$timestamp}-{$random}";
    }
}
