@extends('layout.layout')

@section('title', 'Careers | Homax Homes')
@section('description', 'Explore current job openings at Homax Homes and build your career in real estate.')
@section('keywords', 'careers, jobs, real estate jobs, join our team, Homax Homes')
@section('og_title', 'Careers at Homax Homes')
@section('og_description', 'Explore current job openings and build your career in real estate.')
@section('og_url', url()->current())
@section('og_type', 'website')
@section('twitter_card', 'summary_large_image')
@section('twitter_title', 'Careers at Homax Homes')
@section('twitter_description', 'Explore current job openings and build your career in real estate.')
@section('canonical', route('careers'))

@section('content')
<style>
    .job-card { transition: transform .3s, box-shadow .3s; }
    .job-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,128,0.1); }
</style>

@php
    $perks = [
        ['fa-chart-line', 'Career Growth', 'Structured career paths with regular training and mentorship programs to help you advance.'],
        ['fa-wallet', 'Competitive Compensation', 'Attractive salary packages with performance bonuses and commission structures.'],
        ['fa-users', 'Team Culture', 'Collaborative work environment with regular team-building activities and events.'],
    ];
    $filtered = $department !== '' || $type !== '';
@endphp

<div class="min-h-screen bg-gray-50">
    <!-- Hero -->
    <section class="bg-gradient-to-r from-[#000080] to-[#000066] py-20 text-white">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Careers at Homax Homes</h1>
            <p class="text-xl max-w-2xl mx-auto">Build your career with one of the fastest growing real estate companies in the region.</p>
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <a href="#open-positions" class="bg-white text-[#000080] font-bold py-3 px-8 rounded-lg hover:bg-gray-100 transition duration-300">
                    View Open Positions
                </a>
                <a href="#apply" class="border border-white text-white font-bold py-3 px-8 rounded-lg hover:bg-white/10 transition duration-300">
                    Apply Now
                </a>
            </div>
        </div>
    </section>

    <!-- Why join -->
    <section class="container mx-auto px-4 py-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Why Join Our Team?</h2>
            <p class="text-gray-600 max-w-3xl mx-auto">We offer more than just jobs - we offer careers with purpose, growth opportunities, and a supportive environment.</p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            @foreach ($perks as [$icon, $heading, $text])
                <div class="bg-white p-8 rounded-xl shadow-lg text-center">
                    <div class="bg-[#000080]/10 text-[#000080] w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas {{ $icon }} text-2xl" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">{{ $heading }}</h3>
                    <p class="text-gray-600">{{ $text }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Openings -->
    <section id="open-positions" class="bg-gray-100 py-16 scroll-mt-24">
        <div class="container mx-auto px-4">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Current Openings</h2>
                <p class="text-gray-600 max-w-3xl mx-auto">Explore our available positions and find the right fit for your skills and aspirations.</p>
            </div>

            @if ($allOpenings->count() > 1 && ($departments->count() > 1 || count($types) > 1))
                <form method="GET" action="{{ route('careers') }}#open-positions" class="max-w-4xl mx-auto mb-10 flex flex-col sm:flex-row gap-3 justify-center">
                    @if ($departments->count() > 1)
                        <select name="department" aria-label="Department" onchange="this.form.submit()"
                                class="px-4 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-primary">
                            <option value="">All departments</option>
                            @foreach ($departments as $d)
                                <option value="{{ $d }}" @selected($department === $d)>{{ $d }}</option>
                            @endforeach
                        </select>
                    @endif
                    @if (count($types) > 1)
                        <select name="type" aria-label="Job type" onchange="this.form.submit()"
                                class="px-4 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-primary">
                            <option value="">All job types</option>
                            @foreach ($types as $value => $label)
                                <option value="{{ $value }}" @selected($type === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    @endif
                    <noscript><button type="submit" class="px-4 py-2 bg-[#000080] text-white rounded-lg">Filter</button></noscript>
                    @if ($filtered)
                        <a href="{{ route('careers') }}#open-positions" class="px-4 py-2 text-[#000080] font-semibold hover:underline self-center">Clear filters</a>
                    @endif
                </form>
            @endif

            @if ($openings->isEmpty())
                <div class="max-w-2xl mx-auto bg-white rounded-xl shadow p-10 text-center">
                    <i class="fas fa-briefcase text-4xl text-[#000080]/40 mb-4" aria-hidden="true"></i>
                    <p class="text-gray-700 text-lg mb-2">
                        {{ $filtered ? 'No openings match these filters.' : 'There are no open positions right now.' }}
                    </p>
                    <p class="text-gray-500">We're always happy to hear from talented people - send us a general application below.</p>
                </div>
            @else
                <div class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                    @foreach ($openings as $job)
                        <article class="bg-white rounded-xl shadow-lg p-8 job-card flex flex-col">
                            <div class="flex items-start justify-between gap-4 mb-4">
                                <div class="min-w-0">
                                    <h3 class="text-xl font-bold text-gray-800">
                                        <a href="{{ $job->url }}" class="hover:text-[#000080]">{{ $job->title }}</a>
                                    </h3>
                                    @if ($job->department)
                                        <p class="text-[#000080] font-medium">{{ $job->department }}</p>
                                    @endif
                                </div>
                                <span class="shrink-0 bg-[#000080]/10 text-[#000080] px-3 py-1 rounded-full text-sm font-medium">{{ $job->type_label }}</span>
                            </div>
                            <div class="flex flex-wrap gap-x-5 gap-y-1 text-sm text-gray-500 mb-4">
                                @if ($job->location)<span><i class="fas fa-map-marker-alt mr-1" aria-hidden="true"></i>{{ $job->location }}</span>@endif
                                @if ($job->experience)<span><i class="fas fa-user-tie mr-1" aria-hidden="true"></i>{{ $job->experience }}</span>@endif
                                @if ($job->salary)<span><i class="fas fa-rupee-sign mr-1" aria-hidden="true"></i>{{ $job->salary }}</span>@endif
                            </div>
                            @if ($job->summary)
                                <p class="text-gray-600 mb-6">{{ $job->summary }}</p>
                            @endif
                            @if ($job->highlight_list)
                                <div class="flex flex-wrap gap-2 mb-6">
                                    @foreach ($job->highlight_list as $tag)
                                        <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            @endif
                            <div class="mt-auto">
                                <a href="{{ $job->url }}" class="text-[#000080] font-semibold hover:underline inline-flex items-center">
                                    View details &amp; apply <i class="fas fa-chevron-right ml-2 text-sm" aria-hidden="true"></i>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif

        </div>
    </section>

    <!-- Application form -->
    <section id="apply" class="py-16 scroll-mt-24">
        <div class="container mx-auto px-4 max-w-4xl">
            <div class="bg-white rounded-xl shadow-lg p-8 md:p-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">General Application</h2>
                <p class="text-gray-600 mb-8">Don't see a role that fits? Send us your CV and we'll get in touch when a suitable opening comes up. To apply for a specific job, open it from the list above.</p>
                @include('pages.careers.apply-form', ['job' => null])
            </div>
        </div>
    </section>
</div>
@endsection
