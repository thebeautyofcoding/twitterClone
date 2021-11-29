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
            'firstname' => 'required|min:3',
            'lastname' => 'required|min:3',
        ]);
        $userByUsername = User::where('username', $request->username)->first();
        $userByEmail = User::where('email', $request->email)->first();
        if ($userByUsername) {
            return response(
                [
                    'username' =>
                        'User with this username already existent in database',
                ],
                401
            );
        }
        if ($userByEmail) {
            return response(
                [
                    'email' =>
                        'User with this email already existent in database',
                ],
                401
            );
        }

        $user = new User();
        $user->username = $request->username;
        $user->email = strtolower($request->email);
        $user->password = $request->password;
        $user->profile_pic = 'storage/avatar.png';
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->save();

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

        if (!$user) {
            return response(
                [
                    'email' => 'User with this email is non-existent',
                ],
                401
            );
        } elseif (!Hash::check($request->password, $user->password)) {
            return response(
                [
                    'password' => 'Please provide the right password',
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
