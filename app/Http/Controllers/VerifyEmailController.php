<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Notification;
use App\Notifications\VerifyEmail;
use App\Models\User;

class VerifyEmailController extends Controller
{
    // FOR VERIFY EMAIL BUTTON ITO SA EMAIL NA TINANGGAL KO KASI HUHU OKI NA YAN
    // public function verify(Request $request, $id, $hash)
    // {
    //     // Check if verification code is provided
    //     if ($request->has('verification_code')) {
    //         return $this->verifyWithCode($request);
    //     }

    //     // If no verification code, continue with the original verification process
    //     // Find the user by the ID
    //     $user = User::findOrFail($id);

    //     // Validate the hash
    //     if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
    //         return response()->json(['message' => 'Invalid or expired verification link.'], 400);
    //     }

    //     // Mark the user as verified
    //     $user->markEmailAsVerified();
        
    //     event(new Verified($user));

    //     return response()->json(['message' => 'Email verified successfully.']);
    //     // return redirect()->route('verification.success');
    // }

    // verify with code
    public function verifyWithCode(Request $request)
    {
        // Validate the request data
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'verification_code' => 'required|digits:6',
        ]);

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // Check if the verification code matches
        if ($user && $user->verification_code === $request->verification_code) {
            // Mark the email as verified
            $user->markEmailAsVerified();
            
            // Clear the verification code after successful verification
            $user->update(['verification_code' => null]);

            event(new Verified($user));

            return response()->json(['message' => 'Email verified successfully.']);
        }

        return response()->json(['error' => 'Invalid verification code.'], 400);
    }

    public function resendVerificationCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified']);
        }

        // Generate a new verification code
        $newCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->update(['verification_code' => $newCode]);

        // Send email notification
        Notification::send($user, new VerifyEmail());

        return response()->json(['message' => 'A new verification code has been sent to your email.']);
    }
}
