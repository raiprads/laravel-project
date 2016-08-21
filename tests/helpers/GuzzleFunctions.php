<?php 

use GuzzleHttp\Client;

class GuzzleFunctions
{
	
	protected $base_uri;
	protected $date_range;

	public function __construct($base_uri, $date_range = "")
	{
		$this->base_uri = $base_uri;

		$this->date_range = $date_range;
	}

	public function setClientRequest()
	{
		
		$eventsClient = new Client(array('base_uri' => $this->base_uri ));

		$response = $eventsClient->request('GET', '?format=json'.$this->date_range);

		return $response;
	}

	public function getAndParseContent($eventsResponse)
	{
		
		if($eventsResponse->getStatusCode()==200){
			$body = $eventsResponse->getBody(true);

			$body = $eventsResponse->getBody(true);
			
			$bodyContents = $body->getContents();

			return json_decode($bodyContents);
		}else{
			return $eventsResponse->getReasonPhrase();
		}
	
	}


}


