<?php
/**
 * Bookmark model file
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
 * Bookmark model registers bookmarks for the events
 * 
 * @category PHP
 * @package  Laravel
 * @author   Ryan Prader <raiprads@gmail.com>
 * @license  https://www.gnu.org/licenses/gpl.html GNU General Public License
 * @version  Release: @package_version@
 * @link     http://localhost
 */
class Bookmark extends Model
{
    protected $fillable = ['listing_id', 'action'];

    /**
    * A bookmark belongs to linkedevent
    *
    * @return stdObj Linkedevent
    */
    public function linkedevent()
    {
        return $this->belongsTo(Linkedevent::class);
    }

    /**
    * Assign a to variable
    *
    *@param User $user 'current user class info'
    *
    * @return int user_id
    */
    public function by(User $user)
    {
        $this->user_id = $user->id;
    }

    /**
    * A bookmark belongs to user
    *
    * @return stdObj User
    */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
    * Check an event if it is all ready in the database
    *
    * @param string $listing_id 'a listing id from helsinki api'
    *
    * @return stdObj $linkedevent
    */
    public function checkEvent($listing_id)
    {
        $linkedevent = Linkedevent::where('listing_id', $listing_id)->first();

        return $linkedevent;
    }

    /**
    * Assign a proper fields then save the event
    *
    * @param array $eventInfo 'optional ?'
    *
    * @return stdObj $linkedevent
    */
    public function assignEventInfoAndSave($eventInfo = [])
    {
        //assign values
        $linkedevent = new Linkedevent;

        if (isset($eventInfo->name->fi)) {
            $linkedevent->title  = $eventInfo->name->fi;    
        } elseif (isset($eventInfo->name->en)) {
            $linkedevent->title  = $eventInfo->name->en;
        } else {
            //just make the title id if still there is no title 
            $linkedevent->title  = $eventInfo->listing_id;
        }
        
        $linkedevent->listing_id  = $eventInfo->listing_id;
        $linkedevent->start_time = $eventInfo->start_time;
        $linkedevent->end_time = $eventInfo->end_time;
        
        if (isset($eventInfo->description->fi)) {
            $linkedevent->description = $eventInfo->description->fi; 
        } elseif (isset($eventInfo->description->en)) {
            $linkedevent->description = $eventInfo->description->en; 
        }

        if (isset($eventInfo->short_description->fi)) {
            $linkedevent->short_description = $eventInfo->short_description->fi; 
        } elseif (isset($eventInfo->short_description->en)) {
            $linkedevent->short_description = $eventInfo->short_description->en; 
        }

        if (isset($eventInfo->location_extra_info->fi)) {
            $linkedevent->location = $eventInfo->location_extra_info->fi; 
        }

        if (isset($eventInfo->images[0]->url)) {
            $linkedevent->image = $eventInfo->images[0]->url; 
        }

        if (isset($event->info_url->fi)) {
            $linkedevent->info_url = $eventInfo->info_url->fi; 
        }              

        $linkedevent->api_link = 'https://api.hel.fi/linkedevents/v1/event/'
            .$linkedevent->listing_id .'/?format=json';
        
        $linkedevent->save();

        return $linkedevent;
    }

    /**
    * Check if the user has bookmark this event already
    *
    * @param array $bookmark 'contains user_id, linkedevent_id, and action'
    *
    * @return int $count
    */
    public function checkExistingBookmark($bookmark = [])
    {
        $count = Bookmark::where('action', $bookmark['action'])
         ->where('linkedevent_id', $bookmark['linkedevent_id'])
         ->where('user_id', $bookmark['user_id'])->count();
        return $count;
    }

    /**
    * Get number of bookmarks fo an event
    *
    * @param array $bookmark 'action, linkedevent_id'
    *
    * @return int $count
    */
    public function getNumberOfBookmarks($bookmark = [])
    {
        $count = Bookmark::where('action', $bookmark['action'])
            ->where('linkedevent_id', $bookmark['linkedevent_id'])->count();
        return $count;
    }

    /**
    * Set button label with counts ex. (100) Add to Favorites
    *
    * @param string $action 'favorite, wish, watch'
    * @param int    $count  'current number of bookmarks'
    *
    * @return string
    */
    public function getButtonLabel($action, $count)
    {
        if ($count>0) {
            if ($action=='wish') {
                return "($count) Added to Wishlists";
            } elseif ($action=='watch') {
                return "($count) Added to Watchlists";
            } elseif ($action=='favorite') {
                return "($count) Added to Favorites";
            }
        } else {
            if ($action=='wish') {
                return "Add to Wishlists";
            } elseif ($action=='watch') {
                return "Add to Watchlists";
            } elseif ($action=='favorite') {
                return "Add to Favorites";
            }
        }
    }

}
