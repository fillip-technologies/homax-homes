<?php

namespace Tests\Feature;

use App\Models\OurTeam;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountPasswordTest extends TestCase
{
    use RefreshDatabase;

    private function admin(array $flags = [], array $attributes = []): User
    {
        $user = User::factory()->create(['role' => 'admin', 'password' => 'Old-Pass-1'] + $attributes);
        UserPermission::create(['user_id' => $user->id] + $flags);

        return $user;
    }

    public function test_guests_cannot_reach_the_password_page(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->get(route('admin.password.edit', $user))->assertRedirect(route('admin.login'));
        $this->put(route('admin.password.update', $user), ['password' => 'Whatever123', 'password_confirmation' => 'Whatever123'])
            ->assertRedirect(route('admin.login'));
    }

    public function test_you_can_change_your_own_password_without_the_current_one(): void
    {
        $me = $this->admin(); // no permissions at all: changing your own password needs none

        $this->actingAs($me, 'admin')->get(route('admin.password.edit', $me))
            ->assertOk()->assertDontSee('Current password');

        $this->put(route('admin.password.update', $me), [
            'password' => 'New-Pass-2',
            'password_confirmation' => 'New-Pass-2',
        ])->assertSessionHas('success')->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('New-Pass-2', $me->fresh()->password));
        $this->assertFalse(Hash::check('Old-Pass-1', $me->fresh()->password));
    }

    public function test_a_new_password_must_be_decent_and_confirmed(): void
    {
        $me = $this->admin();
        $this->actingAs($me, 'admin');
        $url = route('admin.password.update', $me);

        $this->put($url, ['password' => 'short1', 'password_confirmation' => 'short1'])
            ->assertSessionHasErrors('password');   // under 8 characters

        $this->put($url, ['password' => 'onlyletters', 'password_confirmation' => 'onlyletters'])
            ->assertSessionHasErrors('password');   // no numbers

        $this->put($url, ['password' => 'New-Pass-2', 'password_confirmation' => 'different-1'])
            ->assertSessionHasErrors('password');   // confirmation mismatch

        $this->put($url, [])->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('Old-Pass-1', $me->fresh()->password)); // nothing changed
    }

    public function test_an_admin_who_manages_users_can_set_another_admins_password_without_their_current_one(): void
    {
        $manager = $this->admin(['manage_users' => true]);
        $other = $this->admin();

        $this->actingAs($manager, 'admin')->get(route('admin.password.edit', $other))
            ->assertOk()->assertDontSee('Current password')->assertSee($other->email);

        $this->put(route('admin.password.update', $other), ['password' => 'Reset-Pass-3', 'password_confirmation' => 'Reset-Pass-3'])
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check('Reset-Pass-3', $other->fresh()->password));
        $this->assertTrue(Hash::check('Old-Pass-1', $manager->fresh()->password)); // their own is untouched
    }

    public function test_other_accounts_are_off_limits_without_manage_users_or_for_non_admins(): void
    {
        $viewer = $this->admin(['our_team' => true]);           // may see the table, but not manage users
        $otherAdmin = $this->admin();
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($viewer, 'admin');
        $this->get(route('admin.password.edit', $otherAdmin))->assertForbidden();
        $this->put(route('admin.password.update', $otherAdmin), ['password' => 'Hack-Pass-9', 'password_confirmation' => 'Hack-Pass-9'])
            ->assertForbidden();
        $this->assertTrue(Hash::check('Old-Pass-1', $otherAdmin->fresh()->password));

        // Even a user manager cannot touch a non-admin account here.
        $manager = $this->admin(['manage_users' => true]);
        $this->actingAs($manager, 'admin')->get(route('admin.password.edit', $student))->assertForbidden();
    }

    public function test_the_table_shows_update_password_only_where_it_is_allowed(): void
    {
        $student = User::factory()->create(['role' => 'student', 'name' => 'Old Student', 'email' => 'old@example.com']);
        $otherAdmin = $this->admin([], ['email' => 'other@example.com']);
        $teamAdmin = $this->admin([], ['email' => 'teamadmin@example.com']);
        OurTeam::create(['employee_name' => 'Asha', 'designation' => 'Agent', 'user_id' => $teamAdmin->email]);

        // Can manage users: own row + every other admin (team member or login-only), never the student.
        $manager = $this->admin(['our_team' => true, 'manage_users' => true]);
        $html = $this->actingAs($manager, 'admin')->get('/admin/our_team')->assertOk()->getContent();
        $this->assertStringContainsString(route('admin.password.edit', $manager), $html);
        $this->assertStringContainsString(route('admin.password.edit', $otherAdmin), $html);
        $this->assertStringContainsString(route('admin.password.edit', $teamAdmin), $html);
        $this->assertStringNotContainsString(route('admin.password.edit', $student), $html);

        // Cannot manage users: only their own row.
        $limited = $this->admin(['our_team' => true]);
        $html = $this->actingAs($limited, 'admin')->get('/admin/our_team')->assertOk()->getContent();
        $this->assertStringContainsString(route('admin.password.edit', $limited), $html);
        $this->assertStringNotContainsString(route('admin.password.edit', $otherAdmin), $html);
        $this->assertStringNotContainsString(route('admin.password.edit', $teamAdmin), $html);
        $this->assertStringContainsString('Update password', $html);
    }
}
