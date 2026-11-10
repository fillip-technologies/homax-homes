<?php

namespace App\Http\Controllers;

use App\Models\AboutMember;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class AboutMemberController extends Controller
{
    private const IMAGE_DIR = 'upload/about_members';

    /** Public About Us page. */
    public function show()
    {
        return view('pages.about', [
            'about' => SiteSetting::footerAbout(),
            'members' => AboutMember::visible()->get(),
        ]);
    }

    public function index()
    {
        return view('admin.about_members', [
            'about' => SiteSetting::footerAbout(),
            'members' => AboutMember::orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['sort_order'] = $request->filled('sort_order') ? (int) $request->sort_order : (int) AboutMember::max('sort_order') + 1;

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->storeImage($request->file('photo'));
        }

        AboutMember::create($data);

        return back()->with('success', 'Person added.');
    }

    public function update(Request $request, AboutMember $member)
    {
        $data = $this->validated($request);
        if ($request->filled('sort_order')) {
            $data['sort_order'] = (int) $request->sort_order;
        }

        if ($request->hasFile('photo')) {
            $this->deleteImage($member->photo);
            $data['photo'] = $this->storeImage($request->file('photo'));
        } elseif ($request->boolean('remove_photo')) {
            $this->deleteImage($member->photo);
            $data['photo'] = null;
        }

        $member->update($data);

        return back()->with('success', 'Person updated.');
    }

    public function destroy(AboutMember $member)
    {
        $this->deleteImage($member->photo);
        $member->delete();

        return back()->with('success', 'Person deleted.');
    }

    private function validated(Request $request): array
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'role' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only(['name', 'role', 'description']);
        $data['is_active'] = $request->boolean('is_active');

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

    private function deleteImage(?string $path): void
    {
        if ($path && !Str::startsWith($path, ['http://', 'https://']) && is_file(public_path($path))) {
            @unlink(public_path($path));
        }
    }
}
