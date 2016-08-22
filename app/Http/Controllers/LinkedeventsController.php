<?php
/**
 * LinkedeventsController
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Laravel
 * @author   Ryan Prader <raiprads@gmail.com>
 * @license  https://www.gnu.org/licenses/gpl.html GNU General Public License
 * @link     http://localhost
 */

namespace App\Http\Controllers;

use App\Linkedevent;
use App\Bookmark;

use Auth;
use Illuminate\Http\Request;
use App\Http\Requests;
use GuzzleHttp\Client;

/**
 * LinkedeventsController class manages all the events function from the model
 * 
 * @category PHP
 * @package  Laravel
 * @author   Ryan Prader <raiprads@gmail.com>
 * @license  https://www.gnu.org/licenses/gpl.html GNU General Public License
 * @version  Release: @package_version@
 * @link     http://localhost
 */
class LinkedeventsController extends Controller
{
    protected $eventsResponse;
    protected $eventsClientUrl;

    protected $eventsDateRange;
    protected $comingEventsDateRange;
    protected $pastEventsDateRange;

    /**
    * Initilized the controller
    *
    * @param Linkedevent $linkedevent 'the events model from helsinki api'
    *
    * @return void
    */
    public function __construct(Linkedevent $linkedevent)
    {
        $this->eventsDateRange = $linkedevent->setDateParams("+1 month");
        $this->comingEventsDateRange = $linkedevent->setDateParams("+2 month");
        $this->pastEventsDateRange = $linkedevent->setDateParams("-1 month");

        $this->eventsClientUrl = $linkedevent->returnClientEventsUrl();
    }

    /**
    * Get client request for guzzle 
    *
    * @return string
    */
    public function getClientRequest()
    {
        $eventsClient = new Client($this->eventsClientUrl);

        return $eventsClient->request('GET', '?format=json'.$this->eventsDateRange);
    }

    /**
    * Parse the event content to stdclass
    *
    * @return stdclass 'events data'
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
    * Show the welcome page with lists of events
    *
    * @return view
    */
    public function welcome()
    {

        $events = $this->getEventsContent();

        return view('events.index', compact('events'));
    }

    /**
    * Show the index page with carousel
    *
    * @return view
    */
    public function index()
    {
        
        $events = $this->getEventsContent();

        //assign carousel value
        $carousel = [];

        foreach ($events->data as $key => $value) {

            if (isset($value->image)) {

                array_push($carousel, ($value));

            }

        }

        if (count($carousel)>5) {
            $carousel = array_slice($carousel, 0, 5); 
        } else {
            $carousel = array_slice($carousel, 0, count($carousel)); 
        }

        return view('home', compact('carousel', 'events'));
    
    }

    /**
    * Show lists events 2 months from now
    *
    * @return view
    */
    public function comingSoon()
    {
        $this->eventsDateRange = $this->comingEventsDateRange;

        $events = $this->getEventsContent();

        return view('events.comingsoon', compact('events'));
    }

    /**
    * Show lists events 1 month ago
    *
    * @return view
    */
    public function pastEvents()
    {
        $this->eventsDateRange = $this->pastEventsDateRange;

        $events = $this->getEventsContent();

        return view('events.pastevents', compact('events'));
    }

    /**
    * Show the event in single page
    *
    * @param stdobj $request 'fetch a listing id from the page request'
    *
    * @return view
    */
    public function showEvent(Request $request)
    {
        $this->eventsClientUrl = array(
         'base_uri' => 'https://api.hel.fi/linkedevents/v1/event/'
            .$request->event.'/',
         'timeout'  => 2.0,
        );

        $this->eventsDateRange = "";

        $event = $this->getEventsContent();

        return view('events.event', compact('event'));
    }

    /**
    * Bookmark an event using ajax function, 
    * it may be added to favorites, wishlists or watchlists
    *
    * @param Request  $request  'fetch a request from ajax'
    * @param Bookmark $bookmark 'from the model Bookmark'
    *
    * @return string
    */
    public function addToBookmark(Request $request, Bookmark $bookmark)
    {
        $linkedevent = $bookmark->checkEvent($request->listing_id);

        if (is_null($linkedevent)) {
            //get event info and save it
            $this->eventsClientUrl = array(
             'base_uri' => 'https://api.hel.fi/linkedevents/v1/event/'
                .$request->listing_id.'/',
             'timeout'  => 2.0,
            );
            $this->eventsDateRange = "";
            $event = $this->getEventsContent();
            $event->listing_id = $request->listing_id;
            
            $linkedevent = $bookmark->assignEventInfoAndSave($event);            
        }

        if (Auth::check()) {
            
            ///check here
            $existingEvent = array(
             'action'=>$request->action,
             'linkedevent_id'=>$linkedevent->id,
             'user_id'=>Auth::user()->id,
             );

            $count = $bookmark->checkExistingBookmark($existingEvent);

            if ($count==0) {
                
                $bookmark = new Bookmark($request->except('listing_id'));
                $bookmark->linkedevent_id = $linkedevent->id;
                $linkedevent->saveToBookmark($bookmark, Auth::user());

                $count = $bookmark->getNumberOfBookmarks(
                    array(
                       'linkedevent_id'=> $linkedevent->id,
                       'action'=> $request->action
                    )
                );

                $buttonLabel = $bookmark->getButtonLabel($request->action, $count);

                $message = array(
                    'message' => $linkedevent->setMessage($request->action),
                    'button_label' => $buttonLabel
                );

            } else {
                
                $message = array(
                    'message' => "You have bookmarked this already!"
                );

            }
            
        } else {

            $message = array('message' => 0);

        }
        
        echo json_encode($message);

    }

    /**
    * Show a list of events favorited by the user
    *
    * @return view
    */
    public function showFavorites()
    {
        $bookmark = new Bookmark;

        $events = $bookmark->where('user_id', Auth::id())
            ->where('action', 'favorite')
            ->join(
                'linkedevents', 'linkedevents.id', '=', 'bookmarks.linkedevent_id'
            )
            ->join('users', 'users.id', '=', 'bookmarks.user_id')
            ->orderBy('start_time', 'DESC')->get();
        
        return view('events.favorites', compact('events'));    
    }

    /**
    * Show a list of event in user's wishlists
    *
    * @return view
    */
    public function showWishlists()
    {
        $bookmark = new Bookmark;

        $events = $bookmark->where('user_id', Auth::id())
            ->where('action', 'wish')
            ->join(
                'linkedevents', 'linkedevents.id', '=', 'bookmarks.linkedevent_id'
            )
            ->join('users', 'users.id', '=', 'bookmarks.user_id')
            ->orderBy('start_time', 'DESC')->get();
        
        return view('events.wishlists', compact('events'));    
    }

    /**
    * Show list of events in user's watchlists
    *
    * @return view
    */
    public function showWatched()
    {
        $bookmark = new Bookmark;

        $events = $bookmark->where('user_id', Auth::id())
            ->where('action', 'watch')
            ->join(
                'linkedevents', 'linkedevents.id', '=', 'bookmarks.linkedevent_id'
            )
            ->join('users', 'users.id', '=', 'bookmarks.user_id')
            ->orderBy('start_time', 'DESC')->get();
        
        return view('events.watch', compact('events'));    
    }


}
