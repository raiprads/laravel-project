<?php
/**
 * Linkedevent file
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Laravel
 * @author   Ryan Prader <raiprads@gmail.com>
 * @license  https://www.gnu.org/licenses/gpl.html GNU General Public License
 * @link     http://localhost
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class for Linkedevent model
 * 
 * @category PHP
 * @package  Laravel
 * @author   Ryan Prader <raiprads@gmail.com>
 * @license  https://www.gnu.org/licenses/gpl.html GNU General Public License
 * @version  Release: @package_version@
 * @link     http://localhost
 */
class Linkedevent extends Model
{
    
    protected $fillable = ['title',
    'listing_id',
    'start_time',
    'end_time',
    'short_description',
    'description',
    'location',
    'image',
    'info_url',
    'api_link'];

    /**
    * Set a clients url for guzzle request
    *
    * @return array
    */
    public function returnClientEventsUrl()
    {
        // source: http://dev.hel.fi/apis/
        return array(
        'base_uri' => 'http://api.hel.fi/linkedevents/v0.1/event/',
        'timeout'  => 2.0,
        );
    }

    /**
    * Set the format start and end date parameter for client request
    *
    * @param string $strDate 'this will be converted 
    *to unix timestamp then to php date'
    *
    * @return string
    */
    public function setDateParams($strDate)
    {
        if ($strDate == "+1 month") {
            $startDate = strtotime('now'); 
        } else {
            $startDate = strtotime('-1 month', strtotime($strDate)); 
        }

        $start = "&start=".date("Y-m-d", $startDate);
        $end = "&end=".date("Y-m-d", strtotime($strDate));

        return $start.$end;
    }

    /**
    * Linkedevent has any bookmarks
    *
    * @return string
    */
    public function bookmarks()
    {

        return $this->hasMany(Bookmark::class);
        
    }

    /**
    * Save a bookmark from the user
    *
    * @param Bookmark $bookmark 'bookmark object'
    * @param User     $user     'user object'
    *
    * @return stdObj Bookmark
    */
    public function saveToBookmark(Bookmark $bookmark, User $user)
    {
        $bookmark->by($user);

        return $this->bookmarks()->save($bookmark);
    }

    /**
    * Return a message for ajax request
    *
    * @param string $action 'wish, favorite, watch'
    *
    * @return string
    */
    public function setMessage($action)
    {
        if ($action=='wish') {
            $message = "Added to wishlist!";
        } elseif ($action=='watch') {
            $message = "Added to watched archive!";
        } else {
            $message = "Added to favorites!";
        }

        return $message;
    }

    
}
