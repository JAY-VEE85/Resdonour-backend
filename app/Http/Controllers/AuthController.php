<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

// for emailingz
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use App\Mail\PasswordResetMail;
use Illuminate\Auth\Events\Registered;


class AuthController extends Controller
{
    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:255',
            'barangay' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255', 
            'birthdate' => 'required|date|before:today',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'fname' => $request->fname,
            'lname' => $request->lname,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'city' => $request->city,
            'barangay' => $request->barangay,
            'street' => $request->street,
            'birthdate' => $request->birthdate,
            'password' => Hash::make($request->password),
            // 'badge' => 'Newbie',
        ]);
        
        // Generate a random 6-character alphanumeric code
        $verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Save verification code in the database
        $user->update([
            'verification_code' => $verificationCode
        ]);

        // Send email verification notification
        event(new Registered($user));

        $user->badge = 'Newbie';

        return response()->json([
            'message' => 'Account created successfully. Please verify your email.'
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Generate a random 6-character alphanumeric code
        $token = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => now()]
        );

        // Get the user's first name
        $firstName = $user->fname;

        // Email content
        $subject = "{$token} is your password reset code";
        $body = "Hello, {$firstName}!\n\n".
                "We received a request to reset your password. Use the code below to proceed with resetting your password:\n\n".
                "{$token}\n\n".
                "If you didn’t request a password reset, please ignore this email or contact our support team immediately.\n\n".
                "Thank you,\n".
                "Re’sIt Support Team";

        Mail::raw($body, function ($message) use ($user, $subject) {
            $message->to($user->email)
                    ->subject($subject);
        });

        return response()->json([
            'message' => 'Password reset token sent.',
            'token' => $token, 
        ]);
    }

    public function verifyToken(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
        ]);

        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$resetToken) {
            return response()->json(['error' => 'Invalid or expired token'], 400);
        }

        if (now()->diffInMinutes($resetToken->created_at) > 60) {
            return response()->json(['error' => 'Token has expired'], 400);
        }

        return response()->json(['message' => 'Token is valid']);
    }

    // without auto login
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
            'token' => 'required', // Ensure the token is provided
        ]);

        // Find the reset token from the database
        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$resetToken) {
            return response()->json(['error' => 'Invalid or expired token'], 400);
        }

        // Check if the token has expired
        if (now()->diffInMinutes($resetToken->created_at) > 60) {
            return response()->json(['error' => 'Token has expired'], 400);
        }

        // Proceed to update the user's password if token is valid
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Update the password
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the reset token after password change
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Password has been reset successfully']);
    }

    public function login(Request $request)
    {
        // Validate login request
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);

        // If validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Attempt to authenticate user
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Get authenticated user
        $user = Auth::user();

        // Check if the user has verified their email
        if (!$user->hasVerifiedEmail()) {
            return response()->json(['error' => 'Please verify your email before logging in.'], 403);
        }

        // Generate token if email is verified
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return response with token and user info
        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,   // user data
            'role' => $user->role   // assuming 'role' column exists in users table
        ]);
    }

    public function logout(Request $request)
    {
        Log::info('Logout request received', ['user' => $request->user()]);

        if (!$request->user()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // Revoke all tokens
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logout successful'], 200);
    }

    public function index()
    {
        // Get all users
        $users = User::all();

        // Return users as JSON
        return response()->json($users, 200);
    }


    // for canceling registration
    public function cancelRegistration(Request $request)
    {
        // Validate the request to ensure email is provided
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // Ensure the user has not yet verified their email
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'You cannot cancel your registration because your email is already verified.'
            ], 403); // Forbidden action
        }

        // Delete the user from the database
        $user->delete();

        return response()->json([
            'message' => 'Your registration has been successfully canceled, and your account has been deleted.'
        ], 200);
    }

    public function cancelDueRefresh(Request $request)
    {
        \Log::info('Cancel Registration Request:', $request->all());

        // Validate the request to ensure email is provided
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // Ensure the user has not yet verified their email
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'You cannot cancel your registration because your email is already verified.'
            ], 403); // Forbidden action
        }

        // Delete the user from the database
        $user->delete();

        return response()->json([
            'message' => 'Your registration has been successfully canceled, and your account has been deleted.'
        ], 200);
    }

}