<?php

namespace App\Http\Controllers;

use App\HelsinkiEvents;

use Illuminate\Http\Request;
use App\Http\Requests;
use GuzzleHttp\Client;

class HelsinkiEventsController extends Controller
{
	
	protected $eventsResponse;
	protected $eventsClientUrl;

	public function __construct(HelsinkiEvents $helsinkiEvents)
	{
		$this->eventsClientUrl = $helsinkiEvents->returnClientEventsUrl();
	}

	public function getClientRequest()
	{
		$eventsClient = new Client($this->eventsClientUrl);

		return $eventsClient->request('GET', '?format=json');
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

		return view('welcome', compact('events'));
	}

}
