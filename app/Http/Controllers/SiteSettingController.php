<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Throwable;

class SiteSettingController extends Controller
{
    private const HERO_PATH = 'upload/site/hero-section.webp';
    private const HERO_MAX_WIDTH = 1920;

    public function edit()
    {
        $file = public_path(self::HERO_PATH);
        $custom = is_file($file);

        return view('admin.site_settings', [
            'heroUrl' => $custom
                ? asset(self::HERO_PATH) . '?v=' . filemtime($file)
                : asset('assets/hero-section.webp'),
            'isCustom' => $custom,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'hero_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120|dimensions:min_width=1200,min_height=500',
        ], [
            'hero_image.dimensions' => 'The image must be at least 1200 x 500 pixels.',
        ]);

        try {
            $source = @imagecreatefromstring(file_get_contents($request->file('hero_image')->getRealPath()));
            if (!$source) {
                throw new \RuntimeException('Unreadable image');
            }

            $width = imagesx($source);
            if ($width > self::HERO_MAX_WIDTH) {
                $resized = imagescale($source, self::HERO_MAX_WIDTH);
                imagedestroy($source);
                $source = $resized;
            }

            $dir = dirname(public_path(self::HERO_PATH));
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            // Write to a temp file first so a failure never breaks the live image.
            $tmp = $dir . '/hero-section.tmp.webp';
            imagepalettetotruecolor($source);
            if (!imagewebp($source, $tmp, 80)) {
                throw new \RuntimeException('WebP conversion failed');
            }
            imagedestroy($source);

            rename($tmp, public_path(self::HERO_PATH));
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors(['hero_image' => 'Could not process this image. Please try another file.']);
        }

        return back()->with('success', 'Hero image updated.');
    }

    public function reset()
    {
        $file = public_path(self::HERO_PATH);
        if (is_file($file)) {
            @unlink($file);
        }

        return back()->with('success', 'Hero image reset to default.');
    }
}
