<?php

namespace App\Http\Controllers;

use App\Models\OurTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class OurTeamController extends Controller
{
    public function index()
    {
        $members = OurTeam::all(); // Fetch all team members
        // dd($members);
        return view('admin.our_team.ourteam',compact('members')); // Pass to view
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
            'user_id'       => 'nullable|email|unique:users,email|max:255',
            'password'      => 'required_with:user_id|string|min:6',
            'joining_date'  => 'nullable|date',
            'employee_image'=> 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'fb_id'         => 'nullable|url',
            'twitter'       => 'nullable|url',
            'linkedin'      => 'nullable|url',
            'instagram'     => 'nullable|url',
            'status'        => 'boolean',
        ]);

        $data = $request->all();

        // Create user in users table if user_id is provided
        if (!empty($request->user_id)) {
            $user = User::create([
                'name' => $request->employee_name,
                'email' => $request->user_id,
                'password' => Hash::make($request->password),
            ]);

           
            // $data['user_table_id'] = $user->id; 
        }

        // Upload image
        if ($request->hasFile('employee_image')) {
            $image = $request->file('employee_image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $destinationPath = public_path('upload/team_images');

            // Create the directory if it doesn't exist
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $image->move($destinationPath, $filename);

            // Save relative path for later use
            $data['employee_image'] = 'upload/team_images/' . $filename;
        }

        OurTeam::create($data);

        return redirect()->route('our_team.index')->with('success', 'Team member added.');
    }

    public function show(OurTeam $our_team)
    {
        return view('admin.our_team.show', compact('our_team'));
    }

    public function edit(OurTeam $our_team)
    {
        return view('admin.our_team.edit', compact('our_team'));
    }


    public function update(Request $request, OurTeam $our_team)
    {
        $request->validate([
            'employee_name' => 'required|string|max:255',
            'designation'   => 'required|string|max:255',
            'user_id'       => 'required|string|email|max:255|unique:our_team,user_id,' . $our_team->id,
            'password'      => 'nullable|string|min:6',
            'joining_date'  => 'nullable|date',
            'employee_image'=> 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'fb_id'         => 'nullable|url',
            'twitter'       => 'nullable|url',
            'linkedin'      => 'nullable|url',
            'instagram'     => 'nullable|url',
            'status'        => 'boolean',
        ]);

        $data = $request->except(['employee_image', 'password']);

        //  Update User table if exists
        $user = User::where('email', $our_team->user_id)->first();

        if ($user) {
            $user->name = $request->employee_name;
            $user->email = $request->user_id;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();
        }

        //  If password is being updated, store it hashed in our_team too
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // 📸 Image update
        if ($request->hasFile('employee_image')) {
            // Delete old image if exists
            if ($our_team->employee_image && file_exists(public_path($our_team->employee_image))) {
                unlink(public_path($our_team->employee_image));
            }

            // Save new image
            $image = $request->file('employee_image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $destinationPath = public_path('upload/team_images');

            // Ensure the directory exists
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $image->move($destinationPath, $filename);

            // Save path to DB
            $data['employee_image'] = 'upload/team_images/' . $filename;
        }

        // Update our_team record
        $our_team->update($data);

        return redirect()->route('our_team.index')->with('success', 'Team member updated successfully.');
    }

    public function destroy(OurTeam $our_team)
    {
        $our_team->delete();
        return redirect()->route('our_team.index')->with('success', 'Team member deleted.');
    }
}

