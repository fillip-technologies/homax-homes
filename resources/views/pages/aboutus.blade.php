@extends('layout.layout')
@section('title', 'Meet the Homax Homes Team - Real Estate Experts Across India')
@section('description', 'Get to know the passionate and experienced professionals behind Homax Homes. Our team is committed to helping you buy, sell, or rent properties with confidence.')
@section('keywords', 'Homax Homes team, real estate experts, property consultants, about our team, real estate professionals')
@section('author', 'Your Name')

{{-- Open Graph (Facebook, LinkedIn, etc.) --}}
@section('og_title', 'Meet the Homax Homes Team - Real Estate Experts Across India')
@section('og_description', 'Meet the experts driving Homax Homes forward. Learn about our experienced team and their dedication to exceptional property services.')
@section('og_image', asset('images/team-hero.jpg')) {{-- Replace with your actual team hero image --}}
@section('og_url', url()->current())
@section('og_type', 'website')

{{-- Twitter Meta --}}
@section('twitter_card', 'summary_large_image')
@section('twitter_title', 'Meet the Homax Homes Team - Real Estate Experts Across India')
@section('twitter_description', 'Get to know the team behind Homax Homes - experts committed to guiding you through your real estate journey.')
@section('twitter_image', asset('images/team-hero.jpg')) {{-- Replace with your actual team hero image --}}
@section('twitter_site', '@yourtwitterhandle')
@section('twitter_creator', '@yourtwitterhandle')

{{-- Canonical URL --}}
@section('canonical', url()->current())

@section('content')
<style>
    :root {
            /* Primary Colors */
            --primary: #5146C7;
            --primary-dark: #4038A8;
            --primary-darker: #17113B;

            /* Neutral Colors */
            --gray-dark: #717271;
            --gray-light: #b1b2b1;
            --white: #ffffff;

            /* Additional Colors */
            --teal: #38b2ac;
            /* Keeping teal for some elements as accent */
            --red: #e53e3e;
            --yellow: #f6e05e;
            --purple: #805ad5;
            --blue: #4299e1;
            --green: #48bb78;
        }

        /* Text Colors */
        .text-primary {
            color: var(--primary);
        }

        .text-primary-dark {
            color: var(--primary-dark);
        }

        .text-primary-darker {
            color: var(--primary-darker);
        }

        .text-gray-dark {
            color: var(--gray-dark);
        }

        .text-gray-light {
            color: var(--gray-light);
        }

        /* Background Colors */
        .bg-primary {
            background-color: var(--primary);
        }

        .bg-primary-dark {
            background-color: var(--primary-dark);
        }

        .bg-primary-darker {
            background-color: var(--primary-darker);
        }

        .bg-gray-dark {
            background-color: var(--gray-dark);
        }

        .bg-gray-light {
            background-color: var(--gray-light);
        }

        /* Gradient Backgrounds */
        .bg-gradient-primary {
            background-image: linear-gradient(to right, var(--primary), var(--primary-dark));
        }

        .bg-gradient-primary-dark {
            background-image: linear-gradient(to right, var(--primary-dark), var(--primary-darker));
        }

        /* Border Colors */
        .border-primary {
            border-color: var(--primary);
        }

        .border-primary-dark {
            border-color: var(--primary-dark);
        }

        /* Hover States */
        .hover\:bg-primary:hover {
            background-color: var(--primary);
        }

        .hover\:bg-primary-dark:hover {
            background-color: var(--primary-dark);
        }

        .hover\:text-primary:hover {
            color: var(--primary);
        }

        /* Focus States */
        .focus\:ring-primary:focus {
            --tw-ring-color: var(--primary);
        }
</style>

<div class="min-h-screen bg-white">
    <!-- Hero Section -->
    <section class="relative h-96 md:h-screen/80 bg-cover bg-center flex items-center justify-center"
             style="background-image: url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80')">
        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

        <!-- Content -->
        <div class="relative z-10 text-center px-4 sm:px-8">
            <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
                About <span class="text-primary">Us</span>
            </h1>
            <p class="text-xl md:text-2xl text-white max-w-3xl mx-auto mb-8">
                Building trust in real estate through innovation, integrity, and exceptional service since 2008.
            </p>
            <a href="#our-mission" class="inline-block bg-primary hover:bg-primary-dark text-white px-8 py-3 rounded-full font-bold transition-all duration-300 transform hover:scale-105 shadow-lg">
                Explore Our Journey
            </a>
        </div>
    </section>

    <!-- Mission Section -->
    <section id="our-mission" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <!-- Image -->
                <div class="relative rounded-xl overflow-hidden shadow-2xl">
                    <img src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf" alt="Our Team" class="w-full h-auto">
                    <div class="absolute inset-0 bg-gradient-to-t from-primary/20 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 bg-primary/90 text-white p-6 text-center">
                        <h3 class="text-xl font-bold">15+ Years of Excellence</h3>
                        <p class="text-sm">Serving clients across India</p>
                    </div>
                </div>

                <!-- Content -->
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">
                        Our <span class="text-primary">Mission</span>
                    </h2>
                    <div class="w-24 h-1.5 bg-gradient-to-r from-[#5146C7] to-[#4038A8] rounded-full mb-8"></div>

                    <p class="text-lg text-gray-600 mb-6">
                        At Homax Homes, we're revolutionizing the real estate experience by combining cutting-edge technology with personalized service. Our mission is to make property transactions transparent, efficient, and stress-free for everyone.
                    </p>

                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="ml-4 text-gray-600">
                                <span class="font-semibold text-gray-800">Transparency:</span> We believe in complete honesty about every property, with verified listings and accurate information.
                            </p>
                        </div>

                        <div class="flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="ml-4 text-gray-600">
                                <span class="font-semibold text-gray-800">Innovation:</span> Leveraging technology to simplify property search, transactions, and management.
                            </p>
                        </div>

                        <div class="flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="ml-4 text-gray-600">
                                <span class="font-semibold text-gray-800">Client-Centric Approach:</span> Your needs come first, with personalized solutions and dedicated support.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-gradient-to-r from-[#5146C7] to-[#4038A8] text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <!-- Stat 1 -->
                <div class="p-6 transform hover:scale-105 transition-all duration-300">
                    <div class="text-4xl md:text-5xl font-bold mb-2">1,250+</div>
                    <div class="text-lg md:text-xl font-medium">Properties Listed</div>
                </div>

                <!-- Stat 2 -->
                <div class="p-6 transform hover:scale-105 transition-all duration-300">
                    <div class="text-4xl md:text-5xl font-bold mb-2">950+</div>
                    <div class="text-lg md:text-xl font-medium">Happy Clients</div>
                </div>

                <!-- Stat 3 -->
                <div class="p-6 transform hover:scale-105 transition-all duration-300">
                    <div class="text-4xl md:text-5xl font-bold mb-2">15+</div>
                    <div class="text-lg md:text-xl font-medium">Years Experience</div>
                </div>

                <!-- Stat 4 -->
                <div class="p-6 transform hover:scale-105 transition-all duration-300">
                    <div class="text-4xl md:text-5xl font-bold mb-2">25+</div>
                    <div class="text-lg md:text-xl font-medium">Cities Covered</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Journey -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                    Our <span class="text-primary">Journey</span>
                </h2>
                <div class="mx-auto w-24 h-1.5 bg-gradient-to-r from-[#5146C7] to-[#4038A8] rounded-full mb-6"></div>
                <p class="text-gray-600 max-w-3xl mx-auto text-lg">
                    From humble beginnings to becoming one of the most trusted names in Indian real estate.
                </p>
            </div>

            <!-- Timeline -->
            <div class="max-w-3xl mx-auto">
                <!-- Timeline Item 1 -->
                <div class="relative timeline-item pl-8 pb-12">
                    <div class="absolute left-0 top-0 w-4 h-4 rounded-full bg-primary border-4 border-white z-10"></div>
                    <div class="bg-white p-6 rounded-xl shadow-md">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-xl font-bold text-gray-800">Founded in Patna</h3>
                            <span class="bg-primary/10 text-primary px-3 py-1 rounded-full text-sm font-medium">2008</span>
                        </div>
                        <p class="text-gray-600">
                            Started as a small real estate consultancy with just 3 employees, serving local clients in Bihar.
                        </p>
                    </div>
                </div>

                <!-- Timeline Item 2 -->
                <div class="relative timeline-item pl-8 pb-12">
                    <div class="absolute left-0 top-0 w-4 h-4 rounded-full bg-primary border-4 border-white z-10"></div>
                    <div class="bg-white p-6 rounded-xl shadow-md">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-xl font-bold text-gray-800">First Expansion</h3>
                            <span class="bg-primary/10 text-primary px-3 py-1 rounded-full text-sm font-medium">2012</span>
                        </div>
                        <p class="text-gray-600">
                            Expanded operations to Jharkhand and West Bengal, establishing our reputation in Eastern India.
                        </p>
                    </div>
                </div>

                <!-- Timeline Item 3 -->
                <div class="relative timeline-item pl-8 pb-12">
                    <div class="absolute left-0 top-0 w-4 h-4 rounded-full bg-primary border-4 border-white z-10"></div>
                    <div class="bg-white p-6 rounded-xl shadow-md">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-xl font-bold text-gray-800">Tech Transformation</h3>
                            <span class="bg-primary/10 text-primary px-3 py-1 rounded-full text-sm font-medium">2016</span>
                        </div>
                        <p class="text-gray-600">
                            Launched our digital platform, revolutionizing property search with virtual tours and AI recommendations.
                        </p>
                    </div>
                </div>

                <!-- Timeline Item 4 -->
                <div class="relative timeline-item pl-8 pb-12">
                    <div class="absolute left-0 top-0 w-4 h-4 rounded-full bg-primary border-4 border-white z-10"></div>
                    <div class="bg-white p-6 rounded-xl shadow-md">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-xl font-bold text-gray-800">National Presence</h3>
                            <span class="bg-primary/10 text-primary px-3 py-1 rounded-full text-sm font-medium">2020</span>
                        </div>
                        <p class="text-gray-600">
                            Expanded to 10 major Indian cities with over 100 employees, becoming a pan-India real estate solutions provider.
                        </p>
                    </div>
                </div>

                <!-- Timeline Item 5 -->
                <div class="relative timeline-item pl-8">
                    <div class="absolute left-0 top-0 w-4 h-4 rounded-full bg-primary border-4 border-white z-10"></div>
                    <div class="bg-white p-6 rounded-xl shadow-md">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-xl font-bold text-gray-800">Today</h3>
                            <span class="bg-primary/10 text-primary px-3 py-1 rounded-full text-sm font-medium">2023</span>
                        </div>
                        <p class="text-gray-600">
                            Serving clients across 25+ cities with innovative solutions and maintaining 98% customer satisfaction.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Values -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                    Our <span class="text-primary">Values</span>
                </h2>
                <div class="mx-auto w-24 h-1.5 bg-gradient-to-r from-[#5146C7] to-[#4038A8] rounded-full mb-6"></div>
                <p class="text-gray-600 max-w-3xl mx-auto text-lg">
                    The principles that guide every decision we make and every interaction we have.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Value 1 -->
                <div class="value-card bg-white border-2 border-gray-100 rounded-xl p-8 text-center transition-all duration-300 hover:-translate-y-2">
                    <div class="w-20 h-20 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Integrity</h3>
                    <p class="text-gray-600">
                        We operate with complete honesty and transparency in all our dealings.
                    </p>
                </div>

                <!-- Value 2 -->
                <div class="value-card bg-white border-2 border-gray-100 rounded-xl p-8 text-center transition-all duration-300 hover:-translate-y-2">
                    <div class="w-20 h-20 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Innovation</h3>
                    <p class="text-gray-600">
                        Constantly evolving to bring you the most advanced real estate solutions.
                    </p>
                </div>

                <!-- Value 3 -->
                <div class="value-card bg-white border-2 border-gray-100 rounded-xl p-8 text-center transition-all duration-300 hover:-translate-y-2">
                    <div class="w-20 h-20 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Community</h3>
                    <p class="text-gray-600">
                        We believe in building communities, not just selling properties.
                    </p>
                </div>

                <!-- Value 4 -->
                <div class="value-card bg-white border-2 border-gray-100 rounded-xl p-8 text-center transition-all duration-300 hover:-translate-y-2">
                    <div class="w-20 h-20 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Excellence</h3>
                    <p class="text-gray-600">
                        Striving for the highest standards in everything we do.
                    </p>
                </div>
            </div>
        </div>
    </section>


</div>
<script>
  // Add any JavaScript functionality here if needed
</script>
</body>
</html>
@endsection



