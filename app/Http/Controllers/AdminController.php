<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPost;
use App\Models\User;
use Carbon\Carbon;

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

        public function allPost(Request $request)
        {
            $user = auth()->user();
        
            if (!in_array($user->role, ['admin', 'agri'])) {
                return response()->json([
                    'message' => 'Access denied. Admins only.'
                ], 403);
            }
        
            $query = UserPost::with(['user:id,fname,lname,role']);
        
            if ($request->has('start_date') && $request->has('end_date')) {
                try {
                    $start_date = Carbon::createFromFormat('m-d-y', $request->start_date)->startOfDay();
                    $end_date = Carbon::createFromFormat('m-d-y', $request->end_date)->endOfDay();
    
                    $query->whereBetween('created_at', [$start_date, $end_date]);
                } catch (\Exception $e) {
                    return response()->json([
                        'message' => 'Invalid date format. Please use MM-DD-YY.',
                        'error' => $e->getMessage()
                    ], 400);
                }
            }
        
            if ($request->has('category') && in_array($request->category, ['Compost', 'Plastic', 'Rubber', 'Wood and Paper', 'Miscellaneous Products'])) {
                $query->where('category', $request->category);
            }
        
            $posts = $query->get()->map(function ($post) use ($user) {
                if (in_array($post->user->role, ['admin', 'agri'])) {
                    return null;
                }
        
                $post->liked_by_user = $post->usersWhoLiked->contains('id', $user->id);
                $post->fname = $post->user->fname ?? null;
                $post->lname = $post->user->lname ?? null;
                unset($post->user);
        
                return $post;
            })->filter();
        
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

    public function awardBadge(Request $request, $id)
        {
            if (!in_array(auth()->user()->role, ['admin', 'agri'])) {
                return response()->json(['message' => 'Unauthorized. Only admin and agri-admin can award badges.'], 403);
            }
        
            $user = User::findOrFail($id);
        
            $badge = $request->input('badge');
        
            $user->awardBadge($badge);
        
            return response()->json(['message' => 'Badge awarded successfully!', 'badges' => json_decode($user->badges)]);
        }
    
    public function removeBadge(Request $request, $id)
        {
            if (!in_array(auth()->user()->role, ['admin', 'agri'])) {
                return response()->json(['message' => 'Unauthorized. Only admin and agri-admin can remove badges.'], 403);
            }

            $user = User::findOrFail($id);

            $badge = $request->input('badge');

            $currentBadges = json_decode($user->badges, true) ?? [];
            if (in_array($badge, $currentBadges)) {
                $currentBadges = array_filter($currentBadges, fn($b) => $b !== $badge);
                $user->badges = json_encode(array_values($currentBadges));
                $user->save();
            }

            return response()->json([
                'message' => 'Badge removed successfully!',
                'badges' => json_decode($user->badges)
            ]);
        }

        
}
