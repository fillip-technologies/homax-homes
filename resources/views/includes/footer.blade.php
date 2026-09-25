<!-- Footer -->
<footer class="bg-[#000033] text-white">
  <!-- Main Footer Content -->
  <div class="container mx-auto px-6 py-12">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-12">
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
        {{-- Inline SVG: no requests to a third-party icon host. Twitter and
             LinkedIn removed - no accounts were supplied for them and both
             were pointing at "#". Gold hover: navy was invisible on #000033. --}}
        <div class="flex space-x-4 mt-6">
          <a href="https://www.facebook.com/homaxhomes" target="_blank" rel="noopener noreferrer" aria-label="Facebook"
            class="text-gray-400 hover:text-[#DAA520] transition duration-300">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path d="M22 12.06C22 6.5 17.52 2 12 2S2 6.5 2 12.06c0 5 3.66 9.15 8.44 9.94v-7.03H7.9v-2.9h2.54V9.85c0-2.52 1.49-3.91 3.77-3.91 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.78-1.63 1.57v1.89h2.78l-.44 2.9h-2.34V22c4.78-.79 8.44-4.94 8.44-9.94z"/>
            </svg>
          </a>
          <a href="https://www.instagram.com/homaxhomes" target="_blank" rel="noopener noreferrer" aria-label="Instagram"
            class="text-gray-400 hover:text-[#DAA520] transition duration-300">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path d="M12 2.16c3.2 0 3.58.01 4.85.07 1.17.05 1.8.25 2.23.41.56.22.96.48 1.38.9.42.42.68.82.9 1.38.16.42.36 1.06.41 2.23.06 1.27.07 1.65.07 4.85s-.01 3.58-.07 4.85c-.05 1.17-.25 1.8-.41 2.23-.22.56-.48.96-.9 1.38-.42.42-.82.68-1.38.9-.42.16-1.06.36-2.23.41-1.27.06-1.65.07-4.85.07s-3.58-.01-4.85-.07c-1.17-.05-1.8-.25-2.23-.41-.56-.22-.96-.48-1.38-.9-.42-.42-.68-.82-.9-1.38-.16-.42-.36-1.06-.41-2.23C2.17 15.58 2.16 15.2 2.16 12s.01-3.58.07-4.85c.05-1.17.25-1.8.41-2.23.22-.56.48-.96.9-1.38.42-.42.82-.68 1.38-.9.42-.16 1.06-.36 2.23-.41C8.42 2.17 8.8 2.16 12 2.16zm0 3.68a6.16 6.16 0 100 12.32 6.16 6.16 0 000-12.32zm0 10.16a4 4 0 110-8 4 4 0 010 8zm7.84-10.4a1.44 1.44 0 11-2.88 0 1.44 1.44 0 012.88 0z"/>
            </svg>
          </a>
          <a href="https://www.youtube.com/@Homax-homes" target="_blank" rel="noopener noreferrer" aria-label="YouTube"
            class="text-gray-400 hover:text-[#DAA520] transition duration-300">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path d="M23.5 6.19a3.02 3.02 0 00-2.12-2.14C19.5 3.55 12 3.55 12 3.55s-7.5 0-9.38.5A3.02 3.02 0 00.5 6.19C0 8.08 0 12 0 12s0 3.92.5 5.81a3.02 3.02 0 002.12 2.14c1.88.5 9.38.5 9.38.5s7.5 0 9.38-.5a3.02 3.02 0 002.12-2.14C24 15.92 24 12 24 12s0-3.92-.5-5.81zM9.55 15.57V8.43L15.82 12l-6.27 3.57z"/>
            </svg>
          </a>
        </div>
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
        <a href="/about-us" class="text-gray-400 hover:text-[#DAA520] transition duration-300 mb-2">About Us</a>
        <a href="/our-team" class="text-gray-400 hover:text-[#DAA520] transition duration-300 mb-2">Our Team</a>
        <a href="/#blog" class="text-gray-400 hover:text-[#DAA520] transition duration-300 mb-2">Blog</a>
        <a href="/careers" class="text-gray-400 hover:text-[#DAA520] transition duration-300 mb-2">Careers</a>
        <a href="/contact" class="text-gray-400 hover:text-[#DAA520] transition duration-300 mb-2">Contact</a>
      </div>

      <!-- Legal -->
      <div class="flex flex-col">
        <h3 class="font-semibold text-lg text-gray-300 mb-4">Legal</h3>
        <a href="#" class="text-gray-400 hover:text-[#000080] transition duration-300 mb-2">Privacy Policy</a>
        <a href="#" class="text-gray-400 hover:text-[#000080] transition duration-300 mb-2">Terms of Service</a>
        <a href="#" class="text-gray-400 hover:text-[#000080] transition duration-300 mb-2">Cookie Policy</a>
        <a href="#" class="text-gray-400 hover:text-[#000080] transition duration-300 mb-2">GDPR Compliance</a>
        <a href="#" class="text-gray-400 hover:text-[#000080] transition duration-300 mb-2">Accessibility Statement</a>
        <a href="#" class="text-gray-400 hover:text-[#000080] transition duration-300 mb-2">Sitemap</a>
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

    <!-- SEO Content Section -->
    <div class="mt-16 border-t border-[#25204F] pt-8">
      <h3 class="text-xl font-semibold text-gray-300 mb-4">About Homax Homes Commercial Real Estate</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-gray-400">
        <div>
          <p class="mb-4">
            Homax Homes is the leading commercial real estate marketplace connecting business owners, investors, and real estate professionals with premium office spaces, retail locations, industrial properties, and land for sale or lease across the United States.
          </p>
          <p class="mb-4">
            Our comprehensive database features thousands of commercial listings with detailed property information, high-resolution images, virtual tours, and market analytics to help you make informed real estate decisions.
          </p>
        </div>
        <div>
          <p class="mb-4">
            Whether you're looking to buy, sell, or lease commercial property, Homax Homes provides the tools and resources you need. Our network of licensed commercial real estate brokers and agents are available to assist with your transaction.
          </p>
          <p>
            Homax Homes serves major markets including New York, Los Angeles, Chicago, Houston, Phoenix, Philadelphia, San Antonio, San Diego, Dallas, and San Jose, with expanding coverage nationwide.
          </p>
        </div>
      </div>
    </div>

    <!-- Legal Disclaimers -->
    <div class="mt-12 border-t border-[#25204F] pt-8">
      <div class="text-gray-400 text-sm">
        <h4 class="font-semibold text-gray-300 mb-2">Legal Disclaimer:</h4>
        <p class="mb-4">
          The information provided on this website does not constitute legal, financial, or professional real estate advice. All property information is deemed reliable but not guaranteed and should be independently verified. Homax Homes does not guarantee the accuracy of listing information, including but not limited to square footage, pricing, or availability. All property measurements are approximate.
        </p>

        <h4 class="font-semibold text-gray-300 mb-2">Equal Housing Opportunity:</h4>
        <p class="mb-4">
          Homax Homes is committed to compliance with all federal, state, and local fair housing laws. We provide equal professional service to all persons without regard to race, color, religion, sex, handicap, familial status, national origin, sexual orientation, or gender identity.
        </p>

        <h4 class="font-semibold text-gray-300 mb-2">Licensing Information:</h4>
        <p>
          Homax Homes, Inc. is a licensed real estate brokerage in all 50 states (License #1234567). Brokerage services are provided by Homax Homes Realty Services, LLC. All agents listed on this site are licensed professionals.
        </p>
      </div>
    </div>

    <!-- Copyright -->
    <div class="mt-12 border-t border-[#25204F] pt-6 text-center text-gray-400 text-sm">
      <p>&copy; {{ date('Y') }} Homax Homes, Inc. All rights reserved. Various trademarks held by their respective owners.</p>
      <p class="mt-2">Designed and Developed by Fillip Technologies</p>
    </div>
  </div>
</footer>




