@extends('admin.layouts.app')

@section('title', 'Booknest Admin - Manage ' . $customer->name)

@section('styles')
<style>
    .customer-profile-grid {
        display: grid;
        grid-template-columns: 1fr 1.2fr;
        gap: 25px;
        margin-top: 20px;
    }
    
    @media (max-width: 1024px) {
        .customer-profile-grid {
            grid-template-columns: 1fr;
        }
    }
    
    .detail-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 8px 30px rgba(76, 45, 23, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.6);
        margin-bottom: 25px;
        position: relative;
    }
    
    .detail-card-header {
        border-bottom: 1px solid #EDE8D0;
        padding-bottom: 12px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .detail-card-header h4 {
        margin: 0;
        font-size: 1.15rem;
        color: var(--text-main);
        font-weight: 700;
    }

    .profile-hero {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        margin-bottom: 20px;
    }

    .profile-avatar-large {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        border: 4px solid var(--border-color);
        object-fit: cover;
        box-shadow: 0 4px 15px rgba(76, 45, 23, 0.12);
        margin-bottom: 10px;
    }

    .profile-avatar-placeholder-large {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        background-color: var(--sidebar-bg);
        color: var(--sidebar-text);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 2.2rem;
        box-shadow: 0 4px 15px rgba(76, 45, 23, 0.12);
        margin-bottom: 10px;
        border: 4px solid var(--border-color);
    }
    
    .info-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    
    .info-item {
        display: flex;
        justify-content: space-between;
        font-size: 0.95rem;
        line-height: 1.5;
    }
    
    .info-label {
        color: var(--text-muted);
        font-weight: 600;
    }
    
    .info-value {
        color: var(--text-main);
        font-weight: 700;
        text-align: right;
    }
    
    .status-select-form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .status-form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    
    .status-form-group label {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text-muted);
    }
    
    .status-select-control {
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
    
    .status-select-control:focus {
        border-color: #4C2D17;
        background-color: #FFFFFF;
    }
    
    .btn-update-status {
        background-color: #4C2D17;
        color: #EDE8D0;
        border: none;
        border-radius: 8px;
        padding: 12px 20px;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s ease;
        margin-top: 10px;
    }
    
    .btn-update-status:hover {
        background-color: #351f0f;
    }

    .btn-toggle-block {
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        border: none;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s ease;
        margin-top: 20px;
    }

    .btn-block-active {
        background-color: var(--accent-red);
        color: #fff;
    }

    .btn-block-active:hover {
        background-color: #ad3e26;
    }

    .btn-block-inactive {
        background-color: var(--accent-green);
        color: #fff;
    }

    .btn-block-inactive:hover {
        background-color: #21513c;
    }
    
    .badge-status {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    
    .status-account-active { color: #166534; background-color: #dcfce7; border: 1px solid #bbf7d0; }
    .status-account-inactive { color: #991b1b; background-color: #fee2e2; border: 1px solid #fecaca; }
    
    .status-sub-active { color: #1e3a8a; background-color: #dbeafe; border: 1px solid #bfdbfe; }
    .status-sub-inactive { color: #374151; background-color: #f3f4f6; border: 1px solid #e5e7eb; }

    .progress-bar-track-small {
        width: 120px;
        height: 8px;
        background-color: #EAE6DF;
        border-radius: 4px;
        overflow: hidden;
        display: inline-block;
        vertical-align: middle;
        margin-right: 8px;
    }

    .progress-bar-fill-small {
        height: 100%;
        background-color: var(--accent-green);
        border-radius: 4px;
    }

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
</style>
@endsection

@section('content')
<div class="dashboard-wrapper-new">
    
    <div class="form-header-modern" style="margin-bottom: 20px;">
        <h2><i class="fa-solid fa-user-gear"></i> Manage Customer Account</h2>
        <a href="{{ route('admin.customers.index') }}" class="btn-back-modern">
            <i class="fa-solid fa-arrow-left"></i> Back to Customers
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="customer-profile-grid">
        <!-- Left Column: Customer Profile Card -->
        <div>
            <div class="detail-card">
                <div class="profile-hero">
                    @if($customer->image)
                        <img src="{{ asset('storage/' . $customer->image) }}" alt="avatar" class="profile-avatar-large">
                    @else
                        <div class="profile-avatar-placeholder-large">
                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                        </div>
                    @endif
                    <h3 style="margin: 5px 0 0 0; font-size: 1.3rem;">{{ $customer->name }}</h3>
                    <div style="margin-top: 5px;">
                        <span class="badge-status status-account-{{ $customer->status }}">
                            {{ $customer->status === 'active' ? 'Active' : 'Blocked' }}
                        </span>
                    </div>
                </div>

                <div class="info-list" style="border-top: 1px solid #EDE8D0; padding-top: 15px;">
                    <div class="info-item">
                        <span class="info-label">Customer ID:</span>
                        <span class="info-value">#{{ $customer->id }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email Address:</span>
                        <span class="info-value">{{ $customer->email }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Phone Number:</span>
                        <span class="info-value">{{ $customer->phone }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Gender:</span>
                        <span class="info-value" style="text-transform: capitalize;">{{ $customer->gender ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date of Birth:</span>
                        <span class="info-value">{{ $customer->dob ? $customer->dob->format('M d, Y') : 'N/A' }}</span>
                    </div>
                    <div class="info-item" style="flex-direction: column; align-items: flex-start; gap: 4px;">
                        <span class="info-label">Address:</span>
                        <span class="info-value" style="text-align: left; font-weight: 500;">{{ $customer->address ?? 'No address provided.' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Joined Date:</span>
                        <span class="info-value">{{ $customer->created_at->format('M d, Y') }}</span>
                    </div>
                </div>

                <!-- Account block toggle form -->
                <form action="{{ route('admin.customers.toggleStatus', $customer->id) }}" method="POST">
                    @csrf
                    @if($customer->status === 'active')
                        <button type="submit" class="btn-toggle-block btn-block-active">
                            <i class="fa-solid fa-user-slash"></i> Block Account / Deactivate
                        </button>
                    @else
                        <button type="submit" class="btn-toggle-block btn-block-inactive">
                            <i class="fa-solid fa-user-check"></i> Unblock Account / Activate
                        </button>
                    @endif
                </form>
            </div>
        </div>

        <!-- Right Column: Subscription & VIP Membership Details -->
        <div>
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="fa-solid fa-crown" style="color: var(--brand-gold); font-size: 1.25rem;"></i>
                    <h4>VIP Membership details</h4>
                </div>
                
                <form action="{{ route('admin.customers.updateSubscription', $customer->id) }}" method="POST" class="status-select-form">
                    @csrf
                    
                    <div class="info-list" style="margin-bottom: 10px;">
                        <div class="info-item">
                            <span class="info-label">Current Membership Type:</span>
                            <span class="info-value" style="text-transform: uppercase;">{{ $customer->subscription_status === 'active' ? 'VIP' : 'FREE' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">VIP Status:</span>
                            <span class="info-value">
                                <span class="badge-status status-sub-{{ $customer->subscription_status }}">
                                    {{ $customer->subscription_status === 'active' ? 'Active' : 'Inactive' }}
                                </span>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Membership Days Left:</span>
                            <span class="info-value">
                                @if($customer->hasActiveSubscription())
                                    <span style="color: var(--accent-green);">{{ $customer->getSubscriptionDaysLeft() }} days</span>
                                @else
                                    <span style="color: var(--text-muted);">No active subscription</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="status-form-group">
                        <label for="subscription_status">VIP Subscription Status</label>
                        <select name="subscription_status" id="subscription_status" class="status-select-control">
                            <option value="active" {{ $customer->subscription_status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $customer->subscription_status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="status-form-group">
                        <label for="subscription_expires_at">VIP Expiration Date</label>
                        <input type="date" name="subscription_expires_at" id="subscription_expires_at" 
                               value="{{ $customer->subscription_expires_at ? $customer->subscription_expires_at->format('Y-m-d') : '' }}" 
                               class="status-select-control">
                    </div>

                    <button type="submit" class="btn-update-status">
                        <i class="fa-solid fa-floppy-disk"></i> Save Membership Settings
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bottom Row: Order logs -->
    <div class="data-table-card" style="margin-top: 10px;">
        <div class="detail-card-header" style="border-bottom: 1px solid #EDE8D0; margin-bottom: 20px;">
            <i class="fa-solid fa-basket-shopping" style="color: var(--brand-gold); font-size: 1.25rem;"></i>
            <h4>Customer Order History</h4>
        </div>

        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date Placed</th>
                        <th>Total Amount</th>
                        <th>Payment Status</th>
                        <th>Order Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td><strong>#{{ $order->id }}</strong></td>
                            <td>{{ $order->created_at->format('M d, Y h:i A') }}</td>
                            <td><strong>{{ number_format($order->total_amount) }} Ks</strong></td>
                            <td>
                                <span class="badge-status status-payment-{{ $order->payment_status }}">
                                    {{ $order->payment_status }}
                                </span>
                            </td>
                            <td>
                                <span class="badge-status status-order-{{ $order->status }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn-table-action" title="View Order">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="table-empty-state">
                                📭 This customer has not placed any orders yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bottom Row: Downloads & Reading Progress -->
    <div class="data-table-card" style="margin-top: 25px;">
        <div class="detail-card-header" style="border-bottom: 1px solid #EDE8D0; margin-bottom: 20px;">
            <i class="fa-solid fa-book-reader" style="color: var(--brand-gold); font-size: 1.25rem;"></i>
            <h4>Downloads & Reading Progress</h4>
        </div>

        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Cover</th>
                        <th>Book Title</th>
                        <th>Author</th>
                        <th>Current Page</th>
                        <th>Total Pages</th>
                        <th>Completion Progress</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($progressList as $progress)
                        @php
                            $pages = $progress->item->pages ?? 0;
                            $current = $progress->current_page ?? 0;
                            $pct = $pages > 0 ? min(100, round(($current / $pages) * 100)) : 0;
                        @endphp
                        <tr>
                            <td>
                                <img src="{{ $progress->item->image ? asset('storage/' . $progress->item->image) : asset('images/default-book.png') }}" alt="{{ $progress->item->name }}" class="table-book-cover">
                            </td>
                            <td><strong>{{ $progress->item->name }}</strong></td>
                            <td><span class="text-author">{{ $progress->item->author?->name ?? 'Unknown Author' }}</span></td>
                            <td>Page {{ $current }}</td>
                            <td>{{ $pages }} pages</td>
                            <td>
                                <div class="progress-bar-track-small">
                                    <div class="progress-bar-fill-small" style="width: {{ $pct }}%"></div>
                                </div>
                                <strong style="font-size: 0.85rem; color: var(--text-main);">{{ $pct }}%</strong>
                                @if($progress->completed)
                                    <span style="color: var(--accent-green); font-size: 0.8rem; margin-left: 8px; font-weight: 700;"><i class="fa-solid fa-circle-check"></i> Completed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="table-empty-state">
                                📚 No active reading progress or book downloads recorded for this customer.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
