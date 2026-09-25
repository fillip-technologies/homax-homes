<?php

namespace App\Support;

/**
 * Turns the free-text prices admins type ("75 Lakh", "50L-70L", "1.25 Cr",
 * "1200000") into rupee amounts so they can be filtered and sorted.
 */
class PriceParser
{
    private const MULTIPLIERS = [
        'cr' => 10000000,
        'crore' => 10000000,
        'crores' => 10000000,
        'l' => 100000,
        'lac' => 100000,
        'lacs' => 100000,
        'lakh' => 100000,
        'lakhs' => 100000,
        'k' => 1000,
    ];

    /**
     * @return array{0: int, 1: int}|null [min, max] in rupees, or null when no number is found.
     */
    public static function range(?string $text): ?array
    {
        if ($text === null || trim($text) === '') {
            return null;
        }

        $text = str_replace(',', '', $text);
        if (!preg_match_all('/(\d+(?:\.\d+)?)\s*(crores?|cr|lakhs?|lacs?|l|k)?\b/i', $text, $matches, PREG_SET_ORDER)) {
            return null;
        }

        $values = [];
        $trailingUnit = null;
        // In "50-70 Lakh" only the last number carries the unit, so remember it for the earlier ones.
        foreach (array_reverse($matches) as $m) {
            $unit = strtolower($m[2] ?? '') ?: $trailingUnit;
            $trailingUnit = $unit;
            $values[] = (int) round((float) $m[1] * ($unit ? self::MULTIPLIERS[$unit] : 1));
        }

        return [min($values), max($values)];
    }
}
