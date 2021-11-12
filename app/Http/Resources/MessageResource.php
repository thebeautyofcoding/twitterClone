<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'chat_id' => $this->chat->id,
            'id' => $this->id,
            'created_at' => $this->created_at,
            'sender' => $this->sender,
            'content' => $this->content,
        ];
    }
}
