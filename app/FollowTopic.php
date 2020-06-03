<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FollowTopic extends Model
{

  public $incrementing = false;
  protected $keyType = 'integer';
  protected $fillable = ['topic_id', 'user_id'];
  public $timestamps = false;

  /**
   * The topics a user follows.
   */
  public function members() {
    return $this->followTopic('App\User');
  }


  /**
   * The stories belonging to this topic.
   */
  public function topics() {
    return $this->followTopic('App\Topic');
  }
}
