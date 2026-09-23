<form action="{{ route('property.search') }}" method="GET"
    class="bg-white rounded-xl p-6 w-full mx-auto grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 border border-[#E7E7F0] transition-shadow duration-300"
    style="box-shadow: 0 10px 35px rgba(0,0,128,0.10);">

    <select name="category"
        class="border border-[#E7E7F0] bg-white text-[#5F6472] px-4 py-3 rounded-md w-full lg:col-span-1 focus:outline-none focus:ring-2 focus:ring-[#DAA520]">
        <option class="text-gray-800" value="">Project Type</option>
        <option class="text-gray-800" value="Residential"
            {{ request('category') == 'Residential' ? 'selected' : '' }}>Residential</option>
        <option class="text-gray-800" value="Commercial"
            {{ request('category') == 'Commercial' ? 'selected' : '' }}>Commercial</option>
    </select>

    {{-- Options come from the listings themselves (see indexwelcome),
         so the dropdown can never offer a city with nothing behind it. --}}
    <select name="city"
        class="border border-[#E7E7F0] bg-white text-[#5F6472] px-4 py-3 rounded-md w-full lg:col-span-1 focus:outline-none focus:ring-2 focus:ring-[#DAA520]">
        <option class="text-gray-800" value="">Location</option>
        @foreach ($searchCities ?? [] as $cityOption)
            <option class="text-gray-800" value="{{ $cityOption }}"
                {{ request('city') == $cityOption ? 'selected' : '' }}>{{ $cityOption }}</option>
        @endforeach
    </select>

    <input type="text" name="search" placeholder="Search by project name, locality, city"
        value="{{ request('search') }}"
        class="border border-[#E7E7F0] bg-white text-[#5F6472] placeholder-[#5F6472] px-4 py-3 rounded-md w-full sm:col-span-2 lg:col-span-2 focus:outline-none focus:ring-2 focus:ring-[#DAA520]" />

    <select name="status"
        class="border border-[#E7E7F0] bg-white text-[#5F6472] px-4 py-3 rounded-md w-full lg:col-span-1 focus:outline-none focus:ring-2 focus:ring-[#DAA520]">
        <option class="text-gray-800" value="">Availability</option>
        <option class="text-gray-800" value="upcoming"
            {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
        <option class="text-gray-800" value="pre-launch"
            {{ request('status') == 'pre-launch' ? 'selected' : '' }}>Pre-Launch</option>
        <option class="text-gray-800" value="early-possession"
            {{ request('status') == 'early-possession' ? 'selected' : '' }}>Early Possession</option>
        <option class="text-gray-800" value="ready-to-move"
            {{ request('status') == 'ready-to-move' ? 'selected' : '' }}>Ready to move</option>
    </select>
    {{-- Carry any OTHER active filter through (sort, page...).
         The fields this form renders itself are excluded, or they
         would be submitted twice - once by the control and once as
         a hidden copy of the previous value. --}}
    @foreach (request()->except(['sort', 'page', 'category', 'city', 'search', 'status']) as $key => $value)
        @if (!is_array($value) && filled($value))
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endif
    @endforeach
    <button type="submit"
        class="bg-[#DAA520] hover:bg-[#B8860B] text-white font-semibold px-4 py-3 rounded-md transition-colors duration-300 shadow-md sm:col-span-2 lg:col-span-1 flex items-center justify-center whitespace-nowrap">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        Search Projects
    </button>
</form>
