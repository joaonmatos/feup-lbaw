<?php

namespace App\Http\Controllers\Story;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\BelongTo;
use App\Story;
use App\Comment;

class StoryController extends Controller {

      /**
     * Shows topic's feed
     *
     * @param  $topic
     * @return Response
     */
    protected function showStoryPage($story_id)
    {
        // $topic_name = strtolower($topic_name);
        // $topic_id = Topic::select('id')->whereName($topic_name)->get()[0]['id'];

        // TODO: What if topic_id doesn't exist
   
        $story = Story::select('stories.id', 'title', 'author_id', 'username', 'published_date', 'reality_check', 'rating', 'url')
                    ->join('member', 'author_id', '=', 'member.id')
                    ->where('stories.id', '=', $story_id)
                    ->get();
        
        $story_topics = BelongTo::select('topic_id', 'topics.name')
                        ->join('topics', 'topic_id', '=', 'topics.id')
                        ->whereStoryId($story_id)
                        ->get();

        $comments = Comment::select('content', 'author_id', 'published_date', 'username')
                        ->join('member', 'author_id', '=', 'member.id')
                        ->whereStoryId($story_id)
                        ->get();

        
        return view('pages.story', ['story' => $story[0], 'topics' => $story_topics, 'comments' => $comments]);
    }
}