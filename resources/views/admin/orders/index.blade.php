@extends('admin.layouts.app')

@section('title', 'Booknest Admin - Orders')

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
    
    .status-payment-pending { color: #d97706; background-color: #fef3c7; border: 1px solid #fde68a; }
    .status-payment-paid { color: #166534; background-color: #dcfce7; border: 1px solid #bbf7d0; }
    .status-payment-failed { color: #991b1b; background-color: #fee2e2; border: 1px solid #fecaca; }
    
    .status-order-pending { color: #4b5563; background-color: #f3f4f6; border: 1px solid #e5e7eb; }
    .status-order-shipped { color: #1d4ed8; background-color: #dbeafe; border: 1px solid #bfdbfe; }
    .status-order-completed { color: #166534; background-color: #dcfce7; border: 1px solid #bbf7d0; }
    .status-order-cancelled { color: #991b1b; background-color: #fee2e2; border: 1px solid #fecaca; }
    
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

    <!-- Filters -->
    <form method="GET" action="{{ route('admin.orders.index') }}" class="filters-row-card">
        <button type="submit" style="display: none;"></button>
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
