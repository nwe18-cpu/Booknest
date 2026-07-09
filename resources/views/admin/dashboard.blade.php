@extends('admin.layouts.app')

@section('content')
<div class="dashboard-wrapper-new">
    
    <div class="main-dashboard-grid">
        <div class="left-dashboard-column">
            <div class="stat-box-large">
            @if(auth()->guard('staff')->user()->role?->name === 'admin')
                <div class="box-header-inline">
                    <h3>Sales & Catalog Analytics</h3>
                    <span class="badge-time">Last 7 Days</span>
                </div>
                
                <div class="box-content-split">
                    <div class="analytics-chart-panel">
                        @php
                            $formatY = function($val) {
                                if ($val >= 1000000) {
                                    return round($val / 1000000, 1) . 'M';
                                }
                                if ($val >= 1000) {
                                    return round($val / 1000) . 'k';
                                }
                                return $val;
                            };
                        @endphp
                        
                        <div class="chart-layout-flex">
                            <!-- Y Axis Labels -->
                            <div class="chart-y-axis-labels">
                                <span>{{ $formatY($maxVal) }}</span>
                                <span>{{ $formatY($maxVal * 0.6) }}</span>
                                <span>{{ $formatY($maxVal * 0.3) }}</span>
                                <span>0</span>
                            </div>
                            
                            <div class="chart-main-area">
                                <!-- Dynamic SVG Chart -->
                                <svg viewBox="0 0 280 100" class="sales-svg-chart">
                                    <defs>
                                        <!-- Gradient for the bars -->
                                        <linearGradient id="bar-grad" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0%" stop-color="#cca353" />
                                            <stop offset="100%" stop-color="rgba(204, 163, 83, 0.25)" />
                                        </linearGradient>
                                        <!-- Gradient for the curve line -->
                                        <linearGradient id="line-grad" x1="0" y1="0" x2="1" y2="0">
                                            <stop offset="0%" stop-color="#1b3d34" />
                                            <stop offset="100%" stop-color="#cca353" />
                                        </linearGradient>
                                    </defs>
                                    
                                    <!-- Horizontal Grid Lines -->
                                    <line x1="0" y1="18" x2="280" y2="18" stroke="rgba(76, 45, 23, 0.05)" stroke-width="1" stroke-dasharray="3 3" />
                                    <line x1="0" y1="51" x2="280" y2="51" stroke="rgba(76, 45, 23, 0.05)" stroke-width="1" stroke-dasharray="3 3" />
                                    <line x1="0" y1="84" x2="280" y2="84" stroke="rgba(76, 45, 23, 0.05)" stroke-width="1" stroke-dasharray="3 3" />
                                    
                                    <!-- 1. Vertical Bars -->
                                    @php $idx = 0; @endphp
                                    @foreach($salesData as $date => $val)
                                        @php
                                            $x = ($idx / 6) * 280;
                                            $y = 100 - (($val / $maxVal) * 82) - 8;
                                            $barWidth = 14;
                                        @endphp
                                        <rect x="{{ $x - ($barWidth / 2) }}" y="{{ $y }}" width="{{ $barWidth }}" height="{{ 100 - $y }}" rx="3" fill="url(#bar-grad)" opacity="0.85" />
                                        @php $idx++; @endphp
                                    @endforeach
                                    
                                    <!-- 2. Curved Polyline -->
                                    <polyline points="{{ $pointsString }}" fill="none" stroke="url(#line-grad)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                    
                                    <!-- 3. Points circles -->
                                    @php $idx = 0; @endphp
                                    @foreach($salesData as $date => $val)
                                        @php
                                            $x = ($idx / 6) * 280;
                                            $y = 100 - (($val / $maxVal) * 82) - 8;
                                            $formattedVal = number_format($val) . ' Ks';
                                            $dayName = date('D', strtotime($date));
                                        @endphp
                                        <circle cx="{{ $x }}" cy="{{ $y }}" r="4" fill="#1b3d34" stroke="#ffffff" stroke-width="1.5">
                                            <title>{{ $dayName }} ({{ date('M d', strtotime($date)) }}): {{ $formattedVal }}</title>
                                        </circle>
                                        @php $idx++; @endphp
                                    @endforeach
                                </svg>
                            </div>
                        </div>
                        
                        <!-- X Axis Labels Row -->
                        <div class="chart-x-axis">
                            @php $idx = 0; @endphp
                            @foreach($salesData as $date => $val)
                                <div class="x-axis-label">
                                    <span class="day-short">{{ date('D', strtotime($date)) }}</span>
                                    <span class="date-short">{{ date('d', strtotime($date)) }}</span>
                                </div>
                                @php $idx++; @endphp
                            @endforeach
                        </div>
                    </div>
                    <div class="numerical-stats">
                        <div class="stat-sub-item">
                            <span class="dot gold"></span>
                            <div>
                                <h4>{{ number_format($todaySales) }} Ks</h4>
                                <p>Today's Sales</p>
                            </div>
                        </div>
                        <div class="stat-sub-item">
                            <span class="dot purple"></span>
                            <div>
                                <h4>{{ $totalOrders }}</h4>
                                <p>Total Orders</p>
                            </div>
                        </div>
                        <div class="stat-sub-item">
                            <span class="dot blue"></span>
                            <div>
                                <h4>{{ $newCustomersCount }}</h4>
                                <p>New Customers (7d)</p>
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
            @else
                <div class="box-header-inline header-no-border">
                    <h3>Store Catalog Overview</h3>
                    <span class="badge-time badge-staff-overview">General Stats</span>
                </div>
                
                <!-- Elegant Alert Bar for Restriction -->
                <div class="restricted-alert-bar">
                    <div class="restricted-icon-circle">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <div>
                        <h4 class="restricted-text-title">Sales Dashboard Restricted</h4>
                        <p class="restricted-text-desc">
                            Daily sales charts, revenues, and transaction volumes are reserved for administrator accounts only.
                        </p>
                    </div>
                </div>

                <!-- 3 Premium Statistics Cards Grid -->
                <div class="general-stats-grid">
                    
                    <!-- Card 1: Total Books -->
                    <div class="general-stat-card">
                        <div class="general-stat-icon-wrapper books">
                            <i class="fa-solid fa-book-bookmark"></i>
                        </div>
                        <div>
                            <span class="general-stat-label">Total Books</span>
                            <strong class="general-stat-value books">{{ $totalBooks }}</strong>
                        </div>
                    </div>

                    <!-- Card 2: New Customers -->
                    <div class="general-stat-card">
                        <div class="general-stat-icon-wrapper customers">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div>
                            <span class="general-stat-label">New Customers (7d)</span>
                            <strong class="general-stat-value customers">{{ $newCustomersCount }}</strong>
                        </div>
                    </div>

                    <!-- Card 3: Low Stock Items -->
                    @php
                        $isLowStock = $lowStockCount > 0;
                    @endphp
                    <div class="general-stat-card">
                        <div class="general-stat-icon-wrapper lowstock @if($isLowStock) warning @endif">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div>
                            <span class="general-stat-label">Low Stock Items</span>
                            <strong class="general-stat-value lowstock @if($isLowStock) warning @endif">{{ $lowStockCount }}</strong>
                        </div>
                    </div>

                </div>
            @endif
        </div>


        
        <div class="data-table-card">
            <div class="card-header-flex">
                <div class="header-title-group">
                    <h3>Low Stock Alerts</h3>
                    <p><i class="fa-solid fa-triangle-exclamation"></i> Stock units less than 5</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="modern-table low-stock-table">
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
                                    @if($book->image)
                                        <img src="{{ asset('storage/'.$book->image) }}" alt="Cover" class="table-book-cover">
                                    @else
                                        <div class="table-book-cover-placeholder" title="{{ $book->name }}">
                                            <i class="fa-solid fa-book"></i>
                                        </div>
                                    @endif
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
        </div> <!-- End Left Dashboard Column -->

        <div class="side-widgets-column">
            <!-- 1. Quick Admin Insights -->
            <div class="offer-box-dark">
                <h3>Quick Admin Insights</h3>
                <p>Keep your catalog updated. Out-of-stock products affect your front-end store availability and sales conversions.</p>
                <a href="{{ route('admin.authors.index') }}" class="btn-manage-offer">
                    <i class="fa-solid fa-plus"></i> Add New Book
                </a>
            </div>
            
            <div class="widget-card">
                <div class="widget-header-inline">
                    <h3>Active Staff</h3>
                    @if(auth()->guard('staff')->user()->role?->name === 'admin')
                        <a href="{{ route('admin.staff.index') }}" class="btn-widget-link">See all &rsaquo;</a>
                    @endif
                </div>
                
                <div class="staff-list">
                    @forelse($activeStaff as $staff)
                        <div class="staff-item">
                            <div class="staff-avatar" style="display: flex; align-items: center; justify-content: center; overflow: hidden; background: #eef2f5; border-radius: 50%;">
                                @if($staff->image)
                                    <img src="{{ asset('storage/' . $staff->image) }}" alt="{{ $staff->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <i class="fa-solid fa-user-tie" style="color: #4a5568; font-size: 1.1rem;"></i>
                                @endif
                            </div>
                            <div class="staff-details">
                                <h5>{{ $staff->name }}</h5>
                                <p>{{ $staff->role?->name === 'admin' ? 'System Administrator' : 'Staff Member' }}</p>
                            </div>
                            <span class="staff-status-badge">Active</span>
                        </div>
                    @empty
                        <p class="text-mute" style="font-size: 0.85rem; padding: 10px 0; color: #a0aec0;">No active staff found.</p>
                    @endforelse
                </div>
            </div>

            <!-- Activity History Widget -->
            <div class="widget-card" style="margin-bottom: 24px;">
                <div class="widget-header-inline">
                    <h3>Recent Activity</h3>
                    <a href="{{ route('admin.activity-logs.index') }}" class="btn-widget-link">See all &rsaquo;</a>
                </div>
                
                <div class="staff-list" style="display: flex; flex-direction: column; gap: 12px; margin-top: 15px;">
                    @forelse($activeStaffLogs ?? [] as $log)
                        @php
                            $logIcon = 'fa-circle-dot';
                            $logColor = '#718096';
                            switch($log->action) {
                                case 'create':
                                    $logIcon = 'fa-circle-plus';
                                    $logColor = '#38a169';
                                    break;
                                case 'update':
                                    $logIcon = 'fa-pen-to-square';
                                    $logColor = '#dd6b20';
                                    break;
                                case 'delete':
                                    $logIcon = 'fa-trash-can';
                                    $logColor = '#e53e3e';
                                    break;
                                case 'status_change':
                                    $logIcon = 'fa-toggle-on';
                                    $logColor = '#805ad5';
                                    break;
                                case 'login':
                                    $logIcon = 'fa-right-to-bracket';
                                    $logColor = '#3182ce';
                                    break;
                                case 'logout':
                                    $logIcon = 'fa-right-from-bracket';
                                    $logColor = '#3182ce';
                                    break;
                            }
                        @endphp
                        <div class="staff-item" style="align-items: flex-start; padding: 6px 0; border-bottom: 1px solid rgba(0,0,0,0.03); gap: 10px; margin-bottom: 0;">
                            <div class="log-widget-icon" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; border-radius: 4px; background: rgba(0,0,0,0.03); color: {{ $logColor }}; font-size: 0.85rem; flex-shrink: 0; margin-top: 2px;">
                                <i class="fa-solid {{ $logIcon }}"></i>
                            </div>
                            <div style="flex-grow: 1; min-width: 0;">
                                <div style="font-size: 0.82rem; color: #2d3748; line-height: 1.35; word-wrap: break-word;">
                                    <strong>{{ $log->staff?->name ?? 'System' }}</strong>: {{ Str::limit($log->description, 60) }}
                                </div>
                                <span style="font-size: 0.72rem; color: #a0aec0;">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-mute" style="font-size: 0.82rem; padding: 10px 0; color: #a0aec0;">No recent actions recorded.</p>
                    @endforelse
                </div>
            </div>



        </div>

    </div>
</div>
@endsection