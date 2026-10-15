<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Support\HtmlSanitizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/** Admin > Blog > Posts. */
class BlogPostController extends Controller
{
    private const IMAGE_DIR = 'upload/blog';
    private const CONTENT_IMAGE_DIR = 'upload/blog/content';

    public function index(Request $request)
    {
        $posts = BlogPost::with('category')
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->q . '%';
                $q->where(fn ($w) => $w->where('title', 'like', $term)->orWhere('excerpt', 'like', $term));
            })
            ->when($request->filled('category'), fn ($q) => $q->where('blog_category_id', $request->category))
            ->when($request->filled('status'), function ($q) use ($request) {
                match ($request->status) {
                    'draft' => $q->where('status', 'draft'),
                    'published' => $q->visible(),
                    'scheduled' => $q->whereIn('status', ['published', 'scheduled'])->where('published_at', '>', now()),
                    default => null,
                };
            })
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        $categories = BlogCategory::orderBy('name')->get();

        return view('admin.blog.posts.index', compact('posts', 'categories'));
    }

    public function create()
    {
        return view('admin.blog.posts.form', [
            'post' => new BlogPost(['author_name' => 'Homax Editorial', 'status' => 'draft']),
            'categories' => BlogCategory::active()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data = array_merge($data, $this->derived($request, $data));

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $this->storeImage($request->file('featured_image'), self::IMAGE_DIR);
        }

        $post = new BlogPost($data);
        $post->slug = BlogPost::uniqueSlug($request->input('slug') ?: $data['title']);
        $post->created_by = Auth::guard('admin')->id();
        $post->save();

        return redirect()->route('admin.blog.posts.edit', $post)->with('success', 'Post saved.');
    }

    public function edit(BlogPost $post)
    {
        return view('admin.blog.posts.form', [
            'post' => $post,
            // Keep a deactivated category selectable on a post that already uses it.
            'categories' => BlogCategory::where('is_active', true)
                ->orWhere('id', $post->blog_category_id)
                ->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, BlogPost $post): RedirectResponse
    {
        $data = $this->validated($request);
        $data = array_merge($data, $this->derived($request, $data));

        if ($request->boolean('remove_image') && !$request->hasFile('featured_image')) {
            $this->deleteImage($post->featured_image);
            $data['featured_image'] = null;
        }

        if ($request->hasFile('featured_image')) {
            $this->deleteImage($post->featured_image);
            $data['featured_image'] = $this->storeImage($request->file('featured_image'), self::IMAGE_DIR);
        }

        $post->fill($data);
        $post->slug = BlogPost::uniqueSlug($request->input('slug') ?: $post->slug, $post->id);
        $post->save();

        return redirect()->route('admin.blog.posts.edit', $post)->with('success', 'Post updated.');
    }

    public function destroy(BlogPost $post): RedirectResponse
    {
        $post->delete();

        return redirect()->route('admin.blog.posts.index')->with('success', 'Post deleted.');
    }

    /** Flip a post between draft and published from the list. */
    public function togglePublish(BlogPost $post): RedirectResponse
    {
        if ($post->status === 'draft') {
            $post->published_at = $post->published_at && $post->published_at->isFuture() ? $post->published_at : now();
            $post->status = $post->published_at->isFuture() ? 'scheduled' : 'published';
        } else {
            $post->status = 'draft';
        }

        $post->save();

        return back()->with('success', $post->status === 'draft' ? 'Post moved to drafts.' : 'Post published.');
    }

    public function toggleFeatured(BlogPost $post): RedirectResponse
    {
        $post->is_featured = !$post->is_featured;
        $post->save();

        return back()->with('success', $post->is_featured ? 'Post marked as featured.' : 'Post is no longer featured.');
    }

    /** Inline image from the editor; returns the public URL to insert. */
    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate(['image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048']);

        $path = $this->storeImage($request->file('image'), self::CONTENT_IMAGE_DIR);

        return response()->json(['url' => asset($path)]);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:200',
            'excerpt' => 'required|string|max:500',
            'body' => 'required|string',
            'blog_category_id' => 'nullable|exists:blog_categories,id',
            'author_name' => 'required|string|max:100',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'is_featured' => 'boolean',
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'featured_image_alt' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
        ]);

        $data['body'] = HtmlSanitizer::clean($data['body']);

        // An editor full of empty tags is still an empty article.
        if (trim(strip_tags($data['body'])) === '' && !str_contains($data['body'], '<img')) {
            throw ValidationException::withMessages(['body' => 'The article body cannot be empty.']);
        }

        unset($data['featured_image']);

        return $data;
    }

    /** Fields worked out from the form rather than typed in. */
    private function derived(Request $request, array $data): array
    {
        $publishedAt = $request->filled('published_at') ? \Illuminate\Support\Carbon::parse($request->published_at) : null;

        if ($data['status'] === 'published') {
            $publishedAt ??= now();
            $status = $publishedAt->isFuture() ? 'scheduled' : 'published';
        } else {
            $status = 'draft';
        }

        return [
            'status' => $status,
            'published_at' => $publishedAt,
            'is_featured' => $request->boolean('is_featured'),
            'read_time' => BlogPost::readTimeFor($data['body']),
        ];
    }

    private function storeImage(UploadedFile $image, string $dir): string
    {
        $directory = public_path($dir);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = time() . '_' . Str::random(8) . '.' . $image->guessExtension();
        $image->move($directory, $filename);

        return $dir . '/' . $filename;
    }

    private function deleteImage(?string $path): void
    {
        if ($path && !Str::startsWith($path, ['http://', 'https://']) && is_file(public_path($path))) {
            @unlink(public_path($path));
        }
    }
}
