<?php

namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ProtoneMedia\LaravelCrossEloquentSearch\Search;

class Post extends Model
{
    use HasFactory;
    public $table = 'posts';
    const SEARCHABLE_FIELDS = ['id', 'content'];
    protected $fillable = [
        'user_id_posted_by',
        'user_id_likes',
        'retweet_users',
        'post_id_retweet_data',
        'post_id_reply_to',
        'content',
        'post_like_id',
        'retweet_id',
        'post_reply_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id_posted_by');
    }
    public function post_like()
    {
        return $this->hasMany(PostLike::class);
    }

    public function retweets()
    {
        return $this->hasMany(Retweet::class);
    }

    public function postReplies()
    {
        return $this->hasMany(PostReply::class);
    }
}
