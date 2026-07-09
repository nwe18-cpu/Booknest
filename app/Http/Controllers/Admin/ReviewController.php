<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\ActivityLogger;

class ReviewController extends Controller
{
    /**
     * Display a listing of book reviews.
     */
    public function index(Request $request)
    {
        // 1. Calculate General Metrics
        $totalReviews = Review::count();
        $averageRating = Review::avg('rating') ? round(Review::avg('rating'), 1) : 0;
        
        $positiveReviewsCount = Review::where('rating', '>=', 4)->count();
        $positiveReviewsPercent = $totalReviews > 0 ? round(($positiveReviewsCount / $totalReviews) * 100) : 0;

        // Calculate distribution
        $distributionData = Review::select('rating', DB::raw('count(*) as total'))
            ->groupBy('rating')
            ->pluck('total', 'rating')
            ->toArray();

        $ratingDistribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $count = $distributionData[$i] ?? 0;
            $percent = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
            $ratingDistribution[$i] = [
                'count' => $count,
                'percent' => $percent
            ];
        }

        // 2. Query reviews with search and filters
        $query = Review::with(['customer', 'item.author'])
            ->orderBy('created_at', 'desc');

        // Search text: comment or customer name/email or book title
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($custQ) use ($search) {
                      $custQ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('item', function($itemQ) use ($search) {
                      $itemQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->paginate(5)->withQueryString();

        return view('admin.reviews.index', compact(
            'reviews',
            'totalReviews',
            'averageRating',
            'positiveReviewsPercent',
            'ratingDistribution'
        ));
    }

    /**
     * Delete a review.
     */
    public function destroy($id)
    {
        $review = Review::with(['customer', 'item'])->findOrFail($id);
        $customerName = $review->customer?->name ?? 'Unknown Customer';
        $bookName = $review->item?->name ?? 'Unknown Book';
        
        $review->delete();
        ActivityLogger::log('delete', "Deleted/moderated book review by customer '{$customerName}' for book '{$bookName}'.");

        return redirect()->route('admin.reviews.index')->with('success', 'Review deleted successfully!');
    }
}
