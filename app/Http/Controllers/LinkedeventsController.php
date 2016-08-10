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

	public function __construct(Linkedevent $linkedevent)
	{
		$this->eventsDateRange = $linkedevent->setDateParams("+1 month");
		
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

	public function assignEventInfo(Linkedevent $linkedevent, $requestListingId)
	{
		//get event info and saving it
		$this->eventsClientUrl = array(
			'base_uri' => 'https://api.hel.fi/linkedevents/v1/event/'.$requestListingId.'/',
		    'timeout'  => 2.0,
		);
		$this->eventsDateRange = "";
		$event = $this->getEventsContent();

		//assign values
		$linkedevent->title  = $event->name->fi;
		$linkedevent->listing_id  = $request->listing_id;
		$linkedevent->start_time = $event->start_time;
		$linkedevent->end_time = $event->end_time;
		
		if(isset($event->description->fi))
			$linkedevent->description = $event->description->fi;

		if(isset($event->short_description->fi))
			$linkedevent->short_description = $event->short_description->fi;

		if(isset($event->location_extra_info->fi))
			$linkedevent->location = $event->location_extra_info->fi;

		if(isset($event->images[0]->url))
			$linkedevent->image = $event->images[0]->url;

		if(isset($event->info_url->fi))
			$linkedevent->info_url = $event->info_url->fi;				

		$linkedevent->api_link = 'https://api.hel.fi/linkedevents/v1/event/'.$requestListingId.'/?format=json';

		return $linkedevent;
	}

	public function addToBookmark(Request $request, Linkedevent $linkedevent)
	{
		$event = Linkedevent::where('listing_id', $request->listing_id )->first();

		if($event === NULL ){
			$linkedevent = assignEventInfo($linkedevent,$request->listing_id);

			$linkedevent->save();
		}else{
			$linkedevent->id = $event->id;
		}

		//echo json_encode($linkedevent->id);

		if (Auth::check())
		{
			///check here
			$findBookmark = array(
				'action'=>$request->action,
				'linkedevent_id'=>$linkedevent->id,
				'user_id'=>Auth::user()->id,
				);
			$count = Bookmark()->checkExistingBookmark($findBookmark);

			if($count==0){
				$bookmark = new Bookmark($request->except('listing_id'));
				$bookmark->linkedevent_id = $linkedevent->id;
				$linkedevent->saveToBookmark($bookmark,Auth::user());
				$message = array('message' => $linkedevent->setMessage($request->action));
			}else{
				$message = array('message' => "You have book marked this already!");
			}
			
		}else{
			$message = array('message' => 0);
		}
		
		echo json_encode($message);

	}

}
