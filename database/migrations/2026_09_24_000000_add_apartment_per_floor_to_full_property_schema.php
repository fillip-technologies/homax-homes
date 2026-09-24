<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('full_property_schema', 'apartment_per_floor')) {
            Schema::table('full_property_schema', function (Blueprint $table) {
                $table->string('apartment_per_floor', 100)->nullable();
            });
        }

        // Carry over the first per-configuration value each property already has.
        $rows = DB::table('property_details')
            ->whereNotNull('apartment_per_floor')
            ->where('apartment_per_floor', '!=', '')
            ->orderBy('id')
            ->get(['property_id', 'apartment_per_floor'])
            ->unique('property_id');
        foreach ($rows as $row) {
            DB::table('full_property_schema')
                ->where('id', $row->property_id)
                ->update(['apartment_per_floor' => $row->apartment_per_floor]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('full_property_schema', 'apartment_per_floor')) {
            Schema::table('full_property_schema', function (Blueprint $table) {
                $table->dropColumn('apartment_per_floor');
            });
        }
    }
};
