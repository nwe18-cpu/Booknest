@extends('admin.layouts.app')

@section('title', 'Booknest Admin - Customers')

@section('styles')
<style>
    .filters-row-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 8px 30px rgba(76, 45, 23, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.6);
        margin-bottom: 25px;
        display: grid;
        grid-template-columns: 2fr 1fr 1fr auto;
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
    
    .status-account-active { color: #166534; background-color: #dcfce7; border: 1px solid #bbf7d0; }
    .status-account-inactive { color: #991b1b; background-color: #fee2e2; border: 1px solid #fecaca; }
    
    .status-sub-active { color: #1e3a8a; background-color: #dbeafe; border: 1px solid #bfdbfe; }
    .status-sub-inactive { color: #374151; background-color: #f3f4f6; border: 1px solid #e5e7eb; }
    
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

    .customer-avatar-small {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        border: 2px solid #DCD6BC;
        object-fit: cover;
    }

    .customer-avatar-placeholder {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background-color: #4C2D17;
        color: #EDE8D0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.95rem;
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

    <!-- Filters -->
    <form method="GET" action="{{ route('admin.customers.index') }}" class="filters-row-card">
        <button type="submit" style="display: none;"></button>
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

    <div class="data-table-card">
        <div class="card-header-flex">
            <div class="header-title-group">
                <h3><i class="fa-solid fa-users"></i> Registered Customers</h3>
                <p>Manage customer access permissions, edit membership tiers, and view reading statistics.</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Avatar</th>
                        <th>Name</th>
                        <th>Contact Details</th>
                        <th>Account Status</th>
                        <th>Membership / VIP</th>
                        <th>VIP Expiration</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td>
                                @if($customer->image)
                                    <img src="{{ asset('storage/' . $customer->image) }}" alt="avatar" class="customer-avatar-small">
                                @else
                                    <div class="customer-avatar-placeholder">
                                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                                    </div>
                                @endif
                            </td>
                            <td><strong>{{ $customer->name }}</strong></td>
                            <td>
                                <div><i class="fa-regular fa-envelope" style="font-size: 0.8rem; width: 14px;"></i> {{ $customer->email }}</div>
                                <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 2px;"><i class="fa-solid fa-phone" style="font-size: 0.8rem; width: 14px;"></i> {{ $customer->phone }}</div>
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
                            <td>
                                <span style="font-size: 0.85rem;">
                                    @if($customer->subscription_expires_at)
                                        {{ $customer->subscription_expires_at->format('M d, Y') }}
                                        @if($customer->subscription_expires_at->isPast())
                                            <span style="color: var(--accent-red); font-weight: 600; display: block; font-size: 0.75rem;">(Expired)</span>
                                        @else
                                            <span style="color: var(--accent-green); font-weight: 600; display: block; font-size: 0.75rem;">({{ ceil(now()->diffInDays($customer->subscription_expires_at, false)) }} days left)</span>
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
