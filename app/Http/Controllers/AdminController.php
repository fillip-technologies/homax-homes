<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.login');
    }
    public function dashboard()
    {
        return view('admin.dashboard');
    }
    public function ourteam()
    {
        return view('admin.ourteam');
    }
    public function form()
    {
        return view('admin.form');
    }
    public function table()
    {
        return view('admin.table');
    }
    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
            if (Auth::guard('admin')->user()->role != 'admin') {
                Auth::guard('admin')->logout();
                return redirect()->route('admin.login')->with('error', 'Unautherise user, credentials');
            } else {
                return redirect()->route('admin.dashboard');
            }
        } else {
            return redirect()->route('admin.login')->with('error', 'Invalid credentials');
        }
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
    public function register()
    {
        $user = new User();
        $user->name = 'Student';
        $user->role = 'student';
        $user->email = 'student@gmail.com';
        $user->password = Hash::make('register123');
        $user->save();
    }
}
