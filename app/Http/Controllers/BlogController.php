<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/** Public /blog pages. */
class BlogController extends Controller
{
    private const PER_PAGE = 9;

    public function index(Request $request)
    {
        $categories = BlogCategory::active()
            ->whereHas('posts', fn ($q) => $q->visible())
            ->get();

        $activeCategory = $request->filled('category')
            ? $categories->firstWhere('slug', $request->category)
            : null;
        $search = trim((string) $request->query('q', ''));
        $filtering = $activeCategory || $search !== '' || (int) $request->query('page', 1) > 1;

        $query = BlogPost::visible()->with('category')
            ->when($activeCategory, fn ($q) => $q->where('blog_category_id', $activeCategory->id))
            ->when($search !== '', function ($q) use ($search) {
                $term = '%' . addcslashes($search, '%_\\') . '%';
                $q->where(fn ($w) => $w->where('title', 'like', $term)->orWhere('excerpt', 'like', $term));
            });

        // The newest featured post leads the first, unfiltered page; the grid skips it.
        $featured = null;
        if (!$filtering) {
            $featured = (clone $query)->where('is_featured', true)->latest('published_at')->first();
        }

        $posts = $query
            ->when($featured, fn ($q) => $q->where('id', '!=', $featured->id))
            ->latest('published_at')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('pages.blog', compact('posts', 'featured', 'categories', 'activeCategory', 'search'));
    }

    public function show(string $slug)
    {
        $post = BlogPost::with('category')->where('slug', $slug)->firstOrFail();

        // Drafts and scheduled posts are 404 for visitors; a signed-in admin may preview them.
        $preview = !$post->isVisible();
        if ($preview && !Auth::guard('admin')->check()) {
            abort(404);
        }

        if (!$preview) {
            $post->increment('views');
        }

        $related = BlogPost::visible()->with('category')
            ->where('id', '!=', $post->id)
            ->orderByRaw('blog_category_id <=> ? desc', [$post->blog_category_id])
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('pages.blog-show', compact('post', 'related', 'preview'));
    }
}
