<?php

namespace App\Http\Controllers\Auth;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Verified;

class VerificationController extends Controller
{
    public function verify($id,$hash)
    {
        // Validate the request
        // $request->validate([
        //     'id' => 'required|integer',
        //     'hash' => 'required|string',
        // ]);

        // echo "<pre>"; print_r($id); die;

        // Find the user by ID
        $message = "";
        $user = User::findOrFail($id);

        // Check if the hash matches
        if (! hash_equals((string) $hash, (string) sha1($user->getEmailForVerification()))) {
            $message = 'Invalid verification link.';
        }

        // Mark the user as verified
        if ($user->hasVerifiedEmail()) {
            $message = 'Email already verified.';
        }

        // Verify the user
        $user->markEmailAsVerified();
        // event(new Verified($user));
        if(empty($message)){
            $message = " Your registration is now being reviewed by our admin team. Please allow some time for the approval process. You will be notified via email once your account has been approved.";
        }

        return view('thankyou',compact('message'));
    }
}
