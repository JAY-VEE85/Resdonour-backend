<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangayPost;

class BarangayPostsController extends Controller
{
    // CREATE POST
    public function createPost(Request $request)
    {
        $validated = $request->validate([
            'caption' => 'required|string|max:255',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,gif|max:10240',
        ]);

        $imagesPaths = []; // Array to store file paths

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagesPaths[] = $image->store('brgy_images', 'public'); // Store each image
            }
        }

        // Create the post (without user_id)
        $barangayPost = BarangayPost::create([
            'caption' => $request->caption, // Store the caption
            'images' => json_encode($imagesPaths), // Store images as JSON
        ]);

        return response()->json($barangayPost, 201);
    }

    // GET ALL POSTS
    public function getAllPosts()
    {
        $posts = BarangayPost::latest()->get();

        // Decode images JSON and generate full URLs
        $posts->transform(function ($post) {
            $post->images = collect(json_decode($post->images))->map(function ($image) {
                return asset('storage/' . $image); // ✅ Generate full URL
            })->toArray();
            return $post;
        });

        return response()->json($posts);
    }

    // GET POST BY ID
    public function getPost($id)
    {
        $barangayPost = BarangayPost::find($id);

        if (!$barangayPost) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        return response()->json($barangayPost);
    }

    // UPDATE POST
    public function updatePost(Request $request, $id)
    {
        $barangayPost = BarangayPost::find($id);

        if (!$barangayPost) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $validated = $request->validate([
            'caption' => 'sometimes|string|max:255',
            'images' => 'sometimes|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,gif|max:10240',
        ]);

        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('brgy_images', 'public');
            }
            $barangayPost->images = json_encode($imagePaths);
        }

        if ($request->has('caption')) {
            $barangayPost->caption = $request->caption;
        }

        $barangayPost->save();

        return response()->json($barangayPost);
    }

    // DELETE POST
    public function deletePost($id)
    {
        $barangayPost = BarangayPost::find($id);

        if (!$barangayPost) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $barangayPost->delete();
        return response()->json(['message' => 'Post deleted successfully']);
    }
}
