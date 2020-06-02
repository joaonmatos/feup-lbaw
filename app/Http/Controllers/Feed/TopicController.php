<?php

namespace App\Http\Controllers\Feed;

use App\BelongTo;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Story;
use App\Comment;
use App\Topic;
use App\FollowTopic;

class TopicController extends Controller
{
    public function followTopic($topic_name)
    {
        if (Auth::check()) {
            $topic_id = Topic::select('id')->whereName($topic_name)->get()[0]["id"];

            DB::table('follow_topics')
                ->insert(['user_id' => Auth::getUser()->id, 'topic_id' => $topic_id]);

            return redirect('topics/');
        }
        
        return redirect('/');
    }

    public function unfollowTopic($topic_name)
    {
        if (Auth::check()) {
            $topic_id = Topic::select('id')->whereName($topic_name)->get()[0]["id"];

            FollowTopic::find([Auth::getUser()->id, $topic_id])->delete();

            return redirect('topics/');
        }
        
        return redirect('/');
    }

    protected function showAllTopics() 
    {
        if (!Auth::check()) return redirect('/');

        $followed = FollowTopic::select('topics.name')
            ->join('topics', 'topics.id', '=', 'topic_id')
            ->join('member', 'member.id', '=', 'user_id')
            ->pluck('name');

        $diff = Topic::select('name')->pluck('name')->diff($followed)->all();
        
        return view('pages.topics', ['followed' => $followed, 'other_topics' => $diff]);
    }

    /**
     * Shows topic's feed
     *
     * @param  $topic
     * @return Response
     */
    protected function showTopicFeed($topic_name)
    {
        $topic_name = strtolower($topic_name);
        $topic_id = Topic::select('id')->whereName($topic_name)->get()[0]['id'];

        // TODO: What if topic_id doesn't exist
   
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

        return view('pages.feed', ['topic_name' => $topic_name, 'stories' => $stories, 'topics' => $topics, 'comments' => $comments]);
    }
}