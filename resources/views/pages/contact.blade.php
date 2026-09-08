@extends('layout.layout')

@section('title', 'Contact Us')
@section('description', 'Contact us for any inquiries or support.')
@section('keywords', 'contact, support, inquiries')
@section('author', 'Your Name')
@section('og_title', 'Contact Us')
@section('og_description', 'Get in touch with us for any questions or support.')
@section('og_image', asset('images/contact.jpg'))
@section('og_url', url()->current())
@section('og_type', 'website')
@section('twitter_card', 'summary_large_image')
@section('twitter_title', 'Contact Us')
@section('twitter_description', 'Reach out to us for any inquiries or support.')
@section('twitter_image', asset('images/contact.jpg'))
@section('twitter_site', '@yourtwitterhandle')
@section('twitter_creator', '@yourtwitterhandle')
@section('canonical', url()->current())


@section('content')

<style>
  :root {
    /* Primary Colors */
    --primary:#5146C7;
    --primary-dark: #4038A8;
    --primary-darker: #000000;

    /* Neutral Colors */
    --gray-dark: #717271;
    --gray-light: #b1b2b1;
    --white: #ffffff;

    /* Additional Colors */
    --teal: #38b2ac; /* Keeping teal for some elements as accent */
    --red: #e53e3e;
    --yellow: #f6e05e;
    --purple: #805ad5;
    --blue: #4299e1;
    --green: #48bb78;
  }

  /* Text Colors */
  .text-primary { color: var(--primary); }
  .text-primary-dark { color: var(--primary-dark); }
  .text-primary-darker { color: var(--primary-darker); }
  .text-gray-dark { color: var(--gray-dark); }
  .text-gray-light { color: var(--gray-light); }

  /* Background Colors */
  .bg-primary { background-color: var(--primary); }
  .bg-primary-dark { background-color: var(--primary-dark); }
  .bg-primary-darker { background-color: var(--primary-darker); }
  .bg-gray-dark { background-color: var(--gray-dark); }
  .bg-gray-light { background-color: var(--gray-light); }

  /* Gradient Backgrounds */
  .bg-gradient-primary { background-image: linear-gradient(to right, var(--primary), var(--primary-dark)); }
  .bg-gradient-primary-dark { background-image: linear-gradient(to right, var(--primary-dark), var(--primary-darker)); }

  /* Border Colors */
  .border-primary { border-color: var(--primary); }
  .border-primary-dark { border-color: var(--primary-dark); }

  /* Hover States */
  .hover\:bg-primary:hover { background-color: var(--primary); }
  .hover\:bg-primary-dark:hover { background-color: var(--primary-dark); }
  .hover\:text-primary:hover { color: var(--primary); }

  /* Focus States */
  .focus\:ring-primary:focus { --tw-ring-color: var(--primary); }
</style>

<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-[#5146C7] to-[#4038A8] py-20 text-white">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Contact Our Real Estate Team</h1>
            <p class="text-xl max-w-2xl mx-auto">Whether you're buying, selling, or just exploring options, we're here to help with all your property needs.</p>
        </div>
    </div>

    <!-- Contact Form Section -->
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-12">
            <!-- Contact Form -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Send Us a Message</h2>
                <form action="#" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label for="name" class="block text-gray-700 mb-2">Full Name</label>
                        <input type="text" id="name" name="name" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition">
                    </div>
                    <div>
                        <label for="email" class="block text-gray-700 mb-2">Email Address</label>
                        <input type="email" id="email" name="email" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition">
                    </div>
                    <div>
                        <label for="phone" class="block text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" id="phone" name="phone"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition">
                    </div>
                    <div>
                        <label for="interest" class="block text-gray-700 mb-2">I'm interested in</label>
                        <select id="interest" name="interest"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition">
                            <option value="buying">Buying a property</option>
                            <option value="selling">Selling a property</option>

                        </select>
                    </div>
                    <div>
                        <label for="message" class="block text-gray-700 mb-2">Your Message</label>
                        <textarea id="message" name="message" rows="5" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition"></textarea>
                    </div>
                    <button type="submit"
                            class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-3 px-4 rounded-lg transition duration-300 transform hover:scale-105">
                        Send Message
                    </button>
                </form>
            </div>

            <!-- Contact Info -->
            <div class="space-y-8">
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Contact Information</h2>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="bg-primary/10 p-3 rounded-full mr-4">
                                <i class="fas fa-map-marker-alt text-primary"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Office Address</h3>
                                <p class="text-gray-600">Mumbai, Maharashtra, India</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-primary/10 p-3 rounded-full mr-4">
                                <i class="fas fa-phone-alt text-primary"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Phone Numbers</h3>
                                <p class="text-gray-600">Sales: (123) 456-7890<br>Support: (123) 456-7891</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-primary/10 p-3 rounded-full mr-4">
                                <i class="fas fa-envelope text-primary"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Email Addresses</h3>
                                <p class="text-gray-600">sales@homaxhomes.com<br>support@homaxhomes.com</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Office Hours -->
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Office Hours</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-700">Monday - Friday</span>
                            <span class="font-medium">9:00 AM - 6:00 PM</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">Saturday</span>
                            <span class="font-medium">10:00 AM - 4:00 PM</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">Sunday</span>
                            <span class="font-medium">Closed</span>
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="bg-red-50 border border-red-100 rounded-xl p-6">
                    <div class="flex items-start">
                        <div class="bg-red-100 p-3 rounded-full mr-4">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-red-700 mb-2">Emergency After Hours</h3>
                            <p class="text-red-600 mb-3">For urgent property matters outside office hours</p>
                            <a href="tel:+11234567892" class="font-bold text-red-700 hover:underline">(123) 456-7892</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Section -->
    <div class="container mx-auto px-4 pb-16">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <iframe src="https://maps.google.com/maps?q=Mumbai%2C%20Maharashtra&z=11&output=embed"
                    width="100%"
                    height="450"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    title="Map of Mumbai, Maharashtra"
                    class="rounded-xl"></iframe>


        </div>
    </div>
</div>
@endsection


