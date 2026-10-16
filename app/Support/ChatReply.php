<?php

namespace App\Support;

/**
 * Turns one assistant answer into the short messages a person would send in a
 * chat. Blank lines are the author's message breaks; a bullet list stays in
 * one message; a long paragraph is cut on sentence boundaries.
 */
class ChatReply
{
    /** A paragraph longer than this is cut into sentence groups. */
    private const LONG = 240;

    /** More bubbles than this feels like spam, so the tail is merged into the last one. */
    private const MAX_MESSAGES = 4;

    /** @return list<string> */
    public static function split(string $text): array
    {
        $text = trim(str_replace("\r\n", "\n", $text));
        if ($text === '') {
            return [];
        }

        $messages = [];
        foreach (preg_split('/\n{2,}/', $text) as $block) {
            $block = trim($block);
            if ($block === '') {
                continue;
            }
            foreach (self::cut($block) as $piece) {
                $messages[] = $piece;
            }
        }

        if (count($messages) > self::MAX_MESSAGES) {
            $tail = array_splice($messages, self::MAX_MESSAGES - 1);
            $messages[] = implode("\n\n", $tail);
        }

        return $messages ?: [$text];
    }

    /** @return list<string> */
    private static function cut(string $block): array
    {
        $isList = (bool) preg_match('/^\s*-\s/m', $block);
        if ($isList || mb_strlen($block) <= self::LONG || str_contains($block, "\n")) {
            return [$block];
        }

        // Long plain paragraph: group whole sentences into chunks of roughly LONG characters.
        $sentences = preg_split('/(?<=[.!?])\s+(?=[A-Z0-9"\'(])/u', $block) ?: [$block];
        $chunks = [];
        $current = '';
        foreach ($sentences as $sentence) {
            if ($current !== '' && mb_strlen($current . ' ' . $sentence) > self::LONG) {
                $chunks[] = $current;
                $current = $sentence;
            } else {
                $current = $current === '' ? $sentence : $current . ' ' . $sentence;
            }
        }
        if ($current !== '') {
            $chunks[] = $current;
        }

        return $chunks;
    }
}
