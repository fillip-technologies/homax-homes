<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    use SoftDeletes;

    public const STATUSES = ['draft', 'published', 'scheduled'];

    protected $fillable = [
        'title', 'slug', 'excerpt', 'body', 'featured_image', 'featured_image_alt',
        'blog_category_id', 'author_name', 'status', 'is_featured', 'published_at',
        'read_time', 'meta_title', 'meta_description', 'created_by',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'read_time' => 'integer',
        'views' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    /** Live on the public site: published (or scheduled) and the date has arrived. */
    public function scopeVisible($query)
    {
        return $query->whereIn('status', ['published', 'scheduled'])
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function isVisible(): bool
    {
        return in_array($this->status, ['published', 'scheduled'], true)
            && $this->published_at !== null
            && $this->published_at->lte(now());
    }

    /** Label for admin lists: a published post with a future date is "Scheduled". */
    public function getStatusLabelAttribute(): string
    {
        if ($this->status === 'draft') {
            return 'Draft';
        }

        return $this->isVisible() ? 'Published' : 'Scheduled';
    }

    /** Uploaded images are stored as public paths, seeded ones as full URLs. */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->featured_image) {
            return null;
        }

        return Str::startsWith($this->featured_image, ['http://', 'https://'])
            ? $this->featured_image
            : asset($this->featured_image);
    }

    public function getUrlAttribute(): string
    {
        return route('blog.show', $this->slug);
    }

    /** Whole minutes to read the given HTML at ~200 words a minute (never below 1). */
    public static function readTimeFor(string $html): int
    {
        $words = str_word_count(strip_tags($html));

        return max(1, (int) ceil($words / 200));
    }

    /** A slug no other post (trashed ones included) uses. */
    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::limit(Str::slug($title) ?: 'post', 200, '');
        $slug = $base;
        $i = 2;

        while (static::withTrashed()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
