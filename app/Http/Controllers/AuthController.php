<?php

namespace App\Http\Controllers;
use App\Http\Controllers;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use App\Models\User;

use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function returnUser(Request $request)
    {
        return $request->user;
    }
    public function process_signup(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'email' => 'required',
            'password' => 'required|min:6',
        ]);

        $user = new User();
        $user->username = $request->username;
        $user->email = strtolower($request->email);
        $user->password = $request->password;
        $user->profile_pic = 'storage/avatar.png';
        $user->save();
        dd('HALLO');
        $token = $user->createToken('myapptoken')->plainTextToken;

        return response()->json(
            [
                'user' => $user,
                'token' => $token,
            ],
            201
        );
    }
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required|min:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response(
                [
                    'message' => 'Bad Login',
                ],
                401
            );
        }
        $token = $user->createToken('myapptoken')->plainTextToken;

        return response()->json(
            [
                'user' => new UserResource($user),
                'token' => $token,
            ],
            201
        );
    }

    public function profilePic(User $user)
    {
        return response()->file($user->profile_pic);
    }

    public function logout(Request $request)
    {
        $user = auth()->user();

        if ($user->currentAccessToken()->delete()) {
            return response()->json(['loggedOut' => 'true'], 200);
        }
    }
}
