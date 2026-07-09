@extends('admin.layouts.app')

@section('title', 'Booknest Admin - Reviews Moderation')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/reviews.css') }}?v=1.0.2">
@endsection

@section('content')
<div class="dashboard-wrapper-new">
    
    @if(session('success'))
        <div class="alert alert-success alert-success-custom">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Top Layout: Stats Mini boxes & Distribution -->
    <div class="reviews-layout-grid">
        <!-- Stats Row -->
        <div class="stats-widget-card">
            <h3 class="reviews-summary-title"><i class="fa-solid fa-chart-pie"></i> Reviews Summary</h3>
            
            <div class="stats-flat-row">
                <div class="flat-stat-box">
                    <div class="flat-stat-icon icon-reviews-total"><i class="fa-solid fa-comments"></i></div>
                    <div class="flat-stat-info">
                        <h4>{{ $totalReviews }}</h4>
                        <p>Total Reviews</p>
                    </div>
                </div>
                
                <div class="flat-stat-box">
                    <div class="flat-stat-icon icon-reviews-rating"><i class="fa-solid fa-star"></i></div>
                    <div class="flat-stat-info">
                        <h4>
                            {{ $averageRating }}
                            <span class="rating-avg-stars-inline">
                                @php
                                    $fullStars = floor($averageRating);
                                    $hasHalf = ($averageRating - $fullStars) >= 0.5;
                                @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $fullStars)
                                        <i class="fa-solid fa-star"></i>
                                    @elseif($i == $fullStars + 1 && $hasHalf)
                                        <i class="fa-solid fa-star-half-stroke"></i>
                                    @else
                                        <i class="fa-regular fa-star star-empty"></i>
                                    @endif
                                @endfor
                            </span>
                        </h4>
                        <p>Average Rating</p>
                    </div>
                </div>

                <div class="flat-stat-box">
                    <div class="flat-stat-icon icon-reviews-sentiment"><i class="fa-solid fa-fire"></i></div>
                    <div class="flat-stat-info">
                        <h4>{{ $positiveReviewsPercent }}%</h4>
                        <p>Positive Sentiment</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rating Distribution -->
        <div class="rating-distribution-card">
            <h3 class="reviews-summary-title"><i class="fa-solid fa-star-half-stroke"></i> Rating Breakdown</h3>
            @foreach($ratingDistribution as $star => $data)
                <div class="distribution-row">
                    <div class="distribution-stars">
                        {{ $star }} Stars
                    </div>
                    <div class="distribution-bar-bg">
                        <div class="distribution-bar-fill" style="width: {{ $data['percent'] }}%;"></div>
                    </div>
                    <div class="distribution-count">
                        {{ $data['count'] }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Table of Reviews -->
    <div class="data-table-card">
        <div class="card-header-flex">
            <div class="header-title-group">
                <h3><i class="fa-solid fa-comments"></i> Book Reviews Feed</h3>
                <p>Monitor recent ratings and delete spam or offensive comments.</p>
            </div>
        </div>

        <!-- Filters integrated inside the card -->
        <form method="GET" action="{{ route('admin.reviews.index') }}" class="filters-row-card-inline filters-grid-3">
            <button type="submit" class="display-none"></button>
            <div>
                <input type="text" name="search" placeholder="Search comments, customer name/email, or book title..." value="{{ request('search') }}" class="filter-input">
            </div>
            <div>
                <select name="rating" class="filter-input" onchange="this.form.requestSubmit()">
                    <option value="">-- All Ratings --</option>
                    @for($r = 5; $r >= 1; $r--)
                        <option value="{{ $r }}" {{ request('rating') == $r ? 'selected' : '' }}>{{ $r }} Stars</option>
                    @endfor
                </select>
            </div>
            <div>
                <a href="{{ route('admin.reviews.index') }}" class="btn-filter-reset" title="Reset Filters"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Book</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Date Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $rev)
                        <tr>
                            <td>
                                <div><strong>{{ $rev->customer?->name ?? 'Unknown Customer' }}</strong></div>
                                <div class="font-size-0-78-text-muted">{{ $rev->customer?->email ?? 'N/A' }}</div>
                            </td>
                            <td>
                                <div class="table-book-info-cell">
                                    @if($rev->item)
                                        @if($rev->item->image)
                                            <img src="{{ asset('storage/'.$rev->item->image) }}" alt="Cover" class="table-book-cover">
                                        @else
                                            <div class="table-book-cover-placeholder" title="{{ $rev->item->name }}">
                                                <i class="fa-solid fa-book"></i>
                                            </div>
                                        @endif
                                    @else
                                        <span class="deleted-book-icon"><i class="fa-solid fa-book"></i></span>
                                    @endif
                                    <div class="table-book-meta-inline">
                                        <strong>{{ $rev->item?->name ?? 'Deleted Book' }}</strong>
                                        <div class="font-size-0-78-text-muted">by {{ $rev->item?->author?->name ?? 'Unknown' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="review-stars-size">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fa-solid fa-star {{ $i <= $rev->rating ? 'star-filled' : 'star-empty' }}"></i>
                                    @endfor
                                </span>
                            </td>
                            <td>
                                <div class="review-comment-box">
                                    "{{ $rev->comment }}"
                                </div>
                            </td>
                            <td>
                                {{ $rev->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td>
                                <form action="{{ route('admin.reviews.destroy', $rev->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this review? This action cannot be undone.');" class="display-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete-review">
                                        <i class="fa-solid fa-trash-can"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="table-empty-state">
                                📭 No reviews matched your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $reviews->links() }}
        </div>
    </div>

</div>
@endsection
