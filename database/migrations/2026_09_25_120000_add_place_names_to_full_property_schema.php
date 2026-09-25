<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Optional names for the six fixed nearby/connectivity places, keyed by
 * bazar, hospital, school, bus_stand, junction, airport
 * (e.g. {"hospital": "Apollo Hospital"}), so the public page can show the
 * real place next to the distance.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('full_property_schema', function (Blueprint $table) {
            $table->json('place_names')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('full_property_schema', function (Blueprint $table) {
            $table->dropColumn('place_names');
        });
    }
};
