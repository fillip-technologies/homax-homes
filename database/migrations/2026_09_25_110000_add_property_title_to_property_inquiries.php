<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Keep the property's name on the enquiry itself. The admin list then reads well
 * without a join, and the name survives the property being renamed or deleted.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('property_inquiries', function (Blueprint $table) {
            $table->string('property_title')->nullable()->after('property_id');
        });

        // Backfill existing rows (including properties that were soft-deleted since).
        DB::table('property_inquiries')->where('property_id', '>', 0)->orderBy('id')->get()->each(function ($row) {
            $title = DB::table('full_property_schema')->where('id', $row->property_id)->value('title');

            if ($title !== null) {
                DB::table('property_inquiries')->where('id', $row->id)->update(['property_title' => $title]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('property_inquiries', function (Blueprint $table) {
            $table->dropColumn('property_title');
        });
    }
};
