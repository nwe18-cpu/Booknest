<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Author;
use App\Models\Item;
use App\Helpers\ActivityLogger;

class CatalogController extends Controller
{
    public function index()
    {
        $books = Item::with(['author', 'classifications'])->orderBy('name')->paginate(5);
        return view('admin.catalog.index', compact('books'));
    }



    public function store(Request $request)
    {
        // စည်းကမ်းချက်များနှင့်အညီ Validation စစ်ဆေးခြင်း
        $request->validate([
            'name' => 'required|string|min:3|max:100',
            'author_id' => 'required|exists:authors,id',
            'pages' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'status' => 'nullable|string|in:active,inactive',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Cover ပုံအတွက် (gif removed)
            'pdf_file' => 'nullable|file|max:102400', // PDF ဖိုင်အတွက် (100MB limit, manually checked extension below)
            'classifications' => 'nullable|array',
            'classifications.*' => 'exists:classifications,id',
        ]);

        $data = $request->all();

        // အကယ်၍ ပုံတင်ထားလျှင် Storage ထဲ သိမ်းဆည်းခြင်း
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('books', 'public');
            $data['image'] = $imagePath;
        }

        // အကယ်၍ PDF တင်ထားလျှင် Storage ထဲ သိမ်းဆည်းခြင်း
        if ($request->hasFile('pdf_file')) {
            $pdfFile = $request->file('pdf_file');
            
            // Validate extension manually to bypass Windows/XAMPP MIME type detection bugs
            if (strtolower($pdfFile->getClientOriginalExtension()) !== 'pdf') {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'message' => 'The given data was invalid.',
                        'errors' => [
                            'pdf_file' => ['The pdf file field must be a file of type: pdf.']
                        ]
                    ], 422);
                }
                return redirect()->back()->withErrors(['pdf_file' => 'The pdf file field must be a file of type: pdf.'])->withInput();
            }

            $pdfPath = $pdfFile->store('books/pdfs', 'public');
            $data['pdf_file'] = $pdfPath;
        }

        // Database ထဲသို့ သိမ်းဆည်းခြင်း
        $book = Item::create($data);
        ActivityLogger::log('create', "Created book '{$book->name}' (ID: {$book->id}) with stock {$book->stock_quantity}.");

        // Classifications Sync ပြုလုပ်ခြင်း
        if ($request->has('classifications')) {
            $book->classifications()->sync($request->input('classifications'));
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Book added successfully!',
                'book' => $book->load(['author', 'classifications'])
            ]);
        }

        // အောင်မြင်ကြောင်း Alert ပြပြီး Dashboard သို့ ပြန်ညွှန်ကြားခြင်း
        return redirect()->route('admin.dashboard')->with('success', 'Book added successfully!');
    }

    public function getAuthorBooks(Author $author)
    {
        $books = Item::with(['classifications'])->where('author_id', $author->id)->orderBy('name')->get();
        return response()->json([
            'success' => true,
            'books' => $books
        ]);
    }

    public function update(Request $request, $id)
    {
        $book = Item::findOrFail($id);

        $request->validate([
            'name' => 'required|string|min:3|max:100',
            'author_id' => 'required|exists:authors,id',
            'pages' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'status' => 'nullable|string|in:active,inactive',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'pdf_file' => 'nullable|file|max:102400',
            'classifications' => 'nullable|array',
            'classifications.*' => 'exists:classifications,id',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('books', 'public');
            $data['image'] = $imagePath;
        }

        if ($request->hasFile('pdf_file')) {
            $pdfFile = $request->file('pdf_file');
            
            if (strtolower($pdfFile->getClientOriginalExtension()) !== 'pdf') {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => [
                        'pdf_file' => ['The pdf file field must be a file of type: pdf.']
                    ]
                ], 422);
            }

            $pdfPath = $pdfFile->store('books/pdfs', 'public');
            $data['pdf_file'] = $pdfPath;
        }

        $book->update($data);
        ActivityLogger::log('update', "Updated book '{$book->name}' (ID: {$book->id}) details.");

        if ($request->has('classifications')) {
            $book->classifications()->sync($request->input('classifications'));
        } else {
            $book->classifications()->detach();
        }

        return response()->json([
            'success' => true,
            'message' => 'Book updated successfully!',
            'book' => $book->load(['author', 'classifications'])
        ]);
    }

    public function destroy($id)
    {
        $book = Item::findOrFail($id);
        $bookName = $book->name;
        $bookId = $book->id;
        $book->delete();
        ActivityLogger::log('delete', "Deleted book '{$bookName}' (ID: {$bookId}).");

        return response()->json([
            'success' => true,
            'message' => 'Book deleted successfully!'
        ]);
    }
}