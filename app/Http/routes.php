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
Route::get('/events/{event}', 'LinkedeventsController@showEvent');
Route::post('/bookmark', 'LinkedeventsController@addToBookmark');

Route::auth();

Route::get('/home', 'HomeController@index');
Route::get('/favorites', 'LinkedeventsController@showFavorites');

// Route::get('api/user', ['middleware' => 'auth.basic.once', function() {
//     // Only authenticated users may enter...
// }]);
