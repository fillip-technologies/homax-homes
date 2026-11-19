<!-- Footer -->
<footer class="bg-[#000033] text-white">
  <!-- Main Footer Content -->
  <div class="container mx-auto px-6 py-12">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
      <!-- Logo and Brand -->
      <div class="flex flex-col">
        {{-- <span class="text-4xl font-bold text-[#000080] cursor-pointer">Homax Homes</span> --}}
        {{-- Light variant: the footer is #000033, and the master logo's wordmark
             is navy, so the full-color file would all but disappear here. Only
             the navy strokes are recolored - the gold house mark is untouched. --}}
        <a href="/" class="flex items-center" aria-label="Homax Homes">
          <img src="{{ asset('assets/logo/homax-logo-light.png') }}" alt="Homax Homes" width="480" height="160"
            loading="lazy" decoding="async" class="h-14 w-auto">
        </a>
        <p class="text-gray-400 mt-4">
          Your trusted platform for finding and listing commercial properties nationwide.
          Connecting buyers, sellers, and renters since 2015.
        </p>
      </div>

      <!-- Quick Links -->
      <div class="flex flex-col">
        {{-- Mirrors the header menu with real search filters. These used to be
             six "#" placeholders labeled for a US commercial marketplace
             (Office Spaces, Retail Locations, Agents Directory) that this site
             has no pages for. Gold hover to match the Company column. --}}
        <h3 class="font-semibold text-lg text-gray-300 mb-4">Quick Links</h3>
        <a href="/" class="text-gray-400 hover:text-[#DAA520] transition duration-300 mb-2">Home</a>
        <a href="{{ route('property.search', ['category' => 'residential']) }}"
          class="text-gray-400 hover:text-[#DAA520] transition duration-300 mb-2">Residential Projects</a>
        <a href="{{ route('property.search', ['category' => 'commercial']) }}"
          class="text-gray-400 hover:text-[#DAA520] transition duration-300 mb-2">Commercial Projects</a>
        <a href="{{ route('property.search', ['sort' => 'newest']) }}"
          class="text-gray-400 hover:text-[#DAA520] transition duration-300 mb-2">New Launches</a>
        <a href="{{ route('property.search', ['status' => 'ready-to-move']) }}"
          class="text-gray-400 hover:text-[#DAA520] transition duration-300 mb-2">Ready to Move</a>
        <a href="{{ route('property.search') }}"
          class="text-gray-400 hover:text-[#DAA520] transition duration-300 mb-2">All Projects</a>
      </div>

      <!-- Company -->
      <div class="flex flex-col">
        {{-- Blog and the About group live here now rather than in the header.
             Real URLs, not the "#" placeholders these used to carry. --}}
        <h3 class="font-semibold text-lg text-gray-300 mb-4">Company</h3>
        <a href="{{ route('about') }}" class="text-gray-400 hover:text-[#DAA520] transition duration-300 mb-2">About Us</a>
        <a href="/our-team" class="text-gray-400 hover:text-[#DAA520] transition duration-300 mb-2">Our Team</a>
        <a href="{{ route('blog') }}" class="text-gray-400 hover:text-[#DAA520] transition duration-300 mb-2">Blog</a>
        <a href="{{ route('careers') }}" class="text-gray-400 hover:text-[#DAA520] transition duration-300 mb-2">Careers</a>
        <a href="/contact" class="text-gray-400 hover:text-[#DAA520] transition duration-300 mb-2">Contact</a>
      </div>

      <!-- Newsletter -->
      <div class="flex flex-col">
        <h3 class="font-semibold text-lg text-gray-300 mb-4">Get Property Alerts</h3>
        <p class="text-gray-400 mb-4">
          Receive the latest commercial property listings directly to your inbox.
        </p>
        <div class="flex flex-col space-y-3">
          <input
            type="email"
            placeholder="Your email address"
            class="p-3 rounded-md bg-[#25204F] text-white focus:outline-none"
          />
          <button class="bg-[#000080] text-white p-3 rounded-md hover:bg-[#000066] transition duration-300">
            Subscribe
          </button>
        </div>
      </div>
    </div>

    @php
      $fa = \App\Models\SiteSetting::footerAbout();
      $faNotices = array_values(array_filter($fa['notices'], fn ($n) => $n['title'] !== '' || $n['text'] !== ''));
    @endphp

    @if ($faNotices)
    <!-- Legal Disclaimers -->
    <div class="mt-12 border-t border-[#25204F] pt-8">
      <div class="text-gray-400 text-sm">
        @foreach ($faNotices as $notice)
        @if ($notice['title'] !== '')
        <h4 class="font-semibold text-gray-300 mb-2">{{ $notice['title'] }}:</h4>
        @endif
        <p class="{{ $loop->last ? '' : 'mb-4' }}">
          {{ $notice['text'] }}
        </p>
        @endforeach
      </div>
    </div>
    @endif

    <!-- Copyright -->
    <div class="mt-12 border-t border-[#25204F] pt-6 text-center text-gray-400 text-sm">
      <p>&copy; {{ date('Y') }} Homax Homes, Inc. All rights reserved. Various trademarks held by their respective owners.</p>
      <p class="mt-2">Designed and Developed by Fillip Technologies</p>
    </div>
  </div>
</footer>




