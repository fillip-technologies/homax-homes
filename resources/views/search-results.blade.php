@extends('layout.layout')

@section('title', 'Search Results')
@section('head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
@endsection

@section('content')

    <body class="bg-white text-gray-800 font-sans">
        @php
            $wrap = fn($key) => \Illuminate\Support\Arr::wrap(request($key));
            $o = $filterOptions;

            // Active filter chips: label + URL that removes just that value.
            $chips = [];
            $urlWithout = function ($key, $value = null) {
                $q = request()->except(['page']);
                if ($value !== null && is_array($q[$key] ?? null)) {
                    $q[$key] = array_values(array_diff($q[$key], [$value]));
                } else {
                    unset($q[$key]);
                }
                return route('property.search', $q);
            };
            foreach (['search' => 'Search', 'city' => 'City', 'locality' => 'Locality', 'category' => 'Category', 'builder' => 'Builder'] as $k => $label) {
                if (filled(request($k))) {
                    $chips[] = [$label . ': ' . request($k), $urlWithout($k)];
                }
            }
            foreach (['furnishing' => 'Furnishing', 'amenities' => 'Amenity'] as $k => $label) {
                foreach ($wrap($k) as $v) {
                    if (filled($v)) {
                        $chips[] = [$v, $urlWithout($k, $v)];
                    }
                }
            }
            foreach ($wrap('bhk') as $v) {
                $chips[] = [$v . ($v >= 5 ? '+' : '') . ' BHK', $urlWithout('bhk', $v)];
            }
            foreach ($wrap('status') as $v) {
                if (isset($o['statuses'][strtolower($v)])) {
                    $chips[] = [$o['statuses'][strtolower($v)], $urlWithout('status', $v)];
                }
            }
            if (filled(request('budget_min')) || filled(request('budget_max'))) {
                $chips[] = [
                    'Budget: ' . ($o['budgets'][request('budget_min')] ?? 'Any') . ' - ' . ($o['budgets'][request('budget_max')] ?? 'Any'),
                    route('property.search', request()->except(['page', 'budget_min', 'budget_max'])),
                ];
            }
            if (filled(request('area_min')) || filled(request('area_max'))) {
                $chips[] = [
                    'Area: ' . (request('area_min') ?: '0') . ' - ' . (request('area_max') ?: 'Any') . ' sqft',
                    route('property.search', request()->except(['page', 'area_min', 'area_max'])),
                ];
            }
            if (filled(request('bathrooms'))) {
                $chips[] = [request('bathrooms') . '+ Bathrooms', $urlWithout('bathrooms')];
            }
            foreach (['rera' => 'RERA approved', 'verified' => 'Verified', 'video' => 'Has video'] as $k => $label) {
                if (request()->boolean($k)) {
                    $chips[] = [$label, $urlWithout($k)];
                }
            }
        @endphp

        <style>
            .sf-layout { display: grid; grid-template-columns: 1fr; gap: 24px; }
            @media (min-width: 1024px) { .sf-layout { grid-template-columns: 300px minmax(0, 1fr); align-items: start; } }
            .sf-panel { background: #fff; border: 1px solid #E7E7F0; border-radius: 12px; box-shadow: 0 10px 35px rgba(0,0,128,.10); }
            @media (min-width: 1024px) { .sf-panel { position: sticky; top: 16px; max-height: calc(100vh - 32px); display: flex; flex-direction: column; } }
            @media (max-width: 1023px) {
                .sf-panel { position: fixed; inset: 0 auto 0 0; width: min(88vw, 360px); z-index: 60; border-radius: 0 12px 12px 0;
                    transform: translateX(-105%); transition: transform .25s ease; display: flex; flex-direction: column; }
                .sf-panel.open { transform: none; }
            }
            .sf-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 55; }
            .sf-backdrop.open { display: block; }
            @media (min-width: 1024px) { .sf-backdrop, .sf-mobile-only { display: none !important; } }
            .sf-head { display: flex; justify-content: space-between; align-items: center; padding: 14px 16px; border-bottom: 1px solid #E7E7F0; }
            .sf-head h4 { font-weight: 700; color: #000080; font-size: 1.05rem; }
            /* Scrollbar and its track stay invisible until the panel is scrolled (.is-scrolling is set by JS). */
            .sf-body { overflow-y: auto; padding: 4px 16px; flex: 1; scrollbar-width: thin; scrollbar-color: transparent transparent; }
            .sf-body.is-scrolling { scrollbar-color: #D5D8EC #F6F6FB; }
            .sf-body::-webkit-scrollbar { width: 5px; }
            .sf-body::-webkit-scrollbar-track { background: transparent; border-radius: 999px; transition: background .25s; }
            .sf-body::-webkit-scrollbar-thumb { background: transparent; border-radius: 999px; transition: background .25s; }
            .sf-body.is-scrolling::-webkit-scrollbar-track { background: #F6F6FB; }
            .sf-body.is-scrolling::-webkit-scrollbar-thumb { background: #D5D8EC; }
            .sf-group { border-bottom: 1px solid #F0F0F6; padding: 12px 0; }
            .sf-group:last-child { border-bottom: 0; }
            .sf-group > summary { cursor: pointer; font-weight: 600; font-size: .9rem; color: #1f2937; list-style: none; display: flex; justify-content: space-between; }
            .sf-group > summary::-webkit-details-marker { display: none; }
            .sf-group > summary::after { content: '+'; color: #000080; font-weight: 700; }
            .sf-group[open] > summary::after { content: '\2212'; }
            .sf-group .sf-opts { margin-top: 10px; display: grid; gap: 8px; }
            .sf-opt { display: flex; align-items: center; gap: 8px; font-size: .875rem; color: #5F6472; cursor: pointer; }
            .sf-opt input { accent-color: #000080; width: 16px; height: 16px; }
            .sf-sub { font-size: .75rem; font-weight: 700; color: #000080; text-transform: uppercase; letter-spacing: .04em; margin-top: 4px; }
            .sf-input { width: 100%; border: 1px solid #E7E7F0; background: #fff; color: #5F6472; padding: 9px 12px; border-radius: 6px; font-size: .875rem; }
            select.sf-input { appearance: none; -webkit-appearance: none; cursor: pointer; padding-right: 34px;
                background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1.5l5 5 5-5' fill='none' stroke='%23000080' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") no-repeat right 12px center; }
            .sf-dd { position: relative; }
            .sf-dd-btn { display: flex; align-items: center; gap: 8px; background: #fff; border: 1px solid #E7E7F0; border-radius: 999px;
                padding: 9px 16px; font-size: .875rem; cursor: pointer; box-shadow: 0 2px 10px rgba(0,0,128,.06); transition: box-shadow .2s, border-color .2s; }
            .sf-dd-btn:hover, .sf-dd.open .sf-dd-btn { border-color: #DAA520; box-shadow: 0 4px 16px rgba(0,0,128,.12); }
            .sf-dd-label { color: #5F6472; }
            .sf-dd-value { color: #000080; font-weight: 700; }
            .sf-dd-caret { color: #000080; transition: transform .2s; }
            .sf-dd.open .sf-dd-caret { transform: rotate(180deg); }
            .sf-dd-menu { position: absolute; right: 0; top: calc(100% + 8px); min-width: 230px; margin: 0; padding: 6px; list-style: none;
                background: #fff; border: 1px solid #E7E7F0; border-radius: 14px; box-shadow: 0 14px 40px rgba(0,0,128,.16); z-index: 40;
                transform-origin: top right; animation: sfDdIn .16s ease; }
            .sf-dd-menu[hidden] { display: none; }
            @keyframes sfDdIn { from { opacity: 0; transform: translateY(-6px) scale(.98); } to { opacity: 1; transform: none; } }
            .sf-dd-menu li { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 9px 12px; border-radius: 9px;
                font-size: .875rem; color: #374151; cursor: pointer; }
            .sf-dd-menu li:hover, .sf-dd-menu li.focus { background: #FBF3DC; color: #000080; }
            .sf-dd-menu li.active { color: #000080; font-weight: 700; background: #F2F2FA; }
            .sf-dd-tick { color: #DAA520; opacity: 0; }
            .sf-dd-menu li.active .sf-dd-tick { opacity: 1; }
            .sf-input:focus { outline: 2px solid #DAA520; border-color: transparent; }
            .sf-row { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 10px; }
            .sf-pills { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
            .sf-pill input { position: absolute; opacity: 0; pointer-events: none; }
            .sf-pill span { display: inline-block; padding: 6px 14px; border: 1px solid #E7E7F0; border-radius: 999px; font-size: .85rem; color: #5F6472; cursor: pointer; }
            .sf-pill input:checked + span { background: #000080; border-color: #000080; color: #fff; }
            .sf-foot { display: flex; gap: 8px; padding: 12px 16px; border-top: 1px solid #E7E7F0; }
            .sf-btn { flex: 1; text-align: center; padding: 10px 14px; border-radius: 6px; font-weight: 600; font-size: .9rem; cursor: pointer; }
            .sf-btn-primary { background: #DAA520; color: #fff; border: 0; }
            .sf-btn-primary:hover { background: #B8860B; }
            .sf-btn-ghost { background: #fff; color: #000080; border: 1px solid #000080; }
            .sf-chip { display: inline-flex; align-items: center; gap: 6px; background: #eef; color: #000080; border-radius: 999px; padding: 4px 6px 4px 12px; font-size: .8rem; font-weight: 600; }
            .sf-chip a { width: 18px; height: 18px; line-height: 18px; text-align: center; border-radius: 50%; background: #000080; color: #fff; font-size: .7rem; }
            .sf-more { display: none; }
            .sf-more.open { display: grid; }
        </style>

        <section class="py-12">
            <div class="container mx-auto px-4 md:px-6">
                <div class="sf-layout">
                    <div class="sf-backdrop" id="sfBackdrop"></div>

                    <aside class="sf-panel" id="sfPanel" aria-label="Filters">
                        <form method="GET" action="{{ route('property.search') }}" id="sfForm" style="display:flex;flex-direction:column;min-height:0;flex:1;">
                            <div class="sf-head">
                                <h4>Filters</h4>
                                <button type="button" class="sf-mobile-only" id="sfClose" aria-label="Close filters"
                                    style="font-size:1.4rem;line-height:1;color:#000080;">&times;</button>
                            </div>
                            @if (request('sort'))
                                <input type="hidden" name="sort" value="{{ request('sort') }}">
                            @endif

                            <div class="sf-body">
                                <div class="sf-group" style="padding-top:12px;">
                                    <input type="text" name="search" class="sf-input" value="{{ request('search') }}"
                                        placeholder="Project, builder, locality">
                                </div>

                                <details class="sf-group" open>
                                    <summary>Location</summary>
                                    <div class="sf-opts">
                                        <select name="city" id="sfCity" class="sf-input">
                                            <option value="">All cities</option>
                                            @foreach ($o['cities'] as $c)
                                                <option value="{{ $c }}" @selected(request('city') == $c)>{{ $c }}</option>
                                            @endforeach
                                        </select>
                                        <select name="locality" id="sfLocality" class="sf-input">
                                            <option value="">All localities</option>
                                            @foreach ($o['localities'] as $l)
                                                <option value="{{ $l['name'] }}" data-city="{{ $l['city'] }}"
                                                    @selected(request('locality') == $l['name'])>{{ $l['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </details>

                                <details class="sf-group" open>
                                    <summary>Category</summary>
                                    <div class="sf-opts">
                                        <select name="category" class="sf-input">
                                            <option value="">Residential & Commercial</option>
                                            @foreach (['Residential', 'Commercial'] as $cat)
                                                <option value="{{ $cat }}" @selected(strtolower(request('category', '')) === strtolower($cat))>{{ $cat }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </details>

                                <details class="sf-group" open>
                                    <summary>Budget</summary>
                                    <div class="sf-row">
                                        <select name="budget_min" class="sf-input">
                                            <option value="">Min</option>
                                            @foreach ($o['budgets'] as $v => $label)
                                                <option value="{{ $v }}" @selected(request('budget_min') == $v)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <select name="budget_max" class="sf-input">
                                            <option value="">Max</option>
                                            @foreach ($o['budgets'] as $v => $label)
                                                <option value="{{ $v }}" @selected(request('budget_max') == $v)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </details>

                                <details class="sf-group" open>
                                    <summary>BHK</summary>
                                    <div class="sf-pills">
                                        @foreach ([1, 2, 3, 4, 5] as $n)
                                            <label class="sf-pill"><input type="checkbox" name="bhk[]" value="{{ $n }}"
                                                    @checked(in_array($n, array_map('intval', $wrap('bhk'))))><span>{{ $n }}{{ $n == 5 ? '+' : '' }} BHK</span></label>
                                        @endforeach
                                    </div>
                                </details>

                                <details class="sf-group" open>
                                    <summary>Possession Status</summary>
                                    <div class="sf-opts">
                                        @foreach ($o['statuses'] as $slug => $label)
                                            <label class="sf-opt"><input type="checkbox" name="status[]" value="{{ $slug }}"
                                                    @checked(in_array($slug, array_map('strtolower', $wrap('status'))))> {{ $label }}</label>
                                        @endforeach
                                    </div>
                                </details>

                                <details class="sf-group">
                                    <summary>Size (sq ft)</summary>
                                    <div class="sf-row">
                                        <input type="number" min="0" name="area_min" class="sf-input" placeholder="Min" value="{{ request('area_min') }}">
                                        <input type="number" min="0" name="area_max" class="sf-input" placeholder="Max" value="{{ request('area_max') }}">
                                    </div>
                                </details>

                                <details class="sf-group">
                                    <summary>Bathrooms</summary>
                                    <div class="sf-pills">
                                        @foreach ([1, 2, 3, 4] as $n)
                                            <label class="sf-pill"><input type="radio" name="bathrooms" value="{{ $n }}"
                                                    @checked(request('bathrooms') == $n)><span>{{ $n }}+</span></label>
                                        @endforeach
                                    </div>
                                </details>

                                <details class="sf-group">
                                    <summary>Furnishing</summary>
                                    <div class="sf-opts">
                                        @foreach ($o['furnishing'] as $f)
                                            <label class="sf-opt"><input type="checkbox" name="furnishing[]" value="{{ $f }}"
                                                    @checked(in_array($f, $wrap('furnishing')))> {{ $f }}</label>
                                        @endforeach
                                    </div>
                                </details>

                                @if (count($o['amenities']))
                                    <details class="sf-group">
                                        <summary>Amenities</summary>
                                        <div class="sf-opts">
                                            @foreach ($o['amenities'] as $i => $a)
                                                <label class="sf-opt {{ $i >= 6 && !in_array($a, $wrap('amenities')) ? 'sf-extra' : '' }}"
                                                    @if ($i >= 6 && !in_array($a, $wrap('amenities'))) style="display:none" @endif>
                                                    <input type="checkbox" name="amenities[]" value="{{ $a }}"
                                                        @checked(in_array($a, $wrap('amenities')))> {{ $a }}</label>
                                            @endforeach
                                            @if (count($o['amenities']) > 6)
                                                <button type="button" id="sfMoreAmenities"
                                                    style="text-align:left;color:#000080;font-weight:600;font-size:.85rem;">Show more</button>
                                            @endif
                                        </div>
                                    </details>
                                @endif

                                @if (count($o['builders']))
                                    <details class="sf-group">
                                        <summary>Builder</summary>
                                        <div class="sf-opts">
                                            <select name="builder" class="sf-input">
                                                <option value="">All builders</option>
                                                @foreach ($o['builders'] as $b)
                                                    <option value="{{ $b }}" @selected(request('builder') == $b)>{{ $b }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </details>
                                @endif

                                <details class="sf-group" open>
                                    <summary>More</summary>
                                    <div class="sf-opts">
                                        <label class="sf-opt"><input type="checkbox" name="rera" value="1" @checked(request()->boolean('rera'))> RERA approved</label>
                                        <label class="sf-opt"><input type="checkbox" name="verified" value="1" @checked(request()->boolean('verified'))> Verified listings</label>
                                        <label class="sf-opt"><input type="checkbox" name="video" value="1" @checked(request()->boolean('video'))> Has video</label>
                                    </div>
                                </details>
                            </div>

                            <div class="sf-foot">
                                <a href="{{ route('property.search') }}" class="sf-btn sf-btn-ghost">Clear all</a>
                                <button type="submit" class="sf-btn sf-btn-primary">Show {{ $properties->total() }} {{ \Illuminate\Support\Str::plural('result', $properties->total()) }}</button>
                            </div>
                        </form>
                    </aside>

                    <div>
                        <div class="flex md:flex-row flex-col justify-between md:items-center gap-3 mb-4">
                            <h3 class="text-2xl font-semibold">
                                Search Results:
                                <span class="text-[#000080]">{{ $properties->total() }} properties found</span>
                            </h3>
                            <div class="flex items-center gap-2">
                                <button type="button" id="sfOpen" class="sf-mobile-only sf-btn sf-btn-ghost" style="flex:none;">
                                    Filters{{ count($chips) ? ' (' . count($chips) . ')' : '' }}
                                </button>
                                @php
                                    $sortOptions = ['newest' => 'Newest First', 'oldest' => 'Oldest First', 'price_asc' => 'Price: Low to High', 'price_desc' => 'Price: High to Low', 'area_asc' => 'Area: Small to Large', 'area_desc' => 'Area: Large to Small', 'bedrooms_asc' => 'Bedrooms: Few to Many', 'bedrooms_desc' => 'Bedrooms: Many to Few'];
                                    $currentSort = array_key_exists(request('sort', 'newest'), $sortOptions) ? request('sort', 'newest') : 'newest';
                                @endphp
                                <div class="sf-dd" id="sortDd">
                                    <button type="button" class="sf-dd-btn" id="sortBtn" aria-haspopup="listbox" aria-expanded="false">
                                        <span class="sf-dd-label">Sort by</span>
                                        <span class="sf-dd-value">{{ $sortOptions[$currentSort] }}</span>
                                        <svg class="sf-dd-caret" width="12" height="8" viewBox="0 0 12 8" fill="none"><path d="M1 1.5l5 5 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                    <ul class="sf-dd-menu" id="sortMenu" role="listbox" hidden>
                                        @foreach ($sortOptions as $val => $label)
                                            <li role="option" data-value="{{ $val }}" aria-selected="{{ $currentSort == $val ? 'true' : 'false' }}"
                                                class="{{ $currentSort == $val ? 'active' : '' }}">
                                                <span>{{ $label }}</span>
                                                <svg class="sf-dd-tick" width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M5 12.5l4.5 4.5L19 7.5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>

                        @if (count($chips))
                            <div class="flex flex-wrap items-center gap-2 mb-4">
                                @foreach ($chips as [$label, $url])
                                    <span class="sf-chip">{{ $label }}<a href="{{ $url }}" aria-label="Remove filter">&times;</a></span>
                                @endforeach
                                <a href="{{ route('property.search') }}" class="text-sm font-semibold text-[#000080] underline">Clear all</a>
                            </div>
                        @endif

                <div class="flex flex-col space-y-6">
                    @foreach ($properties as $property)
                        <!-- Property Card -->
                        <div
                            class="w-full bg-white rounded-lg overflow-hidden shadow-[0_4px_20px_rgba(0,0,128,0.15)] text-gray-800 flex flex-col md:flex-row">
                            <!-- Image Section -->
                            <div class="md:w-1/3 relative overflow-hidden">
                                <div class="carousel-images relative w-full h-64 md:h-full">
                                    @if ($property->main_image)
                                        <img src="{{ asset($property->main_image) }}"
                                            class="absolute inset-0 w-full h-full object-cover opacity-100 transition-opacity duration-1000"
                                            alt="{{ $property->title }}" />
                                    @endif
                                    @foreach ($property->images as $image)
                                        <img src="{{ asset($image->image_path) }}"
                                            class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-1000"
                                            alt="{{ $property->title }}" />
                                    @endforeach
                                </div>

                                <!-- Badge -->
                                @if ($property->project_status)
                                    <div
                                        class="absolute top-0 left-0 bg-[#000080] text-white text-xs font-bold px-2 py-1 rounded-br-lg z-10">
                                        {{ $property->project_status }}
                                    </div>
                                @endif
                            </div>

                            <!-- Content Section -->
                            <div class="md:w-2/3 flex flex-col">
                                <!-- Property Title -->
                                <div class="px-4 pt-4">
                                    <h2 class="font-bold text-lg">
                                        {{ $property->title }}
                                    </h2>
                                    <p class="flex items-center text-[#000080] font-semibold">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1 text-[#000080]"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 11c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 22s8-4.5 8-11a8 8 0 10-16 0c0 6.5 8 11 8 11z" />
                                        </svg>
                                        {{ $property->address }}, {{ $property->city }} {{ $property->zip_code }}
                                    </p>
                                </div>

                                <!-- Property Details Grid -->
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2 px-4 py-3 text-sm">
                                    <div>
                                        <p class="text-gray-500">SUPER AREA</p>
                                        <p class="font-medium">
                                            {{ $property->super_area ? $property->super_area . ' sqft' : 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">CATEGORY</p>
                                        <p class="font-medium">{{ $property->category ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">STATUS</p>
                                        <p class="font-medium">{{ $property->project_status ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">FURNISHING</p>
                                        <p class="font-medium">{{ $property->furnishing ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">BATHROOM</p>
                                        <p class="font-medium">{{ $property->bathrooms ?? 'N/A' }}</p>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="px-4 py-2 text-sm border-t border-gray-200 flex-grow">
                                    <p>{{ Str::limit($property->description, 200) }}</p>
                                </div>

                                <!-- Price Section -->
                                <div class="px-4 py-3 bg-gray-50 flex justify-between items-center">
                                    <div>
                                        <p class="font-bold text-lg">
                                            {{ $property->price_unit ?? '₹' }}{{$property->price}}</p>
                                        @if ($property->super_area && is_numeric($property->price))
                                            <p class="text-sm">
                                                {{ number_format($property->price / $property->super_area) }} per sqft</p>
                                        @endif
                                    </div>
                                    @if ($property->possession_date)
                                        <div class="text-xs text-[#000080]">
                                            <p>Possession: {{ \Carbon\Carbon::parse($property->possession_date)->format('m-Y') }}</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Action Buttons -->
                                <div class="grid grid-cols-3 divide-x divide-gray-200 border-t border-gray-200 text-sm">
                                    <a class="py-3 hidden md:block text-center font-medium text-[#000080] hover:text-[#000066] transition">
                                     ID: {{ $property->property_id }}
                                    </a>
                                    <a href="{{ route('property.show', $property->id) }}"
                                        class="py-3 text-center font-medium text-[#000080] hover:text-[#000066] transition">
                                        Get Info
                                    </a>
                                    <a href="tel:+919920685877"
                                        class="py-3 text-center font-medium text-[#000080] hover:text-[#000066] transition">
                                        Call Now
                                    </a>
                                </div>

                            </div>
                        </div>
                    @endforeach

                    @if ($properties->isEmpty())
                        <div class="w-full bg-white rounded-lg p-8 text-center">
                            <h3 class="text-xl font-semibold text-gray-700">No properties found matching your criteria</h3>
                            <p class="text-gray-500 mt-2">Try adjusting your search filters</p>
                        </div>
                    @endif
                </div>

                        <div class="flex justify-center my-12">
                            {{ $properties->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script>
            (function () {
                const panel = document.getElementById('sfPanel');
                const backdrop = document.getElementById('sfBackdrop');
                const toggle = (open) => {
                    panel.classList.toggle('open', open);
                    backdrop.classList.toggle('open', open);
                };
                document.getElementById('sfOpen')?.addEventListener('click', () => toggle(true));
                document.getElementById('sfClose')?.addEventListener('click', () => toggle(false));
                backdrop.addEventListener('click', () => toggle(false));

                // Custom sort dropdown
                const dd = document.getElementById('sortDd');
                const ddBtn = document.getElementById('sortBtn');
                const ddMenu = document.getElementById('sortMenu');
                const items = Array.from(ddMenu.querySelectorAll('li'));
                const setOpen = (open) => {
                    ddMenu.hidden = !open;
                    dd.classList.toggle('open', open);
                    ddBtn.setAttribute('aria-expanded', open);
                };
                const choose = (value) => {
                    const url = new URL(window.location.href);
                    url.searchParams.set('sort', value);
                    url.searchParams.delete('page');
                    window.location.href = url.toString();
                };
                ddBtn.addEventListener('click', () => setOpen(ddMenu.hidden));
                items.forEach((li) => li.addEventListener('click', () => choose(li.dataset.value)));
                document.addEventListener('click', (e) => { if (!dd.contains(e.target)) setOpen(false); });
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') setOpen(false);
                    if (ddMenu.hidden || !['ArrowDown', 'ArrowUp', 'Enter'].includes(e.key)) return;
                    e.preventDefault();
                    let i = items.findIndex((li) => li.classList.contains('focus'));
                    if (e.key === 'Enter') { if (i >= 0) choose(items[i].dataset.value); return; }
                    items.forEach((li) => li.classList.remove('focus'));
                    i = e.key === 'ArrowDown' ? (i + 1) % items.length : (i <= 0 ? items.length - 1 : i - 1);
                    items[i].classList.add('focus');
                });

                // Show the scrollbar only while the filter panel is being scrolled
                const body = document.querySelector('.sf-body');
                let scrollTimer;
                body.addEventListener('scroll', () => {
                    body.classList.add('is-scrolling');
                    clearTimeout(scrollTimer);
                    scrollTimer = setTimeout(() => body.classList.remove('is-scrolling'), 800);
                }, { passive: true });

                // Locality options follow the selected city
                const city = document.getElementById('sfCity');
                const locality = document.getElementById('sfLocality');
                const syncLocalities = () => {
                    Array.from(locality.options).forEach((opt) => {
                        if (!opt.value) return;
                        const show = !city.value || opt.dataset.city === city.value;
                        opt.hidden = !show;
                        opt.disabled = !show;
                        if (!show && opt.selected) locality.value = '';
                    });
                };
                city.addEventListener('change', syncLocalities);
                syncLocalities();

                document.getElementById('sfMoreAmenities')?.addEventListener('click', function () {
                    const hidden = document.querySelectorAll('.sf-extra');
                    const show = this.textContent.trim() === 'Show more';
                    hidden.forEach((el) => (el.style.display = show ? 'flex' : 'none'));
                    this.textContent = show ? 'Show less' : 'Show more';
                });
            })();
        </script>

        <script>
            document.querySelectorAll(".carousel-images").forEach((carousel) => {
                const images = carousel.querySelectorAll("img");
                if (images.length <= 1) return;

                let index = 0;

                setInterval(() => {
                    images.forEach((img, i) => {
                        img.classList.remove("opacity-100");
                        img.classList.add("opacity-0");
                    });
                    images[index].classList.remove("opacity-0");
                    images[index].classList.add("opacity-100");
                    index = (index + 1) % images.length;
                }, 2000);
            });
        </script>
    </body>
@endsection


