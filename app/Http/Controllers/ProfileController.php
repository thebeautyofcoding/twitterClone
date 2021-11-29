<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Post;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request, $username)
    {
        $user = User::where('username', $username)
            ->get()
            ->first();
        if (!$user) {
            return response()->json(['user' => null], 400);
        }
        return response()->json(['user' => new UserResource($user)], 200);
    }

    public function getPostsByUsername(Request $request, $username)
    {
        $user = User::where('username', $username)
            ->get()
            ->first();

        $postsOfUser = Post::where('user_id_posted_by', $user->id)->get();
        return response()->json(['posts' => $postsOfUser], 200);
    }

    public function uploadProfileAvatar(Request $request, $id)
    {
        $authUser = User::find($id);

        $image = $request->file('image');
        $path = $image->store('avatars/' . $authUser->id);
        $authUser->profile_pic = $path;
        $authUser->save();

        return response()->file(storage_path('app/') . $path);
    }

    public function showProfileAvatar(Request $request, $id)
    {
        $authUser = User::find($id);

        $avatarPath = $authUser->profile_pic;
        $avatarPath = utf8_encode($avatarPath);
        $avatarPath = storage_path('app/') . $avatarPath;
        $avatarPlaceholder = storage_path('app/avatars/') . 'avatar.png';
        if (file_exists($avatarPath)) {
            return response()->file($avatarPath);
        } else {
            return response()->file($avatarPlaceholder);
        }

        // response()->json(['hall0' => 'nice']);
    }
}
