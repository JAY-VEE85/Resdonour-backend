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

    public function getUser(Request $request)
    {

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'No authenticated user found.'
            ], 401);
        }

        return response()->json([
            'user' => $user,
            'message' => 'User Data Successfully Fetched',
        ], 200);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            $validatedData = $request->validate([
                'fname' => 'required|string|max:255',
                'lname' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $user->id,
                'phone_number' => 'nullable|string|max:16',
                'city' => 'nullable|string|max:255',
                'barangay' => 'nullable|string|max:255',
                'street' => 'required|string',
                'age' => 'required|integer|min:1|max:120',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        \Log::info('User before update:', $user->toArray());
        \Log::info('Updating with data:', $validatedData);

        $user->update($validatedData);

        \Log::info('User after update:', $user->fresh()->toArray());

        return response()->json([
            'message' => 'User information updated successfully!',
            'user' => $user->fresh() // Ensures the latest data is returned
        ], 200);
    }



    // public function update(Request $request)
    // {

    //     $user = Auth::user();

    //     if (!$user) {
    //         return response()->json(['message' => 'Unauthorized'], 401);
    //     }

    //     $request->validate([
    //         'fname' => 'required|string|max:255',
    //         'lname' => 'required|string|max:255',
    //         'email' => 'required|email|max:255|unique:users,email,' . $user->id,
    //         'phone_number' => 'nullable|string|max:15',
    //         'city' => 'nullable|string|max:255',
    //         'barangay' => 'nullable|string|max:255',
    //         'street' => 'required|string', // Street validation
    //         'age' => 'required|integer|min:1|max:120', // Age validation
    //     ]);

    //     // if ($request->filled('currentPassword')) {
    //     //     if (!Hash::check($request->input('currentPassword'), $user->password)) {
    //     //         return response()->json(['message' => 'Current password is incorrect'], 400);
    //     //     }

    //     //     if ($request->filled('password')) {
    //     //         $user->password = Hash::make($request->input('password'));
    //     //     }
    //     // }

    //     // $user->fname = $request->input('fname');
    //     // $user->lname = $request->input('lname');
    //     // $user->email = $request->input('email');
    //     // $user->phone_number = $request->input('phone_number');
    //     // $user->city = $request->input('city');
    //     // $user->barangay = $request->input('barangay');
    //     // $user->street = $request->input('street');
    //     // $user->age = $request->input('age');

    //     // if ($request->filled('password')) {
    //     //     $user->password = Hash::make($request->input('password'));
    //     // }

    //     // $user->save();

    //     return response()->json([
    //         'message' => 'User information updated successfully!'
    //     ], 200);
    // }

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

    public function index(Request $request)
    {
        $users = User::all();  // Retrieve all users from the database
        return response()->json($users);  // Return users as JSON
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            if ($user->role == 'admin' || $user->id == $request->user_id) {
                $userToDelete = User::find($request->user_id);  // Get user by ID

                if ($userToDelete) {
                    // Perform the deletion
                    $userToDelete->delete();
                    return response()->json(['message' => 'User deleted successfully.'], 200);
                } else {
                    return response()->json(['message' => 'User not found.'], 404);
                }
            } else {
                return response()->json(['message' => 'Unauthorized action.'], 403);
            }
        } else {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
    }

}
