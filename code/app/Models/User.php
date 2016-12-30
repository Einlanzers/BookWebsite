<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Scout\Searchable;

class User extends Authenticatable
{
	use Notifiable;
	use Searchable;

	protected $fillable = [
		"first_name", "last_name", "email",
	];

	protected $hidden = [
		"password", "remember_token",
	];

	protected $dates = [];

	public $searchable = [
		"first_name", "last_name", "email",
	];
	
	public function userBooks()
	{
		return $this->hasMany("App\Models\UserBook");
	}
	
	public function getFullName()
	{
		if (empty($this->first_name))
			return $this->last_name;
		if (empty($this->last_name))
			return $this->first_name;
		return "{$this->last_name}, {$this->first_name}";
	}
}
