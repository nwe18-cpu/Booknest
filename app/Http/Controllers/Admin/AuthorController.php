<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Author;
use Illuminate\Support\Facades\Storage;
use App\Helpers\ActivityLogger;

class AuthorController extends Controller
{
    public function index()
    {
        $authors = Author::withCount('items')->orderBy('name')->paginate(5);
        $classifications = \App\Models\Classification::where('status', 'active')->get();
        return view('admin.authors.index', compact('authors', 'classifications'));
    }

    public function create()
    {
        return view('admin.authors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:authors,name',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only('name');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('authors', 'public');
        }

        $author = Author::create($data);
        ActivityLogger::log('create', "Created author '{$author->name}' (ID: {$author->id}).");

        return redirect()->route('admin.authors.index')->with('success', 'Author created successfully!');
    }

    public function edit(Author $author)
    {
        return view('admin.authors.edit', compact('author'));
    }

    public function update(Request $request, Author $author)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:authors,name,' . $author->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only('name');

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($author->image) {
                Storage::disk('public')->delete($author->image);
            }
            $data['image'] = $request->file('image')->store('authors', 'public');
        }

        $author->update($data);
        ActivityLogger::log('update', "Updated author '{$author->name}' (ID: {$author->id}) details.");

        return redirect()->route('admin.authors.index')->with('success', 'Author updated successfully!');
    }

    public function destroy(Author $author)
    {
        // Check if author has books associated
        if ($author->items()->count() > 0) {
            return redirect()->route('admin.authors.index')->with('error', 'Cannot delete author because they have associated books!');
        }

        if ($author->image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($author->image);
        }

        $authorName = $author->name;
        $authorId = $author->id;
        $author->delete();
        ActivityLogger::log('delete', "Deleted author '{$authorName}' (ID: {$authorId}).");

        return redirect()->route('admin.authors.index')->with('success', 'Author deleted successfully!');
    }
}
