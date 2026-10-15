<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/** Admin > Blog > Categories. */
class BlogCategoryController extends Controller
{
    public function index()
    {
        $categories = BlogCategory::withCount('posts')->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.blog.categories', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = BlogCategory::uniqueSlug($data['name']);
        $data['sort_order'] ??= (int) BlogCategory::max('sort_order') + 1;

        BlogCategory::create($data);

        return back()->with('success', 'Category added.');
    }

    public function update(Request $request, BlogCategory $category): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = BlogCategory::uniqueSlug($data['name'], $category->id);
        $data['sort_order'] ??= $category->sort_order;

        $category->update($data);

        return back()->with('success', 'Category updated.');
    }

    public function destroy(BlogCategory $category): RedirectResponse
    {
        // Posts keep existing; the foreign key sets their category to none.
        $category->delete();

        return back()->with('success', 'Category deleted. Its posts are now uncategorised.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0|max:100000',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
