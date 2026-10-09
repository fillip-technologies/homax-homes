<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Throwable;

class SiteSettingController extends Controller
{
    private const HERO_MAX_WIDTH = 1920;

    public function edit()
    {
        $hero = \App\Models\SiteSetting::hero();

        return view('admin.site_settings', [
            'heroUrl' => $hero['url'],
            'isCustom' => $hero['custom'],
            'testimonials' => \App\Models\Testimonial::orderBy('sort_order')->orderBy('id')->get(),
            'about' => \App\Models\SiteSetting::about(),
            'footerAbout' => \App\Models\SiteSetting::footerAbout(),
        ]);
    }

    /** Public: streams the uploaded hero image (URL is versioned, so cache hard). */
    public function hero()
    {
        $file = \App\Models\SiteSetting::heroFile();

        if (!is_file($file) || !is_readable($file) || filesize($file) === 0) {
            return redirect(asset('assets/hero-section.webp'));
        }

        return response()->file($file, [
            'Content-Type' => 'image/webp',
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'hero_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120|dimensions:min_width=1200,min_height=500',
        ], [
            'hero_image.dimensions' => 'The image must be at least 1200 x 500 pixels.',
            'hero_image.uploaded' => 'The upload failed. The file may be larger than the server allows; try a smaller image.',
        ]);

        try {
            // Decoding a large photo needs far more RAM than the file size suggests.
            @ini_set('memory_limit', '512M');

            $source = @imagecreatefromstring(file_get_contents($request->file('hero_image')->getRealPath()));
            if (!$source) {
                throw new \RuntimeException('Unreadable image');
            }

            if (imagesx($source) > self::HERO_MAX_WIDTH) {
                $resized = imagescale($source, self::HERO_MAX_WIDTH);
                imagedestroy($source);
                if (!$resized) {
                    throw new \RuntimeException('Resize failed');
                }
                $source = $resized;
            }

            imagepalettetotruecolor($source);
            ob_start();
            $ok = imagewebp($source, null, 80);
            $webp = ob_get_clean();
            imagedestroy($source);

            if (!$ok || $webp === '' || $webp === false) {
                throw new \RuntimeException('WebP conversion failed');
            }

            \App\Models\SiteSetting::saveHero($webp);
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
        \App\Models\SiteSetting::resetHero();

        return back()->with('success', 'Hero image reset to default.')->with('open', 'hero');
    }
}
