<?php

namespace App\Http\Controllers\Feed;

use App\BelongTo;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Story;
use App\Comment;
use App\Topic;

class DefaultController extends Controller{

      /**
     * Shows default feed
     *
     * @return Response
     */
    protected function showDefaultFeed()
    {
        $vars = array();
        if (Auth::check()) {
            $stories = Story::select('story_id', 'title', 'author_id', 'username', 'published_date', 'reality_check', 'rating', 'follow_topics.topic_id', 'url', DB::raw('rating/(extract(day from (NOW()-published_date)*86400)+0.0000001) as priority'))
                        ->join('belong_tos', 'id', '=', 'belong_tos.story_id') 
                        ->join('follow_topics', 'follow_topics.topic_id', '=', 'belong_tos.topic_id')
                        ->join('member', 'author_id', '=', 'member.id')
                        ->orderBy('priority', 'desc')
                        ->get();
            $vars['logged_in'] = true;

        } else {
            $stories = Story::select('story_id', 'title', 'author_id', 'username', 'published_date', 'reality_check', 'rating', 'topic_id', 'url', DB::raw('rating/(extract(day from (NOW()-published_date)*86400)+0.0000001) as priority'))
                        ->join('belong_tos', 'id', '=', 'belong_tos.story_id') 
                        ->join('member', 'author_id', '=', 'member.id')  
                        ->orderBy('priority', 'desc')
                        ->get();
            $vars['logged_in'] = false;
        }

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

        $vars['topic_name'] = 'news.ly';
        $vars['stories'] = $stories;
        $vars['topics'] = $topics;
        $vars['comments'] = $comments;
        $vars['search'] = false;

        return view('pages.feed', $vars);
    }
}