<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{

  public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'name',
  ];

  // /**
  //  * The stories belonging to this topic.
  //  */
  // public function belongs_to() {
  //   return $this->hasMany('App\Story');
  // }
}
