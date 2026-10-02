<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PropertyValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function createAdminUser(): User
    {
        return User::factory()->create([
            'role' => 'admin',
        ]);
    }

    public function test_property_add_form_has_validation_markup_and_indicators(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.propertylisting'));

        $response->assertStatus(200);
        $response->assertSee('id="propertyForm"', false);
        $response->assertSee('novalidate', false);
        $response->assertSee('id="client-validation-alert"', false);
        $response->assertSee('Description is required.', false);
        $response->assertSee('Project Name is required.', false);
        $response->assertSee('Address is required.', false);
        $response->assertSee('City is required.', false);
        $response->assertSee('State is required.', false);
        $response->assertSee('Main image is required.', false);
        $response->assertSee('id="unit_type_commercial_datalist"', false);
        $response->assertSee('residential-detail-field', false);
        $response->assertSee('id="security_deposit_in_words"', false);
        $response->assertSee('detail-price-helper', false);
    }

    public function test_property_store_fails_when_required_fields_are_missing(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin, 'admin')->post(route('admin.propertylisting.store'), []);

        $response->assertSessionHasErrors([
            'title',
            'description',
            'address',
            'city',
            'state',
            'main_image',
        ]);
    }

    public function test_property_edit_form_has_validation_markup(): void
    {
        $admin = $this->createAdminUser();
        $property = Property::create([
            'user_id' => $admin->id,
            'title' => 'Test Residence',
            'slug' => 'test-residence-' . uniqid(),
            'description' => 'Test Description',
            'price' => '75 Lakh',
            'address' => 'Test Address',
            'city' => 'Navi Mumbai',
            'state' => 'Maharashtra',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.properties.edit', $property->id));

        $response->assertStatus(200);
        $response->assertSee('id="propertyEditForm"', false);
        $response->assertSee('novalidate', false);
        $response->assertSee('id="client-validation-alert"', false);
        $response->assertSee('Description is required.', false);
        $response->assertSee('Project Name is required.', false);
        $response->assertSee('Address is required.', false);
        $response->assertSee('City is required.', false);
        $response->assertSee('State is required.', false);
        $response->assertSee('id="unit_type_commercial_datalist"', false);
        $response->assertSee('residential-detail-field', false);
        $response->assertSee('id="security_deposit_in_words"', false);
        $response->assertSee('detail-price-helper', false);
    }

    public function test_commercial_property_stores_null_for_bedrooms_bathrooms_balconies(): void
    {
        Storage::fake('public');
        $admin = $this->createAdminUser();

        $payload = [
            'category' => 'Commercial',
            'title' => 'Commercial Tower',
            'description' => 'Prime office space for corporate lease.',
            'price' => '2 Cr',
            'address' => 'Sector 10, Kharghar',
            'city' => 'Navi Mumbai',
            'state' => 'Maharashtra',
            'main_image' => UploadedFile::fake()->image('office.jpg'),
            'bedrooms' => 3,
            'bathrooms' => 2,
            'balconies' => 1,
            'apartment_per_floor' => '4',
            'property_details' => [
                [
                    'unit_type' => 'Office Space',
                    'bedrooms' => 2,
                    'bathrooms' => 1,
                    'balconies' => 1,
                    'carpet_area' => 1250,
                    'super_area' => 1600,
                    'price' => '2 Cr',
                ],
                [
                    'unit_type' => 'Retail Shop',
                    'bedrooms' => 1,
                    'bathrooms' => 1,
                    'balconies' => 0,
                    'carpet_area' => 650,
                    'super_area' => 800,
                    'price' => '1.2 Cr',
                ],
            ],
        ];

        $response = $this->actingAs($admin, 'admin')->post(route('admin.propertylisting.store'), $payload);

        $response->assertSessionHasNoErrors();

        $property = Property::where('title', 'Commercial Tower')->firstOrFail();
        $this->assertEquals('Commercial', $property->category);
        $this->assertNull($property->bedrooms);
        $this->assertNull($property->bathrooms);
        $this->assertNull($property->balconies);
        $this->assertNull($property->apartment_per_floor);

        $details = $property->details()->get();
        $this->assertCount(2, $details);

        $this->assertEquals('Office Space', $details[0]->unit_type);
        $this->assertNull($details[0]->bedrooms);
        $this->assertNull($details[0]->bathrooms);
        $this->assertNull($details[0]->balconies);
        $this->assertEquals(1250, (float)$details[0]->carpet_area);

        $this->assertEquals('Retail Shop', $details[1]->unit_type);
        $this->assertNull($details[1]->bedrooms);
        $this->assertNull($details[1]->bathrooms);
        $this->assertNull($details[1]->balconies);
        $this->assertEquals(650, (float)$details[1]->carpet_area);

        if ($property->main_image && file_exists(public_path($property->main_image))) {
            @unlink(public_path($property->main_image));
        }
    }

    public function test_updating_residential_to_commercial_clears_bedrooms_bathrooms_balconies(): void
    {
        $admin = $this->createAdminUser();
        $property = Property::create([
            'user_id' => $admin->id,
            'category' => 'Residential',
            'title' => 'Sample Complex',
            'slug' => 'sample-complex-' . uniqid(),
            'description' => 'Original residential complex',
            'price' => '90 Lakh',
            'address' => 'Plot 4, Sector 15',
            'city' => 'Navi Mumbai',
            'state' => 'Maharashtra',
            'bedrooms' => 3,
            'bathrooms' => 2,
            'balconies' => 2,
            'apartment_per_floor' => '4',
            'is_active' => true,
        ]);

        $property->details()->create([
            'unit_type' => '3 BHK',
            'bedrooms' => 3,
            'bathrooms' => 2,
            'balconies' => 2,
            'carpet_area' => 1100,
            'price' => '90 Lakh',
        ]);

        $updatePayload = [
            'category' => 'Commercial',
            'title' => 'Sample Commercial Hub',
            'description' => 'Converted commercial business hub',
            'price' => '1.5 Cr',
            'address' => 'Plot 4, Sector 15',
            'city' => 'Navi Mumbai',
            'state' => 'Maharashtra',
            'bedrooms' => 4,
            'bathrooms' => 3,
            'balconies' => 1,
            'apartment_per_floor' => '6',
            'property_details' => [
                [
                    'unit_type' => 'Showroom',
                    'bedrooms' => 2,
                    'bathrooms' => 1,
                    'balconies' => 1,
                    'carpet_area' => 2000,
                    'price' => '1.5 Cr',
                ],
            ],
        ];

        $response = $this->actingAs($admin, 'admin')->put(route('admin.properties.update', $property->id), $updatePayload);

        $response->assertSessionHasNoErrors();

        $property->refresh();
        $this->assertEquals('Commercial', $property->category);
        $this->assertNull($property->bedrooms);
        $this->assertNull($property->bathrooms);
        $this->assertNull($property->balconies);
        $this->assertNull($property->apartment_per_floor);

        $detail = $property->details()->first();
        $this->assertNotNull($detail);
        $this->assertEquals('Showroom', $detail->unit_type);
        $this->assertNull($detail->bedrooms);
        $this->assertNull($detail->bathrooms);
        $this->assertNull($detail->balconies);
        $this->assertEquals(2000, (float)$detail->carpet_area);
    }

    public function test_property_formatted_price_accessor_converts_numeric_prices_and_preserves_text_ranges(): void
    {
        $numericProperty = new Property(['price' => '230000', 'price_unit' => '₹']);
        $this->assertSame('2.3 Lakh', $numericProperty->formatted_price);
        $this->assertSame('₹2.3 Lakh', $numericProperty->display_price);

        $crProperty = new Property(['price' => '14000000', 'price_unit' => '₹']);
        $this->assertSame('1.4 Cr', $crProperty->formatted_price);
        $this->assertSame('₹1.4 Cr', $crProperty->display_price);

        $rangeProperty = new Property(['price' => '50L-70L', 'price_unit' => '₹']);
        $this->assertSame('50L – 70L', $rangeProperty->formatted_price);
        $this->assertSame('₹50L – 70L', $rangeProperty->display_price);

        $textProperty = new Property(['price' => '1.40 Cr', 'price_unit' => '₹']);
        $this->assertSame('1.40 Cr', $textProperty->formatted_price);
        $this->assertSame('₹1.40 Cr', $textProperty->display_price);

        $requestProperty = new Property(['price' => 'Price on request', 'price_unit' => '₹']);
        $this->assertSame('Price on request', $requestProperty->formatted_price);
        $this->assertSame('Price on request', $requestProperty->display_price);
    }

    public function test_public_search_and_detail_pages_display_formatted_price_for_numeric_input(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $property = Property::create([
            'user_id' => $admin->id,
            'title' => 'Alpha Business Hub',
            'slug' => 'alpha-business-hub',
            'description' => 'Commercial office hub',
            'category' => 'Commercial',
            'price' => '230000',
            'price_unit' => '₹',
            'city' => 'Mumbai',
            'is_active' => true,
        ]);

        $searchResponse = $this->get(route('property.search', ['category' => 'Commercial']));
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('₹2.3 Lakh');

        $detailResponse = $this->get(route('property.show', $property->slug));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('2.3 Lakh');
    }
}
