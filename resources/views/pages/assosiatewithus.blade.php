@extends('layout.layout')

@section('title', 'Join Our Team')
@section('description', 'Explore career opportunities with our real estate team.')
@section('keywords', 'careers, jobs, real estate jobs, join our team')
@section('author', 'Your Name')
@section('og_title', 'Join Our Real Estate Team')
@section('og_description', 'Exciting career opportunities in the real estate industry.')
@section('og_image', asset('images/join-us.jpg'))
@section('og_url', url()->current())
@section('og_type', 'website')
@section('twitter_card', 'summary_large_image')
@section('twitter_title', 'Join Our Real Estate Team')
@section('twitter_description', 'Discover rewarding career opportunities with us.')
@section('twitter_image', asset('images/join-us.jpg'))
@section('twitter_site', '@yourtwitterhandle')
@section('twitter_creator', '@yourtwitterhandle')
@section('canonical', url()->current())

@section('content')
<style>
  :root {
    /* Primary Colors */
    --primary: #5146C7;
    --primary-dark: #4038A8;
    --primary-darker: #000000;

    /* Neutral Colors */
    --gray-dark: #717271;
    --gray-light: #b1b2b1;
    --white: #ffffff;
  }

  .job-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
  }
</style>

<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-[#5146C7] to-[#4038A8] py-20 text-white">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Associates With Us</h1>
            <p class="text-xl max-w-2xl mx-auto">Build your career with one of the fastest growing real estate companies in the region.</p>
            {{-- <div class="mt-8">
                <a href="#open-positions" class="bg-white text-primary-dark font-bold py-3 px-8 rounded-lg hover:bg-gray-100 transition duration-300 inline-block">
                    View Open Positions
                </a>
            </div> --}}
        </div>
    </div>

    <!-- Why Join Us Section -->
    <div class="container mx-auto px-4 py-16">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Why Join Our Team?</h2>
            <p class="text-gray-600 max-w-3xl mx-auto">We offer more than just jobs - we offer careers with purpose, growth opportunities, and a supportive environment.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Benefit 1 -->
            <div class="bg-white p-8 rounded-xl shadow-lg text-center">
                <div class="bg-primary/10 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                     <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <!-- User icon -->
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 14c4.418 0 8 1.79 8 4v2H4v-2c0-2.21 3.582-4 8-4z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 12a4 4 0 100-8 4 4 0 000 8z" />
                                            <!-- Speech bubble -->
                                        </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Career Growth</h3>
                <p class="text-gray-600">Structured career paths with regular training and mentorship programs to help you advance.</p>
            </div>

            <!-- Benefit 2 -->
            <div class="bg-white p-8 rounded-xl shadow-lg text-center">
                <div class="bg-primary/10 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <!-- User icon -->
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 14c4.418 0 8 1.79 8 4v2H4v-2c0-2.21 3.582-4 8-4z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 12a4 4 0 100-8 4 4 0 000 8z" />
                                            <!-- Speech bubble -->
                                        </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Competitive Compensation</h3>
                <p class="text-gray-600">Attractive salary packages with performance bonuses and commission structures.</p>
            </div>

            <!-- Benefit 3 -->
            <div class="bg-white p-8 rounded-xl shadow-lg text-center">
                <div class="bg-primary/10 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                     <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <!-- User icon -->
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 14c4.418 0 8 1.79 8 4v2H4v-2c0-2.21 3.582-4 8-4z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 12a4 4 0 100-8 4 4 0 000 8z" />
                                            <!-- Speech bubble -->
                                        </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Team Culture</h3>
                <p class="text-gray-600">Collaborative work environment with regular team-building activities and events.</p>
            </div>
        </div>
    </div>

    <!-- Application Form -->
    <div class="bg-gray-100 py-16">
        <div class="container mx-auto px-4 max-w-4xl">
            <div class="bg-white rounded-xl shadow-lg p-8 md:p-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Associates Now</h2>
                <p class="text-gray-600 mb-8">Fill out the form below and we'll get back to you soon.</p>

                <form class="space-y-6">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="first-name" class="block text-gray-700 mb-2">First Name</label>
                            <input type="text" id="first-name" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition">
                        </div>
                        <div>
                            <label for="last-name" class="block text-gray-700 mb-2">Last Name</label>
                            <input type="text" id="last-name" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition">
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="email" class="block text-gray-700 mb-2">Email Address</label>
                            <input type="email" id="email" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition">
                        </div>
                        <div>
                            <label for="phone" class="block text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" id="phone"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition">
                        </div>
                    </div>



                    <div>
                        <label for="cover-letter" class="block text-gray-700 mb-2">Message</label>
                        <textarea id="cover-letter" rows="5"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition"></textarea>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="agree" required
                               class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                        <label for="agree" class="ml-2 block text-gray-700">
                            I agree to the <a href="#" class="text-primary hover:underline">privacy policy</a> and consent to my data being processed.
                        </label>
                    </div>

                    <button type="submit"
                            class="w-full bg-[#5146C7] hover:bg-[#4038A8] text-white font-bold py-3 px-4 rounded-lg transition duration-300">
                        Submit Application
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


