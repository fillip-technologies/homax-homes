<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('full_property_schema', 'custom_nearby_places')) {
            Schema::table('full_property_schema', function (Blueprint $table) {
                // [{group: nearby|connectivity, label, icon, distance}, ...]
                $table->json('custom_nearby_places')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('full_property_schema', 'custom_nearby_places')) {
            Schema::table('full_property_schema', function (Blueprint $table) {
                $table->dropColumn('custom_nearby_places');
            });
        }
    }
};
