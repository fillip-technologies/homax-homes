<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The people shown under the text on the public About Us page, editable from
 * Admin > About Page People. Seeded with three placeholders to be overwritten.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role')->nullable();
            $table->text('description')->nullable();
            $table->string('photo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $now = now();
        foreach ([1 => 'Founder', 2 => 'Co-Founder', 3 => 'Head of Sales'] as $order => $role) {
            DB::table('about_members')->insert([
                'name' => 'Team Member ' . $order,
                'role' => $role,
                'description' => 'Add a short introduction for this person from the admin panel.',
                'is_active' => true,
                'sort_order' => $order,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('about_members');
    }
};
