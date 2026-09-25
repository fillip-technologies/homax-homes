<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyInquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.login');
    }
    public function dashboard()
    {
        $totalProperties = Property::count();
        $todayListings = Property::whereDate('created_at', today())->count();
        $totalInquiries = PropertyInquiry::count();
        $todayInquiries = PropertyInquiry::whereDate('created_at', today())->count();
        $totalEnquiries = $totalInquiries;
        $todayEnquiries = $todayInquiries;

        return view('admin.dashboard', compact(
            'totalProperties',
            'todayListings',
            'totalInquiries',
            'todayInquiries',
            'totalEnquiries',
            'todayEnquiries'
        ));
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
                return redirect()->route('admin.login')->with('error', 'Unauthorized user. Admin access only.');
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
}
