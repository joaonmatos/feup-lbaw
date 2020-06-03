<?php

namespace App\Http\Controllers\Feed;

use App\BelongTo;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
Use Exception;

use App\Story;
use App\Comment;
use App\Topic;

class TopicController extends Controller{

      /**
     * Shows topic's feed
     *
     * @param  $topic
     * @return Response
     */
    protected function showTopicFeed($topic_name)
    {
        $topic_name = strtolower($topic_name);
        try{
            $topic_id = Topic::select('id')->whereName($topic_name)->get()[0]['id'];

                $stories = Story::select('story_id', 'title', 'author_id', 'username', 'published_date', 'reality_check', 'rating', 'topic_id', 'url')
                            ->join('belong_tos', 'id', '=', 'belong_tos.story_id') 
                            ->join('member', 'author_id', '=', 'member.id')  
                            ->whereTopicId($topic_id)
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

            return view('pages.feed', ['search' => false, 'topic_name' => $topic_name, 'stories' => $stories, 'topics' => $topics, 'comments' => $comments]);
        } catch(Exception $ex){ 
            return view('pages.feed', ['search' => false, 'topic_name' => $topic_name, 'stories' => [], 'topics' => [], 'comments' => []]);
        }
    }
}