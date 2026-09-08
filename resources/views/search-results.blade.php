@extends('layout.layout')

@section('title', 'Search Results')
@section('head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
@endsection

@section('content')

    <body class="bg-white text-gray-800 font-sans">
        <!-- Results Section -->
        <section class="py-12">
            <div class="container mx-auto px-6">
                <div class="flex md:flex-row flex-col justify-between items-center mb-8">
                    <h3 class="text-2xl font-semibold">
                        Search Results:
                        <span class="text-[#5146C7]">{{ $properties->total() }} properties found</span>
                    </h3>
                    <form method="GET" action="{{ route('property.search') }}" class="flex items-center gap-2">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="property_type" value="{{ request('property_type') }}">
                        <label for="sort" class="mr-2 text-gray-600">Sort by:</label>
                        <select name="sort" id="sort" onchange="this.form.submit()"
                            class="p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#5146C7]">
                            <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Newest
                                First</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to
                                High</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High
                                to Low</option>
                            <option value="area_asc" {{ request('sort') == 'area_asc' ? 'selected' : '' }}>Area: Small to
                                Large</option>
                            <option value="area_desc" {{ request('sort') == 'area_desc' ? 'selected' : '' }}>Area: Large to
                                Small</option>
                            <option value="bedrooms_asc" {{ request('sort') == 'bedrooms_asc' ? 'selected' : '' }}>
                                Bedrooms: Few to Many</option>
                            <option value="bedrooms_desc" {{ request('sort') == 'bedrooms_desc' ? 'selected' : '' }}>
                                Bedrooms: Many to Few</option>
                        </select>
                    </form>

                </div>

                <div class="p-6 flex flex-col mx-auto space-y-6 justify-center items-center">
                    @foreach ($properties as $property)
                        <!-- Property Card -->
                        <div
                            class="w-[93vw] md:max-w-7xl bg-white rounded-lg overflow-hidden shadow-[0_4px_20px_rgba(0,0,0,0.15)] text-gray-800 flex flex-col md:flex-row">
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
                                <div
                                    class="absolute top-0 left-0 bg-[#5146C7] text-white text-xs font-bold px-2 py-1 rounded-br-lg z-10">
                                    {{ $property->listing_type }}
                                </div>
                            </div>

                            <!-- Content Section -->
                            <div class="md:w-2/3 flex flex-col">
                                <!-- Property Title -->
                                <div class="px-4 pt-4">
                                    <h2 class="font-bold text-lg">
                                        {{ $property->title }}

                                        {{-- {{ $property->bedrooms ?? 'N/A' }} BHK {{ $property->property_type }} for
                                        {{ $property->listing_type }} in {{ $property->city }} --}}
                                    </h2>
                                    <p class="flex items-center text-[#5146C7] font-semibold">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1 text-[#5146C7]"
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
                                        <p class="text-gray-500">YEAR BUILT</p>
                                        <p class="font-medium">{{ $property->year_built ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">TRANSACTION</p>
                                        <p class="font-medium">{{ $property->listing_type }}</p>
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
                                        @if ($property->super_area)
                                            <p class="text-sm">
                                                {{ number_format($property->price / $property->super_area) }} per sqft</p>
                                        @endif
                                    </div>
                                    <div class="text-xs text-[#5146C7]">
                                        <p>Availability: {{ $property->availability }}</p>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="grid grid-cols-3 divide-x divide-gray-200 border-t border-gray-200 text-sm">
                                    <a class="py-3 hidden md:block text-center font-medium text-[#5146C7] hover:text-[#4038A8] transition">
                                     ID: {{ $property->property_id }}
                                    </a>
                                    <a href="{{ route('property.show', $property->id) }}"
                                        class="py-3 text-center font-medium text-[#5146C7] hover:text-[#4038A8] transition">
                                        Get Info
                                    </a>
                                    <a href="tel:987654123"
                                        class="py-3 text-center font-medium text-[#5146C7] hover:text-[#4038A8] transition">
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
            </div>
        </section>

        <!-- Pagination -->
        <div class="flex justify-center my-12">
            {{ $properties->appends(request()->query())->links() }}
        </div>

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


