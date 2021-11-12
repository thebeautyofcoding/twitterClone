<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\PostLike;
class PostResource extends JsonResource
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
            'content' => $this->content,
            'user_id_posted_by' => $this->user,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'id' => $this->id,
            'user_id_likes' => $this->user_id_likes,
            'post_like' => $this->post_like,
            'post_like_by_user' => PostLike::where('user_id', auth()->id())
                ->where('post_id', $this->id)
                ->get(),
            'post_likes_post' => PostLike::where('post_id', $this->id)
                ->get()
                ->toArray(),
            'post_like_count' => count($this->post_like),
            'retweet' => $this->retweets,
            'retweeted_by' => RetweetResource::collection(
                $this->retweets
            )->first(),
            'retweet_count' => $this->retweets
                ->where('post_id', $this->id)
                ->count(),
            'retweetedByAuthUser' => $this->retweets
                ->where('user_id', auth()->id())
                ->first(),
            'replies' => ReplyResource::collection($this->postReplies),
            'user_id_posted_by' => $this->user,
        ];
    }
}
