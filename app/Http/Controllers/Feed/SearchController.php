<?php

namespace App\Http\Controllers\Feed;

use App\BelongTo;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Story;
use App\Comment;
use App\Topic;

class SearchController extends Controller{

      /**
     * Shows topic's feed
     *
     * @param  $topic
     * @return Response
     */
    protected function showSearchFeed($search_query)
    {

        // TODO: What if topic_id doesn't exist
   
        $stories = Story::select('story_id', 'title', 'author_id', 'username', 'published_date', 'reality_check', 'rating', 'topic_id', 'url')
                    ->join('belong_tos', 'id', '=', 'belong_tos.story_id') 
                    ->join('member', 'author_id', '=', 'member.id')  
                    ->where('title', 'LIKE', '%'.$search_query.'%')
                    ->orderBy('rating', 'desc')
                    ->get();
        
        $topics = array();
        $comments = array();
        foreach ($stories as $story) {
            $story_topics = BelongTo::select('topic_id', 'topics.name')
                            ->join('topics', 'topic_id', '=', 'topics.id')
                            ->whereStoryId($story['story_id'])
                            ->get()->toArray();
            $topics[$story['story_id']] = $story_topics;
    
            $number_comments = Comment::whereStoryId($story['story_id'])->count();
            $comments[$story['story_id']] = $number_comments;
        }   

        return view('pages.feed', ['topic_name' => $search_query, 'stories' => $stories, 'topics' => $topics, 'comments' => $comments]);
    }
}