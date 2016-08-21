<?php

use App\Linkedevent;
use App\Bookmark;
//use Auth;

function disableBookmarkButton($action, $listingId)
{

	if (Auth::check())
	{
	    $user_id = Auth::User()->id;
		$bookmark = new Bookmark;	    
	    $linkedevent = new Linkedevent;
	    $linkedevent = $bookmark->checkEvent($listingId);

	    if(!is_null($linkedevent)){

	    	$count = $bookmark->checkExistingBookmark(array(
	    		'user_id'=> $user_id,
	    		'linkedevent_id'=> $linkedevent->id,
	    		'action'=> $action
	    	));

		    if($count>0){
		    	return ' disabled ';
		    }

	    }

	}
}

function showNumberOfBookmarks($action, $listingId)
{
	$bookmark = new Bookmark;	    
    $linkedevent = new Linkedevent;
    $linkedevent = $bookmark->checkEvent($listingId);
    $count = 0 ;

    if(!is_null($linkedevent)){

    	$count = $bookmark->getNumberOfBookmarks(array(
    		'linkedevent_id'=> $linkedevent->id,
    		'action'=> $action
    	));

    }

    return $bookmark->getButtonLabel($action, $count);

}
