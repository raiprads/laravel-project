<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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

    public function returnClientEventsUrl()
    {
    	// source: http://dev.hel.fi/apis/
    	return array(
		    'base_uri' => 'http://api.hel.fi/linkedevents/v0.1/event/',
		    'timeout'  => 2.0,
		);
    }

    public function setDateParams($strDate)
    {
    	$start = "&start=".date("Y-m-d", strtotime("now"));
    	$end = "&end=".date("Y-m-d", strtotime($strDate));

    	return $start.$end;
    }

    public function bookmarks(){

    	return $this->hasMany(Bookmark::class);
    	
    }

    public function saveToBookmark(Bookmark $bookmark, User $user)
    {
    	$bookmark->by($user);

    	return $this->bookmarks()->save($bookmark);
    }

    public function setMessage($action)
    {
    	if($action=='wish'){
    		$message = "Added to wishlist!";
    	}elseif($action=='watch'){
    		$message = "Added to watched archive!";
    	}else{
    		$message = "Added to favorites!";
    	}

    	return $message;
    }
}
