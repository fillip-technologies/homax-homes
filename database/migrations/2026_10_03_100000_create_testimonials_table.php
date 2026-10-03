<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Cards in the home page "What Our Clients Say" section, editable from
 * Admin > Site Settings. Seeded with the three cards that used to be hard-coded.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subtitle')->nullable();   // e.g. "Project Enquiry"
            $table->text('quote');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->string('caption')->nullable();    // small text next to the stars
            $table->string('photo')->nullable();      // upload path or full URL
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $now = now();
        DB::table('testimonials')->insert([
            [
                'name' => 'Sample Homebuyer', 'subtitle' => 'Project Enquiry',
                'quote' => 'The project information was easy to review, and the team helped me understand the available home options clearly.',
                'rating' => 5, 'caption' => 'Sample feedback',
                'photo' => 'https://randomuser.me/api/portraits/women/43.jpg', 'sort_order' => 1,
            ],
            [
                'name' => 'First-Time Buyer', 'subtitle' => 'Home Search',
                'quote' => 'As a first-time home buyer, I appreciated having clear details and practical guidance before planning a visit.',
                'rating' => 5, 'caption' => 'Sample feedback',
                'photo' => 'https://randomuser.me/api/portraits/men/32.jpg', 'sort_order' => 2,
            ],
            [
                'name' => 'Project Visitor', 'subtitle' => 'Site Visit',
                'quote' => 'The process helped me compare locations, layouts, and next steps without feeling rushed.',
                'rating' => 5, 'caption' => 'Sample feedback',
                'photo' => 'https://randomuser.me/api/portraits/women/68.jpg', 'sort_order' => 3,
            ],
        ]);
        DB::table('testimonials')->update(['is_active' => true, 'created_at' => $now, 'updated_at' => $now]);
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
