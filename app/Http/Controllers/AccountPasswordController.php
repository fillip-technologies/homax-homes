<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

/**
 * Update a login password from the Our Team & Users table.
 *
 * - Your own password: always allowed.
 * - Another account: only for admin accounts, and only for admins who may manage user permissions.
 * The current password is not asked for: an admin can reset any password they are allowed to change.
 */
class AccountPasswordController extends Controller
{
    public function edit(User $user)
    {
        $this->authorizeFor($user);

        return view('admin.account.password', [
            'user' => $user,
            'isSelf' => $this->isSelf($user),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeFor($user);

        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $user->password = $request->password; // hashed by the model cast
        $user->setRememberToken(Str::random(60)); // sign out "remember me" sessions
        $user->save();

        return redirect()
            ->route('admin.password.edit', $user)
            ->with('success', $this->isSelf($user)
                ? 'Your password has been updated.'
                : "Password updated for {$user->email}.");
    }

    private function isSelf(User $user): bool
    {
        return $user->id === Auth::guard('admin')->id();
    }

    private function authorizeFor(User $user): void
    {
        if ($this->isSelf($user)) {
            return;
        }

        $me = Auth::guard('admin')->user();

        abort_unless($me && $me->hasPermission('manage_users') && $user->role === 'admin', 403, 'Unauthorized action.');
    }
}
