<?php

use App\Bookmark;
use App\Linkedevent;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PageTest extends TestCase
{

    /** @test */
    public function a_guzzle_client_connection_to_helsinki_api()
    {
    	
    	$response = $this->fetchDataFromApi("https://api.hel.fi/linkedevents/v1/event/");

		$this->assertEquals($response->getStatusCode(), 200);
    
    }

    /** @test */
    public function an_event_page_is_loading_a_content_through_listing_id_from_helsinki_api()
    {
    	
    	$helsinkiApiListingId = "linkedevents:agg-72";

    	$response = $this->fetchDataFromApi("https://api.hel.fi/linkedevents/v1/event/".$helsinkiApiListingId);

		$this->assertEquals($response->getStatusCode(), 200);

    }

    /** @test */
   	public function a_list_that_shows_incoming_events()
    {
    	$linkedevent = new Linkedevent;
    	$date_range = $linkedevent->setDateParams("+2 month");

    	$guzzleUrl = new GuzzleFunctions("https://api.hel.fi/linkedevents/v1/event/",$date_range);

    	$events = $guzzleUrl->getAndParseContent($guzzleUrl->setClientRequest());

    	$this->assertGreaterThan(0,count($events->data));
    
    }

    protected function fetchDataFromApi($url,$date_range = "")
    {

    	$guzzleUrl = new GuzzleFunctions($url,$date_range);

		return $guzzleUrl->setClientRequest();

    }



}
