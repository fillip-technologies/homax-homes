<?php

namespace App\Services\Chatbot;

use App\Http\Controllers\PropertyListingController;
use App\Models\Property;
use App\Models\PropertyInquiry;
use App\Support\PriceParser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/**
 * The functions Gemini may call to read the live site. Gemini never reaches the
 * database or the web routes itself: it asks for a function by name, this class
 * runs it against active listings only, and the JSON result goes back to it.
 */
class PropertyTools
{
    private const RESULT_LIMIT = 5;

    public function declarations(): array
    {
        return [
            [
                'name' => 'search_properties',
                'description' => 'Search active Homax Homes projects. Use for any question about what is available, where, at what price or configuration. All filters are optional.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => ['type' => 'string', 'description' => 'Free text matched against project name, developer, locality, address and description.'],
                        'city' => ['type' => 'string', 'description' => 'City, e.g. "Thane".'],
                        'locality' => ['type' => 'string', 'description' => 'Area within a city, e.g. "Majiwada".'],
                        'category' => ['type' => 'string', 'enum' => ['Residential', 'Commercial']],
                        'status' => ['type' => 'string', 'enum' => array_values(PropertyListingController::STATUS_SLUGS)],
                        'bhk' => ['type' => 'integer', 'description' => 'Bedrooms; 5 means 5 or more.'],
                        'budget_min' => ['type' => 'integer', 'description' => 'Minimum budget in rupees (1 Lakh = 100000, 1 Cr = 10000000).'],
                        'budget_max' => ['type' => 'integer', 'description' => 'Maximum budget in rupees (1 Lakh = 100000, 1 Cr = 10000000).'],
                        'sort' => ['type' => 'string', 'enum' => ['newest', 'price_low_to_high', 'price_high_to_low']],
                    ],
                ],
            ],
            [
                'name' => 'get_property_details',
                'description' => 'Full details of one project: units and prices, areas, amenities, nearby places, RERA, possession.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => ['property_id' => ['type' => 'integer']],
                    'required' => ['property_id'],
                ],
            ],
            [
                'name' => 'list_locations',
                // No parameters key: Gemini rejects an object schema with empty properties.
                'description' => 'Cities and localities where Homax Homes has active projects, with project counts.',
            ],
            [
                'name' => 'request_callback',
                'description' => 'Save a callback request for the sales team. Only call after the visitor has given their name and phone number and confirmed they want a call.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'name' => ['type' => 'string'],
                        'phone' => ['type' => 'string', 'description' => 'As the visitor typed it, with country code if given.'],
                        'property_id' => ['type' => 'integer', 'description' => 'The project they are interested in, if any.'],
                        'note' => ['type' => 'string', 'description' => 'One line on what they are looking for.'],
                    ],
                    'required' => ['name', 'phone'],
                ],
            ],
        ];
    }

    /** Runs a function Gemini asked for. Always returns an object-shaped array, never throws. */
    public function call(string $name, array $args, string $ip): array
    {
        try {
            return match ($name) {
                'search_properties' => $this->search($args),
                'get_property_details' => $this->details((int) ($args['property_id'] ?? 0)),
                'list_locations' => $this->locations(),
                'request_callback' => $this->callback($args, $ip),
                default => ['error' => "Unknown function {$name}."],
            };
        } catch (\Throwable $e) {
            report($e);

            return ['error' => 'The lookup failed. Tell the visitor to try the search page or contact us.'];
        }
    }

    private function search(array $args): array
    {
        $query = Property::query()->where('is_active', true)->with('details');

        if ($city = $this->text($args, 'city')) {
            $query->where('city', 'like', '%' . $this->escapeLike($city) . '%');
        }
        if ($locality = $this->text($args, 'locality')) {
            $query->where('location', 'like', '%' . $this->escapeLike($locality) . '%');
        }
        if ($category = $this->text($args, 'category')) {
            $query->where('category', 'like', $this->escapeLike($category));
        }
        if ($status = $this->text($args, 'status')) {
            // LIKE without wildcards: a case-insensitive match ("Ready to move" vs "Ready to Move").
            $query->where(function ($q) use ($status) {
                $q->where('project_status', 'like', $this->escapeLike($status));
                if (strcasecmp($status, 'Pre-Launch') === 0) {
                    $q->orWhere('pre_launch_property', true);
                }
            });
        }
        if ($term = $this->text($args, 'query')) {
            $like = '%' . $this->escapeLike($term) . '%';
            $query->where(function ($q) use ($like) {
                foreach (['title', 'developer_name', 'location', 'city', 'address', 'description'] as $column) {
                    $q->orWhere($column, 'like', $like);
                }
            });
        }
        if (!empty($args['bhk'])) {
            $bhk = (int) $args['bhk'];
            $match = fn ($q) => $bhk >= 5 ? $q->where('bedrooms', '>=', 5) : $q->where('bedrooms', $bhk);
            $query->where(fn ($q) => $match($q)->orWhereHas('details', $match));
        }

        // Prices are free text ("75 Lakh", "50L-70L"), so budget and price sort run in PHP.
        $results = $query->latest()->limit(500)->get()->map(fn (Property $p) => [$p, $this->priceRange($p)]);

        $min = (int) ($args['budget_min'] ?? 0);
        $max = !empty($args['budget_max']) ? (int) $args['budget_max'] : PHP_INT_MAX;
        if ($min > 0 || $max < PHP_INT_MAX) {
            // With a BHK, the budget applies to that unit size's price, not the whole project's.
            $bhk = !empty($args['bhk']) ? (int) $args['bhk'] : null;
            $results = $results->filter(function ($r) use ($bhk, $min, $max) {
                $range = $bhk ? $this->priceRange($r[0], $bhk) : $r[1];

                return $range && $range[0] <= $max && $range[1] >= $min;
            });
        }

        $sort = $args['sort'] ?? 'newest';
        // Unpriced projects go last either way.
        if ($sort === 'price_low_to_high') {
            $results = $results->sortBy(fn ($r) => $r[1] ? $r[1][0] : PHP_INT_MAX);
        } elseif ($sort === 'price_high_to_low') {
            $results = $results->sortByDesc(fn ($r) => $r[1] ? $r[1][1] : -1);
        }

        return [
            'total_matches' => $results->count(),
            'projects' => $results->take(self::RESULT_LIMIT)->map(fn ($r) => $this->summary($r[0], $r[1]))->values()->all(),
            'see_all_url' => $this->searchUrl($args),
        ];
    }

    private function details(int $id): array
    {
        $p = Property::with('details')->where('is_active', true)->find($id);
        if (!$p) {
            return ['error' => "No active project with id {$id}."];
        }

        $nearby = [];
        $names = (array) $p->place_names;
        foreach (Property::NAMED_PLACES as $key => $label) {
            $distance = trim((string) $p->{$key . '_distance_km'});
            if ($distance !== '') {
                $name = trim((string) ($names[$key] ?? ''));
                $nearby[] = $label . ($name !== '' ? " ({$name})" : '') . ': ' . $distance;
            }
        }
        foreach ((array) $p->custom_nearby_places as $place) {
            if (filled($place['label'] ?? null) && filled($place['distance'] ?? null)) {
                $nearby[] = $place['label'] . ': ' . $place['distance'];
            }
        }

        return $this->summary($p, $this->priceRange($p)) + array_filter([
            'rera_id' => $p->rera_id,
            'address' => $p->address,
            'landmark' => $p->landmark,
            'description' => Str::limit(trim(strip_tags((string) $p->description)), 700),
            'key_features' => Str::limit(trim(strip_tags((string) $p->keyfeatures)), 400),
            'units' => $p->details->map(fn ($d) => array_filter([
                'type' => $d->unit_type ?: ($d->bedrooms ? $d->bedrooms . ' BHK' : null),
                'bathrooms' => $d->bathrooms,
                'carpet_area_sqft' => $d->carpet_area ? (float) $d->carpet_area : null,
                'super_area_sqft' => $d->super_area ? (float) $d->super_area : null,
                'price' => $d->price,
            ]))->values()->all(),
            'super_area_sqft' => $p->super_area ? (float) $p->super_area : null,
            'carpet_area_sqft' => $p->carpet_area ? (float) $p->carpet_area : null,
            'furnishing' => $p->furnishing,
            'amenities' => array_slice((array) $p->amenities, 0, 25),
            'features' => array_slice((array) $p->features, 0, 25),
            'nearby' => $nearby,
            'brochure_available' => filled($p->brochure) ? 'yes - the visitor can request it on the project page' : null,
            'has_video' => filled($p->video_url) ? 'yes' : null,
        ], fn ($v) => $v !== null && $v !== '' && $v !== []);
    }

    private function locations(): array
    {
        $rows = Property::where('is_active', true)->whereNotNull('city')->where('city', '!=', '')
            ->get(['city', 'location']);

        return [
            'cities' => $rows->groupBy(fn ($r) => trim($r->city))->map(fn ($group, $city) => [
                'city' => $city,
                'projects' => $group->count(),
                'localities' => $group->pluck('location')->filter()->map(fn ($l) => trim($l))->unique()->values()->all(),
            ])->sortByDesc('projects')->values()->all(),
        ];
    }

    private function callback(array $args, string $ip): array
    {
        $name = Str::limit(trim(strip_tags((string) ($args['name'] ?? ''))), 100, '');
        $phone = preg_replace('/[^\d+]/', '', (string) ($args['phone'] ?? ''));
        $digits = strlen(preg_replace('/\D/', '', $phone));

        if (mb_strlen($name) < 2) {
            return ['error' => 'Ask the visitor for their name.'];
        }
        if ($digits < 10 || $digits > 13) {
            return ['error' => 'That phone number looks invalid. Ask the visitor to re-enter it with a 10-digit mobile number.'];
        }

        // Same number asked for a call in the last day: don't create a duplicate lead.
        $existing = PropertyInquiry::where('intent', 'chatbot')->where('phone', $phone)
            ->where('created_at', '>=', now()->subDay())->exists();
        if ($existing) {
            return ['saved' => true, 'note' => 'This number already has a pending callback request.'];
        }

        $key = 'chatbot-callback:' . $ip;
        if (RateLimiter::tooManyAttempts($key, config('chatbot.callbacks_per_ip_per_hour'))) {
            return ['error' => 'Too many callback requests from this visitor. Ask them to use the contact page instead.'];
        }
        RateLimiter::hit($key, 3600);

        $property = !empty($args['property_id'])
            ? Property::where('is_active', true)->find((int) $args['property_id'])
            : null;

        PropertyInquiry::create([
            'property_id' => $property?->id ?? 0,
            'property_title' => $property?->title,
            'name' => $name,
            'email' => null,
            'phone' => $phone,
            'message' => Str::limit(trim(strip_tags((string) ($args['note'] ?? ''))), 1000, '') ?: null,
            'intent' => 'chatbot',
            'source' => 'Chatbot',
            'terms_accepted' => false,
        ]);

        return ['saved' => true];
    }

    private function summary(Property $p, ?array $range): array
    {
        $bhk = collect([$p->bedrooms])->merge($p->details->pluck('bedrooms'))
            ->filter()->unique()->sort()->map(fn ($n) => $n . ' BHK')->values()->all();
        $types = $p->details->pluck('unit_type')->filter()->unique()->values()->all();

        return array_filter([
            'id' => $p->id,
            'name' => $p->title,
            'developer' => $p->developer_name,
            'category' => $p->category,
            'stage' => $p->project_status,
            'locality' => $p->location,
            'city' => $p->city,
            'price' => $range
                ? ($range[0] === $range[1] ? PriceParser::format($range[0]) : PriceParser::format($range[0]) . ' - ' . PriceParser::format($range[1]))
                : ($p->price ?: 'on request'),
            'configurations' => $bhk ?: $types,
            'possession' => $p->possession_date ? Carbon::parse($p->possession_date)->format('M Y') : null,
            'url' => route('property.show', $p->id),
        ], fn ($v) => $v !== null && $v !== '' && $v !== []);
    }

    /**
     * [min, max] rupees across the project price and its unit prices. With $bhk,
     * only the prices of that unit size (5 = 5+) count, falling back to the whole
     * project when no such unit has a readable price.
     */
    public function priceRange(Property $p, ?int $bhk = null): ?array
    {
        $prices = collect([[$p->bedrooms, $p->price]])
            ->merge($p->details->map(fn ($d) => [$d->bedrooms, $d->price]));

        $range = function ($prices) {
            $ranges = $prices->map(fn ($row) => PriceParser::range($row[1]))->filter();

            return $ranges->isEmpty() ? null : [$ranges->min(0), $ranges->max(1)];
        };

        if ($bhk) {
            // Unit rows are the real per-size prices; the project row is only used
            // for a size when the project lists no priced units at all.
            $units = $prices->slice(1)->filter(fn ($row) => PriceParser::range($row[1]));
            $rows = $units->isNotEmpty() ? $units : $prices->take(1);
            $sized = $range($rows->filter(fn ($row) => $row[0] && ($bhk >= 5 ? $row[0] >= 5 : (int) $row[0] === $bhk)));
            if ($sized) {
                return $sized;
            }
        }

        return $range($prices);
    }

    /** The same search on the site's own search page, for "see all results". */
    private function searchUrl(array $args): string
    {
        $slug = array_search($args['status'] ?? null, PropertyListingController::STATUS_SLUGS, true);

        return route('property.search', array_filter([
            // The search page matches locality exactly, so a partial one goes in the free-text box.
            'city' => $this->text($args, 'city'),
            'category' => ($c = $this->text($args, 'category')) ? strtolower($c) : null,
            'status' => $slug ?: null,
            'bhk' => !empty($args['bhk']) ? [min(5, (int) $args['bhk'])] : null,
            'budget_min' => $args['budget_min'] ?? null,
            'budget_max' => $args['budget_max'] ?? null,
            'search' => $this->text($args, 'query') ?? $this->text($args, 'locality'),
        ]));
    }

    private function text(array $args, string $key): ?string
    {
        $value = trim((string) ($args[$key] ?? ''));

        return $value !== '' ? Str::limit($value, 100, '') : null;
    }

    private function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }
}
