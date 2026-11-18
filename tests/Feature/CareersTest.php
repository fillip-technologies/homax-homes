<?php

namespace Tests\Feature;

use App\Models\JobOpening;
use App\Models\PropertyInquiry;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CareersTest extends TestCase
{
    use RefreshDatabase;

    private function opening(array $attrs = []): JobOpening
    {
        $title = $attrs['title'] ?? 'Sales Lead';

        return JobOpening::create($attrs + [
            'title' => $title,
            'slug' => JobOpening::uniqueSlug($title),
            'department' => 'Sales',
            'employment_type' => 'full-time',
            'summary' => 'Lead the sales team.',
            'is_active' => true,
        ]);
    }

    public function test_migration_seeds_the_old_join_us_roles(): void
    {
        $this->assertSame(4, JobOpening::count());
        $this->get('/careers')->assertOk()->assertSee('Real Estate Agent')->assertSee('Real Estate Intern');
    }

    public function test_careers_lists_only_live_openings(): void
    {
        JobOpening::query()->delete();
        $this->opening(['title' => 'Live Role']);
        $this->opening(['title' => 'Hidden Role', 'is_active' => false]);

        $this->get('/careers')->assertOk()->assertSee('Live Role')->assertDontSee('Hidden Role');
    }

    public function test_empty_state_still_offers_a_general_application(): void
    {
        JobOpening::query()->delete();

        $this->get('/careers')->assertOk()
            ->assertSee('There are no open positions right now.')
            ->assertSee('General Application');
    }

    public function test_filters_narrow_the_list(): void
    {
        JobOpening::query()->delete();
        $this->opening(['title' => 'Seller', 'department' => 'Sales']);
        $this->opening(['title' => 'Designer', 'department' => 'Marketing', 'employment_type' => 'internship']);

        $this->get('/careers?department=Marketing')->assertOk()
            ->assertSee('/careers/designer', false)
            ->assertDontSee('/careers/seller', false);
        $this->get('/careers?type=full-time')->assertOk()
            ->assertSee('/careers/seller', false)
            ->assertDontSee('/careers/designer', false);
    }

    public function test_opening_page_shows_the_role_and_hidden_ones_404(): void
    {
        $live = $this->opening(['title' => 'Site Manager', 'description' => '<p>Run the site.</p>']);
        $hidden = $this->opening(['title' => 'Old Role', 'is_active' => false]);

        $this->get("/careers/{$live->slug}")->assertOk()->assertSee('Site Manager')->assertSee('Run the site.', false);
        $this->get("/careers/{$hidden->slug}")->assertNotFound();
        $this->get('/careers/nope')->assertNotFound();
    }

    public function test_apply_form_says_which_role_is_being_applied_for(): void
    {
        $job = $this->opening(['title' => 'Site Supervisor', 'department' => 'Projects']);

        $this->get("/careers/{$job->slug}")->assertOk()
            ->assertSeeInOrder(["You're applying for", 'Site Supervisor', 'Projects'])
            ->assertSee('<input type="hidden" name="position" value="' . $job->id . '">', false)
            ->assertDontSee('Change position')
            ->assertDontSee('<select', false);
        // Older ?position= links go to that opening's own form.
        $this->get("/careers?position={$job->id}")->assertRedirect("/careers/{$job->slug}#apply");
        // The listing page form is a plain general application: no position picker.
        $this->get('/careers')->assertOk()
            ->assertDontSee("You're applying for")
            ->assertDontSee('name="position"', false)
            ->assertSee('View details &amp; apply', false)
            ->assertDontSee("/careers/{$job->slug}#apply", false); // cards link to the job page, not straight to a form
    }

    public function test_join_us_redirects_to_careers(): void
    {
        $this->get('/join-us')->assertStatus(301)->assertRedirect('/careers');
    }

    private function pdf(string $name = 'cv.pdf'): UploadedFile
    {
        return UploadedFile::fake()->create($name, 120, 'application/pdf');
    }

    public function test_application_for_an_opening_saves_a_career_lead_with_files(): void
    {
        Storage::fake('local');
        $job = $this->opening(['title' => 'Field Agent']);

        $this->post('/careers/apply', [
            'first_name' => 'Asha', 'last_name' => 'Rao', 'email' => 'asha@example.com',
            'position' => $job->id, 'resume' => $this->pdf(),
            'cover_letter' => UploadedFile::fake()->create('letter.docx', 40, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
            'agree' => 1,
        ])->assertSessionHasNoErrors()->assertSessionHas('success');

        $lead = PropertyInquiry::firstOrFail();
        $this->assertSame('Asha Rao', $lead->name);
        $this->assertSame('career', $lead->intent);
        $this->assertSame('Careers', $lead->source);
        $this->assertSame($job->id, $lead->job_opening_id);
        $this->assertSame('Field Agent', $lead->job_title);
        Storage::disk('local')->assertExists($lead->resume_path);
        Storage::disk('local')->assertExists($lead->cover_letter_path);
        $this->assertStringStartsWith('job-applications/', $lead->resume_path);
    }

    public function test_general_application_needs_no_position_or_cover_letter(): void
    {
        Storage::fake('local');

        $this->post('/careers/apply', [
            'first_name' => 'A', 'last_name' => 'B', 'email' => 'a@example.com', 'resume' => $this->pdf(), 'agree' => 1,
        ])->assertSessionHas('success');

        $lead = PropertyInquiry::firstOrFail();
        $this->assertNull($lead->job_opening_id);
        $this->assertNull($lead->job_title);
        $this->assertNull($lead->cover_letter_path);
    }

    public function test_application_requires_consent_a_live_position_and_a_resume(): void
    {
        Storage::fake('local');
        $hidden = $this->opening(['title' => 'Closed', 'is_active' => false]);

        $this->post('/careers/apply', ['first_name' => 'A', 'last_name' => 'B', 'email' => 'a@example.com', 'position' => $hidden->id])
            ->assertSessionHasErrors(['agree', 'position', 'resume']);
        $this->post('/careers/apply', ['first_name' => 'A', 'last_name' => 'B', 'email' => 'a@example.com', 'position' => 'ceo', 'resume' => $this->pdf(), 'agree' => 1])
            ->assertSessionHasErrors(['position']);

        $this->assertSame(0, PropertyInquiry::count());
    }

    public function test_errors_show_next_to_their_fields_and_keep_typed_values(): void
    {
        $job = $this->opening(['title' => 'Field Agent']);

        $this->from("/careers/{$job->slug}")
            ->followingRedirects()
            ->post('/careers/apply', ['first_name' => 'Asha', 'last_name' => 'Rao', 'email' => 'not-an-email', 'position' => $job->id])
            ->assertOk()
            ->assertSee('Please fix the highlighted fields')
            ->assertSee('The email field must be a valid email address.')
            ->assertSee('The resume field is required.')
            ->assertSee('value="Asha"', false)
            ->assertSee('border-red-400', false);
    }

    public function test_resume_must_be_a_small_pdf_or_word_file(): void
    {
        Storage::fake('local');
        $base = ['first_name' => 'A', 'last_name' => 'B', 'email' => 'a@example.com', 'agree' => 1];

        $this->post('/careers/apply', $base + ['resume' => UploadedFile::fake()->create('cv.exe', 10, 'application/x-msdownload')])
            ->assertSessionHasErrors(['resume']);
        $this->post('/careers/apply', $base + ['resume' => UploadedFile::fake()->create('cv.pdf', 3000, 'application/pdf')])
            ->assertSessionHasErrors(['resume']);

        $this->assertSame(0, PropertyInquiry::count());
    }

    public function test_enquiry_list_shows_the_job_or_general_application(): void
    {
        Storage::fake('local');
        $live = $this->opening(['title' => 'Live Role']);
        $gone = $this->opening(['title' => 'Gone Role']);
        $base = ['last_name' => 'X', 'email' => 'a@example.com', 'agree' => 1];

        $this->post('/careers/apply', $base + ['first_name' => 'Ann', 'position' => $live->id, 'resume' => $this->pdf()]);
        $this->post('/careers/apply', $base + ['first_name' => 'Ben', 'position' => $gone->id, 'resume' => $this->pdf()]);
        $this->post('/careers/apply', $base + ['first_name' => 'Cal', 'resume' => $this->pdf()]);
        $gone->delete();

        $this->actingAs(User::factory()->create(['role' => 'admin']), 'admin')
            ->get('/admin/inquiryformlist')->assertOk()
            ->assertSee('Property / Job')
            ->assertSee('href="' . $live->url . '"', false)
            ->assertSeeInOrder(['Gone Role', 'removed'])
            ->assertSee('General application');
    }

    public function test_only_permitted_admins_can_download_application_files(): void
    {
        Storage::fake('local');
        $this->post('/careers/apply', [
            'first_name' => 'Asha', 'last_name' => 'Rao', 'email' => 'a@example.com', 'resume' => $this->pdf(), 'agree' => 1,
        ]);
        $lead = PropertyInquiry::firstOrFail();

        $this->get("/admin/inquiries/{$lead->id}/resume")->assertRedirect(route('admin.login'));

        $blocked = User::factory()->create(['role' => 'admin']);
        UserPermission::create(['user_id' => $blocked->id, 'blog' => true]);
        $this->actingAs($blocked, 'admin')->get("/admin/inquiries/{$lead->id}/resume")->assertForbidden();

        $admin = User::factory()->create(['role' => 'admin']);
        UserPermission::create(['user_id' => $admin->id, 'property_image' => true]);
        $this->actingAs($admin, 'admin');

        $this->get("/admin/inquiries/{$lead->id}/resume")->assertOk()
            ->assertDownload('asha-rao-resume.pdf');
        $this->get("/admin/inquiries/{$lead->id}/cover-letter")->assertNotFound();
        $this->get('/admin/inquiryformlist')->assertOk()->assertSee(route('admin.inquiries.file', [$lead, 'resume']), false);
    }

    public function test_admin_needs_the_careers_flag(): void
    {
        $blocked = User::factory()->create(['role' => 'admin']);
        UserPermission::create(['user_id' => $blocked->id, 'blog' => true]);
        $this->actingAs($blocked, 'admin');

        $this->get('/admin/careers')->assertForbidden();
        $this->get('/admin/dashboard')->assertDontSee('fa-briefcase', false);
    }

    public function test_admin_can_create_edit_toggle_and_delete_an_opening(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        UserPermission::create(['user_id' => $admin->id, 'careers' => true]);
        $this->actingAs($admin, 'admin');

        $this->get('/admin/careers')->assertOk()->assertSee('Real Estate Agent');
        $this->get('/admin/careers/create')->assertOk();

        $this->post('/admin/careers', [
            'title' => 'Leasing Executive', 'employment_type' => 'full-time', 'department' => 'Leasing',
            'description' => '<p>Hi</p><script>alert(1)</script>', 'is_active' => 1,
        ])->assertRedirect(route('admin.careers.index'));

        $job = JobOpening::where('title', 'Leasing Executive')->firstOrFail();
        $this->assertSame('leasing-executive', $job->slug);
        $this->assertStringNotContainsString('<script', $job->description);
        $this->assertTrue($job->is_active);

        $this->get("/admin/careers/{$job->id}/edit")->assertOk()->assertSee('Leasing Executive');
        $this->put("/admin/careers/{$job->id}", ['title' => 'Senior Leasing Executive', 'employment_type' => 'contract', 'slug' => $job->slug])
            ->assertRedirect(route('admin.careers.index'));
        $job->refresh();
        $this->assertSame('Senior Leasing Executive', $job->title);
        $this->assertSame('contract', $job->employment_type);
        $this->assertFalse($job->is_active); // unticked checkbox hides it

        $this->put("/admin/careers/{$job->id}/toggle")->assertRedirect();
        $this->assertTrue($job->fresh()->is_active);

        $this->delete("/admin/careers/{$job->id}")->assertRedirect();
        $this->assertDatabaseMissing('job_openings', ['id' => $job->id]);
    }
}
