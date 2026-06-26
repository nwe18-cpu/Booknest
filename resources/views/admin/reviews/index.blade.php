@extends('admin.layouts.app')

@section('title', 'Booknest Admin - Reviews Moderation')

@section('styles')
<style>
    .reviews-layout-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 25px;
        margin-bottom: 25px;
    }
    
    @media (max-width: 1024px) {
        .reviews-layout-grid {
            grid-template-columns: 1fr;
        }
    }

    .stats-widget-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 8px 30px rgba(76, 45, 23, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.6);
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .stats-mini-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .mini-stat-box {
        background: #F4F1EA;
        border: 1px solid #DCD6BC;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
    }

    .mini-stat-box h4 {
        margin: 0;
        font-size: 2.2rem;
        font-weight: 800;
        color: var(--text-main);
    }

    .mini-stat-box p {
        margin: 5px 0 0 0;
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .rating-distribution-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 8px 30px rgba(76, 45, 23, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.6);
    }

    .distribution-row {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 10px;
        font-size: 0.9rem;
    }

    .distribution-stars {
        width: 75px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 4px;
        color: var(--text-main);
    }

    .distribution-stars i {
        color: #ffd166;
    }

    .distribution-bar-bg {
        flex: 1;
        height: 10px;
        background-color: #EDE8D0;
        border-radius: 5px;
        overflow: hidden;
    }

    .distribution-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #ffd166 0%, #e0a96d 100%);
        border-radius: 5px;
        transition: width 0.8s ease;
    }

    .distribution-count {
        width: 45px;
        text-align: right;
        font-weight: 700;
        color: var(--text-muted);
    }

    .filters-row-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 8px 30px rgba(76, 45, 23, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.6);
        margin-bottom: 25px;
        display: grid;
        grid-template-columns: 2fr 1fr auto;
        gap: 15px;
        align-items: center;
    }

    @media (max-width: 768px) {
        .filters-row-card {
            grid-template-columns: 1fr;
        }
    }

    .filter-input {
        padding: 10px 14px;
        border: 1px solid #DCD6BC;
        border-radius: 8px;
        background-color: #FAFAFA;
        color: #1A2E3B;
        font-size: 0.9rem;
        outline: none;
        width: 100%;
        box-sizing: border-box;
    }

    .filter-input:focus {
        border-color: #4C2D17;
        background-color: #FFFFFF;
    }

    .btn-filter-submit {
        background-color: #4C2D17;
        color: #EDE8D0;
        border: none;
        border-radius: 8px;
        width: 38px;
        height: 38px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-filter-submit:hover {
        background-color: #351f0f;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(76, 45, 23, 0.15);
    }

    .btn-filter-reset {
        background-color: #F4F1EA;
        color: #4C2D17;
        border: 1px solid #DCD6BC;
        border-radius: 8px;
        width: 38px;
        height: 38px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-sizing: border-box;
    }

    .btn-filter-reset:hover {
        background-color: #EDE8D0;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(76, 45, 23, 0.08);
    }

    .star-filled {
        color: #ffd166;
        text-shadow: 0 0 5px rgba(255, 209, 102, 0.5);
    }

    .star-empty {
        color: #DCD6BC;
    }

    .review-comment-box {
        background: #FAF8F5;
        border-left: 3px solid #E0A96D;
        padding: 10px 15px;
        border-radius: 0 8px 8px 0;
        font-style: italic;
        color: #555;
        margin-top: 5px;
        max-width: 450px;
        word-wrap: break-word;
    }

    .btn-delete-review {
        background-color: #C84B31;
        color: #FFFFFF;
        border: none;
        border-radius: 6px;
        padding: 6px 12px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background-color 0.2s;
    }

    .btn-delete-review:hover {
        background-color: #a73c25;
    }

    .pagination-wrapper {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }
</style>
@endsection

@section('content')
<div class="dashboard-wrapper-new">
    
    @if(session('success'))
        <div class="alert alert-success" style="background-color: #dcfce7; border: 1px solid #bbf7d0; color: #166534; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 600;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Top Layout: Stats Mini boxes & Distribution -->
    <div class="reviews-layout-grid">
        <!-- Stats Row -->
        <div class="stats-widget-card">
            <h3 style="margin: 0 0 20px 0; font-size: 1.25rem;"><i class="fa-solid fa-chart-pie"></i> Reviews Summary</h3>
            <div class="stats-mini-row">
                <div class="mini-stat-box">
                    <h4>{{ $totalReviews }}</h4>
                    <p>Total Reviews</p>
                </div>
                <div class="mini-stat-box">
                    <h4>
                        {{ $averageRating }}
                        <span style="font-size: 1.2rem; color: #ffd166;">
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
                                    <i class="fa-regular fa-star" style="color: #DCD6BC;"></i>
                                @endif
                            @endfor
                        </span>
                    </h4>
                    <p>Average Rating</p>
                </div>
            </div>
            <div style="margin-top: 20px; text-align: center; font-size: 0.9rem; font-weight: 600; color: var(--text-muted);">
                🔥 Positive Sentiment Score: <strong style="color: #2d6a4f;">{{ $positiveReviewsPercent }}%</strong> (4+ Stars)
            </div>
        </div>

        <!-- Rating Distribution -->
        <div class="rating-distribution-card">
            <h3 style="margin: 0 0 20px 0; font-size: 1.25rem;"><i class="fa-solid fa-star-half-stroke"></i> Rating Breakdown</h3>
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

    <!-- Filter Form -->
    <form method="GET" action="{{ route('admin.reviews.index') }}" class="filters-row-card">
        <button type="submit" style="display: none;"></button>
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

    <!-- Table of Reviews -->
    <div class="data-table-card">
        <div class="card-header-flex">
            <div class="header-title-group">
                <h3><i class="fa-solid fa-comments"></i> Book Reviews Feed</h3>
                <p>Monitor recent ratings and delete spam or offensive comments.</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Book Cover</th>
                        <th>Target Book</th>
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
                                <div style="font-size: 0.78rem; color: var(--text-muted);">{{ $rev->customer?->email ?? 'N/A' }}</div>
                            </td>
                            <td>
                                @if($rev->item)
                                    <img src="{{ $rev->item->image ? asset('storage/'.$rev->item->image) : asset('images/default-book.png') }}" alt="Cover" class="table-book-cover">
                                @else
                                    <span style="font-size: 1.5rem; color: #DCD6BC;"><i class="fa-solid fa-book"></i></span>
                                @endif
                            </td>
                            <td>
                                <div><strong>{{ $rev->item?->name ?? 'Deleted Book' }}</strong></div>
                                <div style="font-size: 0.78rem; color: var(--text-muted);">by {{ $rev->item?->author?->name ?? 'Unknown' }}</div>
                            </td>
                            <td>
                                <span style="font-size: 0.95rem;">
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
                                <form action="{{ route('admin.reviews.destroy', $rev->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this review? This action cannot be undone.');" style="display:inline;">
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
