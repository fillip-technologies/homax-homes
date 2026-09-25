<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $flags = ['all_property', 'featured_image', 'add_now', 'property_image', 'our_team', 'blog'];

    public function up(): void
    {
        // Flags were nullable strings; normalise the values, then make them real booleans.
        foreach ($this->flags as $flag) {
            DB::table('user_permission')->where(function ($q) use ($flag) {
                $q->whereNull($flag)->orWhereIn($flag, ['', '0']);
            })->update([$flag => '0']);
            DB::table('user_permission')->where($flag, '!=', '0')->update([$flag => '1']);
        }

        Schema::table('user_permission', function (Blueprint $table) {
            foreach ($this->flags as $flag) {
                $table->boolean($flag)->default(false)->nullable(false)->change();
            }
            $table->boolean('manage_users')->default(false)->after('our_team');
        });

        // Managing users used to be tied to the our_team flag; keep that access for existing rows.
        DB::table('user_permission')->where('our_team', 1)->update(['manage_users' => 1]);

        // From now on an admin with no row has full access. Existing admins without a row could
        // only see the basics before, so give them an all-off row to keep it that way.
        $withRow = DB::table('user_permission')->pluck('user_id');
        DB::table('users')->where('role', 'admin')->whereNotIn('id', $withRow)->pluck('id')->each(function ($id) {
            DB::table('user_permission')->insert(['user_id' => $id, 'created_at' => now(), 'updated_at' => now()]);
        });

        Schema::table('our_team', function (Blueprint $table) {
            // Login passwords live in users.password only.
            $table->dropColumn('password');
            $table->string('user_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('our_team', function (Blueprint $table) {
            $table->string('password')->nullable();
            $table->string('user_id')->nullable(false)->change();
        });

        Schema::table('user_permission', function (Blueprint $table) {
            $table->dropColumn('manage_users');
            foreach ($this->flags as $flag) {
                $table->string($flag)->nullable()->change();
            }
        });
    }
};
