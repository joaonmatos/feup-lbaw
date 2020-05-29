<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\RegistersUsers;

use App\BelongTo;
use App\Story;
use App\Comment;
use App\User;


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
        $validatedData = $request->validate([
            'password' => 'required|string|min:6|confirmed'
        ]);

        if(Hash::check($request->password, Auth::user()->password)){
            //Current password and new password are same
            return redirect()->back()->with("error","New Password cannot be the same as your current password. Please choose a different password.");
        }

        //Change Password
        $user = Auth::user();
        $user->password = bcrypt($request->password);
        $user->save();


        return redirect()->route('settings-page')->with("success","Password changed successfully !");
    }

    public function deleteAccountForm()
    {
        return view('pages.delete-account');
    }

    public function deleteAccountAction(Request $request)
    {
        if(!(Hash::check($request->password, Auth::user()->password))){
            //Passwords do not match
            return redirect()->route('settings-page')->with("error","Incorrect password");
        }

        $user_id = Auth::user()->id;

        DB::table('stories')->where('author_id', $user_id)->delete();
        DB::table('comments')->where('author_id', $user_id)->delete();

        Auth::logout();
        DB::table('member')->where('id', $user_id)->delete();

        return redirect('/')->with("success","Account has been deleted successfully !");
    }
}