<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    public const DEFAULT_PLACE_ICON = 'fa-location-dot';

    /** Fixed nearby/connectivity places that can carry an optional name (key => label). */
    public const NAMED_PLACES = [
        'bazar' => 'Metro Station',
        'hospital' => 'Hospital',
        'school' => 'School',
        'bus_stand' => 'Bus Stand',
        'junction' => 'Railway Junction',
        'airport' => 'Airport',
    ];

    /** Icons an admin can pick for a custom nearby/connectivity place (Font Awesome 6 class => label). */
    public const PLACE_ICONS = [
        'fa-location-dot' => 'Location pin',
        'fa-hospital' => 'Hospital',
        'fa-pills' => 'Pharmacy',
        'fa-school' => 'School',
        'fa-graduation-cap' => 'College / University',
        'fa-bag-shopping' => 'Shopping mall',
        'fa-store' => 'Market / Store',
        'fa-utensils' => 'Restaurant',
        'fa-building-columns' => 'Bank',
        'fa-tree' => 'Park',
        'fa-dumbbell' => 'Gym',
        'fa-film' => 'Cinema',
        'fa-futbol' => 'Stadium / Sports',
        'fa-place-of-worship' => 'Place of worship',
        'fa-gas-pump' => 'Petrol pump',
        'fa-shield-halved' => 'Police station',
        'fa-laptop-code' => 'IT park / Office',
        'fa-train-subway' => 'Metro station',
        'fa-bus' => 'Bus stop',
        'fa-train' => 'Railway station',
        'fa-plane' => 'Airport',
        'fa-road' => 'Highway / Expressway',
    ];

    use HasFactory;

    protected $table = 'full_property_schema';

    protected $fillable = [
        // Basic Information
        'title',
        'developer_name',
        'description',
        'slug',
        'rera_id',
        'category',
        'price',
        'price_unit',
        'security_deposit',
        'property_id',
        'pre_launch_property',
        'project_status',
        // Location Details
        'location',
        'address',
        'city',
        'state',
        'country',
        'zip_code',
        'latitude',
        'longitude',
        'landmark',
        'google_map_link',

        // Property Details
        'apartment_per_floor',
        'bedrooms',
        'bathrooms',
        'balconies',
        'super_area',
        'carpet_area',
        'plot_area',

        // Furnishing
        'furnishing',
        'furnishing_details',

        // Features & Amenities
        'features',
        'amenities',

        // Possession
        'possession_date',

        // Media
        'main_image',
        'video_url',
        'floor_plan_image',
        'brochure',

        // Additional Info
        'is_featured',
        'is_verified',
        'is_active',
        'property_status',
        'notes',
        'keyfeatures',
        // nearby_places',
        'bazar_distance_km',
        'hospital_distance_km',
        'school_distance_km',
        'bus_stand_distance_km',
        'junction_distance_km',
        'airport_distance_km',
        'custom_nearby_places',
        'place_names',

        // Ownership
        'user_id',
    ];

    protected $casts = [
        'furnishing_details' => 'array',
        'features' => 'array',
        'amenities' => 'array',
        'custom_nearby_places' => 'array',
        'place_names' => 'array',
        'is_featured' => 'boolean',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'pre_launch_property' => 'boolean',
        'possession_date' => 'date',

        'security_deposit' => 'decimal:2',
        'super_area' => 'decimal:2',
        'carpet_area' => 'decimal:2',
        'plot_area' => 'decimal:2',
    ];

    // public function images()
    // {
    //     return $this->hasMany(PropertyImage::class, 'property_id');
    // }

    public function details()
    {
        return $this->hasMany(PropertyDetail::class, 'property_id');
    }

    public function propertyDetails()
    {
        return $this->hasMany(PropertyDetail::class, 'property_id');
    }

    public function similarProperties()
    {
        return $this->belongsToMany(
            Property::class,
            'similar_properties',
            'property_id',
            'similar_property_id'
        )->withTimestamps();
    }
    public function images()
    {
        return $this->hasMany(PropertyImage::class);
    }
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
