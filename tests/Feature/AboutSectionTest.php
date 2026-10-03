<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutSectionTest extends TestCase
{
    use RefreshDatabase;

    private function signIn(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']), 'admin');
    }

    private function payload(array $overrides = []): array
    {
        return ['about' => $overrides + [
            'intro' => 'We build homes people love.',
            'points' => [
                ['title' => 'Trust', 'text' => 'Honest advice at every step'],
                ['title' => '', 'text' => ''],
                ['title' => 'Speed', 'text' => 'Quick site visits'],
            ],
            'stats' => [
                ['value' => '500+', 'label' => 'Happy Families'],
                ['value' => '', 'label' => ''],
                ['value' => '12', 'label' => 'Cities'],
            ],
        ]];
    }

    public function test_home_page_shows_the_default_text_until_something_is_saved(): void
    {
        $this->get('/')
            ->assertSee('Homax Homes is a real estate company focused on thoughtfully planned homes')
            ->assertSee('Project Information:')
            ->assertSee('1K+');
    }

    public function test_guests_cannot_edit_the_section(): void
    {
        $this->post(route('admin.about.update'), $this->payload())->assertRedirect(route('admin.login'));
        $this->assertDatabaseCount('site_settings', 0);
    }

    public function test_admin_edits_are_shown_on_the_home_page_and_blank_rows_are_hidden(): void
    {
        $this->signIn();

        $this->post(route('admin.about.update'), $this->payload())->assertSessionHasNoErrors();

        $this->get('/')
            ->assertSee('We build homes people love.')
            ->assertSee('Trust:')
            ->assertSee('Quick site visits')
            ->assertSee('500+')
            ->assertSee('Happy Families')
            ->assertDontSee('Helpful Guidance')
            ->assertDontSee('98%');
    }

    public function test_admin_page_shows_current_values_and_reset_restores_defaults(): void
    {
        $this->signIn();
        $this->post(route('admin.about.update'), $this->payload());

        $this->get(route('admin.settings.edit'))->assertOk()->assertSee('We build homes people love.');

        $this->delete(route('admin.about.reset'))->assertSessionHas('success');
        $this->assertSame(SiteSetting::ABOUT_DEFAULTS, SiteSetting::about());
        $this->get('/')->assertSee('Helpful Guidance:');
    }

    public function test_validation_requires_an_intro_and_limits_length(): void
    {
        $this->signIn();

        $this->post(route('admin.about.update'), $this->payload(['intro' => '']))->assertSessionHasErrors('about.intro');
        $this->post(route('admin.about.update'), $this->payload(['intro' => str_repeat('a', 601)]))->assertSessionHasErrors('about.intro');
    }

    public function test_home_page_falls_back_to_defaults_if_the_table_is_missing(): void
    {
        \Illuminate\Support\Facades\Schema::drop('site_settings');

        $this->get('/')->assertOk()->assertSee('Project Information:');
    }

    private function footerPayload(array $overrides = []): array
    {
        return ['footer' => $overrides + [
            'heading' => 'About Homax Homes',
            'paragraphs' => ['First para.', 'Second para.', '', 'Fourth para.'],
            'notices' => [
                ['title' => 'Disclaimer', 'text' => 'Verify everything yourself.'],
                ['title' => '', 'text' => ''],
                ['title' => 'Licence', 'text' => 'RERA registered.'],
            ],
        ]];
    }

    public function test_footer_shows_the_default_text_until_something_is_saved(): void
    {
        $this->get('/')
            ->assertSee('About Homax Homes Commercial Real Estate')
            ->assertSee('Legal Disclaimer:')
            ->assertSee('Licensing Information:');
    }

    public function test_admin_can_edit_the_footer_text_and_blank_rows_are_hidden(): void
    {
        $this->signIn();

        $this->post(route('admin.footer.update'), $this->footerPayload())->assertSessionHasNoErrors();

        $this->get('/')
            ->assertSee('About Homax Homes')
            ->assertDontSee('Commercial Real Estate</h3>', false)
            ->assertSee('First para.')
            ->assertSee('Fourth para.')
            ->assertSee('Disclaimer:')
            ->assertSee('RERA registered.')
            ->assertDontSee('Equal Housing Opportunity');
    }

    public function test_footer_sections_disappear_when_everything_is_blank(): void
    {
        $this->signIn();

        $this->post(route('admin.footer.update'), $this->footerPayload([
            'heading' => '',
            'paragraphs' => ['', '', '', ''],
            'notices' => [['title' => '', 'text' => ''], ['title' => '', 'text' => ''], ['title' => '', 'text' => '']],
        ]))->assertSessionHasNoErrors();

        $this->get('/')->assertOk()
            ->assertDontSee('Legal Disclaimers', false)
            ->assertDontSee('Equal Housing Opportunity');
    }

    public function test_footer_edit_needs_login_and_reset_restores_defaults(): void
    {
        $this->post(route('admin.footer.update'), $this->footerPayload())->assertRedirect(route('admin.login'));

        $this->signIn();
        $this->post(route('admin.footer.update'), $this->footerPayload());
        $this->get(route('admin.settings.edit'))->assertOk()->assertSee('First para.');

        $this->delete(route('admin.footer.reset'))->assertSessionHas('success');
        $this->assertSame(SiteSetting::FOOTER_DEFAULTS, SiteSetting::footerAbout());
    }

    public function test_footer_validation_limits_length(): void
    {
        $this->signIn();

        $this->post(route('admin.footer.update'), $this->footerPayload(['heading' => str_repeat('a', 121)]))
            ->assertSessionHasErrors('footer.heading');
    }
}
