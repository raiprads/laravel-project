<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Socials extends Model
{
    protected $fillable = ['helsinki_event_id', 'action'];

    public function helsinkiEvents()
    {
    	return $this->belongsTo(HelsinkiEvents::class);
    }

    public function by(User $user)
    {
    	$this->user_id = $user->id;
    }

    public function user()
    {
		return $this->belongsTo(User::class);
	}

}
