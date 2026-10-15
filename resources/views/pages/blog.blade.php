@extends('layout.layout')

@section('title', 'Blog | Homax Homes')
@section('description', 'Real estate insights, buying guides and investment tips from Homax Homes.')

@section('content')
    <div class="bg-white text-gray-700 font-sans overflow-x-hidden">

        <!-- Hero -->
        <section class="relative py-28 bg-cover bg-center"
            style="background-image: url('https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1350&q=75')">
            <div class="absolute inset-0 bg-gradient-to-b from-[#000033]/80 via-[#000033]/70 to-[#000033]/85"></div>
            <div class="relative z-10 text-center px-4 sm:px-8 [text-shadow:0_2px_8px_rgba(0,0,0,.6)]">
                <h1 class="text-4xl md:text-6xl font-bold mb-6 text-white">
                    Homax <span class="text-accent">Blog</span>
                </h1>
                <p class="mb-8 text-lg md:text-xl max-w-2xl mx-auto text-white font-medium">
                    Real estate insights, buying guides and investment ideas to help you make smarter property decisions.
                </p>
                <nav aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-2 text-sm font-medium">
                        <li><a href="{{ route('home') }}" class="text-white hover:text-accent">Home</a></li>
                        <li class="text-gray-300" aria-hidden="true">/</li>
                        <li aria-current="page" class="text-accent">Blog</li>
                    </ol>
                </nav>
            </div>
        </section>

        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Search + category filter -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-12">
                    <div class="flex flex-wrap gap-2" role="group" aria-label="Filter by category">
                        @php
                            $chip = 'text-sm font-semibold px-4 py-2 rounded-full transition';
                            $on = 'bg-primary text-white';
                            $off = 'bg-white text-gray-700 border border-gray-200 hover:border-primary hover:text-primary';
                            $keep = $search !== '' ? ['q' => $search] : [];
                        @endphp
                        <a href="{{ route('blog', $keep) }}" class="{{ $chip }} {{ $activeCategory ? $off : $on }}">All</a>
                        @foreach ($categories as $category)
                            <a href="{{ route('blog', $keep + ['category' => $category->slug]) }}"
                                class="{{ $chip }} {{ $activeCategory?->id === $category->id ? $on : $off }}">{{ $category->name }}</a>
                        @endforeach
                    </div>
                    <form method="GET" action="{{ route('blog') }}" class="relative w-full md:w-80" role="search">
                        @if ($activeCategory)
                            <input type="hidden" name="category" value="{{ $activeCategory->slug }}">
                        @endif
                        <label for="blog-search" class="sr-only">Search articles</label>
                        <input id="blog-search" type="search" name="q" value="{{ $search }}" placeholder="Search articles..."
                            class="w-full pl-11 pr-4 py-3 rounded-full border border-gray-200 bg-white focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary">
                        <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"></path>
                        </svg>
                    </form>
                </div>

                @if ($featured)
                    <!-- Featured post -->
                    <article class="bg-white rounded-2xl shadow-md overflow-hidden grid lg:grid-cols-2 mb-12 transition hover:shadow-xl">
                        <a href="{{ $featured->url }}" class="block relative h-72 lg:h-full overflow-hidden group">
                            @if ($featured->image_url)
                                <img src="{{ $featured->image_url }}" alt="{{ $featured->featured_image_alt ?: $featured->title }}" loading="lazy" decoding="async"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-primary to-[#000033]"></div>
                            @endif
                            <span class="absolute top-4 left-4 bg-accent text-white text-xs font-semibold px-3 py-1 rounded-full">Featured</span>
                        </a>
                        <div class="p-8 lg:p-12 flex flex-col justify-center">
                            @if ($featured->category)
                                <span class="text-primary text-sm font-semibold uppercase tracking-wide mb-3">{{ $featured->category->name }}</span>
                            @endif
                            <h2 class="text-3xl font-bold text-gray-800 mb-4 leading-tight">
                                <a href="{{ $featured->url }}" class="hover:text-primary transition-colors">{{ $featured->title }}</a>
                            </h2>
                            <p class="text-gray-600 mb-6 text-lg">{{ $featured->excerpt }}</p>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500 mb-6">
                                <span>{{ $featured->author_name }}</span>
                                <span aria-hidden="true">•</span>
                                <time datetime="{{ $featured->published_at->toDateString() }}">{{ $featured->published_at->format('F j, Y') }}</time>
                                <span aria-hidden="true">•</span>
                                <span>{{ $featured->read_time }} min read</span>
                            </div>
                            <a href="{{ $featured->url }}" class="inline-flex items-center self-start bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-full font-semibold transition">
                                Read Article
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </article>
                @endif

                @if ($posts->isEmpty() && !$featured)
                    <p class="text-center text-gray-500 text-lg py-16">
                        @if ($search !== '' || $activeCategory)
                            No articles match your search.
                            <a href="{{ route('blog') }}" class="text-primary font-semibold hover:underline">Clear filters</a>
                        @else
                            No articles have been published yet. Please check back soon.
                        @endif
                    </p>
                @else
                    <!-- Post grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach ($posts as $post)
                            <article class="bg-white rounded-xl shadow-md overflow-hidden flex flex-col transition-all duration-500 hover:shadow-xl hover:-translate-y-2">
                                <a href="{{ $post->url }}" class="block relative h-56 overflow-hidden group">
                                    @if ($post->image_url)
                                        <img src="{{ $post->image_url }}" alt="{{ $post->featured_image_alt ?: $post->title }}" loading="lazy" decoding="async"
                                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-primary to-[#000033]"></div>
                                    @endif
                                    @if ($post->category)
                                        <span class="absolute top-4 left-4 bg-primary text-white text-xs font-semibold px-3 py-1 rounded-full">{{ $post->category->name }}</span>
                                    @endif
                                </a>
                                <div class="p-6 flex flex-col flex-1">
                                    <div class="flex items-center text-sm text-gray-500 mb-3 gap-3">
                                        <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('M j, Y') }}</time>
                                        <span aria-hidden="true">•</span>
                                        <span>{{ $post->read_time }} min read</span>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-800 mb-3 hover:text-primary transition-colors">
                                        <a href="{{ $post->url }}">{{ $post->title }}</a>
                                    </h3>
                                    <p class="text-gray-600 mb-4 flex-1">{{ $post->excerpt }}</p>
                                    <a href="{{ $post->url }}" class="text-primary hover:text-primary-dark font-medium inline-flex items-center transition-colors">
                                        Read More
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    @if ($posts->hasPages())
                        <div class="mt-12">{{ $posts->links() }}</div>
                    @endif
                @endif
            </div>
        </section>

        <!-- Newsletter CTA -->
        <section class="py-16 bg-[#000033]">
            <div class="max-w-3xl mx-auto px-4 text-center">
                <h2 class="text-3xl font-bold text-white mb-3">Never miss an update</h2>
                <p class="text-gray-300 mb-8">Get new articles and property alerts straight to your inbox.</p>
                <form class="flex flex-col sm:flex-row gap-3" onsubmit="return false">
                    <label for="blog-subscribe" class="sr-only">Email address</label>
                    <input id="blog-subscribe" type="email" placeholder="Your email address"
                        class="flex-1 p-3 rounded-md bg-[#25204F] text-white focus:outline-none focus:ring-2 focus:ring-accent">
                    <button type="submit" class="bg-accent hover:bg-accent-dark text-white font-semibold px-8 py-3 rounded-md transition">Subscribe</button>
                </form>
            </div>
        </section>
    </div>
@endsection
