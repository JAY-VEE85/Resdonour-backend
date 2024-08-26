<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


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
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
    }


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);

        // para sa validation to ng login renze
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // tas ito naman for attemp makalogin is user
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();

        // basta token HAHAHHA
        $token = $user->createToken('auth_token')->plainTextToken;

        // response if successful
        return response()->json(['message' => 'Login successful', 'token' => $token, 'user' => $user]);
    }

    public function logout(Request $request)
    {
        // si user
        $user = Auth::user();

        // yung token na binigay dito yung tatanggalin something
        $user->tokens()->delete();

        // ito ewan ko kay gpt
        // Optionally, you can revoke the current token only
        // $request->user()->currentAccessToken()->delete();

        // response
        return response()->json(['message' => 'Logout successful'], 200);
    }


    public function index()
    {
        // Get all users
        $users = User::all();

        // Return users as JSON
        return response()->json($users, 200);
    }
}
