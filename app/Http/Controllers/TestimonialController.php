<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class TestimonialController extends Controller
{
    private const IMAGE_DIR = 'upload/testimonials';

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['sort_order'] = (int) Testimonial::max('sort_order') + 1;

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->storeImage($request->file('photo'));
        }

        Testimonial::create($data);

        return back()->with('success', 'Testimonial added.');
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $data = $this->validated($request);

        if ($request->hasFile('photo')) {
            $this->deleteImage($testimonial->photo);
            $data['photo'] = $this->storeImage($request->file('photo'));
        } elseif ($request->boolean('remove_photo')) {
            $this->deleteImage($testimonial->photo);
            $data['photo'] = null;
        }

        $testimonial->update($data);

        return back()->with('success', 'Testimonial updated.');
    }

    public function destroy(Testimonial $testimonial)
    {
        $this->deleteImage($testimonial->photo);
        $testimonial->delete();

        return back()->with('success', 'Testimonial deleted.');
    }

    private function validated(Request $request): array
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'subtitle' => 'nullable|string|max:100',
            'quote' => 'required|string|max:600',
            'rating' => 'required|integer|between:1,5',
            'caption' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only(['name', 'subtitle', 'quote', 'rating', 'caption']);
        $data['is_active'] = $request->boolean('is_active');
        if ($request->filled('sort_order')) {
            $data['sort_order'] = (int) $request->sort_order;
        }

        return $data;
    }

    private function storeImage(UploadedFile $image): string
    {
        $directory = public_path(self::IMAGE_DIR);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = time() . '_' . Str::random(8) . '.' . $image->guessExtension();
        $image->move($directory, $filename);

        return self::IMAGE_DIR . '/' . $filename;
    }

    /** Only files we uploaded are removed; seeded photos are external URLs. */
    private function deleteImage(?string $path): void
    {
        if ($path && !Str::startsWith($path, ['http://', 'https://']) && is_file(public_path($path))) {
            @unlink(public_path($path));
        }
    }
}
