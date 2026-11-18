<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Careers: job openings shown on /careers (seeded with the four roles that used to
 * be hardcoded on the old /join-us page), the applications sent from it, and the
 * careers permission flag for Admin > Careers.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Older databases (and the proptrue SQL dump) still carry job_openings / job_applications
        // tables from a removed 2025 feature, with a different layout and test rows. Nothing reads
        // them; keep them aside rather than dropping them.
        if (Schema::hasTable('job_applications') && !Schema::hasColumn('job_applications', 'first_name')) {
            Schema::rename('job_applications', 'job_applications_legacy');
        }
        if (Schema::hasTable('job_openings') && !Schema::hasColumn('job_openings', 'slug')) {
            Schema::rename('job_openings', 'job_openings_legacy');
        }

        Schema::create('job_openings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('department')->nullable();
            $table->string('location')->nullable();
            $table->string('employment_type', 30)->default('full-time');
            $table->string('experience')->nullable();
            $table->string('salary')->nullable();
            $table->string('highlights')->nullable(); // comma separated, shown as chips
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('user_permission', function (Blueprint $table) {
            $table->boolean('careers')->default(false);
        });

        // Applications from /careers. job_opening_id is null for a general application; the title
        // is kept so the row still reads right once the opening is deleted. No foreign key: the
        // legacy table above still owns the job_applications_job_opening_id_foreign name.
        // Files are paths on the private "local" disk (storage/app/private).
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_opening_id')->nullable()->index();
            $table->string('job_title')->nullable();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email');
            $table->string('phone', 50)->nullable();
            $table->string('resume_path');
            $table->string('cover_letter_path')->nullable();
            $table->boolean('terms_accepted')->default(false);
            $table->timestamps();
        });

        // Admins who can manage users are full admins; keep Careers reachable for them.
        DB::table('user_permission')->where('manage_users', 1)->update(['careers' => 1]);

        $now = now();
        $roles = [
            ['Real Estate Agent', 'Sales', 'full-time', 'Commission Based, Flexible Hours, Training Provided',
                'Help clients buy, sell, and rent properties while building your own successful real estate business.'],
            ['Property Manager', 'Operations', 'full-time', 'Salary + Bonus, Health Benefits, Weekends Off',
                'Oversee rental properties, handle tenant relations, and ensure properties are well-maintained.'],
            ['Marketing Specialist', 'Marketing', 'full-time', 'Creative Role, Health Benefits, Growth Potential',
                'Develop and implement marketing strategies to promote our properties and brand.'],
            ['Real Estate Intern', 'Various Departments', 'internship', 'Learning Opportunity, Flexible Hours, Mentorship',
                'Gain hands-on experience in the real estate industry with our comprehensive internship program.'],
        ];

        foreach ($roles as $i => [$title, $department, $type, $highlights, $summary]) {
            DB::table('job_openings')->insert([
                'title' => $title,
                'slug' => \Illuminate\Support\Str::slug($title),
                'department' => $department,
                'location' => 'Mumbai',
                'employment_type' => $type,
                'highlights' => $highlights,
                'summary' => $summary,
                'description' => null, // the summary is shown; add a full description from admin
                'is_active' => true,
                'sort_order' => $i + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');

        Schema::table('user_permission', function (Blueprint $table) {
            $table->dropColumn('careers');
        });

        Schema::dropIfExists('job_openings');

        if (Schema::hasTable('job_openings_legacy')) {
            Schema::rename('job_openings_legacy', 'job_openings');
        }
        if (Schema::hasTable('job_applications_legacy')) {
            Schema::rename('job_applications_legacy', 'job_applications');
        }
    }
};
