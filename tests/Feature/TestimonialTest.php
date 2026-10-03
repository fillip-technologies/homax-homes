<?php

namespace Tests\Feature;

use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestimonialTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return $overrides + [
            'name' => 'Asha Verma',
            'subtitle' => 'Site Visit',
            'quote' => 'Great guidance from start to finish.',
            'rating' => 4,
            'caption' => 'Verified buyer',
            'is_active' => 1,
        ];
    }

    private function signIn(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']), 'admin');
    }

    public function test_guests_cannot_manage_testimonials(): void
    {
        $this->post(route('admin.testimonials.store'), $this->payload())->assertRedirect(route('admin.login'));
        $this->assertDatabaseCount('testimonials', 3); // only the seeded ones
    }

    public function test_the_three_original_cards_are_seeded(): void
    {
        $this->assertSame(
            ['Sample Homebuyer', 'First-Time Buyer', 'Project Visitor'],
            Testimonial::visible()->pluck('name')->all()
        );
    }

    public function test_admin_can_add_edit_hide_and_delete_a_card(): void
    {
        $this->signIn();

        $this->post(route('admin.testimonials.store'), $this->payload())->assertSessionHasNoErrors();
        $card = Testimonial::where('name', 'Asha Verma')->firstOrFail();
        $this->assertSame(4, $card->rating);
        $this->assertSame(4, $card->sort_order);

        $this->get('/')->assertSee('Great guidance from start to finish.');

        $this->put(route('admin.testimonials.update', $card), $this->payload(['quote' => 'Updated words.', 'rating' => 5]))
            ->assertSessionHasNoErrors();
        $this->assertSame('Updated words.', $card->fresh()->quote);

        // Unticking "Show on website" hides it from the home page but keeps it.
        $this->put(route('admin.testimonials.update', $card), $this->payload(['is_active' => 0, 'quote' => 'Updated words.']));
        $this->get('/')->assertDontSee('Updated words.');
        $this->assertDatabaseHas('testimonials', ['id' => $card->id]);

        $this->delete(route('admin.testimonials.destroy', $card))->assertSessionHas('success');
        $this->assertDatabaseMissing('testimonials', ['id' => $card->id]);
    }

    public function test_validation_rejects_bad_input(): void
    {
        $this->signIn();

        $this->post(route('admin.testimonials.store'), $this->payload(['name' => '', 'quote' => '', 'rating' => 9]))
            ->assertSessionHasErrors(['name', 'quote', 'rating']);
    }

    public function test_section_is_hidden_when_no_card_is_shown(): void
    {
        Testimonial::query()->update(['is_active' => false]);

        $this->get('/')->assertDontSee('What Our Clients Say');
    }

    public function test_settings_page_lists_the_cards(): void
    {
        $this->signIn();

        $this->get(route('admin.settings.edit'))
            ->assertOk()
            ->assertSee('Sample Homebuyer')
            ->assertSee('Add testimonial');
    }
}
