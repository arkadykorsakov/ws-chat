<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
	use HasFactory;

	protected $fillable = [
		'chat_id',
		'user_id',
		'body',
	];

	public function getTimeAttribute()
	{
		return $this->created_at->diffForHumans();
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}

	public function getIsOwnerAttribute()
	{
		return (int) $this->user_id  === (int)auth()->id();
	}
}
