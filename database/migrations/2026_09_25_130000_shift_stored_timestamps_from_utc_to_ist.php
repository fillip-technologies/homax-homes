<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The app used to run in UTC, so every created_at / updated_at / deleted_at was stored as a UTC
 * wall-clock string. The app timezone is now Asia/Kolkata (config/app.php), which would make all
 * of those read 5h30m too early. Shift them once so existing rows keep showing the right moment.
 *
 * Only tables this app writes are touched; date-only columns (joining_date, possession_date)
 * are calendar dates, not instants, and stay as they are.
 */
return new class extends Migration
{
    private const OFFSET_MINUTES = 330;

    private array $tables = [
        'users',
        'our_team',
        'user_permission',
        'full_property_schema',
        'property_details',
        'property_images',
        'property_inquiries',
        'similar_properties',
    ];

    private array $columns = ['created_at', 'updated_at', 'deleted_at', 'email_verified_at'];

    public function up(): void
    {
        $this->shift(self::OFFSET_MINUTES);
    }

    public function down(): void
    {
        $this->shift(-self::OFFSET_MINUTES);
    }

    private function shift(int $minutes): void
    {
        $driver = DB::connection()->getDriverName();

        foreach ($this->tables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            foreach ($this->columns as $column) {
                if (!Schema::hasColumn($table, $column)) {
                    continue;
                }

                $expression = match ($driver) {
                    'sqlite' => DB::raw("datetime(\"$column\", '" . sprintf('%+d', $minutes) . " minutes')"),
                    default => DB::raw("DATE_ADD(`$column`, INTERVAL $minutes MINUTE)"),
                };

                DB::table($table)->whereNotNull($column)->update([$column => $expression]);
            }
        }
    }
};
