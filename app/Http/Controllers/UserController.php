<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    
     public function __construct()
     {
         $this->middleware('auth');
     }

     public function User(Request $request)
     {

         $user = Auth::user();

         if (!$user) {
             return response()->json([
                 'message' => 'No authenticated user found.'
             ], 401);
         }

         return response()->json([
             'user' => $user
         ], 200);
     }

    // public function update(Request $request)
    // {
    //     // Validate the incoming data
    //     $request->validate([
    //         'fname' => 'required|string|max:255',
    //         'lname' => 'required|string|max:255',
    //         'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
    //         'phone_number' => 'nullable|string|max:15',
    //         'city' => 'nullable|string|max:255',
    //         'barangay' => 'nullable|string|max:255',
    //         'password' => 'nullable|min:8|confirmed', // password confirmation should be required
    //     ]);

    //     // Get the authenticated user
    //     $user = Auth::user();

    //     // Update the user's information
    //     $user->fname = $request->input('fname');
    //     $user->lname = $request->input('lname');
    //     $user->email = $request->input('email');
    //     $user->phone_number = $request->input('phone_number');
    //     $user->city = $request->input('city');
    //     $user->barangay = $request->input('barangay');

    //     // If password is provided, hash it before updating
    //     if ($request->filled('password')) {
    //         $user->password = Hash::make($request->input('password'));
    //     }

    //     // Save the changes
    //     $user->save();

    //     // Return a success response or redirect
    //     return response()->json([
    //         'message' => 'User information updated successfully!'
    //     ]);
    // }


    public function update(Request $request)
    {

        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:15',
            'city' => 'nullable|string|max:255',
            'barangay' => 'nullable|string|max:255',
            'currentPassword' => 'required_with:password',
            'password' => 'nullable|min:8|confirmed',
        ]);

        if ($request->filled('currentPassword')) {
            if (!Hash::check($request->input('currentPassword'), $user->password)) {
                return response()->json(['message' => 'Current password is incorrect'], 400);
            }

            if ($request->filled('password')) {
                $user->password = Hash::make($request->input('password'));
            }
        }

        $user->fname = $request->input('fname');
        $user->lname = $request->input('lname');
        $user->email = $request->input('email');
        $user->phone_number = $request->input('phone_number');
        $user->city = $request->input('city');
        $user->barangay = $request->input('barangay');

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return response()->json([
            'message' => 'User information updated successfully!'
        ], 200);
    }

    public function verifyCurrentPassword(Request $request)
    {
        $user = auth()->user(); 

        $currentPassword = $request->input('currentPassword');

        if (Hash::check($currentPassword, $user->password)) {
            return response()->json(['passwordValid' => true]);
        }

        return response()->json(['passwordValid' => false], 400);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'new_password' => 'required|string|min:8|confirmed', 
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password changed successfully.'], 200);
    }

}
