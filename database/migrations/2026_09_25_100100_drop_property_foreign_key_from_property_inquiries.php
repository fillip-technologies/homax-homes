<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * General leads (Contact, Join Us, Associate) are stored with property_id = 0, which
 * a foreign key on full_property_schema rejects. Databases created by the migrations
 * had that key; the live database did not. Drop it wherever it exists.
 */
return new class extends Migration
{
    public function up(): void
    {
        $hasKey = collect(Schema::getForeignKeys('property_inquiries'))
            ->contains(fn ($fk) => $fk['columns'] === ['property_id']);

        if ($hasKey) {
            Schema::table('property_inquiries', function (Blueprint $table) {
                $table->dropForeign(['property_id']);
            });
        }
    }

    public function down(): void
    {
        // Intentionally empty: restoring the key would break stored general leads (property_id = 0).
    }
};
