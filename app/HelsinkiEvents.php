<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HelsinkiEvents extends Model
{
    
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


}
