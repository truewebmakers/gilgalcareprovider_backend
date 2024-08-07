<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken($request->input('email'))->plainTextToken;
            return response()->json(['token' => $token,'userInfo' =>$user], 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken($request->input('email'))->plainTextToken;
        return response()->json(['token' => $token , 'userInfo' =>$user], 201);
    }

    public function getProfile(Request $request,$userId)
    {
        $user = User::find($userId);
        if($user){
            return response()->json([
                'message' => 'Profile get successfully',
                'user' => $user
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'User Not found',
            ],422);
        }
    }

    public function updateProfile(Request $request,$userId)
    {
        $user = User::find($userId);
        $data = $request->only(['name', 'phone' ,'email', 'notes', 'fb_link', 'twitter_link', 'googleplus_link', 'insta_link']);

        // Handle profile picture upload
        if ($request->hasFile('profile_pic')) {
            // Delete old profile picture if exists
            if ($user->profile_pic && Storage::exists($user->profile_pic)) {
                Storage::delete($user->profile_pic);
            }

            $file = $request->file('profile_pic');
            $path = $file->store('profile_pics', 'public');
            $data['profile_pic'] = $path;
        }

        $user->update($data);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }

    public function updatePassword(Request $request,$userId)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
            'new_password_confirmation' => 'required|string|min:8',
        ]);

        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found.',
            ], 404);
        }
        if (!Hash::check($request->input('current_password'), $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Current password is incorrect.',
            ], 400);
        }

            $user->password = Hash::make($request->input('new_password'));
            $user->save();
            return response()->json([
                'message' => 'Password updated successfully.',
            ]);

    }

    public function logout(Request $request)
    {

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out' ,'status' => true
        ]);
    }
}
