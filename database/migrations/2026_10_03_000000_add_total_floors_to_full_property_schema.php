<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Project-level "Total no. of floors" shown on the public property page.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('full_property_schema', 'total_floors')) {
            Schema::table('full_property_schema', function (Blueprint $table) {
                $table->unsignedSmallInteger('total_floors')->nullable()->after('project_status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('full_property_schema', 'total_floors')) {
            Schema::table('full_property_schema', function (Blueprint $table) {
                $table->dropColumn('total_floors');
            });
        }
    }
};
