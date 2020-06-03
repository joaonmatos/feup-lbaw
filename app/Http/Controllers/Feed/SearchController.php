<?php

namespace App\Http\Controllers\Feed;

use App\BelongTo;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Story;
use App\Comment;
use App\Topic;

class SearchController extends Controller{

      /**
     * Shows result of a search
     *
     * @param  $request ->query 
     * @return Response
     */
    protected function showSearchFeed(Request $request)
    {

        $search_query = $request->query('query');
   
        $stories = Story::select('story_id', 'title', 'author_id', 'username', 'published_date', 'reality_check', 'rating', 'topic_id', 'url')
                    ->join('belong_tos', 'id', '=', 'belong_tos.story_id') 
                    ->join('member', 'author_id', '=', 'member.id')  
                    ->whereRaw("title @@ plainto_tsquery('english', ?)", [$search_query])
                    ->orWhereRaw("url @@ plainto_tsquery('english', ?)", [$search_query])
                    ->orWhereRaw("username @@ plainto_tsquery('english', ?)", [$search_query])
                    ->orderByRaw("ts_rank(to_tsvector(title), plainto_tsquery('english', ?)) DESC", [$search_query])
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

        return view('pages.feed', ['search' => true, 'topic_name' => NULL, 'stories' => $stories, 'topics' => $topics, 'comments' => $comments]);
    }

       /**
     * Shows result of a search
     *
     * @param  $request ->query 
     * @return Response
     */
    protected function showAdvancedSearchFeed(Request $request)
    {

        // $search_query = $query;
        $search_query = $request->query('query');
        $time_filter = $request->query('time_filter');
        $days=19;

        switch ($time_filter) {
            case "all-time":
                $days=99999999999999;
                break;
            case "last-month":
                $days=31;
                break;
            case "last-week":
                $days=7;
                break;
            case "today":
                $days=1;
                break;
        }
   
        $stories = Story::select('story_id', 'title', 'author_id', 'username', 'published_date', 'reality_check', 'rating', 'topic_id', 'url')
                    ->join('belong_tos', 'id', '=', 'belong_tos.story_id') 
                    ->join('member', 'author_id', '=', 'member.id')  
                    ->whereRaw('extract(day from (NOW()-published_date))<=?', [$days])
                    ->whereRaw("title @@ plainto_tsquery('english', ?)", [$search_query])
                    ->orWhereRaw("url @@ plainto_tsquery('english', ?)", [$search_query])
                    ->orWhereRaw("username @@ plainto_tsquery('english', ?)", [$search_query])
                    ->orderByRaw("ts_rank(to_tsvector(title), plainto_tsquery('english', ?)) DESC", [$search_query])
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

        return view('pages.feed', ['search' => true, 'topic_name' => NULL, 'stories' => $stories, 'topics' => $topics, 'comments' => $comments]);
    }
}