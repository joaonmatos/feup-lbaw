<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    /**
     * Creates a new card.
     *
     * @return Card The card created.
     */
    public function create(Request $request)
    {

        if (!Auth::check()) return [];

        $user_id = Auth::user()->id;


        DB::table("rates_stories")->insert([
            ['user_id' => $user_id, 'story_id'=> $request['story_id'], 'rating' => $request['rating'] ]
        ]);
        
        
        $response = array();
        $response['status'] = 200;

        return $response;
    }

    public function delete(Request $request)
    {
      $card = Card::find($id);

      $this->authorize('delete', $card);
      $card->delete();

      return $card;
    }
}
