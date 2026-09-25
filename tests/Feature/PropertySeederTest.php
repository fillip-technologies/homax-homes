<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\PropertyInquiry;
use App\Models\User;
use Database\Seeders\PropertySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertySeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_replaces_data_with_at_most_five_enquiries_from_this_week(): void
    {
        User::factory()->create(['role' => 'admin']);

        $this->seed(PropertySeeder::class);
        $this->seed(PropertySeeder::class); // running twice must replace, not duplicate

        $this->assertGreaterThan(0, Property::count());
        $this->assertLessThanOrEqual(5, PropertyInquiry::count());

        $monday = now('Asia/Kolkata')->startOfWeek();
        foreach (PropertyInquiry::all() as $enquiry) {
            $this->assertTrue($enquiry->created_at->greaterThanOrEqualTo($monday), "{$enquiry->created_at} is before this week");
            $this->assertTrue($enquiry->created_at->lessThanOrEqualTo(now()), "{$enquiry->created_at} is in the future");
        }
    }
}
