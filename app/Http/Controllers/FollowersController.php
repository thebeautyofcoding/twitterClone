<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Resources\UserResource;
class FollowersController extends Controller
{
    public function followOrUnfollow(Request $request, $toFollowId)
    {
        $me = auth()->user();
        $me = User::find($me->id);
        $meFollowing = $me->following->where('id', $toFollowId)->first();

        if ($meFollowing) {
            $me->following()->detach($toFollowId);
            $me->save();
            return response()->json([
                'followed' => false,
                'user' => new UserResource($me),
            ]);
        } else {
            $me->following()->attach($toFollowId);

            $me->save();

            return response()->json([
                'followed' => true,
                'user' => new UserResource($me),
            ]);
        }
    }
    public function getFollowing(Request $request, $username)
    {
        $user = User::where('username', $username)->first();

        $following = $user->following;

        return response()->json([
            'following' => UserResource::collection($following),
        ]);
    }

    public function getFollowers(Request $request, $username)
    {
        $user = User::where('username', $username)->first();

        $followers = $user->followers->where('id', '!=', auth()->id());

        return response()->json([
            'followers' => UserResource::collection($followers),
        ]);
    }
}
