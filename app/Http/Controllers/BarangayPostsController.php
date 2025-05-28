<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangayPost;
use Illuminate\Support\Facades\Storage;


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

        // Decode images JSON and generate full URLs
        $barangayPost->images = collect(json_decode($barangayPost->images))->map(function ($image) {
            return asset('storage/' . $image); // ✅ Convert to full URL
        })->toArray();

        return response()->json($barangayPost);
    }


    // UPDATE POST - working na sya cza
    public function updatePost(Request $request, $id)
    {
        $post = BarangayPost::find($id);
        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        if ($request->has('caption')) {
            $post->caption = $request->input('caption');
        }

        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('barangay_posts', 'public');
                $images[] = $path;
            }
            $post->images = json_encode($images); 
        }

        // ✅ Handle removed images
        if ($request->has('removedImages')) {
            foreach ($request->input('removedImages') as $image) {
                $imagePath = str_replace(asset('storage/'), '', $image);
                
                // ✅ Check if file exists before deleting
                if (\Storage::disk('public')->exists($imagePath)) {
                    \Storage::disk('public')->delete($imagePath);
                }
            }
        }

        // ✅ Save updated post
        $post->save();

        return response()->json([
            'message' => 'Post updated successfully',
            'post' => $post
        ]);
    }

    // DELETE POST
    public function deletePost($id)
    {
        $barangayPost = BarangayPost::find($id);

        if (!$barangayPost) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        if ($barangayPost->images) {
            $images = json_decode($barangayPost->images, true);
            
            foreach ($images as $imagePath) {
                // ✅ Check if file exists before deleting
                if (\Storage::disk('public')->exists($imagePath)) {
                    \Storage::disk('public')->delete($imagePath);
                }
            }
        }

        $barangayPost->delete();
        
        return response()->json(['message' => 'Post and associated images deleted successfully']);
    }
}
