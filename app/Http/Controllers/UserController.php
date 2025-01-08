<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{User,ContactFormEntry};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Mail\SendContactUs;
use Illuminate\Support\Facades\Mail;
class UserController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if (!$user->hasVerifiedEmail()) {
                Auth::logout(); // Log out the user
                return response()->json(['message' => 'Please verify your email before logging in.'], 403);
            }

            $user = Auth::user();
            $User =  User::find($user->id);
            $token = $User->createToken($request->input('email'))->plainTextToken;
            return response()->json(['token' => $token,'userInfo' =>$user,  'message' => 'You’ve successfully logged in',], 200);
        }
        return response()->json([
            'status' => false,
            'message' => 'Invalid email or password. Please try again.',
            'error' => 'Invalid email or password. Please try again.'
        ],422);
    }

    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
            'user_type' => 'required'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->input('user_type')
        ]);

        $token = $user->createToken($request->input('email'))->plainTextToken;
        $user->sendEmailVerificationNotification();
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

    public function resendVerificationEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        $user = User::where('email', $request->email)->first();

        // Check if the email is already verified
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Your email is already verified.', 'status' => true], 422);
        }

        // Send the verification email
        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'A new verification link has been sent to your email address.', 'status' => true], 200);


    }




    public function updateProfile(Request $request,$userId)
    {
        $user = User::find($userId);

        if(!empty($user)){
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
                'status' => true,
                'message' => 'Profile updated successfully',
                'user' => $user
            ],200);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'User not Found',
            ],422);
        }

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

    public function checkEmail(Request $request)
    {
        // Validate the incoming request to ensure 'email' is provided and is a valid email
        $request->validate([
            'email' => 'required|email',
        ]);

        // Check if email exists in the 'users' table
        $emailExists = User::where('email', $request->email)->exists();

        // Return response
        return response()->json([
            'exists' => ($emailExists) ? true : false
        ]);
    }

    public function logout(Request $request)
    {

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out' ,'status' => true
        ]);
    }

    public function sendEmail(Request $request)
    {
        try {
            $request->validate([
                'first_name' => 'required',
                'last_name' => 'nullable|string',
                'subject' => 'nullable|string',
                'email' => 'required',
                'phone' => 'nullable|string',
                'query' => 'nullable|string',

            ]);
            $post = $request->only(['first_name', 'last_name', 'subject', 'email', 'phone' ,'query']);

            $email = $request->input('email');
            $adminEmail = env('MAIL_ADMIN_EMAIL');
            Mail::to($adminEmail) ->cc($email)->send(new SendContactUs(data: $post));

            ContactFormEntry::create( $post );
            return response()->json([
                'message' => 'Email Sent' ,
                'status' => true
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage() ,
                'status' => true
            ]);
        }
    }

    public function FetchContactFormEntires(){
        $entries = ContactFormEntry::orderBy('id','desc')->get();
        return response()->json([
            'message' => 'Fetched Sent' ,
            'data' => $entries,
            'status' => true
        ]);
    }



}
