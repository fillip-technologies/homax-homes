@extends('layout.layout')

@section('title', 'Home Page')
@section('head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
@endsection

@section('content')

    <body class="bg-white text-gray-800 font-sans">
        <!-- Results Section -->
        <section class="py-12">
            <div class="container mx-auto px-6">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-2xl font-semibold">
                        Search Results:
                        <span class="text-[#5146C7]">12 properties found</span>
                    </h3>
                    <div class="flex items-center">
                        <span class="mr-2">Sort by:</span>
                        <select
                            class="p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#5146C7]">
                            <option>Newest</option>
                            <option>Price: Low to High</option>
                            <option>Price: High to Low</option>
                            <option>Most Popular</option>
                        </select>
                    </div>
                </div>

                <div class="p-6 flex flex-col mx-auto space-y-6 justify-center items-center">
                    <!-- First Card -->
                    <div
                        class="w-full max-w-7xl bg-white rounded-lg overflow-hidden shadow-[0_4px_20px_rgba(0,0,0,0.15)] text-gray-800 flex flex-col md:flex-row">
                        <!-- Image Section -->
                        <div class="md:w-1/3 relative overflow-hidden">
                            <div class="carousel-images relative w-full h-64 md:h-full">
                                <img src="{{ asset('assets/images/outer-view.jpg') }}"
                                    class="absolute inset-0 w-full h-full object-cover opacity-100 transition-opacity duration-1000"
                                    alt="Property Image 1" />
                                <img src="{{ asset('assets/images/dinning.jpg') }}"
                                    class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-1000"
                                    alt="Property Image 2" />
                                <img src="{{ asset('assets/images/kitchen.jpg') }}"
                                    class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-1000"
                                    alt="Property Image 3" />
                            </div>

                            <!-- Badge -->
                            <div
                                class="absolute top-0 left-0   bg-[#5146C7]  text-white text-xs font-bold px-2 py-1 rounded-br-lg z-10">
                                FOR SALE
                            </div>
                        </div>

                        <!-- Content Section -->
                        <div class="md:w-2/3 flex flex-col">
                            <!-- Property Title -->
                            <div class="px-4 pt-4">
                                <h2 class="font-bold text-lg">
                                    2 BHK Flat for Sale in Thanisandra Main Road, Bangalore
                                </h2>
                                <p class="flex items-center text-[#5146C7] font-semibold"> <!-- Location Icon --> <svg
                                        xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1 text-[#5146C7]" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 11c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 22s8-4.5 8-11a8 8 0 10-16 0c0 6.5 8 11 8 11z" />
                                    </svg> Kankarbagh, Patna 800020 </p>
                            </div>

                            <!-- Property Details Grid -->
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 px-4 py-3 text-sm">
                                <div>
                                    <p class="text-gray-500">SUPER AREA</p>
                                    <p class="font-medium">1241 sqft</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">UNDER CONSTRUCTION</p>
                                    <p class="font-medium">Poss. by Oct '29</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">TRANSACTION</p>
                                    <p class="font-medium">New Property</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">FURNISHING</p>
                                    <p class="font-medium">Unfurnished</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">BATHROOM</p>
                                    <p class="font-medium">2</p>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="px-4 py-2 text-sm border-t border-gray-200 flex-grow">
                                <p>
                                    Welcome to Concorde Neo, nestled in the thriving hub of
                                    Northeast Bengaluru on Thanisandra Main Road. Designed for
                                    those who seek a balance of modern living and convenience,
                                    this property offers premium amenities and strategic location
                                    advantages.
                                </p>
                            </div>

                            <!-- Price Section -->
                            <div class="px-4 py-3 bg-gray-50 flex justify-between items-center">
                                <div>
                                    <p class="font-bold text-lg">₹1.14 Cr</p>
                                    <p class="text-sm">9,205 per sqft</p>
                                </div>
                                <div class="text-xs text-[#5146C7]">
                                    <p>Builder: Concorde</p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="grid grid-cols-3 divide-x divide-gray-200 border-t border-gray-200 text-sm">
                                <button class="py-3 font-medium   text-[#5146C7] hover:text-[#4038A8] transition">
                                    Request Callback
                                </button>
                                <button class="py-3 font-medium   text-[#5146C7] hover:text-[#4038A8] transition">
                                    Get Info
                                </button>
                                <button class="py-3 font-medium   text-[#5146C7] hover:text-[#4038A8] transition">
                                    Calculate EMI
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- secodn card -->
                    <div
                        class="w-full max-w-7xl bg-white rounded-lg overflow-hidden shadow-[0_4px_20px_rgba(0,0,0,0.15)] text-gray-800 flex flex-col md:flex-row">
                        <!-- Image Section -->
                        <div class="md:w-1/3 relative overflow-hidden">
                            <div class="carousel-images relative w-full h-64 md:h-full">
                                <img src="{{ asset('assets/images/another-outer-view.jpg') }}"
                                    class="absolute inset-0 w-full h-full object-cover opacity-100 transition-opacity duration-1000"
                                    alt="Property Image 1" />
                                <img src="{{ asset('assets/images/home.png') }}"
                                    class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-1000"
                                    alt="Property Image 2" />
                                <img src="{{ asset('assets/images/swiming.jpg') }}"
                                    class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-1000"
                                    alt="Property Image 3" />
                            </div>

                            <!-- Badge -->
                            <div
                                class="absolute top-0 left-0   bg-[#5146C7]  text-white text-xs font-bold px-2 py-1 rounded-br-lg z-10">
                                FOR SALE
                            </div>
                        </div>

                        <!-- Content Section -->
                        <div class="md:w-2/3 flex flex-col">
                            <!-- Property Title -->
                            <div class="px-4 pt-4">
                                <h2 class="font-bold text-lg">
                                    2 BHK Flat for Sale in Thanisandra Main Road, Bangalore
                                </h2>
                                <p class="flex items-center text-[#5146C7] font-semibold"> <!-- Location Icon --> <svg
                                        xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1 text-[#5146C7]" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 11c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 22s8-4.5 8-11a8 8 0 10-16 0c0 6.5 8 11 8 11z" />
                                    </svg> Kankarbagh, Patna 800020 </p>
                            </div>

                            <!-- Property Details Grid -->
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 px-4 py-3 text-sm">
                                <div>
                                    <p class="text-gray-500">SUPER AREA</p>
                                    <p class="font-medium">1241 sqft</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">UNDER CONSTRUCTION</p>
                                    <p class="font-medium">Poss. by Oct '29</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">TRANSACTION</p>
                                    <p class="font-medium">New Property</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">FURNISHING</p>
                                    <p class="font-medium">Unfurnished</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">BATHROOM</p>
                                    <p class="font-medium">2</p>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="px-4 py-2 text-sm border-t border-gray-200 flex-grow">
                                <p>
                                    Welcome to Concorde Neo, nestled in the thriving hub of
                                    Northeast Bengaluru on Thanisandra Main Road. Designed for
                                    those who seek a balance of modern living and convenience,
                                    this property offers premium amenities and strategic location
                                    advantages.
                                </p>
                            </div>

                            <!-- Price Section -->
                            <div class="px-4 py-3 bg-gray-50 flex justify-between items-center">
                                <div>
                                    <p class="font-bold text-lg">₹1.14 Cr</p>
                                    <p class="text-sm">9,205 per sqft</p>
                                </div>
                                <div class="text-xs text-[#5146C7]">
                                    <p>Builder: Concorde</p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="grid grid-cols-3 divide-x divide-gray-200 border-t border-gray-200 text-sm">
                                <button class="py-3 font-medium   text-[#5146C7] hover:text-[#4038A8] transition">
                                    Request Callback
                                </button>
                                <button class="py-3 font-medium   text-[#5146C7] hover:text-[#4038A8] transition">
                                    Get Info
                                </button>
                                <button class="py-3 font-medium   text-[#5146C7] hover:text-[#4038A8] transition">
                                    Calculate EMI
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="relative w-full h-72 overflow-hidden mb-6">
            <img src="{{ asset('assets/images/b2.png') }}" alt="Banner 1" class="w-full h-full object-cover" />
            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                <div class="text-center text-white">
                    <h2 class="text-3xl md:text-4xl font-bold mb-2">
                        Explore Premium Flats in Patna
                    </h2>
                    <p class="text-sm md:text-base">
                        Modern living in the Silicon Valley of India
                    </p>
                </div>
            </div>
        </div>

        <section class="py-12">
            <div class="container mx-auto px-6">
                <div class="p-6 flex flex-col mx-auto space-y-6 justify-center items-center">
                    <!-- First Card -->
                    <div
                        class="w-full max-w-7xl bg-white rounded-lg overflow-hidden shadow-[0_4px_20px_rgba(0,0,0,0.15)] text-gray-800 flex flex-col md:flex-row">
                        <!-- Image Section -->
                        <div class="md:w-1/3 relative overflow-hidden">
                            <div class="carousel-images relative w-full h-64 md:h-full">
                                <img src="{{ asset('assets/images/another-outer-view.jpg') }}"
                                    class="absolute inset-0 w-full h-full object-cover opacity-100 transition-opacity duration-1000"
                                    alt="Property Image 1" />
                                <img src="{{ asset('assets/images/kitchen.jpg') }}"
                                    class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-1000"
                                    alt="Property Image 2" />
                                <img src="{{ asset('assets/images/dinning.jpg') }}"
                                    class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-1000"
                                    alt="Property Image 3" />
                            </div>

                            <!-- Badge -->
                            <div
                                class="absolute top-0 left-0   bg-[#5146C7]  text-white text-xs font-bold px-2 py-1 rounded-br-lg z-10">
                                FOR SALE
                            </div>
                        </div>

                        <!-- Content Section -->
                        <div class="md:w-2/3 flex flex-col">
                            <!-- Property Title -->
                            <div class="px-4 pt-4">
                                <h2 class="font-bold text-lg">
                                    2 BHK Flat for Sale in Thanisandra Main Road, Bangalore
                                </h2>
                                <p class="flex items-center text-[#5146C7] font-semibold"> <!-- Location Icon --> <svg
                                        xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1 text-[#5146C7]"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 11c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 22s8-4.5 8-11a8 8 0 10-16 0c0 6.5 8 11 8 11z" />
                                    </svg> Kankarbagh, Patna 800020 </p>
                            </div>

                            <!-- Property Details Grid -->
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 px-4 py-3 text-sm">
                                <div>
                                    <p class="text-gray-500">SUPER AREA</p>
                                    <p class="font-medium">1241 sqft</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">UNDER CONSTRUCTION</p>
                                    <p class="font-medium">Poss. by Oct '29</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">TRANSACTION</p>
                                    <p class="font-medium">New Property</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">FURNISHING</p>
                                    <p class="font-medium">Unfurnished</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">BATHROOM</p>
                                    <p class="font-medium">2</p>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="px-4 py-2 text-sm border-t border-gray-200 flex-grow">
                                <p>
                                    Welcome to Concorde Neo, nestled in the thriving hub of
                                    Northeast Bengaluru on Thanisandra Main Road. Designed for
                                    those who seek a balance of modern living and convenience,
                                    this property offers premium amenities and strategic location
                                    advantages.
                                </p>
                            </div>

                            <!-- Price Section -->
                            <div class="px-4 py-3 bg-gray-50 flex justify-between items-center">
                                <div>
                                    <p class="font-bold text-lg">₹1.14 Cr</p>
                                    <p class="text-sm">9,205 per sqft</p>
                                </div>
                                <div class="text-xs text-[#5146C7]">
                                    <p>Builder: Concorde</p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="grid grid-cols-3 divide-x divide-gray-200 border-t border-gray-200 text-sm">
                                <button class="py-3 font-medium   text-[#5146C7] hover:text-[#4038A8] transition">
                                    Request Callback
                                </button>
                                <button class="py-3 font-medium   text-[#5146C7] hover:text-[#4038A8] transition">
                                    Get Info
                                </button>
                                <button class="py-3 font-medium   text-[#5146C7] hover:text-[#4038A8] transition">
                                    Calculate EMI
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- secodn card -->
                    <div
                        class="w-full max-w-7xl bg-white rounded-lg overflow-hidden shadow-[0_4px_20px_rgba(0,0,0,0.15)] text-gray-800 flex flex-col md:flex-row">
                        <!-- Image Section -->
                        <div class="md:w-1/3 relative overflow-hidden">
                            <div class="carousel-images relative w-full h-64 md:h-full">
                                <img src="{{ asset('assets/images/outer-view.jpg') }}"
                                    class="absolute inset-0 w-full h-full object-cover opacity-100 transition-opacity duration-1000"
                                    alt="Property Image 1" />
                                <img src="{{ asset('assets/images/swiming.jpg') }}"
                                    class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-1000"
                                    alt="Property Image 2" />
                                <img src="{{ asset('assets/images/dinning.jpg') }}"
                                    class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-1000"
                                    alt="Property Image 3" />
                            </div>

                            <!-- Badge -->
                            <div
                                class="absolute top-0 left-0   bg-[#5146C7]  text-white text-xs font-bold px-2 py-1 rounded-br-lg z-10">
                                FOR SALE
                            </div>
                        </div>

                        <!-- Content Section -->
                        <div class="md:w-2/3 flex flex-col">
                            <!-- Property Title -->
                            <div class="px-4 pt-4">
                                <h2 class="font-bold text-lg">
                                    2 BHK Flat for Sale in Thanisandra Main Road, Bangalore
                                </h2>
                                <p class="flex items-center text-[#5146C7] font-semibold"> <!-- Location Icon --> <svg
                                        xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1 text-[#5146C7]"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 11c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 22s8-4.5 8-11a8 8 0 10-16 0c0 6.5 8 11 8 11z" />
                                    </svg> Kankarbagh, Patna 800020 </p>
                            </div>

                            <!-- Property Details Grid -->
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 px-4 py-3 text-sm">
                                <div>
                                    <p class="text-gray-500">SUPER AREA</p>
                                    <p class="font-medium">1241 sqft</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">UNDER CONSTRUCTION</p>
                                    <p class="font-medium">Poss. by Oct '29</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">TRANSACTION</p>
                                    <p class="font-medium">New Property</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">FURNISHING</p>
                                    <p class="font-medium">Unfurnished</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">BATHROOM</p>
                                    <p class="font-medium">2</p>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="px-4 py-2 text-sm border-t border-gray-200 flex-grow">
                                <p>
                                    Welcome to Concorde Neo, nestled in the thriving hub of
                                    Northeast Bengaluru on Thanisandra Main Road. Designed for
                                    those who seek a balance of modern living and convenience,
                                    this property offers premium amenities and strategic location
                                    advantages.
                                </p>
                            </div>

                            <!-- Price Section -->
                            <div class="px-4 py-3 bg-gray-50 flex justify-between items-center">
                                <div>
                                    <p class="font-bold text-lg">₹1.14 Cr</p>
                                    <p class="text-sm">9,205 per sqft</p>
                                </div>
                                <div class="text-xs text-[#5146C7]">
                                    <p>Builder: Concorde</p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="grid grid-cols-3 divide-x divide-gray-200 border-t border-gray-200 text-sm">
                                <button class="py-3 font-medium   text-[#5146C7] hover:text-[#4038A8] transition">
                                    Request Callback
                                </button>
                                <button class="py-3 font-medium   text-[#5146C7] hover:text-[#4038A8] transition">
                                    Get Info
                                </button>
                                <button class="py-3 font-medium   text-[#5146C7] hover:text-[#4038A8] transition">
                                    Calculate EMI
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Third Card -->
                    <div
                        class="w-full max-w-7xl bg-white rounded-lg overflow-hidden shadow-[0_4px_20px_rgba(0,0,0,0.15)] text-gray-800 flex flex-col md:flex-row">
                        <!-- Image Section -->
                        <div class="md:w-1/3 relative overflow-hidden">
                            <div class="carousel-images relative w-full h-64 md:h-full">
                                <img src="{{ asset('assets/images/background-image.png') }}"
                                    class="absolute inset-0 w-full h-full object-cover opacity-100 transition-opacity duration-1000"
                                    alt="Property Image 1" />
                                <img src="{{ asset('assets/images/dinning.jpg') }}"
                                    class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-1000"
                                    alt="Property Image 2" />
                                <img src="{{ asset('assets/images/swiming.jpg') }}"
                                    class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-1000"
                                    alt="Property Image 3" />
                            </div>

                            <!-- Badge -->
                            <div
                                class="absolute top-0 left-0   bg-[#5146C7]  text-white text-xs font-bold px-2 py-1 rounded-br-lg z-10">
                                FOR SALE
                            </div>
                        </div>

                        <!-- Content Section -->
                        <div class="md:w-2/3 flex flex-col">
                            <!-- Property Title -->
                            <div class="px-4 pt-4">
                                <h2 class="font-bold text-lg">
                                    2 BHK Flat for Sale in Thanisandra Main Road, Bangalore
                                </h2>
                                <p class="flex items-center text-[#5146C7] font-semibold"> <!-- Location Icon --> <svg
                                        xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1 text-[#5146C7]"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 11c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 22s8-4.5 8-11a8 8 0 10-16 0c0 6.5 8 11 8 11z" />
                                    </svg> Kankarbagh, Patna 800020 </p>
                            </div>

                            <!-- Property Details Grid -->
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 px-4 py-3 text-sm">
                                <div>
                                    <p class="text-gray-500">SUPER AREA</p>
                                    <p class="font-medium">1241 sqft</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">UNDER CONSTRUCTION</p>
                                    <p class="font-medium">Poss. by Oct '29</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">TRANSACTION</p>
                                    <p class="font-medium">New Property</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">FURNISHING</p>
                                    <p class="font-medium">Unfurnished</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">BATHROOM</p>
                                    <p class="font-medium">2</p>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="px-4 py-2 text-sm border-t border-gray-200 flex-grow">
                                <p>
                                    Welcome to Concorde Neo, nestled in the thriving hub of
                                    Northeast Bengaluru on Thanisandra Main Road. Designed for
                                    those who seek a balance of modern living and convenience,
                                    this property offers premium amenities and strategic location
                                    advantages.
                                </p>
                            </div>

                            <!-- Price Section -->
                            <div class="px-4 py-3 bg-gray-50 flex justify-between items-center">
                                <div>
                                    <p class="font-bold text-lg">₹1.14 Cr</p>
                                    <p class="text-sm">9,205 per sqft</p>
                                </div>
                                <div class="text-xs text-[#5146C7]">
                                    <p>Builder: Concorde</p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="grid grid-cols-3 divide-x divide-gray-200 border-t border-gray-200 text-sm">
                                <button class="py-3 font-medium   text-[#5146C7] hover:text-[#4038A8] transition">
                                    Request Callback
                                </button>
                                <button class="py-3 font-medium   text-[#5146C7] hover:text-[#4038A8] transition">
                                    Get Info
                                </button>
                                <button class="py-3 font-medium   text-[#5146C7] hover:text-[#4038A8] transition">
                                    Calculate EMI
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="relative w-full h-72 overflow-hidden mb-6">
            <img src="{{ asset('assets/images/b3.png') }}" alt="Banner 3" class="w-full h-full object-cover" />
            <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-end pr-8">
                <div class="text-right text-white">
                    <h2 class="text-3xl md:text-4xl font-bold mb-2">
                        Live in the Heart of the City
                    </h2>
                    <p class="text-sm md:text-base">
                        Exclusive high-rise homes in top locations
                    </p>
                </div>
            </div>
        </div>

        <div class="flex justify-center my-12">
            <nav class="flex items-center space-x-2">
                <button class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="px-4 py-2 border border-[#5146C7]   bg-[#5146C7]  text-white rounded">
                    1
                </button>
                <button class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-100">
                    2
                </button>
                <button class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-100">
                    3
                </button>
                <button class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-100">
                    4
                </button>
                <button class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </nav>
        </div>


        <script>
            document.querySelectorAll(".carousel-images").forEach((carousel) => {
                const images = carousel.querySelectorAll("img");
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


