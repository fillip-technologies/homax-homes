<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create property_details table
        if (!Schema::hasTable('property_details')) {
            Schema::create('property_details', function (Blueprint $table) {
                $table->id();
                $table->foreignId('property_id')->constrained('full_property_schema')->onDelete('cascade');
                $table->string('unit_type')->nullable(); // e.g. 1 BHK, 2 BHK, 3 BHK, Penthouse
                $table->integer('bedrooms')->nullable();
                $table->integer('bathrooms')->nullable();
                $table->integer('balconies')->nullable();
                $table->string('apartment_per_floor')->nullable();
                $table->decimal('carpet_area', 10, 2)->nullable();
                $table->decimal('super_area', 10, 2)->nullable();
                $table->decimal('plot_area', 10, 2)->nullable();
                $table->string('price')->nullable();
                $table->timestamps();
            });
        }

        // 2. Migrate existing property details from full_property_schema if table exists
        if (Schema::hasTable('full_property_schema')) {
            $existingProperties = DB::table('full_property_schema')->get();
            foreach ($existingProperties as $prop) {
                // Check if already has a record in property_details
                $alreadyExists = DB::table('property_details')->where('property_id', $prop->id)->exists();
                if (!$alreadyExists && ($prop->bedrooms || $prop->bathrooms || $prop->balconies || $prop->carpet_area || $prop->super_area || $prop->plot_area)) {
                    DB::table('property_details')->insert([
                        'property_id' => $prop->id,
                        'unit_type' => $prop->bedrooms ? ($prop->bedrooms . ' BHK') : null,
                        'bedrooms' => $prop->bedrooms,
                        'bathrooms' => $prop->bathrooms,
                        'balconies' => $prop->balconies,
                        'apartment_per_floor' => null,
                        'carpet_area' => $prop->carpet_area,
                        'super_area' => $prop->super_area,
                        'plot_area' => $prop->plot_area,
                        'price' => $prop->price,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // 3. Drop year_built, age_of_property, floors, floor_number from full_property_schema
            Schema::table('full_property_schema', function (Blueprint $table) {
                $columnsToDrop = [];
                foreach (['year_built', 'age_of_property', 'floors', 'floor_number'] as $column) {
                    if (Schema::hasColumn('full_property_schema', $column)) {
                        $columnsToDrop[] = $column;
                    }
                }
                if (!empty($columnsToDrop)) {
                    $table->dropColumn($columnsToDrop);
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
                if (!Schema::hasColumn('full_property_schema', 'floors')) {
                    $table->integer('floors')->nullable();
                }
                if (!Schema::hasColumn('full_property_schema', 'floor_number')) {
                    $table->integer('floor_number')->nullable();
                }
                if (!Schema::hasColumn('full_property_schema', 'year_built')) {
                    $table->integer('year_built')->nullable();
                }
                if (!Schema::hasColumn('full_property_schema', 'age_of_property')) {
                    $table->integer('age_of_property')->nullable();
                }
            });
        }

        Schema::dropIfExists('property_details');
    }
};
