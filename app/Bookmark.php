<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    protected $fillable = ['listing_id', 'action'];

    public function linkedevent()
    {
    	return $this->belongsTo(Linkedevent::class);
    }

    public function by(User $user)
    {
    	$this->user_id = $user->id;
    }

    public function user()
    {
		return $this->belongsTo(User::class);
	}

	public function checkEvent($listing_id)
	{
		$linkedevent = Linkedevent::where('listing_id',$listing_id)->first();

		return $linkedevent;
	}

	public function assignEventInfoAndSave($eventInfo = [])
    {
        //assign values
        $linkedevent = new Linkedevent;

        $linkedevent->title  = $eventInfo->name->fi;
        $linkedevent->listing_id  = $eventInfo->listing_id;
        $linkedevent->start_time = $eventInfo->start_time;
        $linkedevent->end_time = $eventInfo->end_time;
        
        if(isset($eventInfo->description->fi))
            $linkedevent->description = $eventInfo->description->fi;

        if(isset($eventInfo->short_description->fi))
            $linkedevent->short_description = $eventInfo->short_description->fi;

        if(isset($eventInfo->location_extra_info->fi))
            $linkedevent->location = $eventInfo->location_extra_info->fi;

        if(isset($eventInfo->images[0]->url))
            $linkedevent->image = $eventInfo->images[0]->url;

        if(isset($event->info_url->fi))
            $linkedevent->info_url = $eventInfo->info_url->fi;              

        $linkedevent->api_link = 'https://api.hel.fi/linkedevents/v1/event/'.$linkedevent->listing_id .'/?format=json';
        
        $linkedevent->save();

        return $linkedevent;
    }

	public function checkExistingBookmark($bookmark = [])
	{
		$count = Bookmark::where('action', $bookmark['action'])
			->where('linkedevent_id', $bookmark['linkedevent_id'])
			->where('user_id', $bookmark['user_id'])->count();
		return $count;
	}

    public function getNumberOfBookmarks($bookmark = [])
    {
        $count = Bookmark::where('action', $bookmark['action'])
            ->where('linkedevent_id', $bookmark['linkedevent_id'])->count();
        return $count;
    }

    public function getButtonLabel($action, $count)
    {
        if($count>0){
            if($action=='wish'){
                return "($count) Added to Wishlists";
            }elseif($action=='watch'){
                return "($count) Added to Watchlists";
            }elseif($action=='favorite'){
                return "($count) Added to Favorites";
            }
        }else{
            if($action=='wish'){
                return "Add to Wishlists";
            }elseif($action=='watch'){
                return "Add to Watchlists";
            }elseif($action=='favorite'){
                return "Add to Favorites";
            }
        }
    }

}
