<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:255',
            'barangay' => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Create the user
        $user = User::create([
            'fname' => $request->fname,
            'lname' => $request->lname,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'city' => $request->city,
            'barangay' => $request->barangay,
            'password' => Hash::make($request->password),
        ]);

        // Return response (e.g., token, success message)
        return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
    }

    public function index()
    {
        // Get all users
        $users = User::all();

        // Return users as JSON
        return response()->json($users, 200);
    }
}
