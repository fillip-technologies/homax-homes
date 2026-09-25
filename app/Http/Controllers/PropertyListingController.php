<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Property;
use App\Support\PriceParser;
use App\Models\PropertyImage;
use App\Models\SimilarProperty;
use Illuminate\Support\Facades\Auth;

class PropertyListingController extends Controller
{
    // ... other methods ...


    public function indexwelcome()
    {
        $newlisted_properties = Property::where('is_active', true)
            ->where(function ($query) {
                $query->where('is_featured', false)
                    ->orWhereNull('is_featured');
            })
            ->orderBy('created_at', 'desc')
            ->take(10) // Limit to 10 newly listed non-featured properties
            ->get();
        $featured_properties = Property::where('is_featured', true)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->take(4) // Limit to 4 featured properties
            ->get();

        $searchCities = Property::where('is_active', true)
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        $readyToMoveProperties = Property::where('is_active', true)
            ->where('project_status', 'Ready to move')
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        $cityStats = $this->cityStats();

        return view('welcome', compact('featured_properties', 'newlisted_properties', 'searchCities', 'readyToMoveProperties', 'cityStats'));
    }
    public function index()
    {
        $properties = Property::all();

        return view('admin.propertylisting', compact('properties'));
    }
    public function indexfeatured()
    {
        $properties = Property::where('is_featured', true)->get();
        $title = 'Featured Properties';
        return view('admin.listofproperties', compact('properties', 'title'));
    }

    public function indexfetured()
    {
        return $this->indexfeatured();
    }

    public const STATUS_SLUGS = [
        'upcoming' => 'Upcoming',
        'pre-launch' => 'Pre-Launch',
        'early-possession' => 'Early Possession',
        'ready-to-move' => 'Ready to Move',
    ];

    public function search(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'budget_min' => 'nullable|integer|min:0',
            'budget_max' => 'nullable|integer|min:0',
            'area_min' => 'nullable|numeric|min:0',
            'area_max' => 'nullable|numeric|min:0',
            'bathrooms' => 'nullable|integer|min:1|max:10',
            'bhk' => 'nullable|array',
            'bhk.*' => 'integer|min:1|max:10',
        ]);

        // Bad filter values (e.g. ?budget_min=abc) are ignored rather than redirecting away.
        if ($validator->fails()) {
            $bad = array_unique(array_map(fn($k) => explode('.', $k)[0], array_keys($validator->errors()->messages())));
            $request->replace(\Illuminate\Support\Arr::except($request->all(), $bad));
        }

        $query = Property::query()->where('is_active', true);

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('locality')) {
            $query->where('location', $request->locality);
        }

        if ($request->filled('category') && in_array(ucfirst(strtolower($request->category)), ['Residential', 'Commercial'])) {
            $query->where('category', ucfirst(strtolower($request->category)));
        }

        // Project stage: Upcoming, Pre-Launch, Early Possession, Ready to move
        $statuses = array_values(array_intersect_key(
            self::STATUS_SLUGS,
            array_flip(array_map('strtolower', $this->arrayParam($request, 'status')))
        ));
        if ($statuses) {
            $query->where(function ($q) use ($statuses) {
                $q->whereIn('project_status', $statuses);
                if (in_array('Pre-Launch', $statuses)) {
                    $q->orWhere('pre_launch_property', true);
                }
            });
        }

        // Budget: prices are free text ("75 Lakh", "50L-70L"), so parse them here and
        // keep projects whose price range overlaps the chosen range.
        $priceRanges = null;
        if ($request->filled('budget_min') || $request->filled('budget_max')) {
            $min = (int) $request->input('budget_min', 0);
            $max = $request->filled('budget_max') ? (int) $request->budget_max : PHP_INT_MAX;
            $priceRanges = $this->priceRanges();
            $ids = array_keys(array_filter(
                $priceRanges,
                fn($r) => $r[0] <= $max && $r[1] >= $min
            ));
            $query->whereIn('id', $ids ?: [0]);
        }

        // BHK: project-level bedrooms or any of its unit configurations (5 means 5+)
        if ($bhk = array_map('intval', $this->arrayParam($request, 'bhk'))) {
            $matchBedrooms = function ($q, $column) use ($bhk) {
                $q->where(function ($q) use ($bhk, $column) {
                    $exact = array_filter($bhk, fn($n) => $n < 5);
                    if ($exact) {
                        $q->whereIn($column, $exact);
                    }
                    if (in_array(5, $bhk)) {
                        $q->orWhere($column, '>=', 5);
                    }
                });
            };
            $query->where(function ($q) use ($matchBedrooms) {
                $matchBedrooms($q, 'bedrooms');
                $q->orWhereHas('details', fn($d) => $matchBedrooms($d, 'bedrooms'));
            });
        }

        // Size (super area, sqft)
        if ($request->filled('area_min') || $request->filled('area_max')) {
            $inRange = function ($q) use ($request) {
                if ($request->filled('area_min')) {
                    $q->where('super_area', '>=', $request->area_min);
                }
                if ($request->filled('area_max')) {
                    $q->where('super_area', '<=', $request->area_max);
                }
            };
            $query->where(function ($q) use ($inRange) {
                $q->where($inRange)->orWhereHas('details', $inRange);
            });
        }

        if ($request->filled('bathrooms')) {
            $min = (int) $request->bathrooms;
            $query->where(function ($q) use ($min) {
                $q->where('bathrooms', '>=', $min)
                    ->orWhereHas('details', fn($d) => $d->where('bathrooms', '>=', $min));
            });
        }

        if ($furnishing = $this->arrayParam($request, 'furnishing')) {
            $query->whereIn('furnishing', $furnishing);
        }

        // Every selected amenity must be present (in features or amenities)
        foreach ($this->arrayParam($request, 'amenities') as $amenity) {
            $query->where(function ($q) use ($amenity) {
                $q->whereJsonContains('amenities', $amenity)
                    ->orWhereJsonContains('features', $amenity);
            });
        }

        if ($request->filled('builder')) {
            $query->where('developer_name', $request->builder);
        }

        if ($request->boolean('rera')) {
            $query->whereNotNull('rera_id')->where('rera_id', '!=', '');
        }

        if ($request->boolean('verified')) {
            $query->where('is_verified', true);
        }

        if ($request->boolean('video')) {
            $query->whereNotNull('video_url')->where('video_url', '!=', '');
        }

        // General search (title, city, address)
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('city', 'like', "%{$searchTerm}%")
                    ->orWhere('location', 'like', "%{$searchTerm}%")
                    ->orWhere('developer_name', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%")
                    ->orWhere('address', 'like', "%{$searchTerm}%");
            });
        }

        switch ($request->get('sort', 'newest')) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'price_asc':
                $this->orderByPrice($query, $priceRanges ?? $this->priceRanges(), false);
                break;
            case 'price_desc':
                $this->orderByPrice($query, $priceRanges ?? $this->priceRanges(), true);
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

        $properties = $query->with('images')->paginate(10);

        return view('search-results', [
            'properties' => $properties,
            'searchParams' => $request->all(),
            'filterOptions' => $this->searchFilterOptions(),
        ]);
    }

    /**
     * [property id => [min, max]] in rupees, parsed from the project and unit price text.
     * Properties with no readable price are left out.
     */
    protected function priceRanges(): array
    {
        $ranges = [];
        $add = function ($id, $text) use (&$ranges) {
            $r = PriceParser::range($text);
            if ($r) {
                $ranges[$id] = isset($ranges[$id])
                    ? [min($ranges[$id][0], $r[0]), max($ranges[$id][1], $r[1])]
                    : $r;
            }
        };

        Property::where('is_active', true)->get(['id', 'price'])->each(fn($p) => $add($p->id, $p->price));
        DB::table('property_details')->get(['property_id', 'price'])->each(fn($d) => $add($d->property_id, $d->price));

        return $ranges;
    }

    protected function orderByPrice($query, array $ranges, bool $desc): void
    {
        $ids = array_keys($ranges);
        usort($ids, fn($a, $b) => $desc ? $ranges[$b][0] <=> $ranges[$a][0] : $ranges[$a][0] <=> $ranges[$b][0]);
        if ($ids) {
            // Properties without a readable price sort last.
            $query->orderByRaw('FIELD(id, ' . implode(',', array_map('intval', $ids)) . ') = 0')
                ->orderByRaw('FIELD(id, ' . implode(',', array_map('intval', $ids)) . ')');
        }
    }

    /**
     * Read a query param as a clean array of non-empty strings (accepts "a" or ["a","b"]).
     */
    protected function arrayParam(Request $request, string $key): array
    {
        $value = $request->input($key);
        $value = is_array($value) ? $value : [$value];

        return array_values(array_filter(array_map(
            fn($v) => is_scalar($v) ? trim((string) $v) : '',
            $value
        ), fn($v) => $v !== ''));
    }

    /**
     * Option lists for the search filter panel, built from live listings.
     */
    protected function searchFilterOptions(): array
    {
        $active = Property::where('is_active', true);

        $distinct = fn($column) => (clone $active)->whereNotNull($column)->where($column, '!=', '')
            ->distinct()->orderBy($column)->pluck($column)->all();

        $amenities = (clone $active)->get(['amenities', 'features'])
            ->flatMap(fn($p) => array_merge($p->amenities ?? [], $p->features ?? []))
            ->map(fn($a) => trim($a))->filter()->unique()->sort()->values()->all();

        return [
            'cities' => $distinct('city'),
            'localities' => (clone $active)->whereNotNull('location')->where('location', '!=', '')
                ->distinct()->orderBy('location')->get(['city', 'location'])
                ->map(fn($p) => ['city' => $p->city, 'name' => $p->location])->all(),
            'builders' => $distinct('developer_name'),
            'amenities' => $amenities,
            'statuses' => self::STATUS_SLUGS,
            'furnishing' => ['Fully Furnished', 'Semi Furnished', 'Unfurnished'],
            'budgets' => [
                500000 => '5 Lakh', 1000000 => '10 Lakh', 2500000 => '25 Lakh', 5000000 => '50 Lakh',
                7500000 => '75 Lakh', 10000000 => '1 Cr', 20000000 => '2 Cr', 50000000 => '5 Cr', 100000000 => '10 Cr',
            ],
        ];
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
        $property = Property::with('details')->findOrFail($id);
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
        $property = Property::with(['images', 'details'])->findOrFail($id);

        // Collect every uploaded file first, then remove the rows and finally the files.
        $files = array_merge(
            [$property->main_image, $property->floor_plan_image, $property->brochure],
            $property->images->pluck('image_path')->all(),
            $property->details->pluck('document')->all(),
        );

        DB::transaction(function () use ($property) {
            $property->images()->delete();
            $property->details()->delete();
            SimilarProperty::where('property_id', $property->id)
                ->orWhere('similar_property_id', $property->id)
                ->delete();
            $property->delete();
        });

        foreach (array_unique(array_filter($files)) as $file) {
            // Never remove a file another listing, gallery photo or unit still points at.
            if ($this->fileIsReferenced($file, $property->id)) {
                continue;
            }

            $path = public_path($file);
            if (is_file($path)) {
                @unlink($path);
            }
        }

        return back()->with('success', 'Property deleted successfully.');
    }


public function update(Request $request, $id)
{
    $property = Property::findOrFail($id);

    // The slug is fixed once created; ignore anything submitted for it.
    $request->merge([
        'slug' => $property->slug
    ]);

    $this->resolveOtherFields($request);

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
            'developer_name' => $validatedData['developer_name'] ?? null,
            'description' => $validatedData['description'],
            'slug' => $validatedData['slug'],
            'rera_id' => $validatedData['rera_id'] ?? null,
            'category' => $validatedData['category'] ?? 'Residential',
            'price' => $validatedData['price'],
            'price_unit' => $validatedData['price_unit'] ?? '₹',
            'security_deposit' => $validatedData['security_deposit'] ?? null,

            // Location Details
            'location' => $validatedData['location'] ?? null,
            'address' => $validatedData['address'],
            'city' => $validatedData['city'],
            'state' => $validatedData['state'],
            'country' => $request->input('country'),
            'zip_code' => $validatedData['zip_code'] ?? null,
            'latitude' => $validatedData['latitude'] ?? null,
            'longitude' => $validatedData['longitude'] ?? null,
            'landmark' => $validatedData['landmark'] ?? null,
            'google_map_link' => $validatedData['google_map_link'] ?? null,

            // Property Details
            'bedrooms' => $validatedData['bedrooms'] ?? null,
            'bathrooms' => $validatedData['bathrooms'] ?? null,
            'balconies' => $validatedData['balconies'] ?? null,
            'apartment_per_floor' => $validatedData['apartment_per_floor'] ?? null,
            'super_area' => $validatedData['super_area'] ?? null,
            'carpet_area' => $validatedData['carpet_area'] ?? null,

            // Furnishing
            'furnishing' => $validatedData['furnishing'] ?? null,
            'furnishing_details' => $validatedData['furnishing_details'] ?? null,

            // Features & Amenities
            'features' => $validatedData['features'] ?? [],
            'amenities' => $validatedData['amenities'] ?? [],

            // Possession
            'possession_date' => !empty($validatedData['possession_date']) ? $validatedData['possession_date'] . '-01' : null,

            // Media
            'main_image' => $mainImagePath,
            'video_url' => $validatedData['video_url'] ?? null,
            'floor_plan_image' => $floorPlanPath,
            'brochure' => $brochurePath,

            // Additional Info
            'is_featured' => $request->has('is_featured'),
            'is_verified' => $request->has('is_verified'),
            'pre_launch_property' => ($request->input('project_status') === 'Pre-Launch' || $request->has('pre_launch_property')),
            'property_status' => $validatedData['property_status'] ?? 'Available',
            'project_status' => $validatedData['project_status'] ?? null,
            'notes' => $validatedData['notes'] ?? null,
            'keyfeatures' => $validatedData['keyfeatures'] ?? null,

            // Nearby locations
            'bazar_distance_km' => $validatedData['bazar_distance_km'] ?? null,
            'hospital_distance_km' => $validatedData['hospital_distance_km'] ?? null,
            'school_distance_km' => $validatedData['school_distance_km'] ?? null,
            'bus_stand_distance_km' => $validatedData['bus_stand_distance_km'] ?? null,
            'junction_distance_km' => $validatedData['junction_distance_km'] ?? null,
            'airport_distance_km' => $validatedData['airport_distance_km'] ?? null,
            'custom_nearby_places' => $this->cleanCustomPlaces($validatedData['custom_places'] ?? []),
            'place_names' => $this->cleanPlaceNames($validatedData['place_names'] ?? []),
        ]);

        // Handle property details (configurations/units)
        $property->details()->delete();
        if (!empty($request->property_details) && is_array($request->property_details)) {
            $firstDetail = null;
            foreach ($request->property_details as $index => $detail) {
                $documentPath = $detail['existing_document'] ?? null;
                if ($request->hasFile("property_details.{$index}.document")) {
                    $documentPath = $this->handleFileUpload($request->file("property_details.{$index}.document"), 'properties/details_documents');
                }
                $hasContent = false;
                foreach (['unit_type', 'bedrooms', 'bathrooms', 'balconies', 'carpet_area', 'super_area', 'price'] as $key) {
                    if (isset($detail[$key]) && $detail[$key] !== '') {
                        $hasContent = true;
                        break;
                    }
                }
                if ($hasContent || !empty($documentPath)) {
                    if (!$firstDetail) {
                        $firstDetail = $detail;
                    }
                    $property->details()->create([
                        'unit_type' => $detail['unit_type'] ?? (!empty($detail['bedrooms']) ? ($detail['bedrooms'] . ' BHK') : null),
                        'bedrooms' => !empty($detail['bedrooms']) ? $detail['bedrooms'] : null,
                        'bathrooms' => !empty($detail['bathrooms']) ? $detail['bathrooms'] : null,
                        'balconies' => !empty($detail['balconies']) ? $detail['balconies'] : null,
                        'carpet_area' => !empty($detail['carpet_area']) ? $detail['carpet_area'] : null,
                        'super_area' => !empty($detail['super_area']) ? $detail['super_area'] : null,
                        'price' => !empty($detail['price']) ? $detail['price'] : null,
                        'document' => $documentPath,
                    ]);
                }
            }
            if ($firstDetail) {
                $property->update([
                    'bedrooms' => !empty($firstDetail['bedrooms']) ? $firstDetail['bedrooms'] : $property->bedrooms,
                    'bathrooms' => !empty($firstDetail['bathrooms']) ? $firstDetail['bathrooms'] : $property->bathrooms,
                    'balconies' => !empty($firstDetail['balconies']) ? $firstDetail['balconies'] : $property->balconies,
                    'super_area' => !empty($firstDetail['super_area']) ? $firstDetail['super_area'] : $property->super_area,
                    'carpet_area' => !empty($firstDetail['carpet_area']) ? $firstDetail['carpet_area'] : $property->carpet_area,
                ]);
            }
        }

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

// Delete one gallery image (row and file). Answers JSON to the edit page's background request.
public function deleteImage(Request $request, $id)
{
    $image = PropertyImage::findOrFail($id);
    $path = $image->image_path;
    $image->delete();

    // Remove the file only if nothing else still points at it.
    if ($path && !$this->fileIsReferenced($path, 0)) { // 0 = check every property, including this one
        $full = public_path($path);
        if (is_file($full)) {
            @unlink($full);
        }
    }

    if ($request->expectsJson()) {
        return response()->json(['deleted' => true]);
    }

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

        $this->resolveOtherFields($request);
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
                'developer_name' => $validatedData['developer_name'] ?? null,
                'description' => $validatedData['description'],
                'slug' => !empty($validatedData['slug']) ? Str::slug($validatedData['slug']) : Str::slug($validatedData['title']),
                'rera_id' => $validatedData['rera_id'] ?? null,
                'category' => $validatedData['category'] ?? 'Residential',
                'price' => $validatedData['price'],
                'price_unit' => $validatedData['price_unit'] ?? '₹',
                'security_deposit' => $validatedData['security_deposit'] ?? null,
                'property_id' => $this->generatePropertyId(),

                // Location Details
                'location' => $validatedData['location'] ?? null,
                'address' => $validatedData['address'],
                'city' => $validatedData['city'],
                'state' => $validatedData['state'],
                'country' => $request->input('country'),
                'zip_code' => $validatedData['zip_code'] ?? null,
                'latitude' => $validatedData['latitude'] ?? null,
                'longitude' => $validatedData['longitude'] ?? null,
                'landmark' => $validatedData['landmark'] ?? null,
                'google_map_link' => $validatedData['google_map_link'] ?? null,

                // Property Details
                'bedrooms' => $validatedData['bedrooms'] ?? null,
                'bathrooms' => $validatedData['bathrooms'] ?? null,
                'balconies' => $validatedData['balconies'] ?? null,
                'apartment_per_floor' => $validatedData['apartment_per_floor'] ?? null,
            'super_area' => $validatedData['super_area'] ?? null,
                'carpet_area' => $validatedData['carpet_area'] ?? null,

                // Furnishing
                'furnishing' => $validatedData['furnishing'] ?? null,
                'furnishing_details' => $validatedData['furnishing_details'] ?? null,

                // Features & Amenities
                'features' => $validatedData['features'] ?? null,
                'amenities' => $validatedData['amenities'] ?? null,

                // Possession
                'possession_date' => !empty($validatedData['possession_date']) ? $validatedData['possession_date'] . '-01' : null,

                // Media
                'main_image' => $mainImagePath,
                'video_url' => $validatedData['video_url'] ?? null,
                'floor_plan_image' => $floorPlanPath,
                'brochure' => $brochurePath,

                // Additional Info
                'is_featured' => $request->has('is_featured'),
                'is_verified' => $request->has('is_verified'),
                'pre_launch_property' => ($request->input('project_status') === 'Pre-Launch' || $request->has('pre_launch_property')),
                'property_status' => $validatedData['property_status'] ?? 'Available',
                'project_status' => $validatedData['project_status'] ?? null,
                'notes' => $validatedData['notes'] ?? null,
                'keyfeatures' => $validatedData['keyfeatures'] ?? null,

                // ... existing fields ...
                'bazar_distance_km' => $validatedData['bazar_distance_km'] ?? null,
                'hospital_distance_km' => $validatedData['hospital_distance_km'] ?? null,
                'school_distance_km' => $validatedData['school_distance_km'] ?? null,
                'bus_stand_distance_km' => $validatedData['bus_stand_distance_km'] ?? null,
                'junction_distance_km' => $validatedData['junction_distance_km'] ?? null,
                'airport_distance_km' => $validatedData['airport_distance_km'] ?? null,
                'custom_nearby_places' => $this->cleanCustomPlaces($validatedData['custom_places'] ?? []),
            'place_names' => $this->cleanPlaceNames($validatedData['place_names'] ?? []),

                // Ownership - assuming you'll use auth later
                'user_id' => Auth::guard('admin')->user()->id  ?? 1, // Default to 1 if no auth
            ]);

            // Handle multiple property details (configurations/units)
            if (!empty($request->property_details) && is_array($request->property_details)) {
                $firstDetail = null;
                foreach ($request->property_details as $index => $detail) {
                    $documentPath = null;
                    if ($request->hasFile("property_details.{$index}.document")) {
                        $documentPath = $this->handleFileUpload($request->file("property_details.{$index}.document"), 'properties/details_documents');
                    }
                    $hasContent = false;
                    foreach (['unit_type', 'bedrooms', 'bathrooms', 'balconies', 'carpet_area', 'super_area', 'price'] as $key) {
                        if (isset($detail[$key]) && $detail[$key] !== '') {
                            $hasContent = true;
                            break;
                        }
                    }
                    if ($hasContent || !empty($documentPath)) {
                        if (!$firstDetail) {
                            $firstDetail = $detail;
                        }
                        $property->details()->create([
                            'unit_type' => $detail['unit_type'] ?? (!empty($detail['bedrooms']) ? ($detail['bedrooms'] . ' BHK') : null),
                            'bedrooms' => !empty($detail['bedrooms']) ? $detail['bedrooms'] : null,
                            'bathrooms' => !empty($detail['bathrooms']) ? $detail['bathrooms'] : null,
                            'balconies' => !empty($detail['balconies']) ? $detail['balconies'] : null,
                            'carpet_area' => !empty($detail['carpet_area']) ? $detail['carpet_area'] : null,
                            'super_area' => !empty($detail['super_area']) ? $detail['super_area'] : null,
                            'price' => !empty($detail['price']) ? $detail['price'] : null,
                            'document' => $documentPath,
                        ]);
                    }
                }
                if ($firstDetail) {
                    $property->update([
                        'bedrooms' => !empty($firstDetail['bedrooms']) ? $firstDetail['bedrooms'] : $property->bedrooms,
                        'bathrooms' => !empty($firstDetail['bathrooms']) ? $firstDetail['bathrooms'] : $property->bathrooms,
                        'balconies' => !empty($firstDetail['balconies']) ? $firstDetail['balconies'] : $property->balconies,
                        'super_area' => !empty($firstDetail['super_area']) ? $firstDetail['super_area'] : $property->super_area,
                        'carpet_area' => !empty($firstDetail['carpet_area']) ? $firstDetail['carpet_area'] : $property->carpet_area,
                    ]);
                }
            }

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
     * Resolve the free-text "add more" amenities/specifications fields into
     * their checkbox array fields before validation, since the form submits
     * these as separate sibling fields rather than as part of the array itself.
     */
    /**
     * Keep only custom places that have both a name and a distance.
     */
    /**
     * One entry per city of the active listings for the homepage cards: project count and the
     * average of the listings' starting prices (null when no listing in the city has a price).
     * Biggest cities first. Cities are grouped ignoring case and stray spaces.
     *
     * @return \Illuminate\Support\Collection<int, array{city: string, count: int, avg: int|null}>
     */
    protected function cityStats()
    {
        return Property::where('is_active', true)
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->get(['city', 'price'])
            ->groupBy(fn ($p) => mb_strtolower(trim($p->city)))
            ->map(function ($group) {
                $prices = $group
                    ->map(fn ($p) => PriceParser::range($p->price))
                    ->filter()
                    ->map(fn ($range) => ($range[0] + $range[1]) / 2);

                return [
                    'city' => trim($group->first()->city),
                    'count' => $group->count(),
                    'avg' => $prices->isNotEmpty() ? (int) round($prices->avg()) : null,
                ];
            })
            ->sortBy([['count', 'desc'], ['city', 'asc']])
            ->values();
    }

    /** True when any other property, gallery image or unit still uses this uploaded file. */
    protected function fileIsReferenced(string $file, int $exceptPropertyId): bool
    {
        return DB::table('full_property_schema')
                ->where('id', '!=', $exceptPropertyId)
                ->where(fn ($q) => $q->where('main_image', $file)->orWhere('floor_plan_image', $file)->orWhere('brochure', $file))
                ->exists()
            || DB::table('property_images')->where('image_path', $file)->exists()
            || DB::table('property_details')->where('document', $file)->exists();
    }

    /** Only the known place keys, trimmed, blanks dropped; null when nothing was named. */
    protected function cleanPlaceNames(array $names): ?array
    {
        $clean = [];
        foreach (array_keys(Property::NAMED_PLACES) as $key) {
            $name = trim((string) ($names[$key] ?? ''));
            if ($name !== '') {
                $clean[$key] = $name;
            }
        }

        return $clean ?: null;
    }

    protected function cleanCustomPlaces(array $places): array
    {
        $clean = [];
        foreach ($places as $place) {
            $label = trim((string) ($place['label'] ?? ''));
            $distance = trim((string) ($place['distance'] ?? ''));
            if ($label === '' || $distance === '') {
                continue;
            }
            $clean[] = [
                'group' => $place['group'],
                'label' => $label,
                'icon' => $place['icon'] ?? Property::DEFAULT_PLACE_ICON,
                'distance' => $distance,
            ];
        }

        return $clean;
    }

    protected function resolveOtherFields(Request $request)
    {
        $this->mergeOtherListItems($request, 'features');
        $this->mergeOtherListItems($request, 'amenities');
    }

    /**
     * Merge a comma-separated "add more" free-text field into its checkbox
     * array field, so custom items typed by the admin are saved alongside
     * the checked options.
     */
    protected function mergeOtherListItems(Request $request, string $field): void
    {
        $otherRaw = (string) $request->input("{$field}_other");
        if (trim($otherRaw) === '') {
            return;
        }

        $existing = $request->input($field, []);
        $existing = is_array($existing) ? $existing : [];

        $extra = array_filter(array_map('trim', explode(',', $otherRaw)));

        $request->merge([$field => array_values(array_unique(array_merge($existing, $extra)))]);
    }

    /**
     * Validate the request data.
     */
    protected function validateRequest(Request $request, $propertyId = null)
    {
        return $request->validate([
            // Basic Information
            'title' => 'required|string|max:255',
            'developer_name' => 'nullable|string|max:255',
            'description' => 'required|string',
            'slug' => ['nullable', 'string', Rule::unique('full_property_schema', 'slug')->ignore($propertyId)],
            'category' => 'nullable|in:Residential,Commercial',
            'price' => 'nullable|string|max:200',
            'price_unit' => 'nullable|string',
            'rera_id' => 'nullable|string|max:255',
            'security_deposit' => 'nullable|numeric|min:0',

            // Location Details
            'address' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'google_map_link' => 'nullable|string',

            // Property Details (multiple units/configurations)
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'balconies' => 'nullable|integer|min:0',
            'super_area' => 'nullable|numeric|min:0',
            'carpet_area' => 'nullable|numeric|min:0',

            'property_details' => 'nullable|array',
            'property_details.*.unit_type' => 'nullable|string|max:100',
            'property_details.*.bedrooms' => 'nullable|integer|min:0',
            'property_details.*.bathrooms' => 'nullable|integer|min:0',
            'property_details.*.balconies' => 'nullable|integer|min:0',
            'apartment_per_floor' => 'nullable|string|max:100',
            'property_details.*.carpet_area' => 'nullable|numeric|min:0',
            'property_details.*.super_area' => 'nullable|numeric|min:0',
            'property_details.*.price' => 'nullable|string|max:200',
            'property_details.*.document' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'property_details.*.existing_document' => 'nullable|string|max:255',

            // Furnishing
            'furnishing' => 'nullable|in:Fully Furnished,Semi Furnished,Unfurnished',
            'furnishing_details' => 'nullable|array',

            // Features & Amenities
            'features' => 'nullable|array',
            'features.*' => 'string',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string',

            // Possession
            'possession_date' => 'nullable|date_format:Y-m',

            // Media
            'main_image' => [$propertyId ? 'nullable' : 'required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'property_images' => 'nullable|array|max:20', // PHP's max_file_uploads is 20 per request
            'property_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'video_url' => 'nullable|url',
            'floor_plan_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'brochure' => 'nullable|file|mimes:pdf|max:10240', // 10MB max for brochure

            // Additional Info
            'is_featured' => 'nullable|boolean',
            'is_verified' => 'nullable|boolean',
            'pre_launch_property' => 'nullable|boolean',
            'property_status' => 'nullable|in:Available,Rented,Sold,Under Maintenance',
            'project_status' => 'nullable|in:Upcoming,Pre-Launch,Early Possession,Ready to move',
            'notes' => 'nullable|string',
            'keyfeatures' => 'nullable|string',
            'similar_properties' => 'nullable|array',
            'similar_properties.*' => 'nullable|exists:full_property_schema,id',
            // Nearby Locations
            'bazar_distance_km' => ['nullable', 'regex:/^\d+(\.\d+)?\s?(m|km)$/i'],
            'hospital_distance_km' => ['nullable', 'regex:/^\d+(\.\d+)?\s?(m|km)$/i'],
            'school_distance_km' => ['nullable', 'regex:/^\d+(\.\d+)?\s?(m|km)$/i'],
            'bus_stand_distance_km' => ['nullable', 'regex:/^\d+(\.\d+)?\s?(m|km)$/i'],
            'junction_distance_km' => ['nullable', 'regex:/^\d+(\.\d+)?\s?(m|km)$/i'],
            'airport_distance_km' => ['nullable', 'regex:/^\d+(\.\d+)?\s?(m|km)$/i'],
            'place_names' => 'nullable|array',
            'place_names.*' => 'nullable|string|max:100',
            'custom_places' => 'nullable|array|max:30',
            'custom_places.*.group' => 'required|in:nearby,connectivity',
            'custom_places.*.label' => 'nullable|string|max:100',
            'custom_places.*.icon' => ['nullable', Rule::in(array_keys(Property::PLACE_ICONS))],
            'custom_places.*.distance' => ['nullable', 'regex:/^\d+(\.\d+)?\s?(m|km)$/i'],
        ], [
            'property_images.max' => 'You can upload at most 20 photos at a time. Save, then add the rest.',
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
