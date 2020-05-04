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

// Authentication

Route::get('signin', 'Auth\LoginController@showLoginForm')->name('signin');
Route::post('signin', 'Auth\LoginController@login');
Route::get('signout', 'Auth\LoginController@logout')->name('signout');
Route::get('signup', 'Auth\RegisterController@showRegistrationForm')->name('signup');
Route::post('signup', 'Auth\RegisterController@register');


// Feeds - Topic
Route::get('/topics/{topic_id}', 'Feed\TopicController@showTopicFeed');

// Stories
Route::get('/stories/{story_id}', 'Story\StoryController@showStoryPage')->name('stories');

// Vote story
Route::put('api/vote', 'VoteController@create');