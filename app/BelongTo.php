<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BelongTo extends Model
{

  public $incrementing = false;
  protected $keyType = 'string';
  protected $fillable = ['story_id', 'topic_id'];
  public $timestamps = false;

  /**
   * The stories belonging to this topic.
   */
  public function stories() {
    return $this->belongsTo('App\Story');
  }


  /**
   * The stories belonging to this topic.
   */
  public function topics() {
    return $this->belongsTo('App\Topic');
  }
}
