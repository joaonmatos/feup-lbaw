<?php

namespace App\Http\Controllers;

use App\Story;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function create(Request $request)
    {
        if (!Auth::check()) return response([], 401);

        $user_id = Auth::user()->id;


        DB::table("rates_stories")->insert([
            ['user_id' => $user_id, 'story_id'=> $request['story_id'], 'rating' => $request['rating'] ]
        ]);

        ;
        $response = ["story" => $request->story_id,
            "rating" => Story::find($request->story_id)->rating ];

        return response($response, 200);
    }
}
