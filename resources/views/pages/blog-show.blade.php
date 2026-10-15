@extends('layout.layout')

@section('title', ($post->meta_title ?: $post->title).' | Homax Homes')
@section('description', $post->meta_description ?: $post->excerpt)
@section('og_title', $post->meta_title ?: $post->title)
@section('og_description', $post->meta_description ?: $post->excerpt)
@if ($post->image_url)
    @section('og_image', $post->image_url)
@endif
@section('og_type', 'article')
@if ($preview)
    @section('robots', 'noindex')
@endif

@section('content')
    @php
        $shareUrl = urlencode(url()->current());
        $shareTitle = urlencode($post->title);
    @endphp
    <style>
        /* Styles for the editor-written body (Tailwind's reset strips them from raw HTML). */
        .article-body > * + * { margin-top: 1.5rem; }
        .article-body h2 { font-size: 1.75rem; line-height: 1.3; font-weight: 700; color: #1f2937; padding-top: .75rem; }
        .article-body h3 { font-size: 1.4rem; line-height: 1.35; font-weight: 700; color: #1f2937; padding-top: .5rem; }
        .article-body h4 { font-size: 1.15rem; font-weight: 700; color: #1f2937; }
        .article-body a { color: #000080; text-decoration: underline; }
        .article-body ul { list-style: disc; padding-left: 1.5rem; }
        .article-body ol { list-style: decimal; padding-left: 1.5rem; }
        .article-body li + li { margin-top: .5rem; }
        .article-body blockquote { background: #F7F6FF; border-left: 4px solid #000080; border-radius: 0 .75rem .75rem 0; padding: 1.25rem 1.5rem; font-style: italic; color: #000080; }
        .article-body img { max-width: 100%; height: auto; border-radius: .75rem; box-shadow: 0 4px 14px rgba(0,0,0,.12); margin-left: auto; margin-right: auto; }
        .article-body figcaption { margin-top: .75rem; text-align: center; font-size: .875rem; color: #6b7280; }
        .article-body hr { border-color: #e5e7eb; }
        .article-body table { width: 100%; border-collapse: collapse; }
        .article-body th, .article-body td { border: 1px solid #e5e7eb; padding: .5rem .75rem; text-align: left; }
        .article-body pre { background: #111827; color: #f9fafb; padding: 1rem; border-radius: .5rem; overflow-x: auto; }
    </style>
    @if ($preview)
        <div class="bg-yellow-100 text-yellow-900 text-center text-sm font-semibold py-2 px-4">
            Preview: this post is {{ strtolower($post->status_label) }} and not visible to the public.
        </div>
    @endif
    <div class="bg-white text-gray-700 font-sans overflow-x-clip">

        <!-- Article header -->
        <section class="relative bg-cover bg-center py-24 md:py-32 {{ $post->image_url ? '' : 'bg-primary' }}"
            @if ($post->image_url) style="background-image: url('{{ $post->image_url }}')" @endif>
            <div class="absolute inset-0 bg-gradient-to-b from-[#000033]/80 via-[#000033]/75 to-[#000033]/90"></div>
            <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 text-center [text-shadow:0_2px_8px_rgba(0,0,0,.6)]">
                <nav aria-label="Breadcrumb" class="mb-6">
                    <ol class="inline-flex flex-wrap justify-center items-center gap-2 text-sm font-medium">
                        <li><a href="{{ route('home') }}" class="text-white hover:text-accent">Home</a></li>
                        <li class="text-gray-300" aria-hidden="true">/</li>
                        <li><a href="{{ route('blog') }}" class="text-white hover:text-accent">Blog</a></li>
                        <li class="text-gray-300" aria-hidden="true">/</li>
                        <li aria-current="page" class="text-accent">{{ $post->category?->name ?? 'Article' }}</li>
                    </ol>
                </nav>
                @if ($post->category)
                    <span class="inline-block bg-accent text-white text-xs font-semibold px-3 py-1 rounded-full mb-5">{{ $post->category->name }}</span>
                @endif
                <h1 class="text-3xl md:text-5xl font-bold text-white leading-tight mb-6">{{ $post->title }}</h1>
                <div class="flex flex-wrap justify-center items-center gap-x-4 gap-y-2 text-sm text-white font-medium">
                    <span>By {{ $post->author_name }}</span>
                    <span aria-hidden="true">•</span>
                    <time datetime="{{ ($post->published_at ?? $post->created_at)->toDateString() }}">{{ ($post->published_at ?? $post->created_at)->format('F j, Y') }}</time>
                    <span aria-hidden="true">•</span>
                    <span>{{ $post->read_time }} min read</span>
                </div>
            </div>
        </section>

        <!-- Article body + sidebar -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-[1fr_320px] gap-12">

                <article class="bg-white rounded-2xl shadow-md p-6 sm:p-10 min-w-0">
                    <p class="text-xl text-gray-600 leading-relaxed mb-8 border-l-4 border-accent pl-5">{{ $post->excerpt }}</p>

                    {{-- Sanitized on save (App\Support\HtmlSanitizer), so safe to print unescaped. --}}
                    <div class="article-body text-lg leading-relaxed text-gray-700">{!! $post->body !!}</div>

                    <!-- Share -->
                    <div class="mt-12 pt-6 border-t border-gray-200 flex flex-wrap items-center gap-4">
                        <span class="font-semibold text-gray-800">Share this article:</span>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener noreferrer"
                            class="px-4 py-2 rounded-full bg-[#1877F2] text-white text-sm font-semibold hover:brightness-110 transition">Facebook</a>
                        <a href="https://wa.me/?text={{ $shareTitle }}%20{{ $shareUrl }}" target="_blank" rel="noopener noreferrer"
                            class="px-4 py-2 rounded-full bg-[#25D366] text-white text-sm font-semibold hover:brightness-110 transition">WhatsApp</a>
                        <button type="button" id="copy-link"
                            class="px-4 py-2 rounded-full border border-gray-300 text-gray-700 text-sm font-semibold hover:border-primary hover:text-primary transition">Copy link</button>
                    </div>

                    <!-- Author -->
                    <div class="mt-8 flex items-center gap-4 bg-gray-50 rounded-xl p-5">
                        <div class="w-14 h-14 rounded-full bg-primary text-white flex items-center justify-center text-xl font-bold shrink-0">H</div>
                        <div>
                            <p class="font-bold text-gray-800">{{ $post->author_name }}</p>
                            <p class="text-sm text-gray-500">Insights and guides from the Homax Homes team.</p>
                        </div>
                    </div>

                    <a href="{{ route('blog') }}" class="inline-flex items-center mt-8 text-primary font-semibold hover:text-primary-dark transition">
                        <svg class="w-4 h-4 mr-2 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        Back to all articles
                    </a>
                </article>

                <!-- Sidebar -->
                <aside class="space-y-8 lg:sticky lg:top-28 self-start">
                    <div class="bg-white rounded-2xl shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Related Articles</h3>
                        @if ($related->isEmpty())
                            <p class="text-sm text-gray-500">More articles are coming soon.</p>
                        @else
                        <ul class="space-y-5">
                            @foreach ($related as $item)
                                <li>
                                    <a href="{{ $item->url }}" class="flex gap-4 group">
                                        @if ($item->image_url)
                                            <img src="{{ $item->image_url }}" alt="" loading="lazy" decoding="async"
                                                class="w-20 h-20 rounded-lg object-cover shrink-0">
                                        @else
                                            <div class="w-20 h-20 rounded-lg bg-gradient-to-br from-primary to-[#000033] shrink-0"></div>
                                        @endif
                                        <div>
                                            <p class="font-semibold text-gray-800 group-hover:text-primary transition-colors leading-snug">{{ $item->title }}</p>
                                            <p class="text-xs text-gray-500 mt-1">{{ $item->published_at->format('M j, Y') }}</p>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>

                    <div class="bg-primary rounded-2xl p-6 text-white">
                        <h3 class="text-lg font-bold mb-2">Looking for your next property?</h3>
                        <p class="text-sm text-gray-200 mb-5">Browse our latest residential and commercial projects.</p>
                        <a href="{{ route('property.search') }}"
                            class="inline-block bg-accent hover:bg-accent-dark text-white font-semibold px-6 py-3 rounded-full transition">View Projects</a>
                    </div>
                </aside>
            </div>
        </section>
    </div>

    <script>
        (function () {
            var btn = document.getElementById('copy-link');
            if (!btn || !navigator.clipboard) return;
            btn.addEventListener('click', function () {
                navigator.clipboard.writeText(window.location.href).then(function () {
                    var t = btn.textContent;
                    btn.textContent = 'Copied!';
                    setTimeout(function () { btn.textContent = t; }, 1800);
                });
            });
        })();
    </script>
@endsection
