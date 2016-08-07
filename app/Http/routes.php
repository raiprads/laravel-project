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



Route::get('/', 'HelsinkiEventsController@welcome');
Route::get('/events/', 'HelsinkiEventsController@welcome');
Route::get('/events/{event}', 'HelsinkiEventsController@showEvent');

Route::auth();

Route::get('/home', 'HomeController@index');

// Route::get('api/user', ['middleware' => 'auth.basic.once', function() {
//     // Only authenticated users may enter...
// }]);
