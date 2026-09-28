<?php

namespace App\Services\Chatbot;

use App\Http\Controllers\PropertyListingController;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * The widget's drill-down suggestion menu, built from the live listings so every
 * chip leads to at least one project. A node is either a branch
 * {label, prompt, children} or a leaf {label, send}, where `send` is the message
 * the chip posts. Leaves are phrased so the offline assistant understands them too.
 */
class Suggestions
{
    /** [label, min rupees, max rupees, phrase used in the message]. */
    private const BUDGETS = [
        ['Under 50 Lakh', 0, 5_000_000, 'under 50 lakh'],
        ['50 Lakh - 1 Cr', 5_000_000, 10_000_000, 'between 50 lakh and 1 cr'],
        ['1 Cr - 2 Cr', 10_000_000, 20_000_000, 'between 1 cr and 2 cr'],
        ['Above 2 Cr', 20_000_000, PHP_INT_MAX, 'above 2 cr'],
    ];

    private const MAX_CITIES = 6;

    public function __construct(private PropertyTools $tools)
    {
    }

    public function forRequest(Request $request): array
    {
        try {
            $id = $request->routeIs('property.show') ? (int) $request->route('id') : 0;

            return $id ? $this->forProperty($id) : $this->general();
        } catch (\Throwable $e) {
            report($e);

            return ['children' => [$this->teamLeaf()]];
        }
    }

    public function general(): array
    {
        return Cache::remember('chatbot.suggestions.general', 600, fn () => ['children' => $this->generalChildren()]);
    }

    public function forProperty(int $id): array
    {
        return Cache::remember("chatbot.suggestions.property.{$id}", 600, function () use ($id) {
            $details = $this->tools->call('get_property_details', ['property_id' => $id], '');
            if (isset($details['error'])) {
                return ['children' => $this->generalChildren()];
            }

            $children = [['label' => 'About this project', 'send' => 'Tell me about this project']];
            if (!empty($details['units']) || ($details['price'] ?? 'on request') !== 'on request') {
                $children[] = ['label' => 'Prices & unit sizes', 'send' => 'What are the unit sizes and prices here?'];
            }
            if (!empty($details['nearby'])) {
                $children[] = ['label' => "What's nearby", 'send' => 'What is nearby this project?'];
            }
            if ($others = $this->generalChildren(withTeam: false)) {
                $children[] = ['label' => 'Explore other projects', 'prompt' => 'What are you looking for?', 'children' => $others];
            }
            $children[] = $this->teamLeaf();

            return ['children' => $children];
        });
    }

    private function generalChildren(bool $withTeam = true): array
    {
        $projects = Property::where('is_active', true)->with('details')->get()
            ->map(function (Property $p) {
                $bhk = collect([$p->bedrooms])->merge($p->details->pluck('bedrooms'))
                    ->filter()->map(fn ($n) => min(5, (int) $n))->unique()->sort()->values()->all();

                return [
                    'city' => trim((string) $p->city),
                    'category' => ucfirst(strtolower(trim((string) $p->category))),
                    'stage' => $this->stage($p),
                    'bhk' => $bhk,
                    'price' => $this->tools->priceRange($p),
                    // Same rule the search uses: a BHK's budget is that unit size's price.
                    'priceByBhk' => collect($bhk)->mapWithKeys(fn ($n) => [$n => $this->tools->priceRange($p, $n)])->all(),
                ];
            });

        $children = [];

        $homes = $projects->where('category', 'Residential');
        if ($homes->isNotEmpty()) {
            $children[] = [
                'label' => 'Find a home',
                'prompt' => 'Which city?',
                'children' => $this->cityLevel($homes, fn ($inCity, $city) => $this->bhkLevel($inCity, $city)),
            ];
        }

        $commercial = $projects->where('category', 'Commercial');
        if ($commercial->isNotEmpty()) {
            $children[] = [
                'label' => 'Commercial spaces',
                'prompt' => 'Which city?',
                'children' => $this->cityLevel($commercial, fn ($inCity, $city) => null, fn ($city) => 'Commercial projects' . ($city ? " in {$city}" : '')),
            ];
        }

        $stages = $projects->pluck('stage')->filter()->countBy();
        if ($stages->isNotEmpty()) {
            $children[] = [
                'label' => 'By project stage',
                'prompt' => 'Which stage?',
                'children' => collect(PropertyListingController::STATUS_SLUGS)->values()
                    ->filter(fn ($s) => $stages->has($s))
                    ->map(fn ($s) => ['label' => "{$s} ({$stages[$s]})", 'send' => "{$s} projects"])->values()->all(),
            ];
        }

        if ($budgets = $this->budgetLevel($projects, fn ($phrase) => 'Projects ' . $phrase)) {
            $children[] = ['label' => 'By budget', 'prompt' => 'What budget?', 'children' => $budgets];
        }

        if ($withTeam) {
            $children[] = $this->teamLeaf();
        }

        return $children;
    }

    /**
     * One chip per city (most projects first) plus "Any city". $next builds the
     * city's sub-menu; when it returns null the chip sends $leaf($city) directly.
     */
    private function cityLevel(Collection $projects, callable $next, ?callable $leaf = null): array
    {
        $cities = $projects->pluck('city')->filter()->countBy()->sortDesc()->take(self::MAX_CITIES);

        $node = function (string $label, ?string $city, Collection $inCity) use ($next, $leaf) {
            $sub = $next($inCity, $city);

            return $sub
                ? ['label' => $label, 'prompt' => 'What size?', 'children' => $sub]
                : ['label' => $label, 'send' => $leaf($city)];
        };

        $level = $cities->map(fn ($n, $city) => $node("{$city} ({$n})", $city, $projects->where('city', $city)))->values()->all();
        if ($cities->count() > 1) {
            $level[] = $node('Any city', null, $projects);
        }

        return $level;
    }

    /** BHK chips available among these homes, each opening the budgets that still match. */
    private function bhkLevel(Collection $homes, ?string $city): array
    {
        $where = $city ? " in {$city}" : '';
        $sizes = $homes->pluck('bhk')->flatten()->unique()->sort()->values();

        $node = function (string $label, string $what, Collection $matching, ?int $bhk = null) use ($where) {
            if ($bhk) {
                $matching = $matching->map(fn ($h) => ['price' => $h['priceByBhk'][$bhk] ?? $h['price']] + $h);
            }
            $budgets = $this->budgetLevel($matching, fn ($phrase) => "{$what}{$where} {$phrase}");

            // One budget band would be a pointless extra tap.
            return count($budgets) > 1
                ? ['label' => $label, 'prompt' => 'What budget?', 'children' => array_merge($budgets, [['label' => 'Any budget', 'send' => "{$what}{$where}"]])]
                : ['label' => $label, 'send' => "{$what}{$where}"];
        };

        $level = $sizes->map(fn ($n) => $node(
            $n >= 5 ? '5+ BHK' : "{$n} BHK",
            ($n >= 5 ? '5+' : $n) . ' BHK homes',
            $homes->filter(fn ($h) => in_array($n, $h['bhk'], true)),
            $n
        ))->all();
        $level[] = $node('Any size', 'Homes', $homes);

        return $level;
    }

    /** Budget bands that at least one of these projects falls into (price ranges overlap). */
    private function budgetLevel(Collection $projects, callable $send): array
    {
        $level = [];
        foreach (self::BUDGETS as [$label, $min, $max, $phrase]) {
            $n = $projects->filter(fn ($p) => $p['price'] && $p['price'][0] <= $max && $p['price'][1] >= $min)->count();
            if ($n > 0) {
                $level[] = ['label' => "{$label} ({$n})", 'send' => $send($phrase)];
            }
        }

        return $level;
    }

    /** The stage as the search filter names it (pre_launch_property counts as Pre-Launch). */
    private function stage(Property $p): ?string
    {
        foreach (PropertyListingController::STATUS_SLUGS as $stage) {
            if (strcasecmp(trim((string) $p->project_status), $stage) === 0) {
                return $stage;
            }
        }

        return $p->pre_launch_property ? 'Pre-Launch' : null;
    }

    private function teamLeaf(): array
    {
        return ['label' => 'Talk to our team', 'send' => "I'd like a callback"];
    }
}
