<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Project-level "Land Parcel" shown on admin forms and public property page.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('full_property_schema', 'land_parcel')) {
            Schema::table('full_property_schema', function (Blueprint $table) {
                $table->string('land_parcel', 100)->nullable()->after('total_floors');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('full_property_schema', 'land_parcel')) {
            Schema::table('full_property_schema', function (Blueprint $table) {
                $table->dropColumn('land_parcel');
            });
        }
    }
};
