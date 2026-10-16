<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * Allow-list HTML cleaner for blog bodies written in the admin editor.
 * Drops every tag, attribute and URL scheme that is not explicitly allowed,
 * so a pasted <script>, onerror= handler or javascript: link never reaches the page.
 */
class HtmlSanitizer
{
    private const TAGS = [
        'p', 'br', 'h2', 'h3', 'h4', 'strong', 'b', 'em', 'i', 'u', 's', 'blockquote',
        'ul', 'ol', 'li', 'a', 'img', 'figure', 'figcaption', 'hr', 'span', 'div',
        'table', 'thead', 'tbody', 'tr', 'th', 'td', 'pre', 'code',
    ];

    private const ATTRS = [
        'a' => ['href', 'title', 'target', 'rel'],
        'img' => ['src', 'alt', 'title', 'width', 'height'],
    ];

    /** Tags removed together with everything inside them. */
    private const DROP_WITH_CONTENT = ['script', 'style', 'iframe', 'object', 'embed', 'form', 'noscript', 'template'];

    public static function clean(?string $html): string
    {
        $html = trim((string) $html);
        if ($html === '') {
            return '';
        }

        $doc = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8"><div id="root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $doc->getElementById('root');
        if (!$root) {
            return e(strip_tags($html));
        }

        self::walk($root);

        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }

        return trim($out);
    }

    private static function walk(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if (!$child instanceof DOMElement) {
                if ($child->nodeType === XML_COMMENT_NODE) {
                    $node->removeChild($child);
                }
                continue;
            }

            $tag = strtolower($child->tagName);

            if (in_array($tag, self::DROP_WITH_CONTENT, true)) {
                $node->removeChild($child);
                continue;
            }

            self::walk($child);

            if (!in_array($tag, self::TAGS, true)) {
                // Unknown wrapper: keep its (already cleaned) children, lose the tag.
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);
                continue;
            }

            self::cleanAttributes($child, $tag);
        }
    }

    private static function cleanAttributes(DOMElement $el, string $tag): void
    {
        $allowed = self::ATTRS[$tag] ?? [];

        foreach (iterator_to_array($el->attributes) as $attr) {
            $name = strtolower($attr->name);

            if (!in_array($name, $allowed, true)) {
                $el->removeAttribute($attr->name);
                continue;
            }

            if (in_array($name, ['href', 'src'], true) && !self::safeUrl($attr->value, $name === 'src')) {
                $el->removeAttribute($attr->name);
            }
        }

        if ($tag === 'a' && $el->getAttribute('target') === '_blank') {
            $el->setAttribute('rel', 'noopener noreferrer');
        }

        if ($tag === 'img' && !$el->hasAttribute('src')) {
            $el->parentNode?->removeChild($el);
        }
    }

    private static function safeUrl(string $url, bool $image): bool
    {
        $url = trim(preg_replace('/[\x00-\x20]+/', '', $url));

        if ($url === '' || str_starts_with($url, '/') || str_starts_with($url, '#')) {
            return !str_starts_with($url, '//');
        }

        $schemes = $image ? ['http', 'https'] : ['http', 'https', 'mailto', 'tel'];

        return (bool) preg_match('/^(' . implode('|', $schemes) . '):/i', $url);
    }
}
