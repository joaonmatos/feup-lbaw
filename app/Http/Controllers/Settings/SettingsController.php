<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show()
    {
        return view('pages.settings');
    }

    public function changePasswordForm()
    {
        return view('pages.change-password');
    }

    public function changePasswordAction(Request $request)
    {
        
        $current_password = Auth::user()->password;
        $new_password = password_hash($request->password, PASSWORD_DEFAULT);

        if(strcmp($current_password, $new_password) == 0){
            //Current password and new password are same
            return redirect()->back()->with("error","New Password cannot be the same as your current password. Please choose a different password.");
        }

        $validatedData = $request->validate([
            'password' => 'required|string|min:6|confirmed'
        ]);

        //Change Password
        $user = Auth::user();
        $user->password = $new_password;
        $user->save();


        return redirect()->route('settings-page')->with("success","Password changed successfully !");
    }
}