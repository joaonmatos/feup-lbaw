<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{

  public $timestamps = false;
   /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'content', 'published_date'
  ];


  /**
   * The card this item belongs to.
   */
  public function story_id() {
    return $this->belongsTo('App\Story');
  }


  /**
   * The card this item belongs to.
   */
  public function author() {
    return $this->belongsTo('App\Member');
  }
}
