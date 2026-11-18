<?php

namespace App\Http\Controllers;

use App\Models\JobOpening;
use App\Support\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Admin > Careers: the job openings listed on /careers. */
class JobOpeningController extends Controller
{
    public function index()
    {
        return view('admin.careers.index', [
            'openings' => JobOpening::withCount('applications')->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.careers.form', ['opening' => new JobOpening(['is_active' => true, 'employment_type' => 'full-time'])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = JobOpening::uniqueSlug($request->input('slug') ?: $data['title']);
        $data['sort_order'] ??= (int) JobOpening::max('sort_order') + 1;

        JobOpening::create($data);

        return redirect()->route('admin.careers.index')->with('success', 'Job opening added.');
    }

    public function edit(JobOpening $opening)
    {
        return view('admin.careers.form', ['opening' => $opening]);
    }

    public function update(Request $request, JobOpening $opening)
    {
        $data = $this->validated($request);
        $data['slug'] = JobOpening::uniqueSlug($request->input('slug') ?: $opening->slug, $opening->id);
        $data['sort_order'] ??= $opening->sort_order;

        $opening->update($data);

        return redirect()->route('admin.careers.index')->with('success', 'Job opening updated.');
    }

    public function toggle(JobOpening $opening)
    {
        $opening->update(['is_active' => !$opening->is_active]);

        return back()->with('success', $opening->is_active ? 'Opening is now live.' : 'Opening hidden from the website.');
    }

    public function destroy(JobOpening $opening)
    {
        $opening->delete();

        return back()->with('success', 'Job opening deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:150',
            'slug' => 'nullable|string|max:200',
            'department' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:100',
            'employment_type' => ['required', Rule::in(array_keys(JobOpening::TYPES))],
            'experience' => 'nullable|string|max:100',
            'salary' => 'nullable|string|max:100',
            'highlights' => 'nullable|string|max:255',
            'summary' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:50000',
            'sort_order' => 'nullable|integer|min:0|max:9999',
        ]);

        unset($data['slug']);
        $data['description'] = HtmlSanitizer::clean($data['description'] ?? null) ?: null;
        $data['is_active'] = $request->boolean('is_active');
        if (!isset($data['sort_order'])) {
            unset($data['sort_order']);
        }

        return $data;
    }
}
