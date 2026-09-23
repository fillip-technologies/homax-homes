<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('full_property_schema', function (Blueprint $table) {
            $table->date('possession_date')->nullable();
        });

        // Carry over existing "available from" dates before the column is dropped.
        DB::table('full_property_schema')
            ->whereNotNull('available_from')
            ->update(['possession_date' => DB::raw('available_from')]);

        Schema::table('full_property_schema', function (Blueprint $table) {
            $table->dropColumn(['availability', 'available_from', 'preferred_tenants']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('full_property_schema', function (Blueprint $table) {
            $table->dropColumn('possession_date');
            $table->enum('availability', ['Immediate', 'After Date', 'Negotiable'])->default('Immediate');
            $table->date('available_from')->nullable();
            $table->enum('preferred_tenants', ['Family', 'Professionals', 'Students', 'Company', 'Anyone'])->nullable();
        });
    }
};
