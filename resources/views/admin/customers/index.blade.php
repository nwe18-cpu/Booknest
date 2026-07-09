@extends('admin.layouts.app')

@section('title', 'Booknest Admin - Customers')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/customers.css') }}?v=1.0.2">
@endsection

@section('content')
<div class="dashboard-wrapper-new">
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="data-table-card">
        <div class="card-header-flex">
            <div class="header-title-group">
                <h3><i class="fa-solid fa-users"></i> Registered Customers</h3>
                <p>Manage customer access permissions, edit membership tiers, and view reading statistics.</p>
            </div>
        </div>

        <!-- Filters integrated inside the card -->
        <form method="GET" action="{{ route('admin.customers.index') }}" class="filters-row-card-inline filters-grid-4">
            <button type="submit" class="display-none"></button>
            <div>
                <input type="text" name="search" placeholder="Search by name, email, phone..." value="{{ request('search') }}" class="filter-input">
            </div>
            <div>
                <select name="status" class="filter-input" onchange="this.form.requestSubmit()">
                    <option value="">-- Account Status --</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Blocked</option>
                </select>
            </div>
            <div>
                <select name="subscription_status" class="filter-input" onchange="this.form.requestSubmit()">
                    <option value="">-- VIP Status --</option>
                    <option value="active" {{ request('subscription_status') === 'active' ? 'selected' : '' }}>VIP Active</option>
                    <option value="inactive" {{ request('subscription_status') === 'inactive' ? 'selected' : '' }}>VIP Inactive</option>
                </select>
            </div>
            <div>
                <a href="{{ route('admin.customers.index') }}" class="btn-filter-reset" title="Reset Filters"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="modern-table customers-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Contact Details</th>
                        <th>Account Status</th>
                        <th>Membership / VIP</th>
                        <th class="tablet-hide">VIP Expiration</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    @if($customer->image)
                                        <img src="{{ asset('storage/' . $customer->image) }}" alt="avatar" class="customer-avatar-small">
                                    @else
                                        <div class="customer-avatar-placeholder">
                                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <strong>{{ $customer->name }}</strong>
                                </div>
                            </td>
                            <td>
                                <div style="white-space: nowrap;"><i class="fa-regular fa-envelope text-email-icon"></i> {{ $customer->email }}</div>
                                <div class="text-phone-wrapper" style="white-space: nowrap;"><i class="fa-solid fa-phone text-phone-icon"></i> {{ $customer->phone }}</div>
                            </td>
                            <td>
                                <span class="badge-status status-account-{{ $customer->status }}">
                                    {{ $customer->status === 'active' ? 'Active' : 'Blocked' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge-status status-sub-{{ $customer->subscription_status }}">
                                    {{ $customer->subscription_status === 'active' ? 'VIP' : 'FREE' }}
                                </span>
                            </td>
                            <td class="tablet-hide">
                                <span class="font-size-0-85">
                                    @if($customer->subscription_expires_at)
                                        {{ $customer->subscription_expires_at->format('M d, Y') }}
                                        @if($customer->subscription_expires_at->isPast())
                                            <span class="expired-badge">(Expired)</span>
                                        @else
                                            <span class="active-badge">({{ ceil(now()->diffInDays($customer->subscription_expires_at, false)) }} days left)</span>
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn-table-action" title="Manage Customer">
                                    <i class="fa-solid fa-user-gear"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="table-empty-state">
                                📭 No customers matched your search or filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="pagination-wrapper">
            {{ $customers->links() }}
        </div>
    </div>
</div>
@endsection
