<?php

namespace App\Models;
use App\Models\Post;
use App\Models\Retweet;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    const SEARCHABLE_FIELDS = ['id', 'username'];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    public $table = 'users';
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_pic',
        'cover_pic',
        'profile_pic',
        'post_like_id',
        'follower_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id_posted_by');
    }

    public function post_like()
    {
        return $this->hasMany(PostLike::class);
    }
    public function profilePic()
    {
        return $this->hasOne(Image::class);
    }
    public function coverPic()
    {
        return $this->hasOne(Image::class);
    }

    public function retweets()
    {
        return $this->hasMany(Retweet::class);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function postReplies()
    {
        return $this->hasMany(PostReply::class);
    }

    public function toSearchableArray()
    {
        return $this->only(self::SEARCHABLE_FIELDS);
    }

    public function following()
    {
        return $this->belongsToMany(
            User::class,
            'followers',
            'following_id',
            'follower_id'
        );
    }

    // users that follow this user
    public function followers()
    {
        return $this->belongsToMany(
            User::class,
            'followers',
            'follower_id',
            'following_id'
        );
    }

    public function chats()
    {
        return $this->belongsToMany(Chat::class);
    }
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function createdChat()
    {
        return $this->belongsTo(Chat::class);
    }
}
