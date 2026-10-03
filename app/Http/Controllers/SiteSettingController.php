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
            'testimonials' => \App\Models\Testimonial::orderBy('sort_order')->orderBy('id')->get(),
            'about' => \App\Models\SiteSetting::about(),
            'footerAbout' => \App\Models\SiteSetting::footerAbout(),
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

        return back()->with('success', 'Hero image updated.')->with('open', 'hero');
    }

    public function updateAbout(Request $request)
    {
        $data = $request->validate([
            'about.intro' => 'required|string|max:600',
            'about.points' => 'nullable|array|max:3',
            'about.points.*.title' => 'nullable|string|max:60',
            'about.points.*.text' => 'nullable|string|max:200',
            'about.stats' => 'nullable|array|max:3',
            'about.stats.*.value' => 'nullable|string|max:12',
            'about.stats.*.label' => 'nullable|string|max:40',
        ])['about'];

        \App\Models\SiteSetting::saveAbout($data);

        return back()->with('success', 'About section updated.')->with('open', 'about');
    }

    public function resetAbout()
    {
        \App\Models\SiteSetting::resetAbout();

        return back()->with('success', 'About section reset to the default text.')->with('open', 'about');
    }

    public function updateFooterAbout(Request $request)
    {
        $data = $request->validate([
            'footer.heading' => 'nullable|string|max:120',
            'footer.paragraphs' => 'nullable|array|max:4',
            'footer.paragraphs.*' => 'nullable|string|max:600',
            'footer.notices' => 'nullable|array|max:3',
            'footer.notices.*.title' => 'nullable|string|max:60',
            'footer.notices.*.text' => 'nullable|string|max:800',
        ])['footer'] ?? [];

        \App\Models\SiteSetting::saveFooterAbout($data);

        return back()->with('success', 'Footer text updated.')->with('open', 'footer');
    }

    public function resetFooterAbout()
    {
        \App\Models\SiteSetting::resetFooterAbout();

        return back()->with('success', 'Footer text reset to the default.')->with('open', 'footer');
    }

    public function reset()
    {
        $file = public_path(self::HERO_PATH);
        if (is_file($file)) {
            @unlink($file);
        }

        return back()->with('success', 'Hero image reset to default.')->with('open', 'hero');
    }
}
