<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBook extends Model
{
	protected $guarded = ["id"];

	protected $hidden = [];

	protected $dates = ["date"];

	public function user()
	{
		return $this->belongsTo("App\Models\User");
	}

	public function book()
	{
		return $this->belongsTo("App\Models\Book");
	}
}
