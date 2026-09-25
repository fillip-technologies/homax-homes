<?php

namespace Tests\Feature;

use App\Models\OurTeam;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    private function admin(array $flags = null): User
    {
        $user = User::factory()->create(['role' => 'admin']);

        if ($flags !== null) {
            UserPermission::create(['user_id' => $user->id] + $flags);
        }

        return $user;
    }

    public function test_guests_are_sent_to_the_login_page(): void
    {
        $this->get('/admin/dashboard')->assertRedirect(route('admin.login'));
        $this->get('/admin/our_team')->assertRedirect(route('admin.login'));
    }

    public function test_public_registration_route_is_gone(): void
    {
        $this->get('/admin/register')->assertNotFound();
    }

    public function test_only_admin_role_can_log_in(): void
    {
        $user = User::factory()->create(['role' => 'student', 'email' => 's@example.com', 'password' => 'secret-pass']);

        $this->post('/admin/login', ['email' => 's@example.com', 'password' => 'secret-pass'])
            ->assertRedirect(route('admin.login'))
            ->assertSessionHas('error', 'Unauthorized user. Admin access only.');

        $this->assertGuest('admin');
    }

    public function test_admin_can_log_in(): void
    {
        $this->admin();
        User::where('role', 'admin')->update(['email' => 'a@example.com']);
        User::where('email', 'a@example.com')->first()->update(['password' => 'secret-pass']);

        $this->post('/admin/login', ['email' => 'a@example.com', 'password' => 'secret-pass'])
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_without_a_permission_row_has_full_access(): void
    {
        $this->actingAs($this->admin(), 'admin');

        foreach (['/admin/our_team', '/admin/user-permission', '/admin/properties', '/admin/inquiryformlist'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_admin_with_all_flags_off_is_blocked_everywhere_but_the_dashboard(): void
    {
        $this->actingAs($this->admin([]), 'admin');

        $this->get('/admin/dashboard')->assertOk();
        $this->get('/admin/site-settings')->assertOk();

        foreach ([
            '/admin/our_team',
            '/admin/user-permission',
            '/admin/properties',
            '/admin/propertiesfeatured',
            '/admin/propertylisting',
            '/admin/inquiryformlist',
            '/admin/enquiryformlist',
            '/admin/properties/1/edit',
        ] as $url) {
            $this->get($url)->assertForbidden();
        }

        $this->delete('/admin/properties/1')->assertForbidden();
        $member = OurTeam::create(['employee_name' => 'X', 'designation' => 'Y']);
        $this->delete("/admin/our_team/{$member->id}")->assertForbidden();
        $this->assertDatabaseHas('our_team', ['id' => $member->id]);
        $this->put('/admin/user-permission/1', ['manage_users' => 1])->assertForbidden();
    }

    public function test_each_flag_unlocks_only_its_own_section(): void
    {
        $this->actingAs($this->admin(['our_team' => true]), 'admin');

        $this->get('/admin/our_team')->assertOk();
        $this->get('/admin/user-permission')->assertForbidden();
        $this->get('/admin/properties')->assertForbidden();
    }

    public function test_sidebar_only_lists_permitted_sections(): void
    {
        $this->actingAs($this->admin(['our_team' => true]), 'admin');

        $this->get('/admin/dashboard')
            ->assertSee('Our Team')
            ->assertDontSee('User Permission')
            ->assertDontSee('All Properties');
    }

    public function test_permissions_can_be_revoked(): void
    {
        $manager = $this->admin(['manage_users' => true]);
        $target = $this->admin(['all_property' => true, 'our_team' => true]);
        $this->actingAs($manager, 'admin');

        // Unticked boxes arrive as the hidden "0" inputs.
        $this->put("/admin/user-permission/{$target->id}", [
            'all_property' => 0,
            'featured_image' => 0,
            'add_now' => 0,
            'property_image' => 0,
            'our_team' => 1,
            'manage_users' => 0,
        ])->assertRedirect();

        $fresh = $target->permission()->first();
        $this->assertFalse($fresh->all_property);
        $this->assertTrue($fresh->our_team);

        // Even with the hidden inputs missing, an omitted box means "off".
        $this->put("/admin/user-permission/{$target->id}", ['our_team' => 1])->assertRedirect();
        $this->assertFalse($target->permission()->first()->all_property);
    }

    public function test_admin_cannot_remove_their_own_permission_access(): void
    {
        $manager = $this->admin(['manage_users' => true, 'our_team' => true]);
        $this->actingAs($manager, 'admin');

        $this->put("/admin/user-permission/{$manager->id}", ['our_team' => 1, 'manage_users' => 0])
            ->assertSessionHas('error');

        $this->assertTrue($manager->permission()->first()->manage_users);
    }

    public function test_new_team_member_login_is_an_admin_with_no_access(): void
    {
        $this->actingAs($this->admin(['our_team' => true]), 'admin');

        $this->post('/admin/our_team', [
            'employee_name' => 'Asha',
            'designation' => 'Agent',
            'user_id' => 'asha@example.com',
            'password' => 'secret-pass',
            'fb_id' => 'https://facebook.com/asha',
            'status' => 1,
        ])->assertRedirect(route('our_team.index'));

        $user = User::where('email', 'asha@example.com')->firstOrFail();
        $this->assertSame('admin', $user->role);
        $this->assertNotSame('secret-pass', $user->password);
        $this->assertFalse($user->hasPermission('all_property', 'our_team', 'manage_users'));

        $member = OurTeam::where('user_id', 'asha@example.com')->firstOrFail();
        $this->assertSame('https://facebook.com/asha', $member->fb_id_link);
        $this->assertArrayNotHasKey('password', $member->getAttributes());
    }

    public function test_team_member_without_login_can_be_added_and_edited(): void
    {
        $this->actingAs($this->admin(['our_team' => true]), 'admin');

        $this->post('/admin/our_team', ['employee_name' => 'Ravi', 'designation' => 'Sales', 'status' => 1])
            ->assertRedirect(route('our_team.index'));

        $member = OurTeam::firstOrFail();
        $this->assertNull($member->user_id);

        $this->put("/admin/our_team/{$member->id}", ['employee_name' => 'Ravi K', 'designation' => 'Sales', 'status' => 1])
            ->assertRedirect(route('our_team.index'));

        $this->assertSame('Ravi K', $member->fresh()->employee_name);
    }

    public function test_deleting_a_team_member_removes_their_login_and_permissions(): void
    {
        $this->actingAs($this->admin(['our_team' => true]), 'admin');
        $this->post('/admin/our_team', [
            'employee_name' => 'Asha', 'designation' => 'Agent', 'user_id' => 'asha@example.com', 'password' => 'secret-pass', 'status' => 1,
        ]);
        $member = OurTeam::firstOrFail();
        $userId = User::where('email', 'asha@example.com')->value('id');

        $this->delete("/admin/our_team/{$member->id}")->assertRedirect(route('our_team.index'));

        $this->assertDatabaseMissing('our_team', ['id' => $member->id]);
        $this->assertDatabaseMissing('users', ['id' => $userId]);
        $this->assertDatabaseMissing('user_permission', ['user_id' => $userId]);
    }

    public function test_admin_cannot_delete_their_own_account_through_the_team_list(): void
    {
        $me = $this->admin(['our_team' => true]);
        $member = OurTeam::create(['employee_name' => 'Me', 'designation' => 'Boss', 'user_id' => $me->email]);
        $this->actingAs($me, 'admin');

        $this->delete("/admin/our_team/{$member->id}")->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $me->id]);
    }

    public function test_property_forms_have_one_name_box_per_fixed_place_and_none_in_custom_rows(): void
    {
        $this->actingAs($this->admin(['add_now' => true]), 'admin');

        $html = $this->get('/admin/propertylisting')->assertOk()->getContent();

        // Six fixed places, each with exactly one optional name field.
        $this->assertSame(6, preg_match_all('/name="place_names\[[a-z_]+\]"/', $html));

        // The custom-place row template has a single "Place name" input and no extra name box.
        preg_match('/<template class="custom-places__template">(.*?)<\/template>/s', $html, $m);
        $this->assertSame(1, substr_count($m[1], 'type="text"'));
        $this->assertStringNotContainsString('place_names[', $m[1]);
    }

    public function test_our_team_page_lists_team_members_and_login_only_users_with_their_access(): void
    {
        $viewer = $this->admin(['our_team' => true, 'manage_users' => true]);
        $owner = User::factory()->create(['role' => 'admin', 'name' => 'Site Owner', 'email' => 'owner@example.com']); // no permission row
        $student = User::factory()->create(['role' => 'student', 'name' => 'Old Student', 'email' => 'old@example.com']);

        $memberUser = $this->admin(['all_property' => true, 'add_now' => true]);
        OurTeam::create(['employee_name' => 'Asha Rao', 'designation' => 'Agent', 'user_id' => $memberUser->email]);
        OurTeam::create(['employee_name' => 'Ravi (no login)', 'designation' => 'Sales']);

        $this->actingAs($viewer, 'admin')->get('/admin/our_team')
            ->assertOk()
            ->assertSee('Asha Rao')
            ->assertSee('Team member + login')
            ->assertSee('Ravi (no login)')
            ->assertSee('Site Owner')            // login-only user, no team profile
            ->assertSee('Login only')
            ->assertSee('Full access')           // owner without a permission row
            ->assertSee('Add New Property')      // Asha's ticked sections
            ->assertSee('No admin access')       // the student account
            ->assertSee('Old Student')
            ->assertSee(route('user_permission.edit', $memberUser->id), false);
    }

    public function test_access_links_only_show_for_users_who_can_manage_permissions(): void
    {
        $viewer = $this->admin(['our_team' => true]); // no manage_users
        $other = User::factory()->create(['role' => 'admin', 'name' => 'Someone Else']);

        $this->actingAs($viewer, 'admin')->get('/admin/our_team')
            ->assertOk()
            ->assertSee('Someone Else')
            ->assertDontSee(route('user_permission.edit', $other->id), false);
    }
}
