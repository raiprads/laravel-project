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

use Auth;
use App\Bookmark;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;

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

    public $clientUrl;
    public $eventsDateRange = "";

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
    * Get client request for guzzle 
    *
    * @return string
    */
    public function getClientRequest()
    {
        $eventsClient = new Client($this->clientUrl);

        return $eventsClient->request('GET', '?format=json'.$this->eventsDateRange);
    }

    /**
    * Parse the event content to stdclass
    *
    * @return stdObj 'events data'
    */
    public function getEventsContent()
    {
        $eventsResponse = $this->getClientRequest();

        if ($eventsResponse->getStatusCode()==200) {
            $body = $eventsResponse->getBody(true);

            $body = $eventsResponse->getBody(true);
            
            $bodyContents = $body->getContents();

            return json_decode($bodyContents);
        } else {
            return $eventsResponse->getReasonPhrase();
        }
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
    * Fetch a record that shows the user's favorite events
    *
    * @return stdObj
    */
    public function fetchUsersFavoriteEvents()
    {
        
        $bookmark = new Bookmark;

        return $bookmark->where('user_id', Auth::id())
            ->where('action', 'favorite')
            ->join(
                'linkedevents', 'linkedevents.id', '=', 'bookmarks.linkedevent_id'
            )
            ->join('users', 'users.id', '=', 'bookmarks.user_id')
            ->orderBy('start_time', 'DESC')->get();
    
    }

    /**
    * Fetch a record that shows the user's event wishlists
    *
    * @return stdObj
    */
    public function fetchUsersWishLists()
    {
        
        $bookmark = new Bookmark;

        return $bookmark->where('user_id', Auth::id())
            ->where('action', 'wish')
            ->join(
                'linkedevents', 'linkedevents.id', '=', 'bookmarks.linkedevent_id'
            )
            ->join('users', 'users.id', '=', 'bookmarks.user_id')
            ->orderBy('start_time', 'DESC')->get();
    
    }

    /**
    * Fetch a record that shows the user's event watchlists
    *
    * @return stdObj
    */
    public function fetchUsersWatchLists()
    {
        
        $bookmark = new Bookmark;

        return $bookmark->where('user_id', Auth::id())
            ->where('action', 'watch')
            ->join(
                'linkedevents', 'linkedevents.id', '=', 'bookmarks.linkedevent_id'
            )
            ->join('users', 'users.id', '=', 'bookmarks.user_id')
            ->orderBy('start_time', 'DESC')->get();
    
    }


    /**
    * Set the content or list of events for carousel and limit the number
    *
    * @param stdObj $events 'events object that contains the whole list'
    * @param int    $count  'limit the number of events'
    *
    * @return string
    */
    public function setEventsForCarousel($events, $count)
    {
        
        //assign carousel value
        $carousel = [];

        foreach ($events->data as $key => $value) {

            if (isset($value->image)) {

                array_push($carousel, ($value));

            }

        }

        if (count($carousel)>$count) {
            $carousel = array_slice($carousel, 0, $count); 
        } else {
            $carousel = array_slice($carousel, 0, count($carousel)); 
        }

        return $carousel;
    
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
