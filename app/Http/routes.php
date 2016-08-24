<?php

/**
 * Routes file
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Laravel
 * @author   Ryan Prader <raiprads@gmail.com>
 * @license  https://www.gnu.org/licenses/gpl.html GNU General Public License
 * @link     http://localhost
 */

Route::get('/', 'LinkedeventsController@index');
Route::get('/events/', 'LinkedeventsController@recentEvents');
Route::get('/coming-soon/', 'LinkedeventsController@comingSoon');
Route::get('/past-events/', 'LinkedeventsController@pastEvents');
Route::get('/events/{event}', 'LinkedeventsController@showEvent');
Route::post('/bookmark', 'LinkedeventsController@addToBookmark');

Route::auth();

Route::group(
    ['middleware' => 'auth'], function () {

        // All my routes that needs a logged in user
        Route::get('/home', 'LinkedeventsController@index');
        Route::get('/favorites', 'LinkedeventsController@showFavorites');
        Route::get('/wishlists', 'LinkedeventsController@showWishlists');
        Route::get('/watched', 'LinkedeventsController@showWatched');

    }
);
