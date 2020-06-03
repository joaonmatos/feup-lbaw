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
Route::get('/topics', 'Feed\TopicController@showAllTopics');
Route::get('/topics/{topic_id}', 'Feed\TopicController@showTopicFeed');
Route::post('/topics/{topic_id}/follow', 'Feed\TopicController@followTopic');
Route::post('/topics/{topic_id}/unfollow', 'Feed\TopicController@unfollowTopic');

// Feeds - Default
Route::get('/', 'Feed\DefaultController@showDefaultFeed');

// Feeds - Search
Route::get('/search', 'Feed\SearchController@showSearchFeed')->name('search');
Route::get('/advanced-search', 'Feed\SearchController@showAdvancedSearchFeed')->name('advanced-search');

// Stories
Route::get('/stories/{story_id}', 'Story\StoryController@showStoryPage')->name('stories')->where('story_id', '[0-9]+');
Route::get('/stories/new', 'Story\StoryController@showNewStoryForm')->name('new-story-form');
Route::post('/stories', 'Story\StoryController@postStory')->name('new-story-action');
Route::delete('/stories/{story_id}', 'Story\StoryController@delete')->where('story_id', '[0-9]+');

// Vote story
Route::put('api/stories/{story_id}/rate', 'VoteController@vote');
Route::delete('api/stories/{story_id}/rate', 'VoteController@removeVote');
Route::get('api/stories/{story_id}/rate', 'VoteController@getVote');

// Comments
Route::put('api/comment', 'CommentController@create');

// Static Pages
Route::view('/about', 'pages.about');

// Settings
Route::get('settings', 'Settings\SettingsController@show')->name('settings-page');
Route::post('settings','Settings\SettingsController@deleteAccountAction')->name('delete-account');
Route::get('settings/password', 'Settings\SettingsController@changePasswordForm')->name('change-password');
Route::post('settings/password','Settings\SettingsController@changePasswordAction')->name('change-password');

// Profile
Route::get('/users/{username}', 'Profile\ProfileController@showProfile');
Route::post('/users/{username}/follow', 'Profile\ProfileController@followProfile');
Route::post('/users/{username}/unfollow', 'Profile\ProfileController@unfollowProfile');