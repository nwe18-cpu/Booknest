<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banners = Banner::orderBy('order')->orderBy('created_at', 'desc')->paginate(5);
        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            'order' => 'required|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('banners', 'public');
            $data['image'] = $imagePath;
        }

        $banner = Banner::create($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Banner added successfully!',
                'banner' => $banner
            ]);
        }

        return redirect()->route('admin.banners.index')->with('success', 'Banner added successfully!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'order' => 'required|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                Storage::disk('public')->delete($banner->image);
            }
            
            $imagePath = $request->file('image')->store('banners', 'public');
            $data['image'] = $imagePath;
        }

        $banner->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Banner updated successfully!',
                'banner' => $banner
            ]);
        }

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        // Delete image file from storage
        if ($banner->image && Storage::disk('public')->exists($banner->image)) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Banner deleted successfully!'
            ]);
        }

        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted successfully!');
    }
}
