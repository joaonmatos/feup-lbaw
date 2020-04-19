<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
  // Don't add create and update timestamps in database.
  public $timestamps  = false;

  /**
   * The user this card belongs to
   */
  public function user() {
    return $this->belongsTo('App\User');
  }

  /**
   * Items inside this card
   */
  public function items() {
    return $this->hasMany('App\Item');
  }
}
/*
{
  "id": "2",
  "title": "Corona Virus is not a thing",
  "url": "https://www.example.com/coronavirus_conspiracy",
  "author_id": "523225",
  "username": "fox_news",
  "published_date": "2020-01-20 03:14:07",
  "reality_check": "0.30",
  "rating": "-4"
}
*/