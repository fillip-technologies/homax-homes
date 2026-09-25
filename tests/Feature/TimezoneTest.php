<?php

namespace Tests\Feature;

use App\Models\PropertyInquiry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimezoneTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_whole_app_runs_in_ist(): void
    {
        $this->assertSame('Asia/Kolkata', config('app.timezone'));
        $this->assertSame('Asia/Kolkata', date_default_timezone_get());
        $this->assertSame('Asia/Kolkata', now()->timezoneName);
    }

    public function test_dashboard_today_counts_use_the_ist_day(): void
    {
        // 00:30 IST today is still "yesterday" in UTC; it must count as today.
        Carbon::setTestNow(Carbon::parse('2026-09-25 09:00:00', 'Asia/Kolkata'));

        $early = new PropertyInquiry(['property_id' => 0, 'name' => 'Early', 'phone' => '1', 'intent' => 'general']);
        $early->created_at = Carbon::parse('2026-09-25 00:30:00', 'Asia/Kolkata');
        $early->save();

        $yesterday = new PropertyInquiry(['property_id' => 0, 'name' => 'Old', 'phone' => '1', 'intent' => 'general']);
        $yesterday->created_at = Carbon::parse('2026-09-24 23:30:00', 'Asia/Kolkata');
        $yesterday->save();

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin, 'admin')->get('/admin/dashboard')
            ->assertOk()
            ->assertViewHas('todayInquiries', 1)
            ->assertViewHas('totalInquiries', 2);

        Carbon::setTestNow();
    }

    public function test_enquiry_list_shows_the_stored_time_without_shifting_it(): void
    {
        $enquiry = new PropertyInquiry(['property_id' => 0, 'name' => 'Sam', 'phone' => '1', 'intent' => 'general']);
        $enquiry->created_at = Carbon::parse('2026-09-24 16:30:00', 'Asia/Kolkata');
        $enquiry->save();

        $this->assertSame('2026-09-24 16:30:00', $enquiry->getRawOriginal('created_at'));

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin, 'admin')->get('/admin/inquiryformlist')
            ->assertSee('24 Sep 2026')
            ->assertSee('04:30 PM');
    }
}
