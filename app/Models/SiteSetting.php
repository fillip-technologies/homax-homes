<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Throwable;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    private const ABOUT_KEY = 'about';
    private const FOOTER_KEY = 'footer_about';

    /** What the home page showed before this section became editable. */
    public const ABOUT_DEFAULTS = [
        'intro' => 'Homax Homes is a real estate company focused on thoughtfully planned homes and projects that support better living. Our approach is centered on clear project information, practical guidance, and quality spaces for homebuyers.',
        'points' => [
            ['title' => 'Project Information', 'text' => 'Clear details to help you understand each home and project'],
            ['title' => 'Helpful Guidance', 'text' => 'Support for comparing projects, layouts, and living needs'],
            ['title' => 'Thoughtful Support', 'text' => 'From project discovery to site visits, our team helps you move forward with clarity'],
        ],
        'stats' => [
            ['value' => '1K+', 'label' => 'Project Options'],
            ['value' => '25+', 'label' => 'Locations'],
            ['value' => '98%', 'label' => 'Interest'],
        ],
    ];

    /** The text block at the bottom of the footer ("About Homax Homes Commercial Real Estate" etc.). */
    public const FOOTER_DEFAULTS = [
        'heading' => 'About Homax Homes Commercial Real Estate',
        'paragraphs' => [
            'Homax Homes is the leading commercial real estate marketplace connecting business owners, investors, and real estate professionals with premium office spaces, retail locations, industrial properties, and land for sale or lease across the United States.',
            'Our comprehensive database features thousands of commercial listings with detailed property information, high-resolution images, virtual tours, and market analytics to help you make informed real estate decisions.',
            "Whether you're looking to buy, sell, or lease commercial property, Homax Homes provides the tools and resources you need. Our network of licensed commercial real estate brokers and agents are available to assist with your transaction.",
            'Homax Homes serves major markets including New York, Los Angeles, Chicago, Houston, Phoenix, Philadelphia, San Antonio, San Diego, Dallas, and San Jose, with expanding coverage nationwide.',
        ],
        'notices' => [
            ['title' => 'Legal Disclaimer', 'text' => 'The information provided on this website does not constitute legal, financial, or professional real estate advice. All property information is deemed reliable but not guaranteed and should be independently verified. Homax Homes does not guarantee the accuracy of listing information, including but not limited to square footage, pricing, or availability. All property measurements are approximate.'],
            ['title' => 'Equal Housing Opportunity', 'text' => 'Homax Homes is committed to compliance with all federal, state, and local fair housing laws. We provide equal professional service to all persons without regard to race, color, religion, sex, handicap, familial status, national origin, sexual orientation, or gender identity.'],
            ['title' => 'Licensing Information', 'text' => 'Homax Homes, Inc. is a licensed real estate brokerage in all 50 states (License #1234567). Brokerage services are provided by Homax Homes Realty Services, LLC. All agents listed on this site are licensed professionals.'],
        ],
    ];

    /** Footer text block; same fallback rules as about(). */
    public static function footerAbout(): array
    {
        try {
            $raw = static::where('key', self::FOOTER_KEY)->value('value');
        } catch (Throwable $e) {
            return self::FOOTER_DEFAULTS;
        }

        $saved = $raw ? json_decode($raw, true) : null;

        return is_array($saved) ? self::normalizeFooter($saved) : self::FOOTER_DEFAULTS;
    }

    public static function saveFooterAbout(array $data): void
    {
        static::updateOrCreate(
            ['key' => self::FOOTER_KEY],
            ['value' => json_encode(self::normalizeFooter($data), JSON_UNESCAPED_UNICODE)]
        );
    }

    public static function resetFooterAbout(): void
    {
        static::where('key', self::FOOTER_KEY)->delete();
    }

    private static function normalizeFooter(array $data): array
    {
        $paragraphs = array_values($data['paragraphs'] ?? []);
        $clean = [];
        for ($i = 0; $i < 4; $i++) {
            $clean[] = trim((string) ($paragraphs[$i] ?? ''));
        }

        return [
            'heading' => trim((string) ($data['heading'] ?? '')),
            'paragraphs' => $clean,
            'notices' => self::pad($data['notices'] ?? [], ['title', 'text'], 3),
        ];
    }

    /**
     * The About section content. Falls back to the defaults when nothing is saved or the
     * table does not exist yet, so the home page never breaks on a half-deployed database.
     */
    public static function about(): array
    {
        try {
            $raw = static::where('key', self::ABOUT_KEY)->value('value');
        } catch (Throwable $e) {
            return self::ABOUT_DEFAULTS;
        }

        $saved = $raw ? json_decode($raw, true) : null;
        if (!is_array($saved)) {
            return self::ABOUT_DEFAULTS;
        }

        return [
            'intro' => (string) ($saved['intro'] ?? self::ABOUT_DEFAULTS['intro']),
            'points' => self::pad($saved['points'] ?? [], ['title', 'text'], 3),
            'stats' => self::pad($saved['stats'] ?? [], ['value', 'label'], 3),
        ];
    }

    public static function saveAbout(array $data): void
    {
        static::updateOrCreate(
            ['key' => self::ABOUT_KEY],
            ['value' => json_encode([
                'intro' => $data['intro'],
                'points' => self::pad($data['points'] ?? [], ['title', 'text'], 3),
                'stats' => self::pad($data['stats'] ?? [], ['value', 'label'], 3),
            ], JSON_UNESCAPED_UNICODE)]
        );
    }

    public static function resetAbout(): void
    {
        static::where('key', self::ABOUT_KEY)->delete();
    }

    /** Exactly $count rows, each with exactly $fields as trimmed strings. */
    private static function pad(array $rows, array $fields, int $count): array
    {
        $out = [];
        $rows = array_values($rows);
        for ($i = 0; $i < $count; $i++) {
            $row = [];
            foreach ($fields as $field) {
                $row[$field] = trim((string) ($rows[$i][$field] ?? ''));
            }
            $out[] = $row;
        }

        return $out;
    }
}
