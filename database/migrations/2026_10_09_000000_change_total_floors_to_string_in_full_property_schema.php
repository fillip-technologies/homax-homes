<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('full_property_schema', 'total_floors')) {
            Schema::table('full_property_schema', function (Blueprint $table) {
                $table->string('total_floors', 100)->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('full_property_schema', 'total_floors')) {
            Schema::table('full_property_schema', function (Blueprint $table) {
                $table->unsignedSmallInteger('total_floors')->nullable()->change();
            });
        }
    }
};
