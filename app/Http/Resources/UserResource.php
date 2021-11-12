<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'username' => $this->username,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'profile_pic' => $this->profile_pic,
            'retweets' => $this->retweets,
            'followers' => $this->followers,
            'posts' => $this->posts->where('user_id_posted_by', $this->id),
            'following' => $this->following,
            'id' => $this->id,
            'chats' => $this->chats,
        ];
    }
}
