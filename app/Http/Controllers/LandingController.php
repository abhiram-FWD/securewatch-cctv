<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Camera;
use App\Models\User;

class LandingController extends Controller
{
    public function index()
    {
        $total_cameras = \App\Models\Camera
            ::where('status','active')->count();
        $total_users = \App\Models\User::count();
        
        return view('landing.index', compact(
            'total_cameras',
            'total_users'
        ));
    }
}
