<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BelongTo extends Model
{

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
