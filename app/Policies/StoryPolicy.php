<?php

namespace App\Policies;

use App\Story;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StoryPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function delete(User $user, Story $story) {
        return $user->is_admin || $user->id == $story->author_id;
    }
}
