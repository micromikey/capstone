<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountSettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->user_type === 'organization') {
            return view('account.organization-settings', compact('user'));
        }
        
        return view('account.hiker-settings', compact('user'));
    }
}
