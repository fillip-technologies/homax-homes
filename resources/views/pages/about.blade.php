@extends('layout.layout')

@section('title', 'About Us')
@section('description', 'Learn about Homax Homes and meet the people behind it.')
@section('canonical', url()->current())

@section('content')
@php
    $paragraphs = array_values(array_filter($about['paragraphs'], fn ($t) => $t !== ''));
    $odd = count($paragraphs) % 2 === 1;
@endphp

<style>
    /* Plain CSS: the Tailwind build only contains classes it has already seen. */
    @media (min-width: 768px) {
        .about-last-odd { grid-column: 1 / -1; width: calc(50% - 1rem); justify-self: center; }
    }
</style>

<section class="bg-[#000080] text-white py-20 text-center px-4">
    <h1 class="text-4xl md:text-5xl font-bold">About <span class="text-[#DAA520]">Us</span></h1>
</section>

<section class="container mx-auto px-6 py-16">
    @if ($about['heading'] !== '')
        <h2 class="text-2xl md:text-3xl font-semibold text-[#000080] mb-8">{{ $about['heading'] }}</h2>
    @endif

    @if ($paragraphs)
        {{-- Two columns, filled row by row; with an odd count the last paragraph sits alone in the middle. --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-gray-600 leading-relaxed">
            @foreach ($paragraphs as $paragraph)
                @if ($odd && $loop->last)
                    <p class="about-last-odd text-center">{{ $paragraph }}</p>
                @else
                    <p>{{ $paragraph }}</p>
                @endif
            @endforeach
        </div>
    @endif
</section>

@if ($members->isNotEmpty())
<section class="bg-[#F2F4FF] py-16">
    <div class="container mx-auto px-6">
        <h2 class="text-2xl md:text-3xl font-semibold text-[#000080] text-center mb-12">Meet the People Behind Homax Homes</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            @foreach ($members as $member)
                <div class="bg-white rounded-xl shadow-md p-8 text-center">
                    @if ($member->photo_url)
                        <img src="{{ $member->photo_url }}" alt="{{ $member->name }}" loading="lazy"
                            class="w-40 h-40 rounded-full object-cover mx-auto mb-5 border-4 border-[#DAA520]">
                    @else
                        <div class="w-40 h-40 rounded-full mx-auto mb-5 bg-[#000080] text-white text-5xl font-bold flex items-center justify-center border-4 border-[#DAA520]">{{ $member->initial }}</div>
                    @endif
                    <h3 class="text-xl font-semibold text-[#000080]">{{ $member->name }}</h3>
                    @if ($member->role)
                        <p class="text-[#DAA520] font-medium mb-3">{{ $member->role }}</p>
                    @endif
                    @if ($member->description)
                        <p class="text-gray-600 whitespace-pre-line">{{ $member->description }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
