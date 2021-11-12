<?php

use App\Models\Chat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });
// Broadcast::channel('chat', function ($user, $message) {
//     return true;
// });
Broadcast::channel('chat', function ($message, $user) {
    return $message->sender->id !== $user->id;
});

Broadcast::channel('chat{chatId}', function ($chatId) {
    return Auth::check();
});
Broadcast::channel('joinedUsers{chatId}', function ($user, $chatId) {
    return $user;
});
Broadcast::channel('posts', function ($post) {
    return Auth::check();
});

Broadcast::channel('likes{userId}', function ($userId) {
    return Auth::check();
});
