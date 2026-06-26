<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classification;
use Illuminate\Http\Request;

class ClassificationController extends Controller
{
    public function index()
    {
        $classifications = Classification::orderBy('name')->get();
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'classifications' => $classifications
            ]);
        }
        
        return view('admin.classifications.index', compact('classifications'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:classifications,name',
            'color' => 'required|string|in:green,blue,gold,red,purple,teal,orange,pink',
        ]);

        $classification = Classification::create([
            'name' => $request->name,
            'color' => $request->color,
            'status' => 'active',
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Classification created successfully!',
                'classification' => $classification
            ]);
        }

        return redirect()->route('admin.classifications.index')->with('success', 'Classification created successfully!');
    }

    public function edit(Classification $classification)
    {
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'classification' => $classification
            ]);
        }
        
        return view('admin.classifications.edit', compact('classification'));
    }

    public function update(Request $request, Classification $classification)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:classifications,name,' . $classification->id,
            'color' => 'required|string|in:green,blue,gold,red,purple,teal,orange,pink',
            'status' => 'required|in:active,inactive',
        ]);

        $classification->update([
            'name' => $request->name,
            'color' => $request->color,
            'status' => $request->status,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Classification updated successfully!',
                'classification' => $classification
            ]);
        }

        return redirect()->route('admin.classifications.index')->with('success', 'Classification updated successfully!');
    }

    public function destroy(Classification $classification)
    {
        $classification->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Classification deleted successfully!'
            ]);
        }

        return redirect()->route('admin.classifications.index')->with('success', 'Classification deleted successfully!');
    }
}
