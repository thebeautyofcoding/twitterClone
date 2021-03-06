<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    //latest message
    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('id', 'asc');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'creator');
    }
}
