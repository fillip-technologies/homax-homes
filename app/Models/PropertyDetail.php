<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyDetail extends Model
{
    use HasFactory;

    protected $table = 'property_details';

    protected $fillable = [
        'property_id',
        'unit_type',
        'bedrooms',
        'bathrooms',
        'balconies',
        'apartment_per_floor',
        'carpet_area',
        'super_area',
        'plot_area',
        'price',
    ];

    protected $casts = [
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'balconies' => 'integer',
        'carpet_area' => 'decimal:2',
        'super_area' => 'decimal:2',
        'plot_area' => 'decimal:2',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }
}
