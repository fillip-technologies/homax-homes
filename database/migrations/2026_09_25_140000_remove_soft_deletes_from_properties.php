<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Deleting a property is now permanent. Properties that were soft-deleted are removed for good
 * (with anything still pointing at them), then the deleted_at column is dropped.
 * Enquiries keep the property name they saved (property_title), so the enquiry list still reads well.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('full_property_schema', 'deleted_at')) {
            return;
        }

        $ids = DB::table('full_property_schema')->whereNotNull('deleted_at')->pluck('id');

        if ($ids->isNotEmpty()) {
            DB::table('property_images')->whereIn('property_id', $ids)->delete();
            DB::table('property_details')->whereIn('property_id', $ids)->delete();
            DB::table('similar_properties')
                ->whereIn('property_id', $ids)
                ->orWhereIn('similar_property_id', $ids)
                ->delete();
            DB::table('full_property_schema')->whereIn('id', $ids)->delete();
        }

        Schema::table('full_property_schema', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('full_property_schema', 'deleted_at')) {
            Schema::table('full_property_schema', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }
};
