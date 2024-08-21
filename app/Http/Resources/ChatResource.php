<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'title' => $this->title ?? 'With ' . $this->chatWith->name,
			'users' => $this->users,
			'unreadable_count' => $this->unreadable_message_statuses_count,
			'last_message' => isset($this->lastMessage) ? MessageResource::make($this->lastMessage)->resolve() : null,

		];
	}
}
