<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('our_team', function (Blueprint $table) {
            $table->id();
            $table->string('employee_name');
            $table->string('designation');
            $table->string('user_id')->unique(); // consider 'email' if it's email-based
            $table->string('password');
            $table->date('joining_date')->nullable();
            $table->string('employee_image')->nullable(); // path or URL
            $table->string('fb_id_link')->nullable();
            $table->string('twitter_link')->nullable();
            $table->string('linkedin_link')->nullable();
            $table->string('instagram_link')->nullable();
            $table->boolean('status')->default(1); // 1 = Active, 0 = Inactive
            $table->timestamps();
        });

        Schema::create('user_permission', function (Blueprint $table) {
            $table->id();
            $table->string('all_property')->nullable();       // e.g. label or section title
            $table->string('featured_image')->nullable();     // file path or URL
            $table->string('add_now')->nullable();            // e.g. a call-to-action text
            $table->string('property_image')->nullable();     // file path or URL
            $table->string('our_team')->nullable();           // reference text or content block
            $table->string('blog')->nullable();               // section name or summary
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('our_team');
        Schema::dropIfExists('user_permission');
    }
};
