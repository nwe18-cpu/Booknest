@extends('admin.layouts.app')

@section('title', 'Booknest Admin - Orders')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/orders.css') }}?v=1.0.1">
@endsection

@section('content')
<div class="dashboard-wrapper-new">
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters -->
    <form method="GET" action="{{ route('admin.orders.index') }}" class="filters-row-card">
        <button type="submit" class="display-none"></button>
        <div>
            <input type="text" name="search" placeholder="Search by Order ID or Customer..." value="{{ request('search') }}" class="filter-input">
        </div>
        <div>
            <select name="status" class="filter-input" onchange="this.form.requestSubmit()">
                <option value="">-- All Order Statuses --</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <div>
            <select name="payment_status" class="filter-input" onchange="this.form.requestSubmit()">
                <option value="">-- All Payment Statuses --</option>
                <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
        </div>
        <div>
            <a href="{{ route('admin.orders.index') }}" class="btn-filter-reset" title="Reset Filters"><i class="fa-solid fa-rotate-left"></i></a>
        </div>
    </form>

    <div class="data-table-card">
        <div class="card-header-flex">
            <div class="header-title-group">
                <h3><i class="fa-solid fa-cart-shopping"></i> Customer Orders</h3>
                <p>Monitor customer purchases, update order fulfillment, and check invoice statuses.</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total Amount</th>
                        <th>Payment Status</th>
                        <th>Order Status</th>
                        <th>Date Ordered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td><strong>#{{ $order->id }}</strong></td>
                            <td>
                                <div><strong>{{ $order->customer?->name ?? 'Guest User' }}</strong></div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $order->customer?->email }}</div>
                            </td>
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
                            <td><span style="font-size: 0.85rem;">{{ $order->created_at->format('M d, Y h:i A') }}</span></td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn-table-action" title="View Order">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="table-empty-state">
                                📭 No orders matched your search or filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="pagination-wrapper">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
