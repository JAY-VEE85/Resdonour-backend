<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
     // Apply auth middleware to ensure user is logged in for this method
     public function __construct()
     {
         $this->middleware('auth');
     }
 
     // Method to get only the logged-in user
     public function User(Request $request)
     {
         // Get the authenticated user
         $user = Auth::user();
 
         // If user is not authenticated, return an error message
         if (!$user) {
             return response()->json([
                 'message' => 'No authenticated user found.'
             ], 401);
         }
 
         // Return the user's information as JSON
         return response()->json([
             'user' => $user
         ], 200);
     }

    public function update(Request $request)
    {
        // Validate the incoming data
        $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'phone_number' => 'nullable|string|max:15',
            'city' => 'nullable|string|max:255',
            'barangay' => 'nullable|string|max:255',
            'password' => 'nullable|min:8|confirmed', // password confirmation should be required
        ]);

        // Get the authenticated user
        $user = Auth::user();

        // Update the user's information
        $user->fname = $request->input('fname');
        $user->lname = $request->input('lname');
        $user->email = $request->input('email');
        $user->phone_number = $request->input('phone_number');
        $user->city = $request->input('city');
        $user->barangay = $request->input('barangay');

        // If password is provided, hash it before updating
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        // Save the changes
        $user->save();

        // Return a success response or redirect
        return response()->json([
            'message' => 'User information updated successfully!'
        ]);
    }
}
