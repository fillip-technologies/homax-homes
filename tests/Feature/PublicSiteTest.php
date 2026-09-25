<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\PropertyInquiry;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Keep the reCAPTCHA check off unless a test turns it on.
        config(['services.recaptcha.secret_key' => null, 'services.recaptcha.site_key' => null]);
    }

    private function property(array $overrides = []): Property
    {
        $owner = User::factory()->create(['role' => 'admin']);

        return Property::create($overrides + [
            'user_id' => $owner->id,
            'title' => 'Sky Villa',
            'slug' => 'sky-villa-' . uniqid(),
            'description' => 'Nice place',
            'price' => '75 Lakh',
            'city' => 'Mumbai',
            'is_active' => true,
        ]);
    }

    public function test_contact_form_saves_an_inquiry(): void
    {
        $this->post('/contact', ['name' => 'Sam', 'email' => 'sam@example.com', 'message' => 'Hello'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('property_inquiries', ['name' => 'Sam', 'source' => 'Contact', 'intent' => 'general', 'property_id' => 0]);
    }

    public function test_contact_form_validates_input(): void
    {
        $this->post('/contact', ['name' => '', 'email' => 'nope', 'message' => ''])
            ->assertSessionHasErrors(['name', 'email', 'message']);

        $this->assertSame(0, PropertyInquiry::count());
    }

    public function test_join_us_form_saves_a_career_lead(): void
    {
        $this->post('/join-us', [
            'first_name' => 'Asha', 'last_name' => 'Rao', 'email' => 'asha@example.com',
            'position' => 'agent', 'resume_link' => 'https://example.com/cv.pdf',
            'cover_letter' => 'I would love to join.', 'agree' => 1,
        ])->assertSessionHas('success');

        $lead = PropertyInquiry::firstOrFail();
        $this->assertSame('Asha Rao', $lead->name);
        $this->assertSame('career', $lead->intent);
        $this->assertStringContainsString('Position: Agent', $lead->message);
        $this->assertStringContainsString('https://example.com/cv.pdf', $lead->message);
    }

    public function test_join_us_requires_consent_and_a_valid_position(): void
    {
        $this->post('/join-us', ['first_name' => 'A', 'last_name' => 'B', 'email' => 'a@example.com', 'position' => 'ceo'])
            ->assertSessionHasErrors(['agree', 'position']);
    }

    public function test_associate_form_saves_a_lead(): void
    {
        $this->post('/associates-us', [
            'first_name' => 'Ben', 'last_name' => 'Roy', 'email' => 'ben@example.com', 'message' => 'Partner?', 'agree' => 1,
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('property_inquiries', ['name' => 'Ben Roy', 'intent' => 'associate', 'source' => 'Associate']);
    }

    public function test_public_forms_are_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/contact', ['name' => 'Sam', 'email' => 'sam@example.com', 'message' => 'Hi'])->assertRedirect();
        }

        $this->post('/contact', ['name' => 'Sam', 'email' => 'sam@example.com', 'message' => 'Hi'])->assertStatus(429);
    }

    public function test_contact_form_requires_recaptcha_when_configured(): void
    {
        config(['services.recaptcha.secret_key' => 'secret']);
        Http::fake(['www.google.com/*' => Http::sequence()->push(['success' => false])->push(['success' => true])]);

        $this->post('/contact', ['name' => 'Sam', 'email' => 'sam@example.com', 'message' => 'Hi'])
            ->assertSessionHasErrors('g-recaptcha-response');

        $this->post('/contact', ['name' => 'Sam', 'email' => 'sam@example.com', 'message' => 'Hi', 'g-recaptcha-response' => 'token'])
            ->assertSessionHasErrors(['g-recaptcha-response' => 'The reCAPTCHA verification failed. Please try again.']);

        $this->post('/contact', ['name' => 'Sam', 'email' => 'sam@example.com', 'message' => 'Hi', 'g-recaptcha-response' => 'token'])
            ->assertSessionHas('success');
    }

    public function test_recaptcha_failure_from_an_unreachable_google_is_a_validation_error_not_a_500(): void
    {
        config(['services.recaptcha.secret_key' => 'secret']);
        Http::fake(fn () => throw new \Illuminate\Http\Client\ConnectionException('down'));

        $this->post('/contact', ['name' => 'Sam', 'email' => 'sam@example.com', 'message' => 'Hi', 'g-recaptcha-response' => 'token'])
            ->assertSessionHasErrors('g-recaptcha-response');
    }

    public function test_inactive_property_is_hidden_from_the_public_but_not_from_admins(): void
    {
        $property = $this->property(['is_active' => false]);

        $this->get("/property/{$property->id}")->assertNotFound();

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin, 'admin')->get("/property/{$property->id}")->assertOk();
    }

    public function test_active_property_page_loads(): void
    {
        $property = $this->property();

        $this->get("/property/{$property->id}")->assertOk()->assertSee('Sky Villa');
    }

    public function test_deleting_a_property_removes_its_uploaded_files(): void
    {
        $dir = public_path('properties/test_cleanup');
        @mkdir($dir, 0755, true);
        file_put_contents("$dir/main.jpg", 'x');
        file_put_contents("$dir/brochure.pdf", 'x');
        file_put_contents("$dir/extra.jpg", 'x');

        $property = $this->property(['main_image' => 'properties/test_cleanup/main.jpg', 'brochure' => 'properties/test_cleanup/brochure.pdf']);
        $property->images()->create(['image_path' => 'properties/test_cleanup/extra.jpg']);

        $admin = User::factory()->create(['role' => 'admin']);
        UserPermission::create(['user_id' => $admin->id, 'all_property' => true]);

        $this->actingAs($admin, 'admin')->delete("/admin/properties/{$property->id}")->assertSessionHas('success');

        $this->assertFileDoesNotExist("$dir/main.jpg");
        $this->assertFileDoesNotExist("$dir/brochure.pdf");
        $this->assertFileDoesNotExist("$dir/extra.jpg");
        $this->assertDatabaseMissing('property_images', ['property_id' => $property->id]);
        @rmdir($dir);
    }

    public function test_static_pages_render(): void
    {
        foreach (['/', '/contact', '/join-us', '/associates-us', '/about-us', '/our-team', '/search'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_dummy_routes_are_removed(): void
    {
        foreach (['/propertydetails', '/searchs', '/admin/form', '/admin/table'] as $url) {
            $this->get($url)->assertNotFound();
        }
    }

    public function test_property_enquiry_saves_the_property_name_and_the_admin_list_shows_it(): void
    {
        $property = $this->property(['title' => 'Harbor View']);

        $this->post("/properties/{$property->id}/inquiry", [
            'name' => 'Rohan', 'phone' => '9820011234', 'terms' => 1,
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('property_inquiries', ['property_id' => $property->id, 'property_title' => 'Harbor View']);

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin, 'admin')->get('/admin/inquiryformlist')
            ->assertOk()
            ->assertSee('Harbor View')
            ->assertSee('Property enquiry');

        // Deleting the listing keeps the name and marks it as removed.
        $property->delete();
        $this->get('/admin/inquiryformlist')->assertSee('Harbor View')->assertSee('removed');
    }

    public function test_general_leads_have_no_property_name(): void
    {
        $this->post('/contact', ['name' => 'Sam', 'email' => 'sam@example.com', 'message' => 'Hello']);

        $this->assertNull(PropertyInquiry::firstOrFail()->property_title);
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin, 'admin')->get('/admin/inquiryformlist')->assertSee('General (no property)')->assertSee('Contact message');
    }

    public function test_deleting_a_property_keeps_files_that_another_listing_still_uses(): void
    {
        $dir = public_path('properties/test_shared');
        @mkdir($dir, 0755, true);
        file_put_contents("$dir/shared.pdf", 'x');
        file_put_contents("$dir/own.jpg", 'x');

        $doomed = $this->property(['main_image' => 'properties/test_shared/own.jpg', 'brochure' => 'properties/test_shared/shared.pdf']);
        $survivor = $this->property(['brochure' => 'properties/test_shared/shared.pdf']);

        $admin = User::factory()->create(['role' => 'admin']);
        UserPermission::create(['user_id' => $admin->id, 'all_property' => true]);
        $this->actingAs($admin, 'admin')->delete("/admin/properties/{$doomed->id}")->assertSessionHas('success');

        $this->assertFileDoesNotExist("$dir/own.jpg");
        $this->assertFileExists("$dir/shared.pdf");

        @unlink("$dir/shared.pdf");
        @rmdir($dir);
        $this->assertNotNull($survivor->fresh());
    }

    public function test_fixed_places_can_carry_a_name_that_shows_on_the_property_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $property = $this->property(['hospital_distance_km' => '2 km', 'school_distance_km' => '1.2 km']);

        // Names are saved through the same validation the admin form uses.
        $controller = new \App\Http\Controllers\PropertyListingController();
        $clean = (new \ReflectionMethod($controller, 'cleanPlaceNames'));
        $clean->setAccessible(true);
        $names = $clean->invoke($controller, ['hospital' => '  Apollo Hospital ', 'school' => '', 'bogus' => 'x']);
        $this->assertSame(['hospital' => 'Apollo Hospital'], $names);
        $this->assertNull($clean->invoke($controller, ['hospital' => '  ']));

        $property->update(['place_names' => $names]);

        $this->get("/property/{$property->id}")
            ->assertOk()
            ->assertSee('Hospital: Apollo Hospital - 2 km', false)   // named
            ->assertSee('School - 1.2 km', false);                    // unnamed keeps the generic label
    }
}
