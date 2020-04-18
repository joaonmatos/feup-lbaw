<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'reality_check', 'rating',
    ];

  /**
   * The topics this story belongs to.
   */
  public function topic() {
    return $this->hasMany('App\Topic');
  }

  /**
   * The member who posted this story.
   */
  public function author_id() {
    return $this->belongsTo('App\User');
  }
}
