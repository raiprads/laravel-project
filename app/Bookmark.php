<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    protected $fillable = ['listing_id', 'action'];

    public function linkedvent()
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

	public function checkExistingBookmark($bookmark = [])
	{
		$count = Linkedevent::where('listing_id', $bookmark->listing_id)
			->where('linkedevent_id', $bookmark->linkedevent_id)
			->where('user_id', $bookmark->user_id)->count();
		return $count;
	}
}
