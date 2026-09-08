@extends('layout.layout')

@section('title', 'About Our Team')
@php
    $primaryColor = $primaryColor ?? '#5146C7'; // fallback
@endphp

@section('content')
    <style>
        :root {
            /* Primary Colors */
            --primary: {{ $primaryColor }};
            --primary-dark: #4038A8;
            --primary-darker: #17113B;

            /* Neutral Colors */
            --gray-dark: #717271;
            --gray-light: #b1b2b1;
            --white: #ffffff;

            /* Additional Colors */
            --teal: #38b2ac;
            --red: #e53e3e;
            --yellow: #f6e05e;
            --purple: #805ad5;
            --blue: #4299e1;
            --green: #48bb78;
        }

        .team-member-card:hover .team-member-img {
            transform: scale(1.05);
        }

        .social-icon {
            transition: all 0.3s ease;
        }

        .social-icon:hover {
            transform: translateY(-3px);
        }
    </style>

    <body class="bg-white text-gray-700 font-sans overflow-x-hidden">

        <!-- Hero Section -->
        <section class="relative py-32 bg-cover bg-center z-0"
            style="background-image: url('https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80')">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

            <!-- Content -->
            <div class="relative z-10 flex flex-col items-center justify-center h-full text-center px-4 sm:px-8">
                <h1 class="text-4xl md:text-6xl font-bold mb-6 text-white">
                    Meet Our <span class="text-primary">Team</span>
                </h1>

                <p class="mb-8 text-lg md:text-xl max-w-2xl mx-auto text-white">
                    The passionate professionals behind Homax Homes who are dedicated to helping you find your dream home.
                </p>

                <!-- Breadcrumb -->
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-2">
                        <li class="inline-flex items-center">
                            <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-white hover:text-primary">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                </svg>
                                Home
                            </a>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-1 text-sm font-medium text-primary md:ml-2">Our Team</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </section>

        <!-- Team Introduction -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <!-- Content Column -->
                    <div>
                        <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-6">
                            Our <span class="text-primary">Expert</span> Team
                        </h2>
                        <div class="w-24 h-1.5 bg-gradient-to-r from-primary to-primary-dark rounded-full mb-8"></div>

                        <p class="text-lg text-gray-600 mb-6">
                            At Homax Homes, we've assembled a team of the most knowledgeable and dedicated real estate professionals in the industry. With decades of combined experience, our team brings unparalleled expertise to help you navigate the property market.
                        </p>

                        <div class="space-y-4 mb-8">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mt-1">
                                    <div class="w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <p class="ml-3 text-gray-600">
                                    <span class="font-semibold">Local Market Experts:</span> Deep knowledge of every neighborhood we serve
                                </p>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0 mt-1">
                                    <div class="w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <p class="ml-3 text-gray-600">
                                    <span class="font-semibold">Client-First Approach:</span> Your needs always come first in every transaction
                                </p>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0 mt-1">
                                    <div class="w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <p class="ml-3 text-gray-600">
                                    <span class="font-semibold">Innovative Solutions:</span> Creative problem solvers who find the perfect property for you
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Image Column -->
                    <div class="relative">
                        <div class="relative rounded-2xl overflow-hidden shadow-xl">
                            <img src="https://images.unsplash.com/photo-1570129477492-45c003edd2be" alt="Our Team"
                                class="w-full h-auto object-cover transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-primary/30 to-transparent"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Leadership Team -->
        <section class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Heading -->
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                        Leadership <span class="text-primary">Team</span>
                    </h2>
                    <div class="mx-auto w-24 h-1 bg-gradient-to-r from-primary to-primary-dark rounded-full mb-6"></div>
                    <p class="text-gray-500 max-w-3xl mx-auto text-lg">
                        Meet the visionary leaders who guide Homax Homes's strategy and ensure we deliver exceptional service to our clients.
                    </p>
                </div>

           
                <!-- Team Members -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
    @foreach($members as $mem)
        <!-- Team Member Card -->
        <div class="team-member-card bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-500 hover:shadow-xl">
            <div class="relative h-80 overflow-hidden">
                <img src="{{ asset($mem->employee_image ?? 'assets/img/default-user.png') }}" 
                     alt="{{ $mem->employee_name }}" 
                     class="team-member-img w-full h-full object-cover transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-6">
                    <h3 class="text-2xl font-bold text-white">{{ $mem->employee_name }}</h3>
                    <p class="text-primary font-medium">{{ $mem->designation }}</p>
                </div>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-2">
                    <strong>Joined:</strong> {{ \Carbon\Carbon::parse($mem->joining_date)->format('F j, Y') }}
                </p>
                <p class="text-gray-600 mb-4">
                    <!-- Add any additional description here -->
                </p>
                <div class="flex space-x-4">
                    @if($mem->fb_id_link)
                        <a href="{{ $mem->fb_id_link }}" class="social-icon text-gray-500 hover:text-primary" target="_blank">
                            <!-- Facebook SVG -->
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                            </svg>
                        </a>
                    @endif
                    @if($mem->twitter_link)
                        <a href="{{ $mem->twitter_link }}" class="social-icon text-gray-500 hover:text-primary" target="_blank">
                            <!-- Twitter SVG -->
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/>
                            </svg>
                        </a>
                    @endif
                    @if($mem->linkedin_link)
                        <a href="{{ $mem->linkedin_link }}" class="social-icon text-gray-500 hover:text-primary" target="_blank">
                            <!-- LinkedIn SVG -->
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452z"/>
                            </svg>
                        </a>
                    @endif
                    @if($mem->instagram_link)
                        <a href="{{ $mem->instagram_link }}" class="social-icon text-gray-500 hover:text-primary" target="_blank">
                            <!-- Instagram SVG -->
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.2c3.2 0 3.6 0 4.9.1 1.2.1 1.9.3 2.4.5.6.3 1 .7 1.5 1.5.2.5.4 1.1.5 2.4.1 1.3.1 1.7.1 4.9s0 3.6-.1 4.9c-.1 1.2-.3 1.9-.5 2.4-.3.6-.7 1-1.5 1.5-.5.2-1.1.4-2.4.5-1.3.1-1.7.1-4.9.1s-3.6 0-4.9-.1c-1.2-.1-1.9-.3-2.4-.5-.6-.3-1-.7-1.5-1.5-.2-.5-.4-1.1-.5-2.4-.1-1.3-.1-1.7-.1-4.9s0-3.6.1-4.9c.1-1.2.3-1.9.5-2.4.3-.6.7-1 1.5-1.5.5-.2 1.1-.4 2.4-.5 1.3-.1 1.7-.1 4.9-.1zm0-2.2C8.7 0 8.3 0 7 0S5.3 0 4.1.1c-1.4.1-2.3.3-3.2.6-.9.3-1.6.8-2.3 1.5C-1 3.5-.5 4.3 0 5.2c.3.9.5 1.8.6 3.2C.7 9.7.7 10.1.7 12s0 2.3-.1 3.6c-.1 1.4-.3 2.3-.6 3.2-.3.9-.8 1.6-1.5 2.3-.7.7-1.5 1.2-2.3 1.5-.9.3-1.8.5-3.2.6-1.3.1-1.7.1-3.6.1s-2.3 0-3.6-.1c-1.4-.1-2.3-.3-3.2-.6-.9-.3-1.6-.8-2.3-1.5-.7-.7-1.2-1.5-1.5-2.3-.3-.9-.5-1.8-.6-3.2C.7 14.3.7 13.9.7 12s0-2.3.1-3.6c.1-1.4.3-2.3.6-3.2.3-.9.8-1.6 1.5-2.3.7-.7 1.5-1.2 2.3-1.5.9-.3 1.8-.5 3.2-.6C5.3.7 5.7.7 7 .7S8.7.7 10 .7c1.3.1 1.7.1 4.9.1s3.6 0 4.9-.1c1.2-.1 1.9-.3 2.4-.5.6-.3 1-.7 1.5-1.5.2-.5.4-1.1.5-2.4.1-1.3.1-1.7.1-4.9s0-3.6-.1-4.9c-.1-1.2-.3-1.9-.5-2.4-.3-.6-.7-1-1.5-1.5-.5-.2-1.1-.4-2.4-.5C15.7.7 15.3.7 12 .7zM12 5.8a6.2 6.2 0 100 12.4 6.2 6.2 0 000-12.4zm0 10.2a4 4 0 110-8 4 4 0 010 8zm6.4-11.6a1.44 1.44 0 11-2.88 0 1.44 1.44 0 012.88 0z"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endforeach




                </div>
            </div>
        </section>

        <!-- Regional Teams -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Heading -->
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                        Our <span class="text-primary">Regional</span> Experts
                    </h2>
                    <div class="mx-auto w-24 h-1 bg-gradient-to-r from-primary to-primary-dark rounded-full mb-6"></div>
                    <p class="text-gray-500 max-w-3xl mx-auto text-lg">
                        Our local experts have in-depth knowledge of their markets to provide you with the best advice and options.
                    </p>
                </div>

                <!-- Regional Teams Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Patna Team -->
                    <div class="bg-gray-50 rounded-xl p-8 shadow-md hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-center mb-6">
                            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center text-primary mr-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">Patna</h3>
                                <p class="text-sm text-gray-500">12 Agents</p>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-4">
                            Our Patna team specializes in luxury apartments, sea-facing properties, and commercial spaces in India's financial capital.
                        </p>
                        <a href="#" class="text-primary font-medium flex items-center">
                            Meet the Patna Team
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>

                    <!-- Delhi Team -->
                    <div class="bg-gray-50 rounded-xl p-8 shadow-md hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-center mb-6">
                            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center text-primary mr-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">Jharkhand</h3>
                                <p class="text-sm text-gray-500">15 Agents</p>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-4">
                            Our Jharkhand team is well-versed in the latest trends in residential and commercial real estate, ensuring you get the best deals.
                        </p>
                        <a href="#" class="text-primary font-medium flex items-center">
                            Meet the Jharkhand Team
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                    <!-- Bangalore Team -->
                    <div class="bg-gray-50 rounded-xl p-8 shadow-md hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-center mb-6">
                            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center text-primary mr-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">Kolkata</h3>
                                <p class="text-sm text-gray-500">15 Agents</p>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-4">
                            Our Kolkata team is well-versed in the latest trends in residential and commercial real estate, ensuring you get the best deals.
                        </p>
                        <a href="#" class="text-primary font-medium flex items-center">
                            Meet the Kolkata Team
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>


        </section>
    </main>
    </body>
    @endsection



