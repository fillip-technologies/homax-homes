<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Testimonial extends Model
{
    protected $fillable = [
        'name',
        'subtitle',
        'quote',
        'rating',
        'caption',
        'photo',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rating' => 'integer',
        'sort_order' => 'integer',
    ];

    public function scopeVisible($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    /** Uploaded photos are stored as public paths, the seeded ones as full URLs. */
    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }

        return Str::startsWith($this->photo, ['http://', 'https://']) ? $this->photo : asset($this->photo);
    }

    public function getInitialAttribute(): string
    {
        return Str::upper(Str::substr(trim($this->name), 0, 1)) ?: '?';
    }
}
