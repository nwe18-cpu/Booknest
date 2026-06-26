@extends('admin.layouts.app')

@section('content')
<div class="dashboard-wrapper-new">
    
    <div class="stats-overview-grid">
        <div class="stat-box-large">
            <div class="box-header-inline">
                <h3>Sales & Catalog Analytics</h3>
                <span class="badge-time">Last 7 Days</span>
            </div>
            
            <div class="box-content-split">
                <div class="visual-placeholder">
                    <!-- Dynamic SVG Line Chart -->
                    <svg viewBox="0 0 280 100" class="sales-svg-chart">
                        <defs>
                            <linearGradient id="chart-grad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#E0A96D" stop-opacity="0.35"/>
                                <stop offset="100%" stop-color="#E0A96D" stop-opacity="0.0"/>
                            </linearGradient>
                        </defs>
                        <!-- Area -->
                        <polygon points="0,100 {{ $pointsString }} 280,100" fill="url(#chart-grad)" />
                        <!-- Path -->
                        <polyline points="{{ $pointsString }}" fill="none" stroke="#4C2D17" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                        <!-- Points -->
                        @php $idx = 0; @endphp
                        @foreach($salesData as $date => $val)
                            @php
                                $x = ($idx / 6) * 280;
                                $y = 100 - (($val / $maxVal) * 82) - 8;
                                $formattedVal = number_format($val) . ' Ks';
                                $dayName = date('D', strtotime($date));
                            @endphp
                            <circle cx="{{ $x }}" cy="{{ $y }}" r="3.5" fill="#E0A96D" stroke="#4C2D17" stroke-width="1.5">
                                <title>{{ $dayName }} ({{ date('M d', strtotime($date)) }}): {{ $formattedVal }}</title>
                            </circle>
                            @php $idx++; @endphp
                        @endforeach
                    </svg>
                </div>
                <div class="numerical-stats">
                    <div class="stat-sub-item">
                        <span class="dot gold" style="background-color: #E0A96D;"></span>
                        <div>
                            <h4>{{ number_format($todaySales) }} Ks</h4>
                            <p>Today's Sales</p>
                        </div>
                    </div>
                    <div class="stat-sub-item">
                        <span class="dot green"></span>
                        <div>
                            <h4>{{ $totalBooks }}</h4>
                            <p>Total Books</p>
                        </div>
                    </div>
                    <div class="stat-sub-item">
                        <span class="dot red"></span>
                        <div>
                            <h4>{{ $lowStockCount }}</h4>
                            <p>Low Stock Items</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="offer-box-dark">
            <h3>Quick Admin Insights</h3>
            <p>Keep your catalog updated. Out-of-stock products affect your front-end store availability and sales conversions.</p>
            <a href="{{ route('admin.authors.index') }}" class="btn-manage-offer">
                <i class="fa-solid fa-plus"></i> Add New Book
            </a>
        </div>
    </div>

    <div class="main-dashboard-grid">
        
        <div class="data-table-card">
            <div class="card-header-flex">
                <div class="header-title-group">
                    <h3>Low Stock Alerts</h3>
                    <p><i class="fa-solid fa-triangle-exclamation"></i> Stock units less than 5</p>
                </div>
                <a href="#" class="btn-csv-export">Export List</a>
            </div>

            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Book Cover</th>
                            <th>Product Name</th>
                            <th>Author</th>
                            <th>Price</th>
                            <th>Stock Left</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowStockBooks ?? [] as $book)
                            <tr>
                                <td>
                                    <img src="{{ $book->image ? asset('storage/'.$book->image) : asset('images/default-book.png') }}" alt="Cover" class="table-book-cover">
                                </td>
                                <td><strong>{{ $book->name }}</strong></td>
                                <td><span class="text-author">{{ $book->author?->name ?? 'Unknown Author' }}</span></td>
                                <td><strong>{{ number_format($book->price) }} Ks</strong></td>
                                <td>
                                    <span class="badge-stock-danger">
                                        {{ $book->stock_quantity }} units
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.authors.index') }}" class="btn-action-restock">
                                        Restock
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="table-empty-state">
                                    🎉 Excellent! All books are well-stocked.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="side-widgets-column">
            
            <div class="widget-card">
                <div class="widget-header-inline">
                    <h3>Active Staff</h3>
                    <a href="#" class="btn-widget-link">See all &rsaquo;</a>
                </div>
                
                <div class="staff-list">
                    <div class="staff-item">
                        <div class="staff-avatar">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <div class="staff-details">
                            <h5>Mama (Admin)</h5>
                            <p>System Administrator</p>
                        </div>
                        <span class="staff-status-badge">Active</span>
                    </div>
                </div>
            </div>

            <div class="widget-card dark-bg-widget">
                <div class="location-widget-content">
                    <h4 style="color: #ffe0a3; margin-bottom: 12px; font-size: 1.05rem; font-weight: 700;"><i class="fa-solid fa-store-gear"></i> Bookstore Activity</h4>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.88rem; color: #DCD6BC;">
                        <span><i class="fa-solid fa-crown" style="color: #ffd700; margin-right: 6px;"></i> Active VIP Readers:</span>
                        <strong style="color: #fff;">{{ $activeVipCount }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.88rem; color: #DCD6BC;">
                        <span><i class="fa-solid fa-users" style="color: #a4b3c6; margin-right: 6px;"></i> Registered Members:</span>
                        <strong style="color: #fff;">{{ $totalCustomersCount }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.88rem; color: #DCD6BC;">
                        <span><i class="fa-solid fa-star" style="color: #ffd166; margin-right: 6px;"></i> Total Book Reviews:</span>
                        <strong style="color: #fff;">{{ $totalReviewsCount }}</strong>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection