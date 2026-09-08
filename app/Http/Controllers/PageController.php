<?php

namespace App\Http\Controllers;
use App\Models\OurTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;



class PageController extends Controller
{
    public function contact()
    {
        return view('pages.contact');
    }
    public function joinus()
    {
        return view('pages.joinus');
    }
    public function assosiatewithus()
    {
        return view('pages.assosiatewithus');
    }
    public function aboutus()
    {
        return view('pages.aboutus');
    }
    public function ourteam()
    {
        $members = OurTeam::all();
        return view('pages.ourteam',compact('members'));
    }

}
