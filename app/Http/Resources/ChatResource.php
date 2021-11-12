<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'chat_name' => $this->chat_name,
            'isGroupChat' => $this->isGroupChat,
            'users' => UserResource::collection($this->users),
            'messages' => MessageResource::collection($this->messages),
        ];
    }
}
