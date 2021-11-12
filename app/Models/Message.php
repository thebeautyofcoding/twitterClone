<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable = ['chat_id', 'content', 'sender_id', 'user_id'];
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }
}
