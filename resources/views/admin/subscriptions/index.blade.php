@extends('admin.layouts.app')

@section('title', 'Booknest Admin - Subscriptions')

@section('styles')
<style>
    .stats-overview-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 25px;
    }
    
    @media (max-width: 768px) {
        .stats-overview-grid {
            grid-template-columns: 1fr;
        }
    }

    .stat-widget-box {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 8px 30px rgba(76, 45, 23, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.6);
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .stat-widget-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .icon-total { background-color: rgba(42, 111, 151, 0.1); color: #2a6f97; }
    .icon-active { background-color: rgba(45, 106, 79, 0.1); color: #2d6a4f; }
    .icon-expired { background-color: rgba(199, 75, 49, 0.1); color: #c84b31; }

    .stat-widget-info h4 {
        margin: 0;
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.1;
    }

    .stat-widget-info p {
        margin: 4px 0 0 0;
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
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
        grid-template-columns: 2fr 1.2fr auto;
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

    .badge-status {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        text-align: center;
    }
    
    .status-active { color: #166534; background-color: #dcfce7; border: 1px solid #bbf7d0; }
    .status-expired { color: #991b1b; background-color: #fee2e2; border: 1px solid #fecaca; }
    
    .tier-monthly { color: #1e3a8a; background-color: #dbeafe; border: 1px solid #bfdbfe; }
    .tier-vip { color: #581c87; background-color: #f3e8ff; border: 1px solid #e9d5ff; }
    .tier-premium { color: #7c2d12; background-color: #ffedd5; border: 1px solid #fed7aa; }

    .btn-table-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        background-color: #2a6f97;
        color: #fff;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .btn-table-action:hover {
        background-color: #1e5575;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(42, 111, 151, 0.25);
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

    <!-- Filters -->
    <form method="GET" action="{{ route('admin.subscriptions.index') }}" class="filters-row-card">
        <button type="submit" style="display: none;"></button>
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

    <!-- Subscribers Table -->
    <div class="data-table-card">
        <div class="card-header-flex">
            <div class="header-title-group">
                <h3><i class="fa-solid fa-users-gear"></i> VIP Subscriber List</h3>
                <p>Monitor active memberships, remaining access days, and subscription tiers.</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Expiration Date</th>
                        <th>Days Remaining</th>
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
                                <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $sub->email }}</div>
                            </td>
                            <td>
                                {{ $sub->subscription_expires_at ? $sub->subscription_expires_at->format('M d, Y') : 'Lifetime / Unlimited' }}
                            </td>
                            <td>
                                @if($sub->subscription_expires_at)
                                    @if($sub->subscription_expires_at->isPast())
                                        <span style="color: var(--accent-red); font-weight: 700;">Expired</span>
                                    @else
                                        <strong>{{ ceil(now()->diffInDays($sub->subscription_expires_at, false)) }} days</strong>
                                    @endif
                                @else
                                    <span style="color: var(--accent-green); font-weight: 700;">Active (No Limit)</span>
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

    <!-- Subscription Payment Log (requested by user) -->
    <div class="data-table-card" style="margin-top: 30px;">
        <div class="card-header-flex" style="border-bottom: 1px solid #EDE8D0; padding-bottom: 12px; margin-bottom: 20px;">
            <div class="header-title-group">
                <h3><i class="fa-solid fa-receipt"></i> VIP Subscription Payment Logs</h3>
                <p>Simulated transaction rates, billing cycles, and payment histories based on membership plans.</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Subscriber</th>
                        <th>Simulated Rate</th>
                        <th>Billing Start Date</th>
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
                                <div style="font-size: 0.78rem; color: var(--text-muted);">{{ $sub->email }}</div>
                            </td>
                            <td><strong>{{ $rate }}</strong></td>
                            <td>{{ $sub->updated_at->format('M d, Y h:i A') }}</td>
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
@endsection
