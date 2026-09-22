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
                if (!Schema::hasColumn('full_property_schema', 'category')) {
                    $table->string('category', 50)->default('Residential')->after('property_type');
                }
                if (!Schema::hasColumn('full_property_schema', 'project_status')) {
                    $table->string('project_status', 50)->nullable()->after('property_status');
                }
                if (!Schema::hasColumn('full_property_schema', 'rera_id')) {
                    $table->string('rera_id')->nullable()->after('slug');
                }
                if (!Schema::hasColumn('full_property_schema', 'pre_launch_property')) {
                    $table->boolean('pre_launch_property')->default(false)->after('is_featured');
                }
            });
        }

        // Defensively ensure property_inquiries exists for clean database setups
        if (!Schema::hasTable('property_inquiries')) {
            Schema::create('property_inquiries', function (Blueprint $table) {
                $table->id();
                $table->foreignId('property_id')->constrained('full_property_schema')->onDelete('cascade');
                $table->string('name');
                $table->string('email')->nullable();
                $table->string('phone');
                $table->text('message')->nullable();
                $table->string('intent')->nullable();
                $table->string('source')->nullable();
                $table->boolean('terms_accepted')->default(false);
                $table->timestamps();
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
                $columns = [];
                if (Schema::hasColumn('full_property_schema', 'category')) {
                    $columns[] = 'category';
                }
                if (Schema::hasColumn('full_property_schema', 'project_status')) {
                    $columns[] = 'project_status';
                }
                if (Schema::hasColumn('full_property_schema', 'pre_launch_property')) {
                    $columns[] = 'pre_launch_property';
                }
                if (Schema::hasColumn('full_property_schema', 'rera_id')) {
                    $columns[] = 'rera_id';
                }
                if (!empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};
