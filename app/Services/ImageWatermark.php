<?php

namespace App\Services;

/**
 * Stamps the navbar logo onto uploaded raster images (JPEG/PNG/WebP) in place.
 * Anything else (SVG, GIF, PDFs, unreadable files) is left untouched.
 */
class ImageWatermark
{
    private const LOGO = 'assets/logo/homax-logo-web.png';
    private const WIDTH_RATIO = 0.22;   // watermark width relative to image width
    private const MIN_WIDTH = 90;
    private const MARGIN_RATIO = 0.02;
    private const OPACITY = 70;         // 0-100

    public static function apply(string $file): void
    {
        $logoPath = public_path(self::LOGO);
        if (!function_exists('imagecreatefrompng') || !is_file($file) || !is_file($logoPath)) {
            return;
        }

        $info = @getimagesize($file);
        if (!$info) {
            return;
        }

        $type = $info[2];
        $loaders = [
            IMAGETYPE_JPEG => 'imagecreatefromjpeg',
            IMAGETYPE_PNG => 'imagecreatefrompng',
            IMAGETYPE_WEBP => 'imagecreatefromwebp',
        ];
        if (!isset($loaders[$type]) || !function_exists($loaders[$type])) {
            return;
        }

        $img = @$loaders[$type]($file);
        $logo = @imagecreatefrompng($logoPath);
        if (!$img || !$logo) {
            return;
        }

        $w = imagesx($img);
        $h = imagesy($img);
        $lw = imagesx($logo);
        $lh = imagesy($logo);

        $targetW = (int) max(self::MIN_WIDTH, round($w * self::WIDTH_RATIO));
        $targetW = min($targetW, $w);
        $targetH = (int) round($lh * $targetW / $lw);

        $scaled = imagecreatetruecolor($targetW, $targetH);
        imagealphablending($scaled, false);
        imagesavealpha($scaled, true);
        imagefill($scaled, 0, 0, imagecolorallocatealpha($scaled, 0, 0, 0, 127));
        imagecopyresampled($scaled, $logo, 0, 0, 0, 0, $targetW, $targetH, $lw, $lh);

        // Apply opacity per pixel so the logo's own transparency is preserved.
        imagealphablending($scaled, false);
        for ($y = 0; $y < $targetH; $y++) {
            for ($x = 0; $x < $targetW; $x++) {
                $c = imagecolorat($scaled, $x, $y);
                $a = ($c >> 24) & 0x7F;
                $newA = (int) (127 - (127 - $a) * self::OPACITY / 100);
                imagesetpixel($scaled, $x, $y, ($c & 0xFFFFFF) | ($newA << 24));
            }
        }

        $margin = (int) round($w * self::MARGIN_RATIO);
        $dx = max(0, $w - $targetW - $margin);
        $dy = max(0, $h - $targetH - $margin);

        imagealphablending($img, true);
        imagecopy($img, $scaled, $dx, $dy, 0, 0, $targetW, $targetH);

        match ($type) {
            IMAGETYPE_JPEG => imagejpeg($img, $file, 90),
            IMAGETYPE_PNG => (function () use ($img, $file) {
                imagesavealpha($img, true);
                imagepng($img, $file);
            })(),
            IMAGETYPE_WEBP => imagewebp($img, $file, 90),
        };
    }
}
