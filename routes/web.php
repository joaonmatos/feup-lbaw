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


// Authentication

Route::get('signin', 'Auth\LoginController@showLoginForm')->name('signin');
Route::post('signin', 'Auth\LoginController@login');
Route::get('signout', 'Auth\LoginController@logout')->name('signout');
Route::get('signup', 'Auth\RegisterController@showRegistrationForm')->name('signup');
Route::post('signup', 'Auth\RegisterController@register');


// Feeds - Topic
Route::get('/topics/{topic_id}', 'Feed\TopicController@showTopicFeed');

// Feeds - Default
Route::get('/', 'Feed\DefaultController@showDefaultFeed');

// Stories
Route::get('/stories/{story_id}', 'Story\StoryController@showStoryPage')->name('stories')->where('story_id', '[0-9]+');
Route::get('/stories/new', 'Story\StoryController@showNewStoryForm')->name('new-story-form');
Route::post('/stories', 'Story\StoryController@postStory')->name('new-story-action');

// Vote story
Route::put('api/vote', 'VoteController@create');

// Comments
Route::put('api/comment', 'CommentController@create');

// Static Pages
Route::view('/about', 'pages.about');

// Settings
Route::get('settings', 'Settings\SettingsController@show')->name('settings-page');
Route::get('settings/password', 'Settings\SettingsController@changePasswordForm')->name('change-password');
Route::post('settings/password','Settings\SettingsController@changePasswordAction')->name('change-password');
Route::post('settings','Settings\SettingsController@deleteAccountAction')->name('delete-account');

// Profile
Route::get('/users/{username}', 'Profile\ProfileController@showProfile');