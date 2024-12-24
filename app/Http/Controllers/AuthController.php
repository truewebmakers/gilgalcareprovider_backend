<?php

namespace App\Http\Controllers;

use App\Helpers\TwilioHelper;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\{UserDocuments, ContactFormEntry};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

use App\Mail\SendContactUs;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;



class AuthController extends Controller
{



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



    public function sendResetLink(Request $request)
    {
        // Validate the email address
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        $response = Password::sendResetLink(
            $request->only('email')
        );

        if ($response === Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Password reset link sent to your email.'], 200);
        } else {
            return response()->json(['message' => 'Failed to send the password reset link.'], 500);
        }
    }

    public function showResetForm($token)
    {
        // Logic to show the password reset form
        return view('auth.reset-password', ['token' => $token]);
    }

    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $response = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = bcrypt($password);
                $user->save();
            }
        );

        if ($response === Password::PASSWORD_RESET) {
            return redirect()->back()->with('status', 'Password has been reset successfully. You can now log in.');

        } else {
            return redirect()->route('password.reset', ['token' => $request->token])
                         ->with('error', 'Failed to reset the password. Please try again.');
        }
    }

    public function logout(Request $request)
    {

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out',
            'status' => true
        ]);
    }


}
