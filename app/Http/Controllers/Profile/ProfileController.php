<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\BelongTo;
use App\Story;
use App\Comment;


class ProfileController extends Controller
{
    public function showProfile($username)
    {

        $user = DB::table('member')->where('username', '=', $username)->get();

        $following = DB::table('follows')->where('user_id', $user[0]->id)->get();

        $followers = DB::table('follows')->where('friend_id', $user[0]->id)->get();
        
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

        // checks if the authenticated user follows the user whose profile he is checking
        $is_follower = false; 

        if(Auth::check())
        {
            if(strcmp($username, Auth::getuser()->username) != 0)
            {
                $is_follower = DB::table('follows')->where([['user_id', Auth::getUser()->id],['friend_id', $user[0]->id]])->exists();
            }
        }
        
        return view('pages.profile', ['username' => $username, 'following' => $following, 'followers' => $followers, 'is_follower' => $is_follower, 'comments' => $comments, 'stories' => $stories, 'story_topics' => $user_story_topics, 'story_comments' => $user_story_comments]);
    }

    public function followProfile($username)
    {
        $user = DB::table('member')->where('username', '=', $username)->get();

        if(Auth::check())
            DB::table('follows')->insert(['user_id' => Auth::getUser()->id, 'friend_id' => $user[0]->id]);

        return redirect('users/'.$username);
    }

    public function unfollowProfile($username)
    {
        $user = DB::table('member')->where('username', '=', $username)->get();

        if(Auth::check())
            DB::table('follows')->where([['user_id', '=', Auth::getUser()->id], ['friend_id', '=', $user[0]->id]])->delete();

        return redirect('users/'.$username);
    }

}