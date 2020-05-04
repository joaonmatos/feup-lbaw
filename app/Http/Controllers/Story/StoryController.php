<?php

namespace App\Http\Controllers\Story;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\BelongTo;
use App\Story;
use App\Comment;
use Illuminate\Support\Facades\DB;

class StoryController extends Controller
{

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


        return view('pages.story', ['story' => $story[0], 'topics' => $story_topics, 'comments' => $comments,
            'show_delete' => Auth::check() && (Auth::user()->is_admin == 'true' || $story[0]->author_id == Auth::user()->id )]);
    }

    protected function showNewStoryForm()
    {
        return view('pages.new-story');
    }

    protected function postStory(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:140',
            'link' => 'required|active_url',
            'topic1' => 'required|alpha_dash|different:topic2|different:topic3',
            'topic2' => 'nullable|alpha_dash|different:topic1|different:topic3',
            'topic3' => 'nullable|alpha_dash|different:topic1|different:topic2'
        ]);

        $story = new Story;
        $story->title = $request->title;
        $story->url = $request->link;
        $story->reality_check = 1.0;
        $story->rating = 0;
        $story->author_id = Auth::user()->id;
        $now = new \DateTime;
        $story->published_date = $now->format(\DateTime::ATOM);
        $story->save();

        $topic1 = DB::select('select id from topics where name = ?', [$request->topic1]);
        if (count($topic1) > 0)
            $topic1 = $topic1[0]->id;
        else
            $topic1 = DB::table('topics')->insertGetId(['name' => $request->topic1]);
        $belong1 = new BelongTo(['story_id' => $story->id, 'topic_id' => $topic1]);
        $belong1->save();

        if ($request->topic2) {
            $topic2 = DB::select('select id from topics where name = ?', [$request->topic2]);
            if (count($topic2) > 0)
                $topic2 = $topic2[0]->id;
            else
                $topic2 = DB::table('topics')->insertGetId(['name' => $request->topic2]);
            $belong2 = new BelongTo(['story_id' => $story->id, 'topic_id' => $topic2]);
            $belong2->save();
        }

        if ($request->topic3) {
            $topic3 = DB::select('select id from topics where name = ?', [$request->topic3]);
            if (count($topic3) > 0)
                $topic3 = $topic3[0]->id;
            else
                $topic3 = DB::table('topics')->insertGetId(['name' => $request->topic3]);
            $belong3 = new BelongTo(['story_id' => $story->id, 'topic_id' => $topic3]);
            $belong3->save();
        }

        return redirect(url('/stories/'.$story->id));
    }

    protected function removeStory($request) {
        if (!Auth::check()) {
            return redirect(url('/signin'));
        }
        $this->validate($request, [
            'story_id' => 'required|exists:App\Story,id'
        ]);
        $user_id = Auth::user()->id;

        $story = Story::find($request->story_id);
        if (Auth::user()->is_admin == 'true' || $story->author_id = $user_id) {
            $story->delete();
            return redirect(url('/'));
        }
        return redirect(url('/stories/'.$request->story_id));
    }
}
