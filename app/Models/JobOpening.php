<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class JobOpening extends Model
{
    public const TYPES = [
        'full-time' => 'Full-time',
        'part-time' => 'Part-time',
        'internship' => 'Internship',
        'contract' => 'Contract',
    ];

    protected $fillable = [
        'title', 'slug', 'department', 'location', 'employment_type', 'experience',
        'salary', 'highlights', 'summary', 'description', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeVisible($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->employment_type] ?? Str::headline((string) $this->employment_type);
    }

    /** The comma separated highlights as a clean list. */
    public function getHighlightListAttribute(): array
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) $this->highlights))));
    }

    public function getUrlAttribute(): string
    {
        return route('careers.show', $this->slug);
    }

    /** A slug no other opening uses. */
    public static function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::limit(Str::slug($source) ?: 'job', 200, '');
        $slug = $base;
        $i = 2;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
