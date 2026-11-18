@extends('layout.layout')

@section('title', $opening->title . ' | Careers | Homax Homes')
@section('description', $opening->summary ?: 'Apply for the ' . $opening->title . ' role at Homax Homes.')
@section('og_title', $opening->title . ' - Careers at Homax Homes')
@section('og_description', $opening->summary ?: 'Apply for the ' . $opening->title . ' role at Homax Homes.')
@section('og_url', url()->current())
@section('og_type', 'website')
@section('twitter_card', 'summary_large_image')
@section('twitter_title', $opening->title . ' - Careers at Homax Homes')
@section('canonical', $opening->url)

@section('content')
<style>
    /* Styles for the editor-written description (Tailwind's reset strips them from raw HTML). */
    .job-body > * + * { margin-top: 1rem; }
    .job-body h2 { font-size: 1.5rem; font-weight: 700; color: #1f2937; padding-top: .5rem; }
    .job-body h3 { font-size: 1.25rem; font-weight: 700; color: #1f2937; padding-top: .5rem; }
    .job-body h4 { font-size: 1.1rem; font-weight: 700; color: #1f2937; }
    .job-body a { color: #000080; text-decoration: underline; }
    .job-body ul { list-style: disc; padding-left: 1.5rem; }
    .job-body ol { list-style: decimal; padding-left: 1.5rem; }
    .job-body li + li { margin-top: .4rem; }
    .job-body img { max-width: 100%; height: auto; border-radius: .5rem; }
</style>

@php
    $facts = array_filter([
        ['fa-building', 'Department', $opening->department],
        ['fa-map-marker-alt', 'Location', $opening->location],
        ['fa-clock', 'Job type', $opening->type_label],
        ['fa-user-tie', 'Experience', $opening->experience],
        ['fa-rupee-sign', 'Salary', $opening->salary],
    ], fn ($f) => filled($f[2]));
@endphp

<div class="min-h-screen bg-gray-50">
    <section class="bg-gradient-to-r from-[#000080] to-[#000066] py-16 text-white">
        <div class="container mx-auto px-4 max-w-5xl">
            <nav aria-label="Breadcrumb" class="mb-5 text-sm">
                <ol class="flex flex-wrap items-center gap-2">
                    <li><a href="{{ route('home') }}" class="hover:underline">Home</a></li>
                    <li aria-hidden="true" class="text-white/60">/</li>
                    <li><a href="{{ route('careers') }}" class="hover:underline">Careers</a></li>
                    <li aria-hidden="true" class="text-white/60">/</li>
                    <li aria-current="page" class="text-white/80 truncate">{{ $opening->title }}</li>
                </ol>
            </nav>
            <h1 class="text-3xl md:text-5xl font-bold mb-4 break-words">{{ $opening->title }}</h1>
            <div class="flex flex-wrap gap-2">
                <span class="bg-white/15 px-3 py-1 rounded-full text-sm">{{ $opening->type_label }}</span>
                @if ($opening->department)<span class="bg-white/15 px-3 py-1 rounded-full text-sm">{{ $opening->department }}</span>@endif
                @if ($opening->location)<span class="bg-white/15 px-3 py-1 rounded-full text-sm"><i class="fas fa-map-marker-alt mr-1" aria-hidden="true"></i>{{ $opening->location }}</span>@endif
            </div>
        </div>
    </section>

    <div class="container mx-auto px-4 max-w-5xl py-12 grid lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8 min-w-0">
            <div class="bg-white rounded-xl shadow-lg p-6 md:p-10">
                @if ($opening->summary)
                    <p class="text-lg text-gray-700 mb-6">{{ $opening->summary }}</p>
                @endif
                @if ($opening->description)
                    <div class="job-body text-gray-700 leading-relaxed break-words">{!! $opening->description !!}</div>
                @endif
                @if ($opening->highlight_list)
                    <div class="flex flex-wrap gap-2 mt-8">
                        @foreach ($opening->highlight_list as $tag)
                            <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm">{{ $tag }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            <div id="apply" class="bg-white rounded-xl shadow-lg p-6 md:p-10 scroll-mt-24">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Apply for this role</h2>
                <p class="text-gray-600 mb-8">Fill out the form below and we'll get back to you soon.</p>
                @include('pages.careers.apply-form', ['job' => $opening])
            </div>
        </div>

        <aside class="space-y-8 lg:sticky lg:top-28 lg:self-start">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Job overview</h2>
                <dl class="space-y-4">
                    @foreach ($facts as [$icon, $label, $value])
                        <div class="flex gap-3">
                            <i class="fas {{ $icon }} text-[#000080] mt-1 w-5 text-center" aria-hidden="true"></i>
                            <div>
                                <dt class="text-sm text-gray-500">{{ $label }}</dt>
                                <dd class="font-medium text-gray-800">{{ $value }}</dd>
                            </div>
                        </div>
                    @endforeach
                </dl>
                <a href="#apply" class="mt-6 block text-center bg-[#000080] hover:bg-[#000066] text-white font-bold py-3 rounded-lg transition duration-300">Apply now</a>
            </div>

            @if ($others->isNotEmpty())
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Other openings</h2>
                    <ul class="space-y-3">
                        @foreach ($others as $job)
                            <li>
                                <a href="{{ $job->url }}" class="font-semibold text-[#000080] hover:underline">{{ $job->title }}</a>
                                <p class="text-sm text-gray-500">{{ collect([$job->department, $job->type_label])->filter()->join(' · ') }}</p>
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('careers') }}#open-positions" class="inline-block mt-4 text-sm font-semibold text-gray-600 hover:underline">All openings</a>
                </div>
            @endif
        </aside>
    </div>
</div>
@endsection
