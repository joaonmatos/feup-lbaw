<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CommentController extends Controller
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
     
        // UNCOMMENT ONCE YOU HAVE THE POSSIBILITY OF REPLYING TO A COMMENT
        
        // if(array_key_exists('comment_id', $request)){
        //     echo 'comment';
        //     DB::table("comments")->insert([
        //         [
        //             'content' => $request['content'], 
        //             'author_id' => $user_id, 
        //             'rating' => 0,
        //             'comment_id' => $request['comment_id'],
                    
        //         ]
        //     ]);
        // }

        // if(array_key_exists('story_id', $request)){
        //     echo 'story';
            DB::table("comments")->insert([
                [
                    'content' => $request['content'], 
                    'author_id' => $user_id, 
                    'story_id'=> $request['story_id'], 
                    'rating' => 0
                    
                ]
            ]);
        // }

        
        
        $response = array();
        $response['status'] = 200;

        return $response;
    }

    // public function delete(Request $request)
    // {
    //   $card = Card::find($id);

    //   $this->authorize('delete', $card);
    //   $card->delete();

    //   return $card;
    // }
}
