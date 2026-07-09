@extends('admin.layouts.app')

@section('title', 'Booknest Admin - Subscriptions')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/subscriptions.css') }}?v=1.0.4">
@endsection

@section('content')
<div class="dashboard-wrapper-new">
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Statistics Overview -->
    <div class="stats-overview-grid">
        <div class="stat-widget-box">
            <div class="stat-widget-icon icon-total">
                <i class="fa-solid fa-crown"></i>
            </div>
            <div class="stat-widget-info">
                <h4>{{ $totalSubscribers }}</h4>
                <p>Total Subscribers</p>
            </div>
        </div>
        
        <div class="stat-widget-box">
            <div class="stat-widget-icon icon-active">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="stat-widget-info">
                <h4>{{ $activeSubscribers }}</h4>
                <p>Active VIPs</p>
            </div>
        </div>

        <div class="stat-widget-box">
            <div class="stat-widget-icon icon-expired">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="stat-widget-info">
                <h4>{{ $expiredSubscribers }}</h4>
                <p>Expired VIPs</p>
            </div>
        </div>
    </div>

    <!-- Tabbed Data Table Card -->
    <div class="data-table-card">
        <div class="card-header-flex" style="border-bottom: 1.5px solid #EDE8D0; padding-bottom: 15px; margin-bottom: 20px; align-items: center;">
            <div class="tabs-navigation-modern" style="border-bottom: none; margin-bottom: 0; padding-bottom: 0; gap: 8px;">
                <button class="tab-btn-modern active" onclick="switchSubTab('subscribers-list', this)">
                    <i class="fa-solid fa-users-gear"></i> VIP Subscriber List
                </button>
                <button class="tab-btn-modern" onclick="switchSubTab('payment-logs', this)">
                    <i class="fa-solid fa-receipt"></i> VIP Subscription Payment Logs
                </button>
            </div>
        </div>

        <!-- Filters integrated inside the card -->
        <form method="GET" action="{{ route('admin.subscriptions.index') }}" class="filters-row-card-inline filters-grid-3">
            <button type="submit" class="display-none"></button>
            <div>
                <input type="text" name="search" placeholder="Search by subscriber name or email..." value="{{ request('search') }}" class="filter-input">
            </div>
            <div>
                <select name="status" class="filter-input" onchange="this.form.requestSubmit()">
                    <option value="">-- All VIP Statuses --</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </div>
            <div>
                <a href="{{ route('admin.subscriptions.index') }}" class="btn-filter-reset" title="Reset Filters"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>

        <!-- Subscribers Table Pane -->
        <div id="subscribers-list-tab" class="tab-pane-modern active">
            <div class="table-responsive">
                <table class="modern-table subscribers-table">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Expiration Date</th>
                            <th class="tablet-hide">Days Remaining</th>
                            <th>VIP Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscribers as $sub)
                            @php
                                $isActive = $sub->subscription_status === 'active' && ($sub->subscription_expires_at === null || $sub->subscription_expires_at->isFuture());
                            @endphp
                            <tr>
                                <td>
                                    <div><strong>{{ $sub->name }}</strong></div>
                                    <div class="font-size-0-8-text-muted">{{ $sub->email }}</div>
                                </td>
                                <td>
                                    {{ $sub->subscription_expires_at ? $sub->subscription_expires_at->format('M d, Y') : 'Lifetime / Unlimited' }}
                                </td>
                                <td class="tablet-hide">
                                    @if($sub->subscription_expires_at)
                                        @if($sub->subscription_expires_at->isPast())
                                            <span class="color-accent-red-bold">Expired</span>
                                        @else
                                            <strong>{{ ceil(now()->diffInDays($sub->subscription_expires_at, false)) }} days</strong>
                                        @endif
                                    @else
                                        <span class="color-accent-green-bold">Active (No Limit)</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge-status status-{{ $isActive ? 'active' : 'expired' }}">
                                        {{ $isActive ? 'Active' : 'Expired' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.customers.show', $sub->id) }}" class="btn-table-action" title="Manage Subscription">
                                        <i class="fa-solid fa-user-gear"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="table-empty-state">
                                    📭 No active or expired subscribers matched your criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="pagination-wrapper">
                {{ $subscribers->links() }}
            </div>
        </div>

        <!-- Payment Logs Table Pane -->
        <div id="payment-logs-tab" class="tab-pane-modern display-none">
            <div class="table-responsive">
                <table class="modern-table billing-logs-table">
                    <thead>
                        <tr>
                            <th>Subscriber</th>
                            <th>Simulated Rate</th>
                            <th class="tablet-hide">Billing Start Date</th>
                            <th>Billing Expiry Date</th>
                            <th>Payment Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscribers as $sub)
                            @php
                                $isActive = $sub->subscription_status === 'active' && ($sub->subscription_expires_at === null || $sub->subscription_expires_at->isFuture());
                                $rate = "9,900 Ks / Month";
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $sub->name }}</strong>
                                    <div class="font-size-0-78-text-muted">{{ $sub->email }}</div>
                                </td>
                                <td><strong>{{ $rate }}</strong></td>
                                <td class="tablet-hide">{{ $sub->updated_at->format('M d, Y h:i A') }}</td>
                                <td>
                                    {{ $sub->subscription_expires_at ? $sub->subscription_expires_at->format('M d, Y h:i A') : 'N/A' }}
                                </td>
                                <td>
                                    <span class="badge-status status-{{ $isActive ? 'active' : 'expired' }}">
                                        {{ $isActive ? 'PAID' : 'EXPIRED' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="table-empty-state">
                                    📭 No subscriber billing logs found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    function switchSubTab(tabId, btn) {
        // Hide all tab panes
        document.getElementById('subscribers-list-tab').classList.add('display-none');
        document.getElementById('payment-logs-tab').classList.add('display-none');
        
        // Show target tab pane
        document.getElementById(tabId + '-tab').classList.remove('display-none');
        
        // Remove active class from all tab buttons
        document.querySelectorAll('.tab-btn-modern').forEach(el => el.classList.remove('active'));
        
        // Add active class to clicked button
        btn.classList.add('active');
    }
    </script>

</div>
@endsection
