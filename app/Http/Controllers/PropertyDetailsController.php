<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\SimilarProperty;
use Illuminate\Support\Facades\Auth;

class PropertyDetailsController extends Controller
{
    public function index($id)
    {
        $property = Property::with(['images', 'owner', 'details'])->findOrFail($id);

        // Inactive listings are hidden from the public; a logged-in admin can still preview them.
        if (!$property->is_active && !Auth::guard('admin')->check()) {
            abort(404);
        }

        // dd($property);
        // All images for this property
        $propertyimagesall = PropertyImage::where('property_id', $id)->get();

        // Get featured image (or fallback)
        $featuredImage = $propertyimagesall->where('is_featured', true)->first() ?? $propertyimagesall->first();

        // Get similar property IDs from the pivot table
        $similarPropertyIds = SimilarProperty::where('property_id', $id)->pluck('similar_property_id');

        // Fetch actual similar properties with their images
        $similarProperties = Property::with('images')
            ->whereIn('id', $similarPropertyIds)
            ->get();

        // Fallback: if no manual similar properties, find properties by type/city or latest
        if ($similarProperties->isEmpty()) {
            $similarProperties = Property::with('images')
                ->where('id', '!=', $id)
                ->where(function ($q) use ($property) {
                    if ($property->category) {
                        $q->where('category', $property->category);
                    }
                    if ($property->city) {
                        $q->orWhere('city', $property->city);
                    }
                })
                ->latest()
                ->take(4)
                ->get();
        }

        $similarProperties = $similarProperties->map(function ($similar) {
            $similar->featuredImage = $similar->images->where('is_featured', true)->first()
                ?? $similar->images->first();
            return $similar;
        });
        // dd($propertyimagesall);
        return view('propertydetails', compact(
            'property',
            'propertyimagesall',
            'featuredImage',
            'similarProperties'
        ));
    }
}
