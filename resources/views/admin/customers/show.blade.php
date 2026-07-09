@extends('admin.layouts.app')

@section('title', 'Booknest Admin - Manage ' . $customer->name)

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/customers.css') }}?v=1.0.1">
@endsection

@section('content')
<div class="dashboard-wrapper-new">
    
    <div class="form-header-modern margin-bottom-20">
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
                    <h3 class="font-size-1-3-margin-top-5-0-0-0">{{ $customer->name }}</h3>
                    <div class="margin-top-5">
                        <span class="badge-status status-account-{{ $customer->status }}">
                            {{ $customer->status === 'active' ? 'Active' : 'Blocked' }}
                        </span>
                    </div>
                </div>

                <div class="info-list border-top-cream-pad-15">
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
                        <span class="info-value text-transform-capitalize">{{ $customer->gender ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date of Birth:</span>
                        <span class="info-value">{{ $customer->dob ? $customer->dob->format('M d, Y') : 'N/A' }}</span>
                    </div>
                    <div class="info-item flex-column-align-start-gap-4">
                        <span class="info-label">Address:</span>
                        <span class="info-value text-align-left-font-weight-500">{{ $customer->address ?? 'No address provided.' }}</span>
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
                    <i class="fa-solid fa-crown color-brand-gold-font-size-1-25"></i>
                    <h4>VIP Membership details</h4>
                </div>
                
                <form action="{{ route('admin.customers.updateSubscription', $customer->id) }}" method="POST" class="status-select-form">
                    @csrf
                    
                    <div class="info-list margin-bottom-10">
                        <div class="info-item">
                            <span class="info-label">Current Membership Type:</span>
                            <span class="info-value text-transform-uppercase">{{ $customer->subscription_status === 'active' ? 'VIP' : 'FREE' }}</span>
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
                                    <span class="color-accent-green">{{ $customer->getSubscriptionDaysLeft() }} days</span>
                                @else
                                    <span class="color-text-muted">No active subscription</span>
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
    <div class="data-table-card margin-top-10">
        <div class="detail-card-header">
            <i class="fa-solid fa-basket-shopping color-brand-gold-font-size-1-25"></i>
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



</div>
@endsection
