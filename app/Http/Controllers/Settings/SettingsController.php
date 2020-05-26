<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function show()
    {
        return view('pages.settings');
    }

    public function changePasswordForm()
    {
        return view('pages.change-password');
    }
}