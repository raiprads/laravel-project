<?php

namespace App\Http\Controllers;

use App\HelsinkiEvents;
use App\Socials;

use Auth;
use Illuminate\Http\Request;
use App\Http\Requests;
use GuzzleHttp\Client;

class HelsinkiEventsController extends Controller
{
	
	protected $eventsResponse;
	protected $eventsClientUrl;
	protected $eventsDateRange;

	public function __construct(HelsinkiEvents $helsinkiEvents)
	{
		$this->eventsDateRange = $helsinkiEvents->setDateParams("+1 month");
		
		$this->eventsClientUrl = $helsinkiEvents->returnClientEventsUrl();
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

	public function social(Request $request, HelsinkiEvents $helsinkiEvents)
	{
		$action = $request->action;

		$social = new Socials($request->all()); 
		
		if (Auth::check())
		{
			$helsinkiEvents->addToSocial($social,Auth::user());

			$message = array('message' => $helsinkiEvents->setMessage($action));
		}else{
			$message = array('message' => 0);
		}
		
		echo json_encode($message);

	}




}
