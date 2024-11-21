<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPost;
use App\Models\User;

class AdminController extends Controller
{

    public function totalUsers(Request $request)
        {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'message' => 'No authenticated user found.'
                ], 401);
            }

            if (!in_array($user->role, ['admin', 'agri'])) {
                return response()->json([
                    'message' => 'Access denied. Admins only. wag papansin'
                ], 403);
            }

            $userCount = User::count();

            return response()->json([
                'Total users of resit app' => $userCount
            ], 200);
        }

    public function totalPosts(Request $request)
        {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'message' => 'No authenticated user found.'
                ], 401);
            }

            if (!in_array($user->role, ['admin', 'agri'])) {
                return response()->json([
                    'message' => 'Access denied. Admins only.'
                ], 403);
            }

            $totalPosts = UserPost::count();

            return response()->json([
                'Total posts of resit' => $totalPosts
            ], 200);
        }

    public function allPost()
        {
            $user = auth()->user();
        
            if (!in_array($user->role, ['admin', 'agri'])) {
                return response()->json([
                    'message' => 'Access denied. Admins only.'
                ], 403);
            }
        
            $posts = UserPost::all()->map(function ($post) use ($user) {
                $post->liked_by_user = $post->usersWhoLiked->contains('id', $user->id);
                return $post;
            });
        
            return response()->json(['posts' => $posts], 200);
        }

    public function approvePost($id)
        {
            $post = UserPost::findOrFail($id);

            if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'agri') {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $post->status = 'approved';
            $post->save();

            return response()->json(['message' => 'Post approved successfully']);
        }

    public function declinePost($id)
        {
            $post = UserPost::findOrFail($id);

            if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'agri') {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $post->status = 'declined';
            $post->save();

            return response()->json(['message' => 'Post declined successfully']);
        }

    // pending count dito

    public function totalPendings(Request $request)
        {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'message' => 'No authenticated user found.'
                ], 401);
            }

            if (!in_array($user->role, ['admin', 'agri'])) {
                return response()->json([
                    'message' => 'Access denied. Admins only.'
                ], 403);
            }

            $pendingCount = UserPost::where('status', 'pending')->count();

            return response()->json([
                'Pending posts of resit' => $pendingCount
            ], 200);
        }

    public function totalApproved(Request $request)
        {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'message' => 'No authenticated user found.'
                ], 401);
            }

            if (!in_array($user->role, ['admin', 'agri'])) {
                return response()->json([
                    'message' => 'Access denied. Admins only.'
                ], 403);
            }

            $pendingCount = UserPost::where('status', 'approved')->count();

            return response()->json([
                'Approved posts of resit' => $pendingCount
            ], 200);
        }

    public function totalDeclined(Request $request)
        {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'message' => 'No authenticated user found.'
                ], 401);
            }

            if (!in_array($user->role, ['admin', 'agri'])) {
                return response()->json([
                    'message' => 'Access denied. Admins only.'
                ], 403);
            }

            $pendingCount = UserPost::where('status', 'declined')->count();

            return response()->json([
                'Declined posts of resit' => $pendingCount
            ], 200);
        }
}
