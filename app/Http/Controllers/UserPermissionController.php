<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPermissionController extends Controller
{
    public function index()
    {
        $permissions = UserPermission::with('user')->has('user')->get();

        return view('admin.user_permission.index', compact('permissions'));
    }

    public function create()
    {
        // Admin accounts that do not have a permission row yet.
        $users = User::where('role', 'admin')->doesntHave('permission')->orderBy('name')->get();

        return view('admin.user_permission.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|unique:user_permission,user_id',
        ]);

        UserPermission::create(['user_id' => $request->user_id] + $this->flagsFrom($request));

        return redirect()->route('user_permission.index')->with('success', 'Permission added successfully.');
    }

    public function edit($userId)
    {
        $user = User::findOrFail($userId);
        $permission = UserPermission::where('user_id', $userId)->first();

        return view('admin.user_permission.edit', compact('user', 'permission'));
    }

    public function update(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $flags = $this->flagsFrom($request);

        // Do not let admins lock themselves out of this page.
        if ($user->id === Auth::guard('admin')->id() && !$flags['manage_users']) {
            return back()
                ->withInput()
                ->with('error', 'You cannot remove your own User Permissions access.');
        }

        UserPermission::updateOrCreate(['user_id' => $user->id], $flags);

        return redirect()->back()->with('success', 'Permissions updated successfully.');
    }

    /**
     * Every flag shown in the form, as a strict boolean. An unticked checkbox is
     * simply missing from the request, so read each flag explicitly instead of
     * looping over whatever was submitted.
     */
    private function flagsFrom(Request $request): array
    {
        $flags = [];

        foreach (array_keys(UserPermission::LABELS) as $flag) {
            $flags[$flag] = $request->boolean($flag);
        }

        return $flags;
    }
}
