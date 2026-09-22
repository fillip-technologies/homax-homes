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
        if (Schema::hasTable('full_property_schema')) {
            Schema::table('full_property_schema', function (Blueprint $table) {
                if (!Schema::hasColumn('full_property_schema', 'developer_name')) {
                    $table->string('developer_name')->nullable()->after('title');
                }
            });
        }

        if (Schema::hasTable('property_inquiries')) {
            Schema::table('property_inquiries', function (Blueprint $table) {
                if (!Schema::hasColumn('property_inquiries', 'intent')) {
                    $table->string('intent')->nullable()->after('message');
                }
                if (!Schema::hasColumn('property_inquiries', 'source')) {
                    $table->string('source')->nullable()->after('intent');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('full_property_schema')) {
            Schema::table('full_property_schema', function (Blueprint $table) {
                if (Schema::hasColumn('full_property_schema', 'developer_name')) {
                    $table->dropColumn('developer_name');
                }
            });
        }

        if (Schema::hasTable('property_inquiries')) {
            Schema::table('property_inquiries', function (Blueprint $table) {
                $columns = [];
                if (Schema::hasColumn('property_inquiries', 'source')) {
                    $columns[] = 'source';
                }
                if (Schema::hasColumn('property_inquiries', 'intent')) {
                    $columns[] = 'intent';
                }
                if (!empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};
