<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', '/topics/politics');

// Cards
Route::get('cards', 'CardController@list');
Route::get('cards/{id}', 'CardController@show');

// API
Route::put('api/cards', 'CardController@create');
Route::delete('api/cards/{card_id}', 'CardController@delete');
Route::put('api/cards/{card_id}/', 'ItemController@create');
Route::post('api/item/{id}', 'ItemController@update');
Route::delete('api/item/{id}', 'ItemController@delete');

// Authentication

Route::get('signin', 'Auth\LoginController@showLoginForm')->name('signin');
Route::post('signin', 'Auth\LoginController@login');
Route::get('signout', 'Auth\LoginController@logout')->name('signout');
Route::get('signup', 'Auth\RegisterController@showRegistrationForm')->name('signup');
Route::post('signup', 'Auth\RegisterController@register');


// Feeds - Topic
Route::get('/topics/{topic_id}', 'Feed\TopicController@showTopicFeed');

// Stories
Route::get('/stories/{story_id}', 'Story\StoryController@showStoryPage')->name('stories')->where('story_id', '[0-9]+');
Route::get('/stories/new', 'Story\StoryController@showNewStoryForm')->name('new-story-form');
Route::post('/stories', 'Story\StoryController@postStory')->name('new-story-action');

// Vote story
Route::put('api/vote', 'VoteController@create');