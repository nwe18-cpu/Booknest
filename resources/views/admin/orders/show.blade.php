@extends('admin.layouts.app')

@section('title', 'Booknest Admin - Order #' . $order->id)

@section('styles')
<style>
    .order-details-grid {
        display: grid;
        grid-template-columns: 1fr 1.2fr;
        gap: 25px;
        margin-top: 20px;
    }
    
    @media (max-width: 1024px) {
        .order-details-grid {
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
    
    .badge-status {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    
    .status-payment-pending { color: #d97706; background-color: #fef3c7; border: 1px solid #fde68a; }
    .status-payment-paid { color: #166534; background-color: #dcfce7; border: 1px solid #bbf7d0; }
    .status-payment-failed { color: #991b1b; background-color: #fee2e2; border: 1px solid #fecaca; }
    
    .status-order-pending { color: #4b5563; background-color: #f3f4f6; border: 1px solid #e5e7eb; }
    .status-order-shipped { color: #1d4ed8; background-color: #dbeafe; border: 1px solid #bfdbfe; }
    .status-order-completed { color: #166534; background-color: #dcfce7; border: 1px solid #bbf7d0; }
    .status-order-cancelled { color: #991b1b; background-color: #fee2e2; border: 1px solid #fecaca; }
</style>
@endsection

@section('content')
<div class="dashboard-wrapper-new">
    
    <div class="form-header-modern" style="margin-bottom: 20px;">
        <h2><i class="fa-solid fa-file-invoice-dollar"></i> Order Details #{{ $order->id }}</h2>
        <a href="{{ route('admin.orders.index') }}" class="btn-back-modern">
            <i class="fa-solid fa-arrow-left"></i> Back to Orders
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="order-details-grid">
        <!-- Left Column: Customer & Shipping Details -->
        <div>
            <!-- Customer Card -->
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="fa-solid fa-user-circle" style="color: var(--brand-gold); font-size: 1.25rem;"></i>
                    <h4>Customer Profile</h4>
                </div>
                <div class="info-list">
                    <div class="info-item">
                        <span class="info-label">Name:</span>
                        <span class="info-value">{{ $order->customer?->name ?? 'Guest User' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email:</span>
                        <span class="info-value">{{ $order->customer?->email ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Phone:</span>
                        <span class="info-value">{{ $order->customer?->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Registered Status:</span>
                        <span class="info-value" style="text-transform: capitalize;">{{ $order->customer?->status ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Shipping Address Card -->
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="fa-solid fa-truck" style="color: var(--brand-gold); font-size: 1.25rem;"></i>
                    <h4>Shipping Address</h4>
                </div>
                @if($order->shippingAddress)
                    <div class="info-list">
                        <div class="info-item">
                            <span class="info-label">Receiver Name:</span>
                            <span class="info-value">{{ $order->shippingAddress->receiver_name }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Phone:</span>
                            <span class="info-value">{{ $order->shippingAddress->phone_number }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Email:</span>
                            <span class="info-value">{{ $order->shippingAddress->email }}</span>
                        </div>
                        <div class="info-item" style="flex-direction: column; align-items: flex-start; gap: 4px;">
                            <span class="info-label">Address Line:</span>
                            <span class="info-value" style="text-align: left; font-weight: 500; margin-top: 2px;">{{ $order->shippingAddress->address_line }}</span>
                        </div>
                    </div>
                @else
                    <div class="table-empty-state" style="padding: 10px;">
                        ℹ️ No shipping details associated with this digital-only order.
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Order status and update form -->
        <div>
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="fa-solid fa-gears" style="color: var(--brand-gold); font-size: 1.25rem;"></i>
                    <h4>Manage Status</h4>
                </div>
                
                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="status-select-form">
                    @csrf
                    
                    <div class="info-list" style="margin-bottom: 10px;">
                        <div class="info-item">
                            <span class="info-label">Order Total:</span>
                            <span class="info-value" style="font-size: 1.2rem; color: var(--text-main);">{{ number_format($order->total_amount) }} Ks</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Date Placed:</span>
                            <span class="info-value">{{ $order->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                        @if($order->stripe_session_id)
                            <div class="info-item">
                                <span class="info-label">Stripe Session:</span>
                                <span class="info-value" style="font-size: 0.8rem; word-break: break-all;">{{ $order->stripe_session_id }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="status-form-group">
                        <label for="payment_status">Payment Status</label>
                        <select name="payment_status" id="payment_status" class="status-select-control">
                            <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>

                    <div class="status-form-group">
                        <label for="status">Order Status</label>
                        <select name="status" id="status" class="status-select-control">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-update-status">
                        <i class="fa-solid fa-floppy-disk"></i> Update Status & Notify Customer
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bottom Row: Order items list -->
    <div class="data-table-card" style="margin-top: 10px;">
        <div class="detail-card-header" style="border-bottom: 1px solid #EDE8D0; margin-bottom: 20px;">
            <i class="fa-solid fa-basket-shopping" style="color: var(--brand-gold); font-size: 1.25rem;"></i>
            <h4>Items Ordered</h4>
        </div>

        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Cover</th>
                        <th>Book Title</th>
                        <th>Author</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderItems as $item)
                        <tr>
                            <td>
                                <img src="{{ $item->item->image ? asset('storage/' . $item->item->image) : asset('images/default-book.png') }}" alt="{{ $item->item->name }}" class="table-book-cover">
                            </td>
                            <td><strong>{{ $item->item->name }}</strong></td>
                            <td><span class="text-author">{{ $item->item->author?->name ?? 'Unknown Author' }}</span></td>
                            <td>{{ number_format($item->price) }} Ks</td>
                            <td>{{ $item->quantity }}</td>
                            <td><strong>{{ number_format($item->price * $item->quantity) }} Ks</strong></td>
                        </tr>
                    @endforeach
                    <tr style="background-color: #faf8f5;">
                        <td colspan="4"></td>
                        <td style="text-align: right;"><strong>Grand Total:</strong></td>
                        <td><strong style="font-size: 1.1rem; color: var(--text-main);">{{ number_format($order->total_amount) }} Ks</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
