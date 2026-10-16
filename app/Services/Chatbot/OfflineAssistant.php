<?php

namespace App\Services\Chatbot;

use App\Models\BlogPost;
use App\Models\Property;
use App\Support\PriceParser;
use Illuminate\Support\Str;

/**
 * Answers without Gemini (no key, outage, quota spent). Picks filters out of the
 * visitor's message with simple rules and runs the same lookups the AI uses, so
 * the widget still gives real, live answers - just less conversational ones.
 */
class OfflineAssistant
{
    private const STATUS_WORDS = [
        'Ready to Move' => ['ready to move', 'ready-to-move', 'ready possession', 'ready'],
        'Early Possession' => ['early possession'],
        'Pre-Launch' => ['pre-launch', 'prelaunch', 'pre launch', 'new launch', 'newly launched'],
        'Upcoming' => ['upcoming', 'under construction', 'new project'],
    ];

    public function __construct(private PropertyTools $tools)
    {
    }

    public function reply(string $message, ?int $viewingPropertyId, string $ip): string
    {
        $text = mb_strtolower(trim($message));
        $c = config('chatbot.contact');

        if (preg_match('/\b(call ?back|call me|contact|phone|number|whats ?app|email|site visit|visit|talk|agent|office|address)\b/', $text)) {
            $addr = !empty($c['address']) ? "- Office: {$c['address']}\n" : '';
            return "Of course, our team will be happy to help you 😊\n\n"
                . $addr
                . "- Call {$c['phone']}\n"
                . "- WhatsApp {$c['whatsapp']}\n"
                . "- Email {$c['email']}\n\n"
                . 'Or leave your details and we will call you back: ' . route('contact');
        }

        // Advice and reading material comes from the blog, unless the message is clearly a property search.
        if (preg_match('/\b(blogs?|articles?|guides?|tips?|advice|resources?|insights?|read|learn|how to|checklist|trends?)\b/', $text)
            && !preg_match('/\b(bhk|rk|flats?|apartments?|villas?|projects?)\b/', $text)) {
            if ($reply = $this->blogReply($text, $ip)) {
                return $reply;
            }
        }

        if ($viewingPropertyId && preg_match('/\b(this|here|it|nearby|unit|units|size|sizes|amenit|price|rera|possession|detail)/', $text)) {
            $details = $this->tools->call('get_property_details', ['property_id' => $viewingPropertyId], $ip);
            if (!isset($details['error'])) {
                return $this->describe($details);
            }
        }

        if (preg_match('/\b(cities|city|locations?|areas?|where)\b/', $text) && !$this->filters($text)) {
            $cities = $this->tools->call('list_locations', [], $ip)['cities'] ?? [];
            if (!$cities) {
                return 'We have no projects listed right now. Please check back soon or contact us: ' . route('contact');
            }

            return "We currently have projects in these cities:\n\n" . collect($cities)
                ->map(fn ($city) => "- {$city['city']} ({$city['projects']} " . Str::plural('project', $city['projects']) . ')')
                ->implode("\n") . "\n\nTell me a city and what you are looking for, e.g. \"2 BHK in {$cities[0]['city']}\".";
        }

        $filters = $this->filters($text);

        if (!$filters) {
            if (preg_match('/^(hi|hello|hey|namaste|good (morning|afternoon|evening))\b/', $text)) {
                return $this->help(greet: true);
            }
            // Maybe a project, developer or locality name.
            $filters = ['query' => Str::limit(trim($message), 100, '')];
        }

        $result = $this->tools->call('search_properties', $filters, $ip);

        if (empty($result['projects'])) {
            return isset($filters['query']) && count($filters) === 1
                ? $this->help()
                : "Hmm, I couldn't find a project matching that.\n\nTry a different budget or area, or browse all projects: "
                    . route('property.search');
        }

        $lines = collect($result['projects'])->map(fn ($p) => '- ' . implode(', ', array_filter([
            $p['name'],
            implode(', ', array_filter([$p['locality'] ?? null, $p['city'] ?? null])) ?: null,
            $p['price'] ?? null,
            implode('/', $p['configurations'] ?? []) ?: null,
            $p['stage'] ?? null,
        ])) . "\n  " . $p['url']);

        $total = $result['total_matches'];
        $reply = ($total === 1 ? 'Good news, I found 1 project' : "Good news, I found {$total} projects") . " that match:\n\n" . $lines->implode("\n");
        if ($total > count($result['projects'])) {
            $reply .= "\n\nSee all of them here: " . $result['see_all_url'];
        }

        return $reply . "\n\nWant me to arrange a callback? Just call or WhatsApp {$c['phone']}.";
    }

    /** search_properties arguments recognised in the message. */
    private function filters(string $text): array
    {
        $filters = [];

        if (preg_match('/(\d)\s*\+?\s*(bhk|bed|bedroom|rk)/', $text, $m)) {
            $filters['bhk'] = (int) $m[1];
        }

        if (preg_match('/\b(commercial|office|shop|retail)\b/', $text)) {
            $filters['category'] = 'Commercial';
        } elseif (preg_match('/\b(residential|flat|flats|apartment|apartments|home|homes|house)\b/', $text)) {
            $filters['category'] = 'Residential';
        }

        foreach (self::STATUS_WORDS as $status => $words) {
            foreach ($words as $word) {
                // Word boundaries, so "already" is not "ready".
                if (preg_match('/\b' . preg_quote($word, '/') . '\b/', $text)) {
                    $filters['status'] = $status;
                    break 2;
                }
            }
        }

        $amount = '(\d+(?:\.\d+)?\s*(?:crores?|cr|lakhs?|lacs?|l|k)?)';
        if (preg_match("/between\s+{$amount}\s+(?:and|to|-)\s+{$amount}/", $text, $m)
            || preg_match("/{$amount}\s*(?:-|to)\s*{$amount}/", $text, $m)) {
            // "50 to 80 lakh": the unit on the second number applies to both.
            $range = PriceParser::range($m[1] . ' - ' . $m[2]);
            if ($range && $range[1] >= 100000) {
                [$filters['budget_min'], $filters['budget_max']] = $range;
            }
        } elseif (preg_match("/(?:under|below|less than|within|upto|up to|max|budget(?: of)?|around)\s*(?:rs\.?|inr|₹)?\s*{$amount}/", $text, $m)) {
            $range = PriceParser::range($m[1]);
            if ($range && $range[1] >= 100000) {
                $filters['budget_max'] = $range[1];
            }
        } elseif (preg_match("/(?:above|over|more than|min|minimum)\s*(?:rs\.?|inr|₹)?\s*{$amount}/", $text, $m)) {
            $range = PriceParser::range($m[1]);
            if ($range && $range[0] >= 100000) {
                $filters['budget_min'] = $range[0];
            }
        }

        // Known cities and localities from the live listings.
        $places = Property::where('is_active', true)->get(['city', 'location']);
        foreach ($places->pluck('city')->filter()->unique() as $city) {
            if (str_contains($text, mb_strtolower(trim($city)))) {
                $filters['city'] = trim($city);
                break;
            }
        }
        foreach ($places->pluck('location')->filter()->unique() as $location) {
            // "Majiwada, Thane West": match on the first part.
            $area = trim(explode(',', $location)[0]);
            if (mb_strlen($area) >= 3 && str_contains($text, mb_strtolower($area))) {
                $filters['locality'] = $area;
                break;
            }
        }

        if (preg_match('/\b(cheap|cheapest|lowest|affordable)\b/', $text)) {
            $filters['sort'] = 'price_low_to_high';
        } elseif (preg_match('/\b(luxury|premium|expensive|costliest)\b/', $text)) {
            $filters['sort'] = 'price_high_to_low';
        }

        return $filters;
    }

    /** Latest or matching blog posts as a short chat answer; null when the blog is empty. */
    private function blogReply(string $text, string $ip): ?string
    {
        $stop = ['blog', 'blogs', 'article', 'articles', 'guide', 'guides', 'tips', 'tip', 'advice', 'resource', 'resources',
            'insight', 'insights', 'read', 'learn', 'how', 'show', 'give', 'about', 'latest', 'some', 'your', 'the', 'and', 'for', 'with', 'any', 'have', 'you', 'can', 'need', 'want', 'please', 'tell', 'trend', 'trends', 'checklist', 'our'];
        $words = array_values(array_diff(preg_split('/[^\p{L}\p{N}]+/u', $text, -1, PREG_SPLIT_NO_EMPTY), $stop));
        $query = implode(' ', $words);

        $result = $this->tools->call('search_blog_posts', $query !== '' ? ['query' => $query] : [], $ip);
        $matched = $query !== '' && !empty($result['posts']);
        if (!$matched) {
            // Nothing on that topic: fall back to the latest posts rather than a dead end.
            $result = $this->tools->call('search_blog_posts', [], $ip);
        }
        if (empty($result['posts'])) {
            return null;
        }

        $lines = collect($result['posts'])->map(fn ($p) => '- ' . $p['title'] . (isset($p['category']) ? " ({$p['category']})" : '')
            . "\n  " . $p['url'])->implode("\n");

        $intro = $matched
            ? 'I have a few articles on that - these might help 📚'
            : ($query !== '' ? "I don't have an article on that yet, but here are our latest reads 📚" : 'Here are our latest articles 📚');

        return $intro . "\n\n" . $lines . "\n\nMore on the blog: " . $result['see_all_url'];
    }

    private function describe(array $d): string
    {
        $lines = ["Here is a quick look at {$d['name']}" . (isset($d['developer']) ? ' by ' . $d['developer'] : '') . ":\n"];
        $lines[] = '- Location: ' . implode(', ', array_filter([$d['locality'] ?? null, $d['city'] ?? null]));
        $lines[] = '- Price: ' . $d['price'];
        foreach (array_slice($d['units'] ?? [], 0, 6) as $u) {
            $area = isset($u['carpet_area_sqft']) ? " ({$u['carpet_area_sqft']} sq ft carpet)" : '';
            $lines[] = '- ' . ($u['type'] ?? 'Unit') . $area . (isset($u['price']) ? ': ' . $u['price'] : '');
        }
        if (isset($d['stage'])) {
            $lines[] = '- Stage: ' . $d['stage'] . (isset($d['possession']) ? ', possession ' . $d['possession'] : '');
        }
        if (isset($d['rera_id'])) {
            $lines[] = '- RERA: ' . $d['rera_id'];
        }
        if (!empty($d['amenities'])) {
            $lines[] = '- Amenities: ' . implode(', ', array_slice($d['amenities'], 0, 8));
        }
        if (!empty($d['nearby'])) {
            $lines[] = '- Nearby: ' . implode('; ', array_slice($d['nearby'], 0, 5));
        }
        $lines[] = "\nYou can enquire on the project page: " . $d['url'];

        return implode("\n", $lines);
    }

    private function help(bool $greet = false): string
    {
        return ($greet ? "Hi there! 👋 I'm the Homax Homes assistant.\n\n" : '')
            . "I can help you find a project. Try asking:\n"
            . "- 2 BHK in Thane under 80 lakh\n"
            . "- Ready to move homes\n"
            . "- Commercial projects\n"
            . "- Which cities do you cover?\n\n"
            . 'Or browse all projects: ' . route('property.search');
    }
}
