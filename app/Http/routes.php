<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

DB::listen(function($query){
	//var_dump($query->sql, $query->bindings);
});


Route::get('/', 'LinkedeventsController@welcome');
Route::get('/events/', 'LinkedeventsController@welcome');
Route::get('/coming-soon/', 'LinkedeventsController@comingSoon');
Route::get('/past-events/', 'LinkedeventsController@pastEvents');
Route::get('/events/{event}', 'LinkedeventsController@showEvent');
Route::post('/bookmark', 'LinkedeventsController@addToBookmark');

Route::auth();

Route::group(['middleware' => 'auth'], function () {

	// All my routes that needs a logged in user
   	Route::get('/home', 'HomeController@index');
	Route::get('/favorites', 'LinkedeventsController@showFavorites');
	Route::get('/wishlists', 'LinkedeventsController@showWishlists');
	Route::get('/watched', 'LinkedeventsController@showWatched');

});
