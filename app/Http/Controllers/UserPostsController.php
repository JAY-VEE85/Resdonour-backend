<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPost;
use App\Models\Announcement;


class UserPostsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // public function getPost($id)
    //     {
    //         $user = auth()->user();

    //         if (!$user) {
    //             return response()->json(['error' => 'Unauthorized'], 401);
    //         }

    //         $post = UserPost::find($id);

    //         if (!$post || $post->user_id !== $user->id) {
    //             return response()->json(['error' => 'Post not found or unauthorized access'], 404);
    //         }

    //         return response()->json(['post' => $post], 200);
    //     }

    public function getPost($id)
        {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $post = UserPost::find($id);

            if (!$post) {
                return response()->json(['error' => 'Post not found'], 404);
            }

            if ($post->user_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized access to this post'], 403);
            }

            $post->image = url('storage/' . $post->image);

            return response()->json(['post' => $post], 200);
        }


        public function getUserPosts()
        {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $posts = $user->posts->map(function ($post) {
                $post->image = url('storage/' . $post->image); 
                return $post;
            });

            return response()->json(['posts' => $posts], 200);
        }

        public function getAllPosts()
        {
            $user = auth()->user();
        
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        
            $posts = UserPost::where('status', 'approved')->get()->map(function ($post) use ($user) {
                $post->liked_by_user = $post->usersWhoLiked->contains('id', $user->id);
        
                $post->image = asset('storage/' . $post->image);
        
                $post->user_name = ($post->user->fname ?? '') . ' ' . ($post->user->lname ?? '');
                return $post;
            });
        
            return response()->json(['posts' => $posts], 200);
        }
        

        
    public function posts(Request $request)
        {
            if (!auth()->check()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        
            $request->validate([
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'category' => 'required|string|max:100',
            ]);
        
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('images', 'public'); 
            }
        
            $post = new UserPost();
            $post->user_id = auth()->id(); 
            $post->image = isset($imagePath) ? $imagePath : null; 
            $post->title = $request->input('title');
            $post->content = $request->input('content');
            $post->category = $request->input('category');

            // $post->status = auth()->user()->role === 'user' ? 'pending' : 'approved';
            $userRole = auth()->user()->role;
            $post->status = in_array($userRole, ['admin', 'agri']) ? 'approved' : 'pending';

            $post->save();
        
            return response()->json(['message' => 'Post created successfully', 'post' => $post]);
        }
        
    // public function updatePost(Request $request, $id)
    //     {
    //         $post = UserPost::find($id);
            
    //         if (!$post) {
    //             return response()->json(['error' => 'Post not found'], 404);
    //         }
        
    //         if ($post->user_id !== auth()->id()) {
    //             return response()->json(['error' => 'Unauthorized'], 403);
    //         }
        
    //         $validated = $request->validate([
    //             'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //             'title' => 'required|string|max:255',
    //             'content' => 'required|string',
    //             'category' => 'required|string|max:100',
    //         ]);
        

    //         if ($request->hasFile('image')) {

    //             if ($post->image && Storage::exists('public/' . $post->image)) {
    //                 Storage::delete('public/' . $post->image);
    //             }
        
    //             $post->image = $request->file('image')->store('images', 'public');
    //         }
        
    //         $post->title = $validated['title'];
    //         $post->content = $validated['content'];
    //         $post->category = $validated['category'];
    
    //         $post->save();
        
    //         return response()->json(['message' => 'Post updated successfully', 'post' => $post], 200);
    //     }

    public function updatePost(Request $request, $id) {

        // return $request->input('title');
        // Validation rules
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|max:255',  // Ensuring category is validated correctly
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        
    
        try {
            $post = UserPost::findOrFail($id);
    
            $post->title = $request->title;
            $post->content = $request->content;
            $post->category = $request->category;
    
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $path = $image->store('images', 'public');
                $post->image = $path;
            }
    
            $post->save();
    
            return response()->json(['message' => 'Post updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    

        
    // public function deletePost($id)
    //     {
    //         $user = auth()->user();

    //         if (!$user) {
    //             return response()->json(['error' => 'Unauthorized'], 401);
    //         }

    //         $post = UserPost::find($id);

    //         if (!$post || $post->user_id !== $user->id) {
    //             return response()->json(['error' => 'Post not found or unauthorized access'], 404);
    //         }

    //         if ($post->image && file_exists(storage_path('app/public/' . $post->image))) {
    //             unlink(storage_path('app/public/' . $post->image)); 
    //         }

    //         $post->delete();

    //         return response()->json(['message' => 'Post deleted successfully'], 200);
    //     }

    // itong delete na ito is for every users natin, bali front nalang ayusin hehe(wag u nalang pansinin yung nasa taas man HAHAHAH)

    public function deletePost($id)
        {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $post = UserPost::find($id);

            if (!$post || ($post->user_id !== $user->id && $user->role !== 'admin' && $user->role !== 'agri')) {
                return response()->json(['error' => 'Post not found or unauthorized access'], 404);
            }

            if ($post->image && file_exists(storage_path('app/public/' . $post->image))) {
                unlink(storage_path('app/public/' . $post->image)); 
            }

            $post->delete();

            return response()->json(['message' => 'Post deleted successfully'], 200);
        }

    public function toggleLikePost($id)
        {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $post = UserPost::find($id);

            if (!$post) {
                return response()->json(['error' => 'Post not found'], 404);
            }

            $liked = $user->likedPosts()->toggle($post->id);

            $isLiked = count($liked['attached']) > 0;

            return response()->json([
                'liked' => $isLiked,
            ], 200);
    }

        
    public function getLikedPosts()
        {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $likedPosts = $user->likedPosts->map(function ($post) use ($user) {
                $post->liked_by_user = $user->likedPosts->contains($post);

                $post->image = asset('storage/' . $post->image);
        
                $post->user_name = ($post->user->fname ?? '') . ' ' . ($post->user->lname ?? '');
                
                return $post;
            });

            return response()->json(['liked_posts' => $likedPosts], 200);
        }
        
    
    public function getAnnouncements(Request $request)
        {
            $announcements = Announcement::all();
    
            return response()->json(['announcements' => $announcements], 200);
        }
}
