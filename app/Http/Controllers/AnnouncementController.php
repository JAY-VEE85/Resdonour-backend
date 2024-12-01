<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;

class AnnouncementController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', 
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('announcement', 'public'); 
        }

        $announcement = Announcement::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'date' => $validated['date'],
            'image' => $imagePath,
        ]);

        return response()->json($announcement, 201); 
    }

    public function index()
    {
        $announcements = Announcement::all(); 
        return response()->json($announcements);
    }

    public function show($id)
    {
        $announcement = Announcement::find($id);

        if (!$announcement) {
            return response()->json(['message' => 'Announcement not found'], 404);
        }

        return response()->json($announcement);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $announcement = Announcement::find($id);

        if (!$announcement) {
            return response()->json(['message' => 'Announcement not found'], 404);
        }

        if ($request->hasFile('image')) {
            if ($announcement->image && file_exists(storage_path('app/public/' . $announcement->image))) {
                unlink(storage_path('app/public/' . $announcement->image));
            }

            $imagePath = $request->file('image')->store('announcement', 'public');
        } else {
            $imagePath = $announcement->image;
        }

        $announcement->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'date' => $validated['date'],
            'image' => $imagePath,
        ]);

        return response()->json($announcement); 
    }

    public function destroy($id)
    {
        $announcement = Announcement::find($id);

        if (!$announcement) {
            return response()->json(['message' => 'Announcement not found'], 404);
        }

        if ($announcement->image && file_exists(storage_path('app/public/' . $announcement->image))) {
            unlink(storage_path('app/public/' . $announcement->image));
        }

        $announcement->delete();

        return response()->json(['message' => 'Announcement deleted successfully'], 200); 
    }
}
