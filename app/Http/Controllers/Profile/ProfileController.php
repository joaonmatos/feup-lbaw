<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\BelongTo;
use App\Story;
use App\Comment;
use App\User;


class ProfileController extends Controller
{
    public function showProfile($username)
    {
        //$user_id = User::select('id')->where('username', '=', $username);

        $following = DB::table('follows')->join('member', 'member.id', '=', 'follows.user_id')->where('member.username', $username)->get();

        $followers = DB::table('follows')->join('member', 'member.id', '=', 'follows.friend_id')->where('member.username', $username)->get();
        
        $comments = Comment::select('content', 'author_id', 'published_date', 'username', 'story_id')
        ->join('member', 'author_id', '=', 'member.id')
        ->where('member.username', '=', $username)
        ->get();

        $stories = Story::select('story_id', 'title', 'author_id', 'username', 'published_date', 'reality_check', 'rating', 'topic_id', 'url', DB::raw('rating/(extract(day from (NOW()-published_date)*86400)+0.0000001) as priority'))
        ->join('belong_tos', 'id', '=', 'belong_tos.story_id') 
        ->join('member', 'author_id', '=', 'member.id')  
        ->where('member.username', '=', $username)
        ->get();

        

        $user_story_topics = array();
        $user_story_comments = array();
        foreach ($stories as $story) {
            $story_topics = BelongTo::select('topic_id', 'topics.name')
                            ->join('topics', 'topic_id', '=', 'topics.id')
                            ->whereStoryId($story['story_id'])
                            ->get()->toArray();
            $user_story_topics[$story['story_id']] = $story_topics;
    
            $number_comments = Comment::whereStoryId($story['story_id'])->count();
            $user_story_comments[$story['story_id']] = $number_comments;
        }   

        
        return view('pages.profile', ['username' => $username, 'following' => $following, 'followers' => $followers, 'comments' => $comments, 'stories' => $stories, 'story_topics' => $user_story_topics, 'story_comments' => $user_story_comments]);
    }

   


}