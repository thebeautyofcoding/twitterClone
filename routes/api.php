<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatsController;
use App\Http\Controllers\FollowersController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use Illuminate\Broadcasting\BroadcastController;

header('Access-Control-Allow-Origin:  http://127.0.0.1:8081/');
header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, PATCH, DELETE');
header(
    'Access-Control-Allow-Headers: Accept, Content-Type, X-Auth-Token, Origin, Authorization, X-Socket-ID'
);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/posts/like', [PostController::class, 'likeOrUnlike']);
    Route::post('/broadcasting/auth', [
        BroadcastController::class,
        'authenticate',
    ]);
    Route::post('/posts/retweet', [PostController::class, 'retweet']);
    Route::post('/posts/reply', [PostController::class, 'replyToPost']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/create/posts/{user}', [PostController::class, 'store']);
    Route::get('/profile/following/{username}', [
        FollowersController::class,
        'getFollowing',
    ]);
    Route::post('/messages', [ChatsController::class, 'sendMessage']);

    Route::post('/private-messages', [
        ChatsController::class,
        'sendPrivateMessage',
    ]);
    Route::get('/messages/chat/{id}', [
        ChatsController::class,
        'getMessagesByChatId',
    ]);
    Route::get('/messages/{id}', [ChatsController::class, 'getChatById']);

    Route::get('/messages', [ChatsController::class, 'getChats']);

    Route::post('/messages/new/', [ChatsController::class, 'createChat']);

    Route::post('/search/users', [SearchController::class, 'searchUsers']);
    Route::get('/profile/followers/{username}', [
        FollowersController::class,
        'getFollowers',
    ]);
    Route::get('/posts/replies/{postId}', [
        PostController::class,
        'allRepliesOfPost',
    ]);

    Route::post('/followers/{toFollowId}', [
        FollowersController::class,
        'followOrUnfollow',
    ]);

    Route::get('/posts/{postId}', [PostController::class, 'show']);
    Route::get('/posts', [PostController::class, 'index']);
    // Route::get('/images/user/{user}', [AuthController::class, 'profilePic']);
});

Route::post('/register', [AuthController::class, 'process_signup']);

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::delete('/delete/posts/{post}', [PostController::class, 'destroy']);

Route::get('/profile/posts/{username}', [
    ProfileController::class,
    'getPostsByUsername',
]);
Route::get('/profile/{username}', [ProfileController::class, 'show']);
Route::post('/search', [SearchController::class, 'search']);

Route::post('/search/posts', [SearchController::class, 'searchPosts']);
Route::post('/profile/avatar/{id}', [
    ProfileController::class,
    'uploadProfileAvatar',
]);
Route::get('/profile/avatar/{id}', [
    ProfileController::class,
    'showProfileAvatar',
]);

// Route::post('/followings/{userId}', [ProfileController::class, '']);
