<?php

use App\User;
use App\Bookmark;
use App\Linkedevent;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    
    use DatabaseTransactions;

    /** @test */
    public function authenticate_a_user_and_visit_a_private_page()
    {
    	
    	$user = factory(App\User::class)->create();

        $this->actingAs($user)
             ->withSession(['foo' => 'bar'])
             ->visit('/favorites')
             ->see('MY FAVORITES');
    	
    }

    /** @test */
    public function a_user_will_add_to_his_favorite_an_event()
    {
    	
    	$user = factory(App\User::class)->create();
    	$listing_id = 'helmet:101421';
    	
    	$count_bookmarks = $this->bookmarkThisEvent($listing_id, $user);
	    
	    $this->assertGreaterThanOrEqual(1,$count_bookmarks);
   
    }

    /** @test */
    public function a_page_that_show_all_favorite_events_of_a_user()
    {
    	$user = factory(App\User::class)->create();
    	$listing_ids = ['helmet:101421','kulke:38874'];

    	foreach($listing_ids as $listing_id){
    		$this->bookmarkThisEvent($listing_id, $user);
    	}
    	
    	$this->visit('/favorites')
              ->see('helmet:101421')
              ->see('kulke:38874');
    
    }

    /** @test */
    public function a_new_user_registration()
    {
    	
    	$this->visit('/register')
        	->type('Ryan Prader', 'name')
        	->type('ryan@foobar.biz', 'email')
        	->type('1234567890', 'password')
        	->type('1234567890', 'password_confirmation')
        	->press('Register')
        	->seePageIs('/');
    
    }

    /** @test */
    public function a_relation_between_user_who_bookmarked_an_event()
    {
    	$bookmark = new Bookmark;	    
	    $linkedevent = new Linkedevent;

    	$user = factory(App\User::class)->create();
    	$listing_id = 'helmet:101421';

    	$this->bookmarkThisEvent($listing_id, $user);

    	$linkedevent = $bookmark->checkEvent($listing_id);

    	$bookmark = Bookmark::where('linkedevent_id', $linkedevent->id)->orderBy('id','desc')->first();

    	$this->assertEquals($bookmark->user_id,$user->id);

    }

    protected function bookmarkThisEvent($listing_id, $user)
    {
    	$bookmark = new Bookmark;	    
	    $linkedevent = new Linkedevent;

    	$action = 'favorite';
    	$guzzleUrl = new GuzzleFunctions('https://api.hel.fi/linkedevents/v1/event/'.$listing_id);

        $this->actingAs($user)
             ->withSession(['foo' => 'bar']);
        
	    $linkedevent = $bookmark->checkEvent($listing_id);

	    if(is_null($linkedevent)){

	    	$eventsResponse = $guzzleUrl->setClientRequest();
	    	$event = $guzzleUrl->getAndParseContent($eventsResponse);
	    	$event->listing_id = $listing_id;

	    	$linkedevent = $bookmark->assignEventInfoAndSave($event);
	    }else{
	    	$ifExistingEvent = array(
				'action'=>$action,
				'linkedevent_id'=>$linkedevent->id,
				'user_id'=>$user->id,
				);
			$count = $bookmark->checkExistingBookmark($ifExistingEvent);

			if($count==0){
				$bookmark->action = $action;
				$bookmark->linkedevent_id = $linkedevent->id;
				$linkedevent->saveToBookmark($bookmark,$user);
			}

	    }

	    $count = $bookmark->getNumberOfBookmarks(array(
		    		'linkedevent_id'=> $linkedevent->id,
		    		'action'=> $action
		    	));

		return $count;

    }

    protected function checkExistingBookmark($action, $inkedevent_id, $user_id)
    {
    	$bookmark = new Bookmark;

    	$existingEvent = array(
				'action'=>$request->action,
				'linkedevent_id'=>$linkedevent_id,
				'user_id'=>$user_id,
				);

		return $bookmark->checkExistingBookmark($existingEvent);

    }


}
