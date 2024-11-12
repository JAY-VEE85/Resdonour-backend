<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPost;


class UserPostsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getPost($id)
        {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $post = UserPost::find($id);

            if (!$post || $post->user_id !== $user->id) {
                return response()->json(['error' => 'Post not found or unauthorized access'], 404);
            }

            return response()->json(['post' => $post], 200);
        }

    public function getAllPosts()
        {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $posts = UserPost::all();

            return response()->json(['posts' => $posts], 200);
        }

    public function posts(Request $request)
        {
            if (!auth()->check()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
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
        

    public function updatePost(Request $request, $id)
        {
            $post = UserPost::find($id);
            
            if (!$post) {
                return response()->json(['error' => 'Post not found'], 404);
            }
        
            if ($post->user_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        
            $validated = $request->validate([
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'category' => 'required|string|max:100',
            ]);
        

            if ($request->hasFile('image')) {

                if ($post->image && Storage::exists('public/' . $post->image)) {
                    Storage::delete('public/' . $post->image);
                }
        
                $post->image = $request->file('image')->store('images', 'public');
            }
        
            $post->title = $validated['title'];
            $post->content = $validated['content'];
            $post->category = $validated['category'];
    
            $post->save();
        
            return response()->json(['message' => 'Post updated successfully', 'post' => $post], 200);
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
}
