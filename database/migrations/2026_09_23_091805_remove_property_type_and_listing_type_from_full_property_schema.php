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
        Schema::table('full_property_schema', function (Blueprint $table) {
            $table->dropColumn(['property_type', 'listing_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('full_property_schema', function (Blueprint $table) {
            $table->string('property_type')->nullable();
            $table->string('listing_type')->nullable();
        });
    }
};
