<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use Carbon\Carbon;

class AnnouncementController extends Controller
{
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'expires_at' => 'nullable|date|after:today', 
        ]);
    
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('announcement', 'public');
        }
    
        $expiresAt = Carbon::parse($validated['expires_at'])->timezone('UTC'); 
        $currentTime = Carbon::now()->timezone('UTC'); 
    
        \Log::info('Expires At:', [$expiresAt]);
        \Log::info('Current Time:', [$currentTime]);
    
        Announcement::where('expires_at', '<', $currentTime)->delete();
    
        $announcement = Announcement::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'image' => $imagePath,
            'expires_at' => $expiresAt, 
        ]);
    
        return response()->json($announcement, 201);
    }
    



    
    public function index(Request $request)
    {
        $dateFrom = $request->input('date_from', null);
        $dateTo = $request->input('date_to', null);

        $query = Announcement::query();

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $announcements = $query->get()->map(function ($announcement) {
            // Add 'storage/announcement/images/' prefix to the image path
            $announcement->image = url('storage/' . $announcement->image);
            return $announcement;
        });

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
