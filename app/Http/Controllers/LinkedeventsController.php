<?php

namespace App\Http\Controllers;

use App\Linkedevent;
use App\Bookmark;

use Auth;
use Illuminate\Http\Request;
use App\Http\Requests;
use GuzzleHttp\Client;


class LinkedeventsController extends Controller
{
    protected $eventsResponse;
	protected $eventsClientUrl;

	protected $eventsDateRange;
	protected $comingEventsDateRange;
	protected $pastEventsDateRange;

	public function __construct(Linkedevent $linkedevent)
	{
		$this->eventsDateRange = $linkedevent->setDateParams("+1 month");
		$this->comingEventsDateRange = $linkedevent->setDateParams("+2 month");
		$this->pastEventsDateRange = $linkedevent->setDateParams("-1 month");

		$this->eventsClientUrl = $linkedevent->returnClientEventsUrl();
	}

	public function getClientRequest()
	{
		$eventsClient = new Client($this->eventsClientUrl);

		return $eventsClient->request('GET', '?format=json'.$this->eventsDateRange);
	}

	public function getEventsContent()
	{
		$eventsResponse = $this->getClientRequest();

		if($eventsResponse->getStatusCode()==200){
			$body = $eventsResponse->getBody(true);

			$body = $eventsResponse->getBody(true);
			
			$bodyContents = $body->getContents();

			return json_decode($bodyContents);
		}else{
			return $eventsResponse->getReasonPhrase();
		}
	}

	public function welcome()
	{

		$events = $this->getEventsContent();

		return view('events.index', compact('events'));
	}

	public function comingSoon()
	{
		$this->eventsDateRange = $this->comingEventsDateRange;

		$events = $this->getEventsContent();

		return view('events.comingsoon', compact('events'));
	}

	public function pastEvents()
	{
		$this->eventsDateRange = $this->pastEventsDateRange;

		$events = $this->getEventsContent();

		return view('events.pastevents', compact('events'));
	}

	public function showEvent(Request $request)
	{
		$this->eventsClientUrl = array(
			'base_uri' => 'https://api.hel.fi/linkedevents/v1/event/'.$request->event.'/',
		    'timeout'  => 2.0,
		);

		$this->eventsDateRange = "";

		$event = $this->getEventsContent();

		return view('events.event', compact('event'));
	}

	public function addToBookmark(Request $request, Bookmark $bookmark)
	{
		$linkedevent = $bookmark->checkEvent($request->listing_id);

		if(is_null($linkedevent)){
			//get event info and save it
			$this->eventsClientUrl = array(
				'base_uri' => 'https://api.hel.fi/linkedevents/v1/event/'.$request->listing_id.'/',
			    'timeout'  => 2.0,
			);
			$this->eventsDateRange = "";
			$event = $this->getEventsContent();
			$event->listing_id = $request->listing_id;
			
			$linkedevent = $bookmark->assignEventInfoAndSave($event);			
		}

		if (Auth::check())
		{
			///check here
			$existingEvent = array(
				'action'=>$request->action,
				'linkedevent_id'=>$linkedevent->id,
				'user_id'=>Auth::user()->id,
				);
			$count = $bookmark->checkExistingBookmark($existingEvent);

			if($count==0){
				$bookmark = new Bookmark($request->except('listing_id'));
				$bookmark->linkedevent_id = $linkedevent->id;
				$linkedevent->saveToBookmark($bookmark,Auth::user());

				$count = $bookmark->getNumberOfBookmarks(array(
		    		'linkedevent_id'=> $linkedevent->id,
		    		'action'=> $request->action
		    	));

		    	$buttonLabel = $bookmark->getButtonLabel($request->action, $count);

				$message = array(
						'message' => $linkedevent->setMessage($request->action),
						'button_label' => $buttonLabel
					);
			}else{
				$message = array('message' => "You have bookmarked this already!");
			}
			
		}else{
			$message = array('message' => 0);
		}
		
		echo json_encode($message);

	}

	public function showFavorites()
	{
		$bookmark = new Bookmark;

		$events = $bookmark->where('user_id', Auth::id())
			->where('action', 'favorite')
			->join('linkedevents', 'linkedevents.id', '=', 'bookmarks.linkedevent_id')
			->join('users', 'users.id', '=', 'bookmarks.user_id')
			->orderBy('start_time', 'DESC')->get();
        
		return view('events.favorites', compact('events'));	
	}

	public function showWishlists()
	{
		$bookmark = new Bookmark;

		$events = $bookmark->where('user_id', Auth::id())
			->where('action', 'wish')
			->join('linkedevents', 'linkedevents.id', '=', 'bookmarks.linkedevent_id')
			->join('users', 'users.id', '=', 'bookmarks.user_id')
			->orderBy('start_time', 'DESC')->get();
        
		return view('events.wishlists', compact('events'));	
	}

	public function showWatched()
	{
		$bookmark = new Bookmark;

		$events = $bookmark->where('user_id', Auth::id())
			->where('action', 'watch')
			->join('linkedevents', 'linkedevents.id', '=', 'bookmarks.linkedevent_id')
			->join('users', 'users.id', '=', 'bookmarks.user_id')
			->orderBy('start_time', 'DESC')->get();
        
		return view('events.watch', compact('events'));	
	}


}
