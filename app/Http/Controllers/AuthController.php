<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

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
            'age' => 'nullable|integer|min:1|max:100',
            'street' => 'nullable|string|max:255', 
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
            'age' => $request->age,   
            'street' => $request->street,
            'password' => Hash::make($request->password),
        ]);
        
        // Generate a random 6-character alphanumeric code
        $verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Save verification code in the database
        $user->update([
            'verification_code' => $verificationCode
        ]);

        // Send email verification notification
        event(new Registered($user));

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

        $token = Str::random(6);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => now()]
        );

        Mail::raw("Your password reset token is: {$token}", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Password Reset Token');
        });

        return response()->json([
            'message' => 'Password reset token sent.',
            'token' => $token, 
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$resetToken) {
            return response()->json(['error' => 'Invalid or expired token'], 400);
        }

        if (now()->diffInMinutes($resetToken->created_at) > 40) {
            return response()->json(['error' => 'Token has expired'], 400);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Password has been reset successfully']);
    }


    // public function login(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required|string|email',
    //         'password' => 'required|string|min:8',
    //     ]);

    //     // para sa validation to ng login renze
    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 422);
    //     }

    //     // tas ito naman for attemp makalogin is user
    //     if (!Auth::attempt($request->only('email', 'password'))) {
    //         return response()->json(['error' => 'Invalid credentials'], 401);
    //     }

    //     $user = Auth::user();

    //     // basta token HAHAHHA
    //     $token = $user->createToken('auth_token')->plainTextToken;

    //     // response if successful
    //     return response()->json(['message' => 'Login successful', 'token' => $token, 'user' => $user]);
    // }

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
        // Check if the user is authenticated
        $user = Auth::user();

        if ($user) {
            // Revoke all tokens
            $user->tokens()->delete();

            // Optionally, revoke the current token only
            // $request->user()->currentAccessToken()->delete();

            return response()->json(['message' => 'Logout successful'], 200);
        } else {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
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

}