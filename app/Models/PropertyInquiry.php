<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyInquiry extends Model
{
    use HasFactory;

    /** How each stored `intent` reads in the admin list. */
    public const TYPE_LABELS = [
        'enquiry' => 'Property enquiry',
        'inquiry' => 'Property enquiry',
        'brochure' => 'Brochure request',
        'general' => 'Contact message',
        'career' => 'Job application',
        'associate' => 'Associate request',
    ];

    protected $fillable = [
        'property_id',
        'property_title',
        'name',
        'email',
        'phone',
        'message',
        'intent',
        'source',
        'terms_accepted'
    ];

    public function property()
    {
        // null once the listing has been deleted; property_title keeps its name for the list.
        return $this->belongsTo(Property::class);
    }

    /** Human label for the enquiry type (falls back to the raw intent). */
    public function getTypeLabelAttribute(): string
    {
        $intent = strtolower((string) $this->intent);

        return self::TYPE_LABELS[$intent] ?? ($intent !== '' ? ucfirst($intent) : 'Property enquiry');
    }

    /** Name saved with the enquiry, else the live property title; null for general leads. */
    public function getPropertyNameAttribute(): ?string
    {
        return $this->property_title ?: $this->property?->title;
    }
}
