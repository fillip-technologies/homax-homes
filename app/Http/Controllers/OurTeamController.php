<?php

namespace App\Http\Controllers;

use App\Models\OurTeam;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OurTeamController extends Controller
{
    private const IMAGE_DIR = 'upload/team_images';

    public function index()
    {
        $members = OurTeam::with('user.permission')->get();

        // Login accounts that have no team profile (owners, old accounts), so every user is manageable here.
        $loginOnlyUsers = User::with('permission')
            ->whereNotIn('email', $members->pluck('user_id')->filter())
            ->orderBy('name')
            ->get();

        return view('admin.our_team.ourteam', compact('members', 'loginOnlyUsers'));
    }

    public function create()
    {
        return view('admin.our_team.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_name' => 'required|string|max:255',
            'designation'   => 'required|string|max:255',
            'user_id'       => 'nullable|email|max:255|unique:users,email|unique:our_team,user_id',
            'password'      => 'required_with:user_id|nullable|string|min:6',
            'joining_date'  => 'nullable|date',
            'employee_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'fb_id'         => 'nullable|url',
            'twitter'       => 'nullable|url',
            'linkedin'      => 'nullable|url',
            'instagram'     => 'nullable|url',
            'status'        => 'boolean',
        ]);

        $data = $this->profileData($request);

        if ($request->hasFile('employee_image')) {
            $data['employee_image'] = $this->storeImage($request->file('employee_image'));
        }

        DB::transaction(function () use ($request, $data) {
            if ($request->filled('user_id')) {
                $this->createLogin($request->employee_name, $request->user_id, $request->password);
            }

            OurTeam::create($data);
        });

        $message = $request->filled('user_id')
            ? 'Team member added. Tick their sections under User Permission so they can use the admin panel.'
            : 'Team member added.';

        return redirect()->route('our_team.index')->with('success', $message);
    }

    public function edit(OurTeam $our_team)
    {
        return view('admin.our_team.edit', compact('our_team'));
    }

    public function update(Request $request, OurTeam $our_team)
    {
        $user = $our_team->user_id ? User::where('email', $our_team->user_id)->first() : null;

        $request->validate([
            'employee_name' => 'required|string|max:255',
            'designation'   => 'required|string|max:255',
            // A member who already has a login must keep an email.
            'user_id'       => [
                $user ? 'required' : 'nullable',
                'email',
                'max:255',
                'unique:our_team,user_id,' . $our_team->id,
                'unique:users,email' . ($user ? ',' . $user->id : ''),
            ],
            // Adding a login to a member who has none needs a password.
            'password'      => [($user ? 'nullable' : 'required_with:user_id'), 'nullable', 'string', 'min:6'],
            'joining_date'  => 'nullable|date',
            'employee_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'fb_id'         => 'nullable|url',
            'twitter'       => 'nullable|url',
            'linkedin'      => 'nullable|url',
            'instagram'     => 'nullable|url',
            'status'        => 'boolean',
        ]);

        $data = $this->profileData($request);

        if ($request->hasFile('employee_image')) {
            $this->deleteImage($our_team->employee_image);
            $data['employee_image'] = $this->storeImage($request->file('employee_image'));
        }

        DB::transaction(function () use ($request, $our_team, $user, $data) {
            if ($user) {
                $user->name = $request->employee_name;
                $user->email = $request->user_id;

                if ($request->filled('password')) {
                    $user->password = Hash::make($request->password);
                }

                $user->save();
            } elseif ($request->filled('user_id')) {
                $this->createLogin($request->employee_name, $request->user_id, $request->password);
            }

            $our_team->update($data);
        });

        return redirect()->route('our_team.index')->with('success', 'Team member updated successfully.');
    }

    public function destroy(OurTeam $our_team)
    {
        $user = $our_team->user_id ? User::where('email', $our_team->user_id)->first() : null;

        if ($user && $user->id === Auth::guard('admin')->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        DB::transaction(function () use ($our_team, $user) {
            // Removing the login also removes its user_permission row (cascade).
            $user?->delete();
            $our_team->delete();
        });

        $this->deleteImage($our_team->employee_image);

        return redirect()->route('our_team.index')->with('success', 'Team member deleted.');
    }

    /** Form fields mapped onto the our_team columns; the login password never goes in this table. */
    private function profileData(Request $request): array
    {
        return [
            'employee_name'  => $request->employee_name,
            'designation'    => $request->designation,
            'user_id'        => $request->filled('user_id') ? $request->user_id : null,
            'joining_date'   => $request->joining_date,
            'fb_id_link'     => $request->fb_id,
            'twitter_link'   => $request->twitter,
            'linkedin_link'  => $request->linkedin,
            'instagram_link' => $request->instagram,
            'status'         => $request->boolean('status'),
        ];
    }

    /**
     * Create an admin login with an empty permission row, so the new account
     * sees nothing until a permission manager ticks its sections.
     */
    private function createLogin(string $name, string $email, string $password): User
    {
        $user = new User([
            'name' => $name,
            'email' => $email,
            'password' => $password, // hashed by the model cast
        ]);
        $user->role = 'admin';
        $user->save();

        UserPermission::create(['user_id' => $user->id]);

        return $user;
    }

    private function storeImage(UploadedFile $image): string
    {
        $directory = public_path(self::IMAGE_DIR);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = time() . '_' . Str::random(8) . '.' . $image->guessExtension();
        $image->move($directory, $filename);

        return self::IMAGE_DIR . '/' . $filename;
    }

    private function deleteImage(?string $path): void
    {
        if ($path && is_file(public_path($path))) {
            @unlink(public_path($path));
        }
    }
}
