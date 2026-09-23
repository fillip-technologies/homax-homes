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
        Schema::table('property_details', function (Blueprint $table) {
            if (!Schema::hasColumn('property_details', 'document')) {
                $table->string('document')->nullable()->after('price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_details', function (Blueprint $table) {
            if (Schema::hasColumn('property_details', 'document')) {
                $table->dropColumn('document');
            }
        });
    }
};
