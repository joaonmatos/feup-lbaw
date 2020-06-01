<?php

namespace App\Http\Controllers;

use App\Story;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function vote(Request $request, $story_id)
    {
        if (!Auth::check()) return response([], 401);

        $user_id = Auth::user()->id;


        DB::table("rates_stories")->updateOrInsert(
            ['user_id' => $user_id, 'story_id' => $story_id],
            ['rating' => $request['rating']]
        );

        $response = ["story" => $story_id,
            "rating" => Story::find($story_id)->rating ];

        return response($response, 200);
    }

    public function removeVote($story_id)
    {
        if (!Auth::check()) return response([], 401);
        
        $user_id = Auth::user()->id;
        DB::table("rates_stories")->whereRaw(
            'user_id = ? and story_id = ?',
            [$user_id, $story_id]
        )->delete();

        $response = ["story" => $story_id,
            "rating" => Story::find($story_id)->rating ];
        return response($response, 200);
    }

    public function getVote($story_id)
    {
        if (!Auth::check()) return response([], 401);
        $user_id = Auth::user()->id;
        
        $vote = DB::table("rates_stories")->whereRaw(
            'user_id = ? and story_id = ?',
            [$user_id, $story_id]
        )->first();
        if (!$vote) return response([], 404);
        
        $response = ["vote" => $vote->rating];
        return response($response, 200);
    }
}
